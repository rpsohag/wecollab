<?php

namespace Modules\Translation\Tests;

use Mcamara\LaravelLocalization\LaravelLocalizationServiceProvider;
use Modules\Core\Providers\CoreServiceProvider;
use Modules\Core\Providers\SidebarServiceProvider;
use Modules\Translation\Providers\TranslationServiceProvider;
use Nwidart\Modules\LaravelModulesServiceProvider;
use Orchestra\Testbench\TestCase;

abstract class BaseTranslationTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->resetDatabase();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelModulesServiceProvider::class,
            CoreServiceProvider::class,
            TranslationServiceProvider::class,
            LaravelLocalizationServiceProvider::class,
            SidebarServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['path.base'] = __DIR__ . '/..';
        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql', [
            'driver' => 'mysql',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('translatable.locales', ['it']);
        $app['config']->set('laravellocalization.supportedLocales', [
            'it' => [],
        ]);
        $app['config']->set('cache.stores.translations', [
            'driver' => 'array',
        ]);
    }

    private function resetDatabase()
    {
        // Makes sure the migrations table is created
        $this->artisan('migrate', [
            '--database' => 'mysql',
        ]);
        // We empty all tables
        $this->artisan('migrate:reset', [
            '--database' => 'mysql',
        ]);
        // Migrate
        $this->artisan('migrate', [
            '--database' => 'mysql',
        ]);
    }
}
