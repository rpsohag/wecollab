<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/statistiche'], function (Router $router) {
    $router->bind('statistica', function ($id) {
        return app('Modules\Statistiche\Repositories\StatisticaRepository')->find($id);
    });
    $router->get('statistica', [
        'as' => 'admin.statistiche.statistica.index',
        'uses' => 'StatisticaController@index',
        'middleware' => 'can:statistiche.statistica.index'
    ]);
    $router->get('statistica/fatturazione', [
        'as' => 'admin.statistiche.statistica.fatturazione',
        'uses' => 'StatisticaController@fatturazione',
        'middleware' => 'can:statistiche.statistica.fatturazione'
    ]);
    $router->get('statistica/richiesteintervento', [
        'as' => 'admin.statistiche.statistica.richiesteintervento',
        'uses' => 'StatisticaController@richiesteIntervento',
        'middleware' => 'can:statistiche.statistica.richiesteintervento'
    ]);
    $router->get('reports', [
        'as' => 'admin.statistiche.reports.index',
        'uses' => 'StatisticaController@reports',
        'middleware' => 'can:statistiche.reports.index'
    ]);
    $router->post('reports/modal', [
        'as' => 'admin.statistiche.reports.modal',
        'uses' => 'StatisticaController@reportsModal',
        'middleware' => 'can:statistiche.reports.index'
    ]);
    $router->get('quadraturatimesheets', [
        'as' => 'admin.statistiche.quadraturatimesheets',
        'uses' => 'StatisticaController@quadraturaTimesheets',
        'middleware' => 'can:statistiche.quadraturatimesheets.index'
    ]);
// append

});
