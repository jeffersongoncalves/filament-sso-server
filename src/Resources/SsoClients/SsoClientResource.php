<?php

namespace JeffersonGoncalves\Filament\SsoServer\Resources\SsoClients;

use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use JeffersonGoncalves\Filament\SsoServer\Concerns\HasPluginNavigationGroup;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoClients\Pages\CreateSsoClient;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoClients\Pages\EditSsoClient;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoClients\Pages\ListSsoClients;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoClients\Schemas\SsoClientForm;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoClients\Tables\SsoClientsTable;
use JeffersonGoncalves\SsoServer\Models\SsoClient;

class SsoClientResource extends Resource
{
    use HasPluginNavigationGroup;

    protected static ?string $model = SsoClient::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return SsoClientForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SsoClientsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSsoClients::route('/'),
            'create' => CreateSsoClient::route('/create'),
            'edit' => EditSsoClient::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('filament-sso-server::default.clients.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-sso-server::default.clients.plural_label');
    }

    /**
     * The plain secret is only shown here, once: the model stores it encrypted
     * and hides it from serialization.
     */
    public static function notifySecret(string $title, string $secret): void
    {
        Notification::make()
            ->title($title)
            ->body(__('filament-sso-server::default.clients.notifications.secret_body', ['secret' => $secret]))
            ->warning()
            ->persistent()
            ->send();
    }
}
