<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/tasklist'], function (Router $router) {
    $router->bind('attivita', function ($id) {
        return app('Modules\Tasklist\Repositories\AttivitaRepository')->find($id);
    });
    $router->get('attivita', [
        'as' => 'admin.tasklist.attivita.index',
        'uses' => 'AttivitaController@index',
        'middleware' => 'can:tasklist.attivita.index'
    ]);
    $router->get('attivita/create', [
        'as' => 'admin.tasklist.attivita.create',
        'uses' => 'AttivitaController@create',
        'middleware' => 'can:tasklist.attivita.create'
    ]);
    $router->get('attivita/multicreate', [
        'as' => 'admin.tasklist.attivita.multicreate',
        'uses' => 'AttivitaController@multiCreate',
        'middleware' => 'can:tasklist.attivita.create'
    ]);
    $router->post('attivita', [
        'as' => 'admin.tasklist.attivita.store',
        'uses' => 'AttivitaController@store',
        'middleware' => 'can:tasklist.attivita.create'
    ]);
    $router->post('attivitas', [
        'as' => 'admin.tasklist.attivita.multistore',
        'uses' => 'AttivitaController@multiStore',
        'middleware' => 'can:tasklist.attivita.create'
    ]);
    $router->get('attivita/{attivita}/edit', [
        'as' => 'admin.tasklist.attivita.edit',
        'uses' => 'AttivitaController@edit',
        'middleware' => 'can:tasklist.attivita.edit'
    ]);
    $router->get('attivita/requisiti', [
        'as' => 'admin.tasklist.attivita.requisiti',
        'uses' => 'AttivitaController@requisitiAttivita',
        'middleware' => 'can:tasklist.attivita.index'
    ]);
    $router->get('attivita/assegnatari', [
        'as' => 'admin.tasklist.attivita.assegnatari',
        'uses' => 'AttivitaController@getAssegnatari',
        'middleware' => 'can:tasklist.attivita.index'
    ]);
    $router->get('attivita/{attivita}/read', [
        'as' => 'admin.tasklist.attivita.read',
        'uses' => 'AttivitaController@read',
        'middleware' => 'can:tasklist.attivita.read'
    ]);
    $router->post('attivita/store/timesheet', [
        'as' => 'admin.tasklist.attivita.store.timesheet',
        'uses' => 'AttivitaController@storeTimesheet',
        'middleware' => 'can:tasklist.attivita.create'
    ]);
    $router->post('attivita/sollecitaAssegnatari', [
        'as' => 'admin.tasklist.attivita.sollecita',
        'uses' => 'AttivitaController@sollecitaAssegnatari',
        'middleware' => 'can:tasklist.attivita.edit'
    ]);
	$router->get('attivita/{attivita}/read', [
        'as' => 'admin.tasklist.attivita.read',
        'uses' => 'AttivitaController@read',
        'middleware' => 'can:tasklist.attivita.read'
    ]);
    $router->put('attivita/{attivita}', [
        'as' => 'admin.tasklist.attivita.update',
        'uses' => 'AttivitaController@update',
        'middleware' => 'can:tasklist.attivita.edit'
    ]);
    $router->delete('attivita/{attivita}', [
        'as' => 'admin.tasklist.attivita.destroy',
        'uses' => 'AttivitaController@destroy',
        'middleware' => 'can:tasklist.attivita.destroy'
    ]);
    $router->post('attivita/attivitable', [
        'as' => 'admin.tasklist.attivita.attivitable',
        'uses' => 'AttivitaController@attivitable',
        'middleware' => 'can:tasklist.attivita.edit'
    ]);
    $router->get('attivita/presavisione/{attivita}', [
        'as' => 'admin.tasklist.attivita.presavisione',
        'uses' => 'AttivitaController@presaVisione',
        'middleware' => 'can:tasklist.attivita.edit'
    ]);
    $router->get('attivita/presavisione/{attivita}/clear', [
        'as' => 'admin.tasklist.attivita.presavisione.clear',
        'uses' => 'AttivitaController@clearPreseVisioni',
        'middleware' => 'can:tasklist.attivita.edit'
    ]);
    $router->get('attivita/gantt', [
        'as' => 'admin.tasklist.attivita.gantt',
        'uses' => 'AttivitaController@gantt',
        'middleware' => 'can:tasklist.attivita.gantt'
    ]);
    $router->get('attivita/pin/{attivita}', [
        'as' => 'admin.tasklist.attivita.pin',
        'uses' => 'AttivitaController@pinAttivita',
        'middleware' => 'can:tasklist.attivita.edit'
    ]);
    $router->get('richiesteinterventi/export/excel', [
        'as' => 'admin.tasklist.attivita.exportexcel',
        'uses' => 'AttivitaController@exportExcel',
        'middleware' => 'can:tasklist.attivita.export'
    ]);

    $router->bind('rinnovo', function ($id) {
        return app('Modules\Tasklist\Repositories\RinnovoRepository')->find($id);
    });
    $router->get('rinnovi', [
        'as' => 'admin.tasklist.rinnovo.index',
        'uses' => 'RinnovoController@index',
        'middleware' => 'can:tasklist.rinnovi.index'
    ]);
    $router->get('rinnovi/create', [
        'as' => 'admin.tasklist.rinnovo.create',
        'uses' => 'RinnovoController@create',
        'middleware' => 'can:tasklist.rinnovi.create'
    ]);
    $router->post('rinnovi', [
        'as' => 'admin.tasklist.rinnovo.store',
        'uses' => 'RinnovoController@store',
        'middleware' => 'can:tasklist.rinnovi.create'
    ]);
    $router->get('rinnovi/{rinnovo}/edit', [
        'as' => 'admin.tasklist.rinnovo.edit',
        'uses' => 'RinnovoController@edit',
        'middleware' => 'can:tasklist.rinnovi.edit'
    ]);
  	$router->get('rinnovi/{rinnovo}/read', [
        'as' => 'admin.tasklist.rinnovo.read',
        'uses' => 'RinnovoController@read',
        'middleware' => 'can:tasklist.rinnovi.read'
    ]);
    $router->put('rinnovi/{rinnovo}', [
        'as' => 'admin.tasklist.rinnovo.update',
        'uses' => 'RinnovoController@update',
        'middleware' => 'can:tasklist.rinnovi.edit'
    ]);
    $router->delete('rinnovi/{rinnovo}', [
        'as' => 'admin.tasklist.rinnovo.destroy',
        'uses' => 'RinnovoController@destroy',
        'middleware' => 'can:tasklist.rinnovi.destroy'
    ]);
    $router->delete('rinnovinotifica/{id}', [
        'as' => 'admin.tasklist.rinnovonotifica.destroy',
        'uses' => 'RinnovoController@destroyNotifica',
        'middleware' => 'can:tasklist.rinnovi.destroy'
    ]);
    $router->get('rinnovi/export/excel', [
        'as' => 'admin.tasklist.rinnovo.exportexcel',
        'uses' => 'RinnovoController@exportExcel',
        'middleware' => 'can:tasklist.rinnovi.export'
    ]);


    /*
    $router->post('ticketintervento/checkordinativo', [
        'as' => 'admin.assistenza.ticketintervento.checkordinativo',
        'uses' => 'TicketInterventoController@checkOrdinativo',
        'middleware' => 'can:assistenza.ticketinterventi.edit'
    ]);
    */


    // $router->bind('attivitavoce', function ($id) {
    //     return app('Modules\Tasklist\Repositories\AttivitaVociRepository')->find($id);
    // });
    // $router->get('attivitavoci', [
    //     'as' => 'admin.tasklist.attivitavoci.index',
    //     'uses' => 'AttivitaVociController@index',
    //     'middleware' => 'can:tasklist.attivitavoci.index'
    // ]);
    // $router->get('attivitavoci/create', [
    //     'as' => 'admin.tasklist.attivitavoci.create',
    //     'uses' => 'AttivitaVociController@create',
    //     'middleware' => 'can:tasklist.attivitavoci.create'
    // ]);
    // $router->post('attivitavoci', [
    //     'as' => 'admin.tasklist.attivitavoci.store',
    //     'uses' => 'AttivitaVociController@store',
    //     'middleware' => 'can:tasklist.attivitavoci.create'
    // ]);
    // $router->get('attivitavoci/{attivitavoci}/edit', [
    //     'as' => 'admin.tasklist.attivitavoci.edit',
    //     'uses' => 'AttivitaVociController@edit',
    //     'middleware' => 'can:tasklist.attivitavoci.edit'
    // ]);
    // $router->put('attivitavoci/{attivitavoci}', [
    //     'as' => 'admin.tasklist.attivitavoci.update',
    //     'uses' => 'AttivitaVociController@update',
    //     'middleware' => 'can:tasklist.attivitavoci.edit'
    // ]);
    // $router->delete('attivitavoci/{attivitavoci}', [
    //     'as' => 'admin.tasklist.attivitavoci.destroy',
    //     'uses' => 'AttivitaVociController@destroy',
    //     'middleware' => 'can:tasklist.attivitavoci.destroy'
    // ]);
    $router->bind('timesheet', function ($id) {
        return app('Modules\Tasklist\Repositories\TimesheetRepository')->find($id);
    });
    $router->get('timesheets', [
        'as' => 'admin.tasklist.timesheet.index',
        'uses' => 'TimesheetController@index',
        'middleware' => 'can:tasklist.timesheets.index'
    ]);
    $router->get('timesheets/admin', [
        'as' => 'admin.tasklist.timesheet.manage',
        'uses' => 'TimesheetController@manage',
        'middleware' => 'can:tasklist.timesheets.manage'
    ]);
    $router->post('timesheets/ajaxrequest', [
        'as' => 'admin.tasklist.timesheet.timesheetsAjaxRequest',
        'uses' => 'TimesheetController@timesheetsAjaxRequest',
        'middleware' => 'can:tasklist.timesheets.index'
    ]);
    // $router->get('timesheets/create', [
    //     'as' => 'admin.tasklist.timesheet.create',
    //     'uses' => 'TimesheetController@create',
    //     'middleware' => 'can:tasklist.timesheets.create'
    // ]);
    $router->post('timesheets', [
        'as' => 'admin.tasklist.timesheet.store',
        'uses' => 'TimesheetController@store',
        'middleware' => 'can:tasklist.timesheets.create'
    ]);
    $router->get('timesheets/{date}/edit', [
        'as' => 'admin.tasklist.timesheet.edit',
        'uses' => 'TimesheetController@edit',
        'middleware' => 'can:tasklist.timesheets.edit'
    ]);
    $router->get('timesheets/{id}/show', [
        'as' => 'admin.tasklist.timesheet.show',
        'uses' => 'TimesheetController@show',
        'middleware' => 'can:tasklist.timesheets.edit'
    ]);
    $router->put('timesheets/{date}', [
        'as' => 'admin.tasklist.timesheet.update',
        'uses' => 'TimesheetController@update',
        'middleware' => 'can:tasklist.timesheets.edit'
    ]);
    $router->get('timesheets/export/excel', [
        'as' => 'admin.tasklist.timesheet.exportexcel',
        'uses' => 'TimesheetController@exportExcel',
        'middleware' => 'can:tasklist.timesheets.manage'
    ]);
    // $router->delete('timesheets/{timesheet}', [
    //     'as' => 'admin.tasklist.timesheet.destroy',
    //     'uses' => 'TimesheetController@destroy',
    //     'middleware' => 'can:tasklist.timesheets.destroy'
    // ]);
// append

});
