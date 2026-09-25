<?php

namespace JeffersonGoncalves\Filament\SsoServer;

use Filament\Contracts\Plugin;
use Filament\Panel;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoActiveSessionResource;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoClientResource;
use JeffersonGoncalves\Filament\SsoServer\Widgets\SsoOverviewWidget;

class SsoServerPlugin implements Plugin
{
    protected ?string $navigationGroup = null;

    protected bool $hasClientResource = true;

    protected bool $hasSessionResource = true;

    protected bool $hasOverviewWidget = true;

    public function getId(): string
    {
        return 'filament-sso-server';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources(array_keys(array_filter([
                SsoClientResource::class => $this->hasClientResource,
                SsoActiveSessionResource::class => $this->hasSessionResource,
            ])))
            ->widgets($this->hasOverviewWidget ? [SsoOverviewWidget::class] : []);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function navigationGroup(?string $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function getNavigationGroup(): ?string
    {
        return $this->navigationGroup ?? __('filament-sso-server::default.navigation.group');
    }

    public function clientResource(bool $condition = true): static
    {
        $this->hasClientResource = $condition;

        return $this;
    }

    public function sessionResource(bool $condition = true): static
    {
        $this->hasSessionResource = $condition;

        return $this;
    }

    public function overviewWidget(bool $condition = true): static
    {
        $this->hasOverviewWidget = $condition;

        return $this;
    }
}
