<?php

use Illuminate\Routing\Router;

$router->post('/register', '\App\Http\Controllers\API\AuthController@register');
$router->post('/login', '\App\Http\Controllers\API\AuthController@login');

/** @var Router $router*/
$router->bind('ticketintervento', function ($id) {
    return app('Modules\Assistenza\Repositories\TicketInterventoRepository')->find($id);
});

$router->group(['prefix' => '/assistenza', 'middleware' => ['auth:api']], function (Router $router) {
    $router->post('richiesteinterventi/getDettaglioTkt', [
        'as' => 'api.assistenza.richiesteintervento.getDettaglioTkt',
        'uses' => 'RichiesteInterventoController@getDettaglioTkt',
        'middleware' => 'auth:api',
    ]);

    $router->post('richiesteinterventi/getElencoTkt', [
        'as' => 'api.assistenza.richiesteintervento.getElencoTkt',
        'uses' => 'RichiesteInterventoController@getElencoTkt',
        'middleware' => 'auth:api',
    ]);

    $router->post('richiesteinterventi/getCliente', [
        'as' => 'api.assistenza.richiesteintervento.getCliente',
        'uses' => 'RichiesteInterventoController@getCliente',
        'middleware' => 'auth:api',
    ]);

    $router->post('richiesteinterventi/getUtenti', [
        'as' => 'api.assistenza.richiesteintervento.getUtenti',
        'uses' => 'RichiesteInterventoController@getUtenti',
        'middleware' => 'auth:api',
    ]);

    $router->post('richiesteinterventi/gethashCliente', [
        'as' => 'api.assistenza.richiesteintervento.gethashCliente',
        'uses' => 'RichiesteInterventoController@gethashCliente',
        'middleware' => 'auth:api',
    ]);

    $router->post('richiesteinterventi/gethashOrdinativo', [
        'as' => 'api.assistenza.richiesteintervento.gethashOrdinativo',
        'uses' => 'RichiesteInterventoController@gethashOrdinativo',
        'middleware' => 'auth:api',
    ]);

    $router->post('richiesteinterventi/getRichiesteOrdinativo', [
        'as' => 'api.assistenza.richiesteintervento.getRichiesteOrdinativo',
        'uses' => 'RichiesteInterventoController@getRichiesteOrdinativo',
        'middleware' => 'auth:api',
    ]);

    $router->post('richiesteinterventi/ticketweb', [
        'as' => 'api.assistenza.richiesteintervento.ticketweb',
        'uses' => 'RichiesteInterventoController@ticketWeb',
        'middleware' => 'auth:api',
    ]);

    $router->post('richiesteinterventi/getAree', [
        'as' => 'api.assistenza.richiesteintervento.getAree',
        'uses' => 'RichiesteInterventoController@getAree',
        'middleware' => 'auth:api',
    ]);

    $router->post('richiesteinterventi/getIdCliente', [
        'as' => 'api.assistenza.richiesteintervento.getIdCliente',
        'uses' => 'RichiesteInterventoController@getIdCliente',
        'middleware' => 'auth:api',
    ]);
    
    $router->post('richiesteinterventi/getAreaId', [
        'as' => 'api.assistenza.richiesteintervento.getAreaId',
        'uses' => 'RichiesteInterventoController@getAreaId',
        'middleware' => 'auth:api',
    ]);

    $router->post('richiesteinterventi/getGruppoId', [
        'as' => 'api.assistenza.richiesteintervento.getGruppoId',
        'uses' => 'RichiesteInterventoController@getGruppoId',
        'middleware' => 'auth:api',
    ]);
});
