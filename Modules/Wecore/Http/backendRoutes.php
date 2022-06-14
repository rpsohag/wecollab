<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/wecore'], function (Router $router) {
    $router->bind('core', function ($id) {
        return app('Modules\Wecore\Repositories\CoreRepository')->find($id);
    });
    $router->get('cores', [
        'as' => 'admin.wecore.core.index',
        'uses' => 'CoreController@index',
        'middleware' => 'can:wecore.cores.index'
    ]);
    $router->get('cores/create', [
        'as' => 'admin.wecore.core.create',
        'uses' => 'CoreController@create',
        'middleware' => 'can:wecore.cores.create'
    ]);
    $router->post('cores', [
        'as' => 'admin.wecore.core.store',
        'uses' => 'CoreController@store',
        'middleware' => 'can:wecore.cores.create'
    ]);
    $router->get('cores/{core}/edit', [
        'as' => 'admin.wecore.core.edit',
        'uses' => 'CoreController@edit',
        'middleware' => 'can:wecore.cores.edit'
    ]);
    $router->put('cores/{core}', [
        'as' => 'admin.wecore.core.update',
        'uses' => 'CoreController@update',
        'middleware' => 'can:wecore.cores.edit'
    ]);
    $router->delete('cores/{core}', [
        'as' => 'admin.wecore.core.destroy',
        'uses' => 'CoreController@destroy',
        'middleware' => 'can:wecore.cores.destroy'
    ]);
    $router->get('allegato/{path}/{name}', [
        'as' => 'admin.wecore.allegato.visualizza',
        'uses' => 'CoreController@allegatoVisualizza',
        //'middleware' => 'can:wecore.cores.index'
    ]);
    $router->post('caricaFiles', [
        'as' => 'admin.wecore.caricafiles',
        'uses' => 'CoreController@caricaFiles',
        //'middleware' => 'can:wecore.cores.index'
    ]);
    $router->delete('allegato/{id}', [
        'as' => 'admin.wecore.allegato.destroy',
        'uses' => 'CoreController@allegatoDestroy',
        //'middleware' => 'can:wecore.allegato.destroy'
    ]);
    $router->get('get-currency-ajax/{value}/{currency}', [
        'as' => 'admin.wecore.getcurrencyajax',
        'uses' => 'CoreController@getCurrencyAjax',
        //'middleware' => 'can:wecore.cores.index'
    ]);

    $router->get('test', [
        'as' => 'admin.wecore.test',
        'uses' => 'CoreController@test',
        'middleware' => 'can:wecore.test'
    ]);
// append

});
