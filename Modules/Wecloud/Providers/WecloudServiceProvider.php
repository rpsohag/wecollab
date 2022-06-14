<?php

namespace Modules\Wecloud\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Core\Events\BuildingSidebar;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Wecloud\Events\Handlers\RegisterWecloudSidebar;
use Illuminate\Support\Arr;

class WecloudServiceProvider extends ServiceProvider
{
    use CanPublishConfiguration;
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
        $this->app['events']->listen(BuildingSidebar::class, RegisterWecloudSidebar::class);

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('file', ['file'=>'file']);
            // append translations
        });
    }

    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'wecloud'
        );
        $this->publishConfig('wecloud', 'permissions');
        $this->publishConfig('wecloud', 'settings');

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    private function registerBindings()
    {
        $this->app->bind(
            'Modules\Wecloud\Repositories\WecloudRepository',
            function () {
                $repository = new \Modules\Wecloud\Repositories\Eloquent\EloquentFileRepository(new \Modules\Wecloud\Entities\File());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Wecloud\Repositories\Cache\CacheWecloudDecorator($repository);
            }
        );
// add bindings
    }
}
