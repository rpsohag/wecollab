<?php

namespace Modules\Amministrazione\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Core\Events\BuildingSidebar;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Amministrazione\Events\Handlers\RegisterAmministrazioneSidebar;
use Illuminate\Support\Arr;

class AmministrazioneServiceProvider extends ServiceProvider
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
        $this->app['events']->listen(BuildingSidebar::class, RegisterAmministrazioneSidebar::class);

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('clienti', Arr::dot(trans('amministrazione::clienti')));
            $event->load('clientiindirizzi', Arr::dot(trans('amministrazione::clientiindirizzi')));
            $event->load('clientereferenti', Arr::dot(trans('amministrazione::clientereferenti')));
            // append translations



        });
    }

    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'amministrazione'
        );
        
        $this->publishConfig('amministrazione', 'permissions');
        $this->publishConfig('amministrazione', 'settings');

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
            'Modules\Amministrazione\Repositories\ClientiRepository',
            function () {
                $repository = new \Modules\Amministrazione\Repositories\Eloquent\EloquentClientiRepository(new \Modules\Amministrazione\Entities\Clienti());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Amministrazione\Repositories\Cache\CacheClientiDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Amministrazione\Repositories\clientiIndirizziRepository',
            function () {
                $repository = new \Modules\Amministrazione\Repositories\Eloquent\EloquentclientiIndirizziRepository(new \Modules\Amministrazione\Entities\clientiIndirizzi());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Amministrazione\Repositories\Cache\CacheclientiIndirizziDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Amministrazione\Repositories\ClienteReferentiRepository',
            function () {
                $repository = new \Modules\Amministrazione\Repositories\Eloquent\EloquentClienteReferentiRepository(new \Modules\Amministrazione\Entities\ClienteReferenti());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Amministrazione\Repositories\Cache\CacheClienteReferentiDecorator($repository);
            }
        );
// add bindings



    }
}
