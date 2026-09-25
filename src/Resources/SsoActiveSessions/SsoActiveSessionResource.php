<?php

namespace JeffersonGoncalves\Filament\SsoServer\Resources\SsoActiveSessions;

use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use JeffersonGoncalves\Filament\SsoServer\Concerns\HasPluginNavigationGroup;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoActiveSessions\Pages\ListSsoActiveSessions;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoActiveSessions\Tables\SsoActiveSessionsTable;
use JeffersonGoncalves\SsoServer\Models\SsoActiveSession;

class SsoActiveSessionResource extends Resource
{
    use HasPluginNavigationGroup;

    protected static ?string $model = SsoActiveSession::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return SsoActiveSessionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSsoActiveSessions::route('/'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('filament-sso-server::default.sessions.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-sso-server::default.sessions.plural_label');
    }

    // Sessions are issued by the token exchange, never by hand.
    public static function canCreate(): bool
    {
        return false;
    }
}
