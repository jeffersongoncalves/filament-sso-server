<?php

namespace JeffersonGoncalves\Filament\SsoServer\Resources\SsoActiveSessionResource\Pages;

use Filament\Resources\Pages\ListRecords;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoActiveSessionResource;

class ListSsoActiveSessions extends ListRecords
{
    protected static string $resource = SsoActiveSessionResource::class;
}
