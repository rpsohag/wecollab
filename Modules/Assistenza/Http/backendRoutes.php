<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/assistenza'], function (Router $router) {
    $router->bind('ticketintervento', function ($id) {
        return app('Modules\Assistenza\Repositories\TicketInterventoRepository')->find($id);
    });
    $router->get('ticketinterventi', [
        'as' => 'admin.assistenza.ticketintervento.index',
        'uses' => 'TicketInterventoController@index',
        'middleware' => 'can:assistenza.ticketinterventi.index'
    ]);
    $router->get('ticketinterventi/create', [
        'as' => 'admin.assistenza.ticketintervento.create',
        'uses' => 'TicketInterventoController@create',
        'middleware' => 'can:assistenza.ticketinterventi.create'
    ]);
    $router->get('ticketinterventi/create/rapportino/{richiestaintervento}', [
        'as' => 'admin.assistenza.ticketintervento.createbyid',
        'uses' => 'TicketInterventoController@createRapportino',
        'middleware' => 'can:assistenza.ticketinterventi.create'
    ]);
    $router->post('ticketinterventi', [
        'as' => 'admin.assistenza.ticketintervento.store',
        'uses' => 'TicketInterventoController@store',
        'middleware' => 'can:assistenza.ticketinterventi.create'
    ]);
    $router->get('ticketinterventi/{ticketintervento}/edit', [
        'as' => 'admin.assistenza.ticketintervento.edit',
        'uses' => 'TicketInterventoController@edit',
        'middleware' => 'can:assistenza.ticketinterventi.edit'
    ]);

	$router->get('ticketinterventi/{ticketintervento}/read', [
        'as' => 'admin.assistenza.ticketintervento.read',
        'uses' => 'TicketInterventoController@read',
        'middleware' => 'can:assistenza.ticketinterventi.read'
    ]);

    $router->put('ticketinterventi/{ticketintervento}', [
        'as' => 'admin.assistenza.ticketintervento.update',
        'uses' => 'TicketInterventoController@update',
        'middleware' => 'can:assistenza.ticketinterventi.edit'
    ]);
    $router->delete('ticketinterventi/{ticketintervento}', [
        'as' => 'admin.assistenza.ticketintervento.destroy',
        'uses' => 'TicketInterventoController@destroy',
        'middleware' => 'can:assistenza.ticketinterventi.destroy'
    ]);
    $router->get('ticketintervento/{id}/pdf', [
        'as' => 'admin.assistenza.ticketintervento.pdf',
        'uses' => 'TicketInterventoController@generaPdf',
        'middleware' => 'can:assistenza.ticketinterventi.edit'
    ]);
    $router->post('ticketintervento/checkordinativo', [
        'as' => 'admin.assistenza.ticketintervento.checkordinativo',
        'uses' => 'TicketInterventoController@checkOrdinativo',
        'middleware' => 'can:assistenza.ticketinterventi.edit'
    ]);
    $router->post('ticketintervento/ajaxrequest', [
        'as' => 'admin.assistenza.ticketintervento.ajaxrequest',
        'uses' => 'TicketInterventoController@ajaxRequest',
        'middleware' => 'can:assistenza.ticketinterventi.edit'
    ]);
    $router->post('ticketintervento/timesheetsAjaxRequest', [
        'as' => 'admin.assistenza.ticketintervento.timesheetsAjaxRequest',
        'uses' => 'TicketInterventoController@timesheetsAjaxRequest',
        'middleware' => 'can:assistenza.ticketinterventi.edit'
    ]);
    $router->post('ticketintervento/store/timesheet', [
        'as' => 'admin.assistenza.ticketintervento.store.timesheet',
        'uses' => 'TicketInterventoController@storeTimesheet',
        'middleware' => 'can:assistenza.ticketinterventi.create'
    ]);
    $router->get('ticketintervento/export/excel', [
        'as' => 'admin.assistenza.ticketintervento.exportexcel',
        'uses' => 'TicketInterventoController@exportExcel',
        'middleware' => 'can:assistenza.ticketinterventi.exportexcel'
    ]);

    //richiesta
    $router->bind('richiesteintervento', function ($id) {
        return app('Modules\Assistenza\Repositories\RichiesteInterventoRepository')->find($id);
    });
    $router->post('richiesteinterventi/ajaxrequestdestinatari', [
        'as' => 'admin.assistenza.richiesteinterventi.ajaxrequestdestinatari',
        'uses' => 'RichiesteInterventoController@ajaxrequestdestinatari',
        'middleware' => 'can:assistenza.richiesteinterventi.edit'
    ]);
    $router->post('richiesteinterventi/ajaxrequestcontatti', [
        'as' => 'admin.assistenza.richiesteinterventi.ajaxrequestcontatti',
        'uses' => 'RichiesteInterventoController@ajaxrequestcontatti',
        'middleware' => 'can:assistenza.richiesteinterventi.edit'
    ]);
    $router->post('richiesteinterventi/ajaxrequestcontatti2', [
        'as' => 'admin.assistenza.richiesteinterventi.ajaxrequestcontatti2',
        'uses' => 'RichiesteInterventoController@ajaxrequestcontatti',
        'middleware' => 'can:assistenza.richiesteinterventi.edit'
    ]);
    $router->post('richiesteinterventi/ajaxrequestrichiesta', [
        'as' => 'admin.assistenza.richiesteinterventi.ajaxrequestrichiesta',
        'uses' => 'RichiesteInterventoController@ajaxrequestrichiesta',
        'middleware' => 'can:assistenza.richiesteinterventi.edit'
    ]);
    $router->post('richiesteinterventi/aggiungireferente', [
        'as' => 'admin.assistenza.richiesteinterventi.aggiungireferente',
        'uses' => 'RichiesteInterventoController@aggiungiReferente',
        'middleware' => 'can:assistenza.richiesteinterventi.edit'
    ]);
    $router->post('richiesteinterventi/iniziolavoro', [
        'as' => 'admin.assistenza.richiesteinterventi.iniziolavoro',
        'uses' => 'RichiesteInterventoController@iniziolavoro',
        'middleware' => 'can:assistenza.richiesteinterventi.edit'
    ]);
    $router->post('richiesteinterventi/startLavorazione', [
        'as' => 'admin.assistenza.richiesteinterventi.startLavorazione',
        'uses' => 'RichiesteInterventoController@startLavorazione',
        'middleware' => 'can:assistenza.richiesteinterventi.edit'
    ]);
    $router->get('richiesteinterventi', [
        'as' => 'admin.assistenza.richiesteintervento.index',
        'uses' => 'RichiesteInterventoController@index',
        'middleware' => 'can:assistenza.richiesteinterventi.index'
    ]);
    $router->get('richiesteinterventi/create', [
        'as' => 'admin.assistenza.richiesteintervento.create',
        'uses' => 'RichiesteInterventoController@create',
        'middleware' => 'can:assistenza.richiesteinterventi.create'
    ]);
    $router->post('richiesteinterventi', [
        'as' => 'admin.assistenza.richiesteintervento.store',
        'uses' => 'RichiesteInterventoController@store',
        'middleware' => 'can:assistenza.richiesteinterventi.create'
    ]);
    $router->get('richiesteinterventi/conversione', [
        'as' => 'admin.assistenza.richiesteinterventi.conversione',
        'uses' => 'RichiesteInterventoController@conversione',
        'middleware' => 'can:assistenza.richiesteinterventi.edit'
    ]);
    $router->get('richiesteinterventi/{richiesteintervento}/read', [
        'as' => 'admin.assistenza.richiesteintervento.read',
        'uses' => 'RichiesteInterventoController@read',
        'middleware' => 'can:assistenza.richiesteinterventi.read'
    ]);
    $router->get('richiesteinterventi/{richiesteintervento}/edit', [
        'as' => 'admin.assistenza.richiesteintervento.edit',
        'uses' => 'RichiesteInterventoController@edit',
        'middleware' => 'can:assistenza.richiesteinterventi.edit'
    ]);
    $router->put('richiesteinterventi/{richiesteintervento}', [
        'as' => 'admin.assistenza.richiesteintervento.update',
        'uses' => 'RichiesteInterventoController@update',
        'middleware' => 'can:assistenza.richiesteinterventi.edit'
    ]);
    $router->delete('richiesteinterventi/{richiesteintervento}', [
        'as' => 'admin.assistenza.richiesteintervento.destroy',
        'uses' => 'RichiesteInterventoController@destroy',
        'middleware' => 'can:assistenza.richiesteinterventi.destroy'
    ]);
    $router->get('richiesteinterventi/{richiesteintervento}/riapri', [
        'as' => 'admin.assistenza.richiesteinterventi.riapri',
        'uses' => 'RichiesteInterventoController@riapriTicket',
        'middleware' => 'can:assistenza.richiesteinterventi.edit'
    ]);
    $router->delete('ticketinterventovoci/{id}', [
        'as' => 'admin.assistenza.ticketinterventovoci.destroy',
        'uses' => 'TicketInterventoController@destroyVoce',
        'middleware' => 'can:assistenza.ticketinterventi.destroy'
    ]);
    $router->get('richiesteinterventi/export/excel', [
        'as' => 'admin.assistenza.richiesteintervento.exportexcel',
        'uses' => 'RichiesteInterventoController@exportExcel',
        'middleware' => 'can:assistenza.richiesteinterventi.exportexcel'
    ]);
    $router->get('richiesteinterventi/export/excel/dipendenti', [
        'as' => 'admin.assistenza.richiesteintervento.exportexceldipendenti',
        'uses' => 'RichiesteInterventoController@exportPerDipendente',
        'middleware' => 'can:assistenza.richiesteinterventi.exportexcel'
    ]);
    $router->get('richiesteinterventi/export/excel/aree', [
        'as' => 'admin.assistenza.richiesteintervento.exportexcelaree',
        'uses' => 'RichiesteInterventoController@exportPerArea',
        'middleware' => 'can:assistenza.richiesteinterventi.exportexcel'
    ]);
    $router->get('richiesteinterventi/export/multiexcel', [
        'as' => 'admin.assistenza.richiesteintervento.exportmultiexcel',
        'uses' => 'RichiesteInterventoController@exportMultiExcel',
        'middleware' => 'can:assistenza.richiesteinterventi.exportexcel'
    ]);
    $router->get('richiesteinterventi/export/statsmultiexcel', [
        'as' => 'admin.assistenza.richiesteintervento.exportstatsmultiexcel',
        'uses' => 'RichiesteInterventoController@exportStatsMultiExcel',
        'middleware' => 'can:assistenza.richiesteinterventi.exportexcel'
    ]);

    $router->get('ambienti', [
        'as' => 'admin.assistenza.ambienti.index',
        'uses' => 'AmbientiController@index',
        'middleware' => 'can:assistenza.ambienti.index'
    ]);
    $router->get('ambienti-conversioni', [
        'as' => 'admin.assistenza.ambienticonversioni.index',
        'uses' => 'AmbientiConversioniController@index',
        'middleware' => 'can:assistenza.ambienticonversioni.index'
    ]);
    $router->get('ambienti-conversioni/informations', [
        'as' => 'admin.assistenza.ambienticonversioni.informations',
        'uses' => 'AmbientiConversioniController@informations',
        'middleware' => 'can:assistenza.ambienticonversioni.edit'
    ]);
    $router->post('ambienti-conversioni', [
        'as' => 'admin.assistenza.ambienticonversioni.store',
        'uses' => 'AmbientiConversioniController@storeOrUpdate',
        'middleware' => 'can:assistenza.ambienticonversioni.create'
    ]);
    $router->delete('ambienti-conversioni/{ambiente}', [
        'as' => 'admin.assistenza.ambienticonversioni.destroy',
        'uses' => 'AmbientiConversioniController@destroy',
        'middleware' => 'can:assistenza.ambienticonversioni.destroy'
    ]);
});
