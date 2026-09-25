<?php

use JeffersonGoncalves\Filament\SsoServer\Tests\Fixtures\User;
use JeffersonGoncalves\Filament\SsoServer\Tests\TestCase;
use JeffersonGoncalves\SsoServer\Models\SsoActiveSession;
use JeffersonGoncalves\SsoServer\Models\SsoClient;

uses(TestCase::class)->in('Feature');

function actingAsAdmin(): void
{
    test()->actingAs(User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => 'secret']));
}

function createClient(array $overrides = []): SsoClient
{
    return SsoClient::create(array_merge([
        'name' => 'Portal',
        'client_id' => (string) str()->uuid(),
        'client_secret' => 'old-secret',
        'redirect_uri' => 'https://portal.test/sso/callback',
        'is_active' => true,
    ], $overrides));
}

function createSession(SsoClient $client, array $overrides = []): SsoActiveSession
{
    return SsoActiveSession::create(array_merge([
        'client_id' => $client->id,
        'user_id' => '1',
        'session_token_hash' => hash('sha256', uniqid('', true)),
        'expires_at' => now()->addHour(),
    ], $overrides));
}
