<?php

namespace Modules\Tasklist\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Core\Events\BuildingSidebar;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Tasklist\Events\Handlers\RegisterTasklistSidebar;
use Illuminate\Support\Arr;

class TasklistServiceProvider extends ServiceProvider
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
        $this->app['events']->listen(BuildingSidebar::class, RegisterTasklistSidebar::class);

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('attivita', Arr::dot(trans('tasklist::attivita')));
            $event->load('rinnovi', Arr::dot(trans('tasklist::rinnovi')));
            $event->load('attivitavoci', Arr::dot(trans('tasklist::attivitavoci')));
            $event->load('timesheets', Arr::dot(trans('tasklist::timesheets')));
            // append translations
        });
    }

    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'tasklist'
        );
        $this->publishConfig('tasklist', 'permissions');
        $this->publishConfig('tasklist', 'settings');

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
            'Modules\Tasklist\Repositories\AttivitaRepository',
            function () {
                $repository = new \Modules\Tasklist\Repositories\Eloquent\EloquentAttivitaRepository(new \Modules\Tasklist\Entities\Attivita());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Tasklist\Repositories\Cache\CacheAttivitaDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Tasklist\Repositories\RinnovoRepository',
            function () {
                $repository = new \Modules\Tasklist\Repositories\Eloquent\EloquentRinnovoRepository(new \Modules\Tasklist\Entities\Rinnovo());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Tasklist\Repositories\Cache\CacheRinnovoDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Tasklist\Repositories\AttivitaVociRepository',
            function () {
                $repository = new \Modules\Tasklist\Repositories\Eloquent\EloquentAttivitaVociRepository(new \Modules\Tasklist\Entities\AttivitaVoci());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Tasklist\Repositories\Cache\CacheAttivitaVociDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Tasklist\Repositories\TimesheetRepository',
            function () {
                $repository = new \Modules\Tasklist\Repositories\Eloquent\EloquentTimesheetRepository(new \Modules\Tasklist\Entities\Timesheet());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Tasklist\Repositories\Cache\CacheTimesheetDecorator($repository);
            }
        );
// add bindings
    }
}
