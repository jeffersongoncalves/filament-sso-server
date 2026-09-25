<?php

namespace JeffersonGoncalves\Filament\SsoServer\Resources\SsoActiveSessions\Pages;

use Filament\Resources\Pages\ListRecords;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoActiveSessions\SsoActiveSessionResource;

class ListSsoActiveSessions extends ListRecords
{
    protected static string $resource = SsoActiveSessionResource::class;
}
