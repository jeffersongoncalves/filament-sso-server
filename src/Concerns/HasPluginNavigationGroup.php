<?php

namespace JeffersonGoncalves\Filament\SsoServer\Concerns;

use JeffersonGoncalves\Filament\SsoServer\SsoServerPlugin;

/**
 * Every resource this plugin registers shares one navigation group.
 * SsoServerPlugin::navigationGroup() overrides it panel-wide; unset, it falls
 * back to a translated default.
 */
trait HasPluginNavigationGroup
{
    public static function getNavigationGroup(): ?string
    {
        return SsoServerPlugin::get()->getNavigationGroup();
    }
}
