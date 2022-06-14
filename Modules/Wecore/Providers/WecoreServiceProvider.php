<?php

namespace Modules\Wecore\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Core\Events\BuildingSidebar;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Wecore\Events\Handlers\RegisterWecoreSidebar;
use Illuminate\Support\Arr;

class WecoreServiceProvider extends ServiceProvider
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
        $this->app['events']->listen(BuildingSidebar::class, RegisterWecoreSidebar::class);

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('cores', Arr::dot(trans('wecore::cores')));
            // append translations

        });
    }

    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'wecore'
        );

        $this->publishConfig('wecore', 'settings');
        $this->publishConfig('wecore', 'permissions');

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        $profile_user = get_profile_user(\Auth::id());

        if(empty(session('azienda')))
            set_azienda($profile_user->azienda);

        return array();
    }

    private function registerBindings()
    {
        $this->app->bind(
            'Modules\Wecore\Repositories\CoreRepository',
            function () {
                $repository = new \Modules\Wecore\Repositories\Eloquent\EloquentCoreRepository(new \Modules\Wecore\Entities\Core());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Wecore\Repositories\Cache\CacheCoreDecorator($repository);
            }
        );
// add bindings

    }
}
