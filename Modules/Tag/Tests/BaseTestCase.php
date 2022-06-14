<?php

namespace Modules\Tag\Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\View\ViewServiceProvider;
use Modules\Core\Providers\CoreServiceProvider;
use Modules\Page\Providers\PageServiceProvider;
use Modules\Tag\Providers\TagServiceProvider;
use Nwidart\Modules\LaravelModulesServiceProvider;
use Orchestra\Testbench\TestCase;

abstract class BaseTestCase extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->resetDatabase();
    }

    protected function getPackageProviders($app)
    {
        return [
            ViewServiceProvider::class,
            LaravelModulesServiceProvider::class,
            CoreServiceProvider::class,
            PageServiceProvider::class,
            TagServiceProvider::class,
        ];
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
        $app['config']->set('modules.paths.modules', __DIR__ . '/../Modules');
    }

    private function resetDatabase()
    {
        // Relative to the testbench app folder: vendors/orchestra/testbench/src/fixture
        $migrationsPath = 'Database/Migrations';
        $artisan = $this->app->make(Kernel::class);
        // Makes sure the migrations table is created
        $artisan->call('migrate', [
            '--database' => 'mysql',
        ]);
        // We empty all tables
        $artisan->call('migrate:reset', [
            '--database' => 'mysql',
        ]);
        // Migrate
        $artisan->call('migrate', [
            '--database' => 'mysql',
        ]);
        $artisan->call('migrate', [
            '--database' => 'mysql',
            '--path'     => 'Modules/Page/Database/Migrations',
        ]);
    }
}
