<?php

namespace JeffersonGoncalves\Filament\SsoServer\Resources;

use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use JeffersonGoncalves\Filament\SsoServer\Concerns\HasPluginNavigationGroup;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoActiveSessionResource\Pages\ListSsoActiveSessions;
use JeffersonGoncalves\SsoServer\Facades\SsoServer;
use JeffersonGoncalves\SsoServer\Models\SsoActiveSession;

class SsoActiveSessionResource extends Resource
{
    use HasPluginNavigationGroup;

    protected static ?string $model = SsoActiveSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->label(__('filament-sso-server::default.sessions.fields.client'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('user_id')
                    ->label(__('filament-sso-server::default.sessions.fields.user_id'))
                    ->fontFamily('mono')
                    ->searchable(),
                TextColumn::make('session_token_hash')
                    ->label(__('filament-sso-server::default.sessions.fields.token_hash'))
                    ->fontFamily('mono')
                    ->limit(16),
                TextColumn::make('expires_at')
                    ->label(__('filament-sso-server::default.sessions.fields.expires_at'))
                    ->dateTime()
                    ->sortable()
                    ->color(fn (SsoActiveSession $record): string => $record->expires_at->isPast() ? 'danger' : 'success'),
                TextColumn::make('created_at')
                    ->label(__('filament-sso-server::default.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('client_id')
                    ->label(__('filament-sso-server::default.sessions.fields.client'))
                    ->relationship('client', 'name'),
                TernaryFilter::make('valid')
                    ->label(__('filament-sso-server::default.sessions.filters.valid'))
                    ->queries(
                        true: fn (Builder $query): Builder => $query->where('expires_at', '>', now()),
                        false: fn (Builder $query): Builder => $query->where('expires_at', '<=', now()),
                        blank: fn (Builder $query): Builder => $query,
                    ),
            ])
            ->actions([
                DeleteAction::make()
                    ->label(__('filament-sso-server::default.sessions.actions.revoke'))
                    ->modalHeading(__('filament-sso-server::default.sessions.actions.revoke'))
                    ->modalDescription(__('filament-sso-server::default.sessions.actions.revoke_description')),
                Action::make('logoutUser')
                    ->label(__('filament-sso-server::default.sessions.actions.logout_user'))
                    ->icon('heroicon-o-arrow-right-start-on-rectangle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('filament-sso-server::default.sessions.actions.logout_user'))
                    ->modalDescription(__('filament-sso-server::default.sessions.actions.logout_user_description'))
                    ->action(function (SsoActiveSession $record): void {
                        // Revokes every session of the user and fires the SLO webhooks.
                        SsoServer::logoutUser($record->user_id);

                        Notification::make()
                            ->title(__('filament-sso-server::default.sessions.notifications.logged_out'))
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('filament-sso-server::default.sessions.actions.revoke_selected')),
                ]),
            ]);
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
