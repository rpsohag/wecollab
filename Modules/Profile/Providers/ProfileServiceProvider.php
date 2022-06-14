<?php

namespace Modules\Profile\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Core\Events\BuildingSidebar;
use Modules\Profile\Events\Handlers\RegisterProfileSidebar;

class ProfileServiceProvider extends ServiceProvider
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
        $this->app['events']->listen(BuildingSidebar::class, RegisterProfileSidebar::class);
    }

    public function boot()
    {
        $this->publishConfig('profile', 'permissions');
        $this->publishConfig('profile', 'settings');

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
            'Modules\Profile\Repositories\ProfileRepository',
            function () {
                $repository = new \Modules\Profile\Repositories\Eloquent\EloquentProfileRepository(new \Modules\Profile\Entities\Profile());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Profile\Repositories\Cache\CacheProfileDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Profile\Repositories\GruppoRepository',
            function () {
                $repository = new \Modules\Profile\Repositories\Eloquent\EloquentGruppoRepository(new \Modules\Profile\Entities\Gruppo());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Profile\Repositories\Cache\CacheGruppoDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Profile\Repositories\areaRepository',
            function () {
                $repository = new \Modules\Profile\Repositories\Eloquent\EloquentareaRepository(new \Modules\Profile\Entities\area());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Profile\Repositories\Cache\CacheareaDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Profile\Repositories\FiguraProfessionaleRepository',
            function () {
                $repository = new \Modules\Profile\Repositories\Eloquent\EloquentFiguraProfessionaleRepository(new \Modules\Profile\Entities\FiguraProfessionale());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Profile\Repositories\Cache\CacheFiguraProfessionaleDecorator($repository);
            }
        );
// add bindings




    }
}
