<?php

namespace JeffersonGoncalves\Filament\SsoServer\Resources\SsoClients\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoClients\SsoClientResource;

class ListSsoClients extends ListRecords
{
    protected static string $resource = SsoClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
