<?php

namespace JeffersonGoncalves\Filament\SsoServer\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use JeffersonGoncalves\Filament\SsoServer\SsoServerServiceProvider;
use JeffersonGoncalves\Filament\SsoServer\Tests\Fixtures\TestPanelProvider;
use JeffersonGoncalves\Filament\SsoServer\Tests\Fixtures\User;
use JeffersonGoncalves\SsoServer\SsoServerServiceProvider as LaravelSsoServerServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\TestCase as Orchestra;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;

#[WithMigration]
abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [
            BladeCaptureDirectiveServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            // Must register before Livewire: it rebinds Livewire's DataStore.
            SupportServiceProvider::class,
            LivewireServiceProvider::class,
            ActionsServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            NotificationsServiceProvider::class,
            SchemasServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            FilamentServiceProvider::class,
            TestPanelProvider::class,
            LaravelSsoServerServiceProvider::class,
            SsoServerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        config()->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        config()->set('auth.providers.users.model', User::class);
    }

    protected function defineDatabaseMigrations(): void
    {
        // laravel-sso-server ships its migrations as .stub files, so copy them
        // to real migration files.
        $tempPath = sys_get_temp_dir().'/filament-sso-server-migrations';

        if (! is_dir($tempPath)) {
            mkdir($tempPath, 0755, true);
        }

        foreach (['create_sso_clients_table', 'create_sso_active_sessions_table'] as $i => $name) {
            copy(
                __DIR__."/../vendor/jeffersongoncalves/laravel-sso-server/database/migrations/{$name}.php.stub",
                $tempPath."/0001_01_01_00000{$i}_{$name}.php"
            );
        }

        $this->loadMigrationsFrom($tempPath);
    }
}
