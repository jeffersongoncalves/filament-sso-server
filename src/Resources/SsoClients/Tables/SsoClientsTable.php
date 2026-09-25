<?php

namespace JeffersonGoncalves\Filament\SsoServer\Resources\SsoClients\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoClients\SsoClientResource;
use JeffersonGoncalves\SsoServer\Models\SsoClient;

class SsoClientsTable
{
    public static function configure(Table $table): Table
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
            ->recordActions([
                EditAction::make(),
                static::rotateSecretAction(),
                DeleteAction::make()
                    ->modalDescription(__('filament-sso-server::default.clients.actions.delete_description')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function rotateSecretAction(): Action
    {
        return Action::make('rotateSecret')
            ->label(__('filament-sso-server::default.clients.actions.rotate_secret'))
            ->icon(Heroicon::OutlinedArrowPath)
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading(__('filament-sso-server::default.clients.actions.rotate_secret'))
            ->modalDescription(__('filament-sso-server::default.clients.actions.rotate_secret_description'))
            ->action(function (SsoClient $record): void {
                $secret = Str::random(64);

                $record->update(['client_secret' => $secret]);

                SsoClientResource::notifySecret(__('filament-sso-server::default.clients.notifications.rotated'), $secret);
            });
    }
}
