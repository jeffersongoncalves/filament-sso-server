<?php

use Filament\Actions\Testing\TestAction;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoClients\Pages\CreateSsoClient;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoClients\Pages\EditSsoClient;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoClients\Pages\ListSsoClients;
use JeffersonGoncalves\SsoServer\Models\SsoClient;

use function Pest\Livewire\livewire;

beforeEach(fn () => actingAsAdmin());

it('lists clients', function () {
    $client = createClient();

    livewire(ListSsoClients::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$client]);
});

it('generates credentials server-side on create', function () {
    livewire(CreateSsoClient::class)
        ->fillForm([
            'name' => 'Portal',
            'redirect_uri' => 'https://portal.test/sso/callback',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified(__('filament-sso-server::default.clients.notifications.created'));

    $client = SsoClient::sole();

    expect($client->client_id)->toBeUuid()
        ->and($client->client_secret)->toHaveLength(64)
        ->and($client->getRawOriginal('client_secret'))->not->toBe($client->client_secret);
});

it('validates the redirect uri', function () {
    livewire(CreateSsoClient::class)
        ->fillForm(['name' => 'Portal', 'redirect_uri' => 'not-a-url'])
        ->call('create')
        ->assertHasFormErrors(['redirect_uri' => 'url']);
});

it('does not let the edit form change the client id', function () {
    $client = createClient();
    $clientId = $client->client_id;

    livewire(EditSsoClient::class, ['record' => $client->getRouteKey()])
        ->fillForm(['name' => 'Renamed', 'client_id' => 'forged'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($client->fresh())
        ->name->toBe('Renamed')
        ->client_id->toBe($clientId);
});

it('rotates the client secret', function () {
    $client = createClient();

    livewire(ListSsoClients::class)
        ->callAction(TestAction::make('rotateSecret')->table($client))
        ->assertNotified(__('filament-sso-server::default.clients.notifications.rotated'));

    expect($client->fresh()->client_secret)->not->toBe('old-secret')->toHaveLength(64);
});
