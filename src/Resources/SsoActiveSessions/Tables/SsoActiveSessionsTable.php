<?php

namespace JeffersonGoncalves\Filament\SsoServer\Resources\SsoActiveSessions\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use JeffersonGoncalves\SsoServer\Facades\SsoServer;
use JeffersonGoncalves\SsoServer\Models\SsoActiveSession;

class SsoActiveSessionsTable
{
    public static function configure(Table $table): Table
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
            ->recordActions([
                DeleteAction::make()
                    ->label(__('filament-sso-server::default.sessions.actions.revoke'))
                    ->modalHeading(__('filament-sso-server::default.sessions.actions.revoke'))
                    ->modalDescription(__('filament-sso-server::default.sessions.actions.revoke_description')),
                Action::make('logoutUser')
                    ->label(__('filament-sso-server::default.sessions.actions.logout_user'))
                    ->icon(Heroicon::OutlinedArrowRightStartOnRectangle)
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
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('filament-sso-server::default.sessions.actions.revoke_selected')),
                ]),
            ]);
    }
}
