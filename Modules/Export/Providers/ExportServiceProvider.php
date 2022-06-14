<?php

namespace Modules\Export\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Core\Events\BuildingSidebar;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Export\Events\Handlers\RegisterExportSidebar;
use Illuminate\Support\Arr;

class ExportServiceProvider extends ServiceProvider
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
        $this->app['events']->listen(BuildingSidebar::class, RegisterExportSidebar::class);

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('exports', Arr::dot(trans('export::exports')));
            // append translations

        });
    }

    public function boot()
    {
        $this->publishConfig('export', 'permissions');

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
            'Modules\Export\Repositories\ExportRepository',
            function () {
                $repository = new \Modules\Export\Repositories\Eloquent\EloquentExportRepository(new \Modules\Export\Entities\Export());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Export\Repositories\Cache\CacheExportDecorator($repository);
            }
        );
// add bindings

    }
}
