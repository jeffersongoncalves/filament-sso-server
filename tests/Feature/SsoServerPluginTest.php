<?php

use Filament\Facades\Filament;
use Filament\Panel;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoActiveSessions\SsoActiveSessionResource;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoClients\SsoClientResource;
use JeffersonGoncalves\Filament\SsoServer\SsoServerPlugin;
use JeffersonGoncalves\Filament\SsoServer\Widgets\SsoOverviewWidget;

it('registers resources and widget on the panel', function () {
    $panel = Filament::getPanel('admin');

    expect(SsoServerPlugin::make()->getId())->toBe('filament-sso-server')
        ->and($panel->getResources())->toContain(SsoClientResource::class, SsoActiveSessionResource::class)
        ->and($panel->getWidgets())->toContain(SsoOverviewWidget::class);
});

it('can disable resources and widget', function () {
    $panel = Panel::make()->id('other');

    SsoServerPlugin::make()
        ->sessionResource(false)
        ->overviewWidget(false)
        ->register($panel);

    expect($panel->getResources())->toBe([SsoClientResource::class])
        ->and($panel->getWidgets())->toBe([]);
});

it('defaults the navigation group to the translated label and allows overriding it', function () {
    $plugin = SsoServerPlugin::get();

    expect(SsoClientResource::getNavigationGroup())->toBe(__('filament-sso-server::default.navigation.group'));

    $plugin->navigationGroup('Auth');

    expect(SsoClientResource::getNavigationGroup())->toBe('Auth')
        ->and(SsoActiveSessionResource::getNavigationGroup())->toBe('Auth');

    $plugin->navigationGroup(null);
});

it('reports live stats on the overview widget', function () {
    $client = createClient();
    createClient(['is_active' => false]);
    createSession($client, ['user_id' => '1']);
    createSession($client, ['user_id' => '1']);
    createSession($client, ['user_id' => '2']);
    createSession($client, ['user_id' => '3', 'expires_at' => now()->subMinute()]);

    $stats = (new ReflectionMethod(SsoOverviewWidget::class, 'getStats'))->invoke(new SsoOverviewWidget);

    expect($stats[0]->getValue())->toBe(1)
        ->and($stats[1]->getValue())->toBe(3)
        ->and($stats[2]->getValue())->toBe(2);
});
