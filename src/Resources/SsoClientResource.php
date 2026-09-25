<?php

namespace JeffersonGoncalves\Filament\SsoServer\Resources;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use JeffersonGoncalves\Filament\SsoServer\Concerns\HasPluginNavigationGroup;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoClientResource\Pages\CreateSsoClient;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoClientResource\Pages\EditSsoClient;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoClientResource\Pages\ListSsoClients;
use JeffersonGoncalves\SsoServer\Models\SsoClient;

class SsoClientResource extends Resource
{
    use HasPluginNavigationGroup;

    protected static ?string $model = SsoClient::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Section::make(__('filament-sso-server::default.clients.sections.identity'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament-sso-server::default.clients.fields.name'))
                            ->required()
                            ->maxLength(255),
                        // client_id and client_secret are generated server-side on
                        // create, never taken from the (tamperable) form payload.
                        TextInput::make('client_id')
                            ->label(__('filament-sso-server::default.clients.fields.client_id'))
                            ->disabled()
                            ->dehydrated(false)
                            ->hiddenOn('create'),
                    ]),
                Section::make(__('filament-sso-server::default.clients.sections.endpoints'))
                    ->schema([
                        TextInput::make('redirect_uri')
                            ->label(__('filament-sso-server::default.clients.fields.redirect_uri'))
                            ->helperText(__('filament-sso-server::default.clients.fields.redirect_uri_help'))
                            ->url()
                            ->required()
                            ->maxLength(2048),
                        TextInput::make('slo_webhook_url')
                            ->label(__('filament-sso-server::default.clients.fields.slo_webhook_url'))
                            ->helperText(__('filament-sso-server::default.clients.fields.slo_webhook_url_help'))
                            ->url()
                            ->maxLength(2048),
                        Toggle::make('is_active')
                            ->label(__('filament-sso-server::default.clients.fields.is_active'))
                            ->helperText(__('filament-sso-server::default.clients.fields.is_active_help'))
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament-sso-server::default.clients.fields.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('client_id')
                    ->label(__('filament-sso-server::default.clients.fields.client_id'))
                    ->fontFamily('mono')
                    ->searchable()
                    ->copyable(),
                IconColumn::make('is_active')
                    ->label(__('filament-sso-server::default.clients.fields.is_active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('active_sessions_count')
                    ->label(__('filament-sso-server::default.clients.fields.active_sessions'))
                    ->counts(['activeSessions' => fn (Builder $query): Builder => $query->where('expires_at', '>', now())])
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'gray'),
                TextColumn::make('created_at')
                    ->label(__('filament-sso-server::default.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('filament-sso-server::default.clients.fields.is_active')),
            ])
            ->actions([
                EditAction::make(),
                static::configureRotateSecretAction(Action::make('rotateSecret')),
                DeleteAction::make()
                    ->modalDescription(__('filament-sso-server::default.clients.actions.delete_description')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
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
     * Filament v3 splits table and page actions into two classes, so both
     * share this configuration.
     *
     * @template T of Action|\Filament\Actions\Action
     *
     * @param  T  $action
     * @return T
     */
    public static function configureRotateSecretAction(Action|\Filament\Actions\Action $action): Action|\Filament\Actions\Action
    {
        return $action
            ->label(__('filament-sso-server::default.clients.actions.rotate_secret'))
            ->icon('heroicon-o-arrow-path')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading(__('filament-sso-server::default.clients.actions.rotate_secret'))
            ->modalDescription(__('filament-sso-server::default.clients.actions.rotate_secret_description'))
            ->action(function (SsoClient $record): void {
                $secret = Str::random(64);

                $record->update(['client_secret' => $secret]);

                static::notifySecret(__('filament-sso-server::default.clients.notifications.rotated'), $secret);
            });
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
