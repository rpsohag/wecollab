<?php

namespace Modules\Commerciale\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Core\Events\BuildingSidebar;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Commerciale\Events\Handlers\RegisterCommercialeSidebar;
use Illuminate\Support\Arr;

class CommercialeServiceProvider extends ServiceProvider
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
        $this->app['events']->listen(BuildingSidebar::class, RegisterCommercialeSidebar::class);

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('offerte', Arr::dot(trans('commerciale::offerte')));
            $event->load('ordinativi', Arr::dot(trans('commerciale::ordinativi')));
            $event->load('fatturazioni', Arr::dot(trans('commerciale::fatturazioni')));
            $event->load('analisivendite', Arr::dot(trans('commerciale::analisivendite')));
            $event->load('censimenticlienti', Arr::dot(trans('commerciale::censimenticlienti')));
            $event->load('segnalazioniopportunita', Arr::dot(trans('commerciale::segnalazioniopportunita')));
            $event->load('simaziendalis', Arr::dot(trans('commerciale::simaziendalis')));
            // append translations
        });
    }

    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'commerciale'
        );

        $this->publishConfig('commerciale', 'settings');
        $this->publishConfig('commerciale', 'permissions');

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
            'Modules\Commerciale\Repositories\OffertaRepository',
            function () {
                $repository = new \Modules\Commerciale\Repositories\Eloquent\EloquentOffertaRepository(new \Modules\Commerciale\Entities\Offerta());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Commerciale\Repositories\Cache\CacheOffertaDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Commerciale\Repositories\OrdinativoRepository',
            function () {
                $repository = new \Modules\Commerciale\Repositories\Eloquent\EloquentOrdinativoRepository(new \Modules\Commerciale\Entities\Ordinativo());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Commerciale\Repositories\Cache\CacheOrdinativoDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Commerciale\Repositories\FatturazioneRepository',
            function () {
                $repository = new \Modules\Commerciale\Repositories\Eloquent\EloquentFatturazioneRepository(new \Modules\Commerciale\Entities\Fatturazione());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Commerciale\Repositories\Cache\CacheFatturazioneDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Commerciale\Repositories\AnalisiVenditaRepository',
            function () {
                $repository = new \Modules\Commerciale\Repositories\Eloquent\EloquentAnalisiVenditaRepository(new \Modules\Commerciale\Entities\AnalisiVendita());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Commerciale\Repositories\Cache\CacheAnalisiVenditaDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Commerciale\Repositories\CensimentoClienteRepository',
            function () {
                $repository = new \Modules\Commerciale\Repositories\Eloquent\EloquentCensimentoClienteRepository(new \Modules\Commerciale\Entities\CensimentoCliente());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Commerciale\Repositories\Cache\CacheCensimentoClienteDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Commerciale\Repositories\SegnalazioneOpportunitaRepository',
            function () {
                $repository = new \Modules\Commerciale\Repositories\Eloquent\EloquentSegnalazioneOpportunitaRepository(new \Modules\Commerciale\Entities\SegnalazioneOpportunita());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Commerciale\Repositories\Cache\CacheSegnalazioneOpportunitaDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Commerciale\Repositories\SimAziendaliRepository',
            function () {
                $repository = new \Modules\Commerciale\Repositories\Eloquent\EloquentSimAziendaliRepository(new \Modules\Commerciale\Entities\SimAziendali());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Commerciale\Repositories\Cache\CacheSimAziendaliDecorator($repository);
            }
        );
// add bindings
    }
}
