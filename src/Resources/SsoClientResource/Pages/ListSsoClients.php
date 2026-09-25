<?php

namespace JeffersonGoncalves\Filament\SsoServer\Resources\SsoClientResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoClientResource;

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
