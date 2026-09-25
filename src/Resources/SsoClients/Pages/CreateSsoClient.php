<?php

namespace JeffersonGoncalves\Filament\SsoServer\Resources\SsoClients\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoClients\SsoClientResource;
use JeffersonGoncalves\SsoServer\Models\SsoClient;

class CreateSsoClient extends CreateRecord
{
    protected static string $resource = SsoClientResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Same credentials shape as `php artisan sso-server:client`.
        return [
            ...$data,
            'client_id' => (string) Str::uuid(),
            'client_secret' => Str::random(64),
        ];
    }

    protected function afterCreate(): void
    {
        /** @var SsoClient $record */
        $record = $this->record;

        SsoClientResource::notifySecret(__('filament-sso-server::default.clients.notifications.created'), $record->client_secret);
    }

    // Replaced by the secret notification sent in afterCreate().
    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }
}
