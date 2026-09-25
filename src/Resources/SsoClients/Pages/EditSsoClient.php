<?php

namespace JeffersonGoncalves\Filament\SsoServer\Resources\SsoClients\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoClients\SsoClientResource;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoClients\Tables\SsoClientsTable;

class EditSsoClient extends EditRecord
{
    protected static string $resource = SsoClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SsoClientsTable::rotateSecretAction(),
            DeleteAction::make(),
        ];
    }
}
