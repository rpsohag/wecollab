<?php

namespace Modules\Assistenza\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Core\Events\BuildingSidebar;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Assistenza\Events\Handlers\RegisterAssistenzaSidebar;
use Illuminate\Support\Arr;

class AssistenzaServiceProvider extends ServiceProvider
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
        $this->app['events']->listen(BuildingSidebar::class, RegisterAssistenzaSidebar::class);

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('ticketinterventi', Arr::dot(trans('assistenza::ticketinterventi')));
            $event->load('richiesteinterventi', Arr::dot(trans('assistenza::richiesteinterventi')));
            // append translations
        });
    }

    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'assistenza'
        );

        $this->publishConfig('assistenza', 'permissions');

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
            'Modules\Assistenza\Repositories\TicketInterventoRepository',
            function () {
                $repository = new \Modules\Assistenza\Repositories\Eloquent\EloquentTicketInterventoRepository(new \Modules\Assistenza\Entities\TicketIntervento());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Assistenza\Repositories\Cache\CacheTicketInterventoDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Assistenza\Repositories\RichiesteInterventoRepository',
            function () {
                $repository = new \Modules\Assistenza\Repositories\Eloquent\EloquentRichiesteInterventoRepository(new \Modules\Assistenza\Entities\RichiesteIntervento());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Assistenza\Repositories\Cache\CacheRichiesteInterventoDecorator($repository);
            }
        );
// add bindings


    }
}
