<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/amministrazione'], function (Router $router) {
    $router->bind('clienti', function ($id) {
        return app('Modules\Amministrazione\Entities\Clienti')->withoutGlobalScope('clienti')->find($id);
    });
    $router->get('clienti', [
        'as' => 'admin.amministrazione.clienti.index',
        'uses' => 'ClientiController@index',
        'middleware' => 'can:amministrazione.clienti.index'
    ]);
    $router->get('clienti/create', [
        'as' => 'admin.amministrazione.clienti.create',
        'uses' => 'ClientiController@create',
        'middleware' => 'can:amministrazione.clienti.create'
    ]);
    $router->post('clienti', [
        'as' => 'admin.amministrazione.clienti.store',
        'uses' => 'ClientiController@store',
        'middleware' => 'can:amministrazione.clienti.create'
    ]);
    $router->get('clienti/{clienti}/edit', [
        'as' => 'admin.amministrazione.clienti.edit',
        'uses' => 'ClientiController@edit',
        'middleware' => 'can:amministrazione.clienti.edit'
    ]);
	$router->get('clienti/{clienti}/read', [
        'as' => 'admin.amministrazione.clienti.read',
        'uses' => 'ClientiController@read',
        'middleware' => 'can:amministrazione.clienti.read'
    ]);
	$router->get('clienti/{cliente}/crea/censimento', [
        'as' => 'admin.amministrazione.clienti.creacensimento',
        'uses' => 'ClientiController@creaCensimento',
        'middleware' => 'can:amministrazione.clienti.edit'
    ]);
    $router->put('clienti/{clienti}', [
        'as' => 'admin.amministrazione.clienti.update',
        'uses' => 'ClientiController@update',
        'middleware' => 'can:amministrazione.clienti.edit'
    ]);
    $router->delete('clienti/{clienti}', [
        'as' => 'admin.amministrazione.clienti.destroy',
        'uses' => 'ClientiController@destroy',
        'middleware' => 'can:amministrazione.clienti.destroy'
    ]);

    $router->bind('cliente/indirizzi', function ($id) {
        return app('Modules\Amministrazione\Repositories\ClienteIndirizziRepository')->find($id);
    });
    // $router->get('clientiindirizzis', [
    //     'as' => 'admin.amministrazione.clientiindirizzi.index',
    //     'uses' => 'clientiIndirizziController@index',
    //     'middleware' => 'can:amministrazione.clientiindirizzis.index'
    // ]);
    $router->get('cliente/{cliente_id}/indirizzi/create', [
        'as' => 'admin.amministrazione.clienti.indirizzi.create',
        'uses' => 'ClientiController@indirizziCreate',
        'middleware' => 'can:amministrazione.clienti.create'
    ]);
    $router->post('cliente/{cliente_id}/indirizzi/', [
        'as' => 'admin.amministrazione.clienti.indirizzi.store',
        'uses' => 'ClientiController@indirizziStore',
        'middleware' => 'can:amministrazione.clienti.create'
    ]);
    $router->get('cliente/indirizzi/{id}/edit', [
        'as' => 'admin.amministrazione.clienti.indirizzi.edit',
        'uses' => 'ClientiController@indirizziEdit',
        'middleware' => 'can:amministrazione.clienti.edit'
    ]);
    $router->put('cliente/indirizzi/{id}', [
        'as' => 'admin.amministrazione.clienti.indirizzi.update',
        'uses' => 'ClientiController@indirizziUpdate',
        'middleware' => 'can:amministrazione.clienti.edit'
    ]);
    $router->delete('cliente/indirizzi/{id}', [
        'as' => 'admin.amministrazione.clienti.indirizzi.destroy',
        'uses' => 'ClientiController@indirizziDestroy',
        'middleware' => 'can:amministrazione.clienti.destroy'
    ]);
    $router->post('cliente/cliente_json', [
        'as' => 'admin.amministrazione.clienti.cliente.json',
        'uses' => 'ClientiController@clienteJson',
        'middleware' => ['can:amministrazione.clienti.index',
                        'can:commerciale.offerte.index']
    ]);
    $router->post('cliente/indirizzi_json', [
        'as' => 'admin.amministrazione.clienti.indirizzi.json',
        'uses' => 'ClientiController@indirizziJson',
        'middleware' => ['can:amministrazione.clienti.index',
                        'can:commerciale.offerte.index']
    ]);

    $router->bind('cliente/referenti', function ($id) {
        return app('Modules\Amministrazione\Repositories\ClienteReferentiRepository')->find($id);
    });
    $router->get('cliente/{cliente_id}/referenti/create', [
        'as' => 'admin.amministrazione.clienti.referenti.create',
        'uses' => 'ClientiController@referentiCreate',
        'middleware' => 'can:amministrazione.clienti.create'
    ]);
    $router->post('cliente/{cliente_id}/referenti/', [
        'as' => 'admin.amministrazione.clienti.referenti.store',
        'uses' => 'ClientiController@referentiStore',
        'middleware' => 'can:amministrazione.clienti.create'
    ]);
    $router->get('cliente/referenti/{id}/edit', [
        'as' => 'admin.amministrazione.clienti.referenti.edit',
        'uses' => 'ClientiController@referentiEdit',
        'middleware' => 'can:amministrazione.clienti.edit'
    ]);
    $router->put('cliente/referenti/{id}', [
        'as' => 'admin.amministrazione.clienti.referenti.update',
        'uses' => 'ClientiController@referentiUpdate',
        'middleware' => 'can:amministrazione.clienti.edit'
    ]);
    $router->delete('cliente/referenti/{id}', [
        'as' => 'admin.amministrazione.clienti.referenti.destroy',
        'uses' => 'ClientiController@referentiDestroy',
        'middleware' => 'can:amministrazione.clienti.destroy'
    ]);

    //AMBIENTE
    $router->get('cliente/ambiente/{id}/edit', [
        'as' => 'admin.amministrazione.clienti.ambienti.edit',
        'uses' => 'ClientiController@ambientiEdit',
        'middleware' => 'can:amministrazione.clienti.edit'
    ]);
    $router->put('cliente/ambiente/{id}', [
        'as' => 'admin.amministrazione.clienti.ambienti.update',
        'uses' => 'ClientiController@ambientiUpdate',
        'middleware' => 'can:amministrazione.clienti.edit'
    ]);
    $router->get('cliente/{cliente_id}/ambienti/create', [
        'as' => 'admin.amministrazione.clienti.ambienti.create',
        'uses' => 'ClientiController@ambientiCreate',
        'middleware' => 'can:amministrazione.clienti.create'
    ]);
    $router->post('cliente/{cliente_id}/ambienti/', [
        'as' => 'admin.amministrazione.clienti.ambienti.store',
        'uses' => 'ClientiController@ambientiStore',
        'middleware' => 'can:amministrazione.clienti.create'
    ]);
    $router->any('cliente/{cliente_id}/urbi/', [
        'as' => 'admin.amministrazione.clienti.login.urbi',
        'uses' => 'ClientiController@loginUrbi',
        'middleware' => 'can:assistenza.richiesteinterventi.edit'
    ]);

    $router->get('beni-strumentali', [
        'as' => 'admin.amministrazione.benistrumentali.index',
        'uses' => 'BeniStrumentaliController@index',
        'middleware' => 'can:amministrazione.benistrumentali.index'
    ]);
    $router->get('beni-strumentali/informations', [
        'as' => 'admin.amministrazione.benistrumentali.informations',
        'uses' => 'BeniStrumentaliController@informations',
        'middleware' => 'can:amministrazione.benistrumentali.edit'
    ]);
    $router->get('beni-strumentali/foglio/{bene}', [
        'as' => 'admin.amministrazione.benistrumentali.foglio',
        'uses' => 'BeniStrumentaliController@generaFoglioAssegnazione',
        'middleware' => 'can:amministrazione.benistrumentali.edit'
    ]);
    $router->post('beni-strumentali', [
        'as' => 'admin.amministrazione.benistrumentali.store',
        'uses' => 'BeniStrumentaliController@storeOrUpdate',
        'middleware' => 'can:amministrazione.benistrumentali.create'
    ]);
    $router->delete('beni-strumentali/{bene}', [
        'as' => 'admin.amministrazione.benistrumentali.destroy',
        'uses' => 'BeniStrumentaliController@destroy',
        'middleware' => 'can:amministrazione.benistrumentali.destroy'
    ]);


});
