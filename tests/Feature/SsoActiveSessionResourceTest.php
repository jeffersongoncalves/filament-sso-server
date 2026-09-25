<?php

use Illuminate\Support\Facades\Queue;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoActiveSessionResource;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoActiveSessionResource\Pages\ListSsoActiveSessions;
use JeffersonGoncalves\SsoServer\Jobs\DispatchSingleLogoutJob;
use JeffersonGoncalves\SsoServer\Models\SsoActiveSession;

use function Pest\Livewire\livewire;

beforeEach(fn () => actingAsAdmin());

it('lists sessions and cannot create them', function () {
    $session = createSession(createClient());

    livewire(ListSsoActiveSessions::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$session]);

    expect(SsoActiveSessionResource::canCreate())->toBeFalse();
});

it('filters valid sessions', function () {
    $client = createClient();
    $live = createSession($client);
    $expired = createSession($client, ['expires_at' => now()->subMinute()]);

    livewire(ListSsoActiveSessions::class)
        ->filterTable('valid', true)
        ->assertCanSeeTableRecords([$live])
        ->assertCanNotSeeTableRecords([$expired]);
});

it('revokes a single session', function () {
    $client = createClient();
    $session = createSession($client);
    $other = createSession($client);

    livewire(ListSsoActiveSessions::class)
        ->callTableAction('delete', $session);

    expect(SsoActiveSession::find($session->id))->toBeNull()
        ->and(SsoActiveSession::find($other->id))->not->toBeNull();
});

it('logs the user out of every client with single logout', function () {
    Queue::fake();

    $portal = createClient(['slo_webhook_url' => 'https://portal.test/sso/logout']);
    $crm = createClient(['name' => 'CRM', 'slo_webhook_url' => 'https://crm.test/sso/logout']);
    $session = createSession($portal, ['user_id' => '7']);
    createSession($crm, ['user_id' => '7']);
    $otherUser = createSession($portal, ['user_id' => '8']);

    livewire(ListSsoActiveSessions::class)
        ->callTableAction('logoutUser', $session);

    expect(SsoActiveSession::where('user_id', '7')->count())->toBe(0)
        ->and(SsoActiveSession::find($otherUser->id))->not->toBeNull();

    Queue::assertPushed(DispatchSingleLogoutJob::class, 2);
});
