<?php

namespace Modules\Statistiche\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Core\Events\BuildingSidebar;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Statistiche\Events\Handlers\RegisterStatisticheSidebar;
use Illuminate\Support\Arr;

class StatisticheServiceProvider extends ServiceProvider
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
        $this->app['events']->listen(BuildingSidebar::class, RegisterStatisticheSidebar::class);

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('statistica', Arr::dot(trans('statistiche::statistica')));
            // append translations

        });
    }

    public function boot()
    {
        $this->publishConfig('statistiche', 'permissions');
        $this->publishConfig('statistiche', 'settings');

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
            'Modules\Statistiche\Repositories\StatisticaRepository',
            function () {
                $repository = new \Modules\Statistiche\Repositories\Eloquent\EloquentStatisticaRepository(new \Modules\Statistiche\Entities\Statistica());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Statistiche\Repositories\Cache\CacheStatisticaDecorator($repository);
            }
        );
// add bindings

    }
}
