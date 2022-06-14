<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/export'], function (Router $router) {
    // $router->bind('export', function ($id) {
    //     return app('Modules\Export\Repositories\ExportRepository')->find($id);
    // });
    // $router->get('exports', [
    //     'as' => 'admin.export.export.index',
    //     'uses' => 'ExportController@index',
    //     'middleware' => 'can:export.exports.index'
    // ]);
    // $router->get('exports/create', [
    //     'as' => 'admin.export.export.create',
    //     'uses' => 'ExportController@create',
    //     'middleware' => 'can:export.exports.create'
    // ]);
    // $router->post('exports', [
    //     'as' => 'admin.export.export.store',
    //     'uses' => 'ExportController@store',
    //     'middleware' => 'can:export.exports.create'
    // ]);
    // $router->get('exports/{export}/edit', [
    //     'as' => 'admin.export.export.edit',
    //     'uses' => 'ExportController@edit',
    //     'middleware' => 'can:export.exports.edit'
    // ]);
    // $router->put('exports/{export}', [
    //     'as' => 'admin.export.export.update',
    //     'uses' => 'ExportController@update',
    //     'middleware' => 'can:export.exports.edit'
    // ]);
    // $router->delete('exports/{export}', [
    //     'as' => 'admin.export.export.destroy',
    //     'uses' => 'ExportController@destroy',
    //     'middleware' => 'can:export.exports.destroy'
    // ]);
// append

});
