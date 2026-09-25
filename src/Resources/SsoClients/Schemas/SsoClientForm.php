<?php

namespace JeffersonGoncalves\Filament\SsoServer\Resources\SsoClients\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SsoClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
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
}
