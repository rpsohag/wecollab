<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/wecloud'], function (Router $router) {
    $router->bind('wecloud', function ($id) {
        return app('Modules\Tasklist\Repositories\WecloudRepository')->find($id);
    });
    $router->get('files', [
        'as' => 'admin.wecloud.file.index',
        'uses' => 'WecloudController@index',
        'middleware' => 'can:wecloud.file.index'
    ]);
    $router->post('files/upload', [
        'as' => 'admin.wecloud.uploadFile',
        'uses' => 'WecloudController@uploadFile',
        'middleware' => 'can:wecloud.file.create'
    ]);
    $router->delete('files/delete/{file}', [
        'as' => 'admin.wecloud.file.destroy',
        'uses' => 'WecloudController@destroy',
        'middleware' => 'can:wecloud.file.destroy'
    ]);
});
