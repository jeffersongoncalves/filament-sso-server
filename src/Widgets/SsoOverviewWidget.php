<?php

namespace JeffersonGoncalves\Filament\SsoServer\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use JeffersonGoncalves\SsoServer\Models\SsoActiveSession;
use JeffersonGoncalves\SsoServer\Models\SsoClient;

class SsoOverviewWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $liveSessions = SsoActiveSession::query()->where('expires_at', '>', now());

        return [
            Stat::make(__('filament-sso-server::default.widget.active_clients'), SsoClient::query()->where('is_active', true)->count())
                ->description(__('filament-sso-server::default.widget.active_clients_description'))
                ->icon('heroicon-o-shield-check')
                ->color('primary'),
            Stat::make(__('filament-sso-server::default.widget.live_sessions'), (clone $liveSessions)->count())
                ->description(__('filament-sso-server::default.widget.live_sessions_description'))
                ->icon('heroicon-o-key')
                ->color('success'),
            Stat::make(__('filament-sso-server::default.widget.connected_users'), $liveSessions->distinct()->count('user_id'))
                ->description(__('filament-sso-server::default.widget.connected_users_description'))
                ->icon('heroicon-o-users')
                ->color('info'),
        ];
    }
}
