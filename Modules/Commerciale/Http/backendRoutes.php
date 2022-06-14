<?php
use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/commerciale'], function (Router $router) {
    $router->bind('offerta', function ($id) {
        return app('Modules\Commerciale\Repositories\OffertaRepository')->find($id);
    });

    // report
    $router->post('ordinativi/report/reportviste/', [
        'as' => 'admin.commerciale.censimentocliente.reportVistastore',
        'uses' => 'OrdinativoController@reportvisteStore',
        // 'middleware' => 'can:commerciale.ordinativi.create'
    ]);

    $router->get('offerte', [
        'as' => 'admin.commerciale.offerta.index',
        'uses' => 'OffertaController@index',
        'middleware' => 'can:commerciale.offerte.index'
    ]);
    $router->get('offerte/create', [
        'as' => 'admin.commerciale.offerta.create',
        'uses' => 'OffertaController@create',
        'middleware' => 'can:commerciale.offerte.create'
    ]);
    $router->get('offerte/create/ordinativo', [
        'as' => 'admin.commerciale.offerta.create.ordinativo',
        'uses' => 'OffertaController@createOrdinativo',
        'middleware' => 'can:commerciale.offerte.create'
    ]);
    $router->post('offerte', [
        'as' => 'admin.commerciale.offerta.store',
        'uses' => 'OffertaController@store',
        'middleware' => 'can:commerciale.offerte.create'
    ]);
    $router->get('offerte/{offerta}/edit', [
        'as' => 'admin.commerciale.offerta.edit',
        'uses' => 'OffertaController@edit',
        'middleware' => 'can:commerciale.offerte.edit'
    ]);
	 $router->get('offerte/{offerta}/read', [
        'as' => 'admin.commerciale.offerta.read',
        'uses' => 'OffertaController@read',
        'middleware' => 'can:commerciale.offerte.read'
    ]);

    $router->put('offerte/{offerta}', [
        'as' => 'admin.commerciale.offerta.update',
        'uses' => 'OffertaController@update',
        'middleware' => 'can:commerciale.offerte.edit'
    ]);
    $router->delete('offerte/{offerta}', [
        'as' => 'admin.commerciale.offerta.destroy',
        'uses' => 'OffertaController@destroy',
        'middleware' => 'can:commerciale.offerte.destroy'
    ]);

    $router->delete('offerta/allegato/{id}', [
        'as' => 'admin.commerciale.offerta.allegato.destroy',
        'uses' => 'OffertaController@allegatoDestroy',
        'middleware' => 'can:commerciale.offerte.destroy'
    ]);
    $router->get('offerta/definitiva/{id}', [
        'as' => 'admin.commerciale.offerta.definitiva',
        'uses' => 'OffertaController@setOffertaDefinitiva',
        'middleware' => 'can:commerciale.offerte.edit'
    ]);
    $router->get('offerta/oda_determina/{id}', [
        'as' => 'admin.commerciale.offerta.oda_determina',
        'uses' => 'OffertaController@setOdaDetermina',
        'middleware' => 'can:commerciale.offerte.edit'
    ]);
    $router->get('offerta/ordine-mepa/{id}', [
        'as' => 'admin.commerciale.offerta.ordine_mepa',
        'uses' => 'OffertaController@setOrdineMepa',
        'middleware' => 'can:commerciale.offerte.edit'
    ]);
    $router->get('offerta/generaordinativo/{id_offerta}', [
        'as' => 'admin.commerciale.offerta.generaordinativo',
        'uses' => 'OffertaController@generaOrdinativo',
        'middleware' => 'can:commerciale.offerte.edit'
    ]);
    $router->get('offerte/export/excel', [
        'as' => 'admin.commerciale.offerte.export.excel',
        'uses' => 'OffertaController@exportExcel',
        'middleware' => 'can:export.exports.offerte'
    ]);
    // Excel Scadenze Fatturazioni
    $router->get('offerte/export/excel/scadenze', [
        'as' => 'admin.commerciale.offerte.export.excel.scadenze',
        'uses' => 'OffertaController@exportScadenzeExcel',
        'middleware' => 'can:export.exports.offerte'
    ]);
    $router->bind('ordinativo', function ($id) {
        return app('Modules\Commerciale\Repositories\OrdinativoRepository')->find($id);
    });
    $router->get('ordinativi', [
        'as' => 'admin.commerciale.ordinativo.index',
        'uses' => 'OrdinativoController@index',
        'middleware' => 'can:commerciale.ordinativi.index'
    ]);
    $router->get('ordinativi/{ordinativo_id}/attivita/create', [
        'as' => 'admin.commerciale.ordinativo.attivita.create',
        'uses' => 'OrdinativoController@createAttivita',
        'middleware' => 'can:commerciale.ordinativi.create'
    ]);
    $router->get('ordinativi/{ordinativo}/edit', [
        'as' => 'admin.commerciale.ordinativo.edit',
        'uses' => 'OrdinativoController@edit',
        'middleware' => 'can:commerciale.ordinativi.edit'
    ]);
	 $router->get('ordinativi/{ordinativo}/read', [
        'as' => 'admin.commerciale.ordinativo.read',
        'uses' => 'OrdinativoController@read',
        'middleware' => 'can:commerciale.ordinativi.read'
    ]);
    $router->get('ordinativi/{ordinativo_id}/attivita/{attivita_id}/edit', [
        'as' => 'admin.commerciale.ordinativo.attivita.edit',
        'uses' => 'OrdinativoController@editAttivita',
        'middleware' => 'can:commerciale.ordinativi.edit'
    ]);
    $router->put('ordinativi/{ordinativo}', [
        'as' => 'admin.commerciale.ordinativo.update',
        'uses' => 'OrdinativoController@update',
        'middleware' => 'can:commerciale.ordinativi.edit'
    ]);
    $router->delete('ordinativi/{ordinativo}', [
        'as' => 'admin.commerciale.ordinativo.destroy',
        'uses' => 'OrdinativoController@destroy',
        'middleware' => 'can:commerciale.ordinativi.destroy'
    ]);
// report sav
    
    // $router->post('ordinativi/report/reportviste/{id}', [
    //     'as' => 'admin.commerciale.censimentocliente.reportVistastore',
    //     'uses' => 'OrdinativoController@reportvisteStore',
    //     'middleware' => 'can:commerciale.ordinativi.create'
    // ]);

    $router->get('ordinativi/export/excel/sal', [
        'as' => 'admin.tasklist.attivita.exportsalexcel',
        'uses' => 'OrdinativoController@exportSALExcel',
        'middleware' => 'can:commerciale.ordinativi.export.sal'
    ]);
    $router->get('ordinativi/export/excel', [
        'as' => 'admin.commerciale.ordinativi.export.excel',
        'uses' => 'OrdinativoController@exportExcel',
        'middleware' => 'can:export.exports.ordinativi'
    ]);
    $router->bind('fatturazione', function ($id) {
        return app('Modules\Commerciale\Repositories\FatturazioneRepository')->find($id);
    });
    $router->get('fatturazioni', [
        'as' => 'admin.commerciale.fatturazione.index',
        'uses' => 'FatturazioneController@index',
        'middleware' => 'can:commerciale.fatturazioni.index'
    ]);
    $router->get('fatturazioni/create', [
        'as' => 'admin.commerciale.fatturazione.create',
        'uses' => 'FatturazioneController@create',
        'middleware' => 'can:commerciale.fatturazioni.create'
    ]);
    $router->post('fatturazioni', [
        'as' => 'admin.commerciale.fatturazione.store',
        'uses' => 'FatturazioneController@store',
        'middleware' => 'can:commerciale.fatturazioni.create'
    ]);
    $router->get('fatturazioni/{fatturazione}/read', [
        'as' => 'admin.commerciale.fatturazione.read',
        'uses' => 'FatturazioneController@read',
        'middleware' => 'can:commerciale.fatturazioni.read'
    ]);
    $router->get('fatturazioni/{fatturazione}/edit', [
        'as' => 'admin.commerciale.fatturazione.edit',
        'uses' => 'FatturazioneController@edit',
        'middleware' => 'can:commerciale.fatturazioni.edit'
    ]);
    $router->put('fatturazioni/{fatturazione}', [
        'as' => 'admin.commerciale.fatturazione.update',
        'uses' => 'FatturazioneController@update',
        'middleware' => 'can:commerciale.fatturazioni.edit'
    ]);
    $router->delete('fatturazioni/{fatturazione}', [
        'as' => 'admin.commerciale.fatturazione.destroy',
        'uses' => 'FatturazioneController@destroy',
        'middleware' => 'can:commerciale.fatturazioni.destroy'
    ]);
    // PDF fatture
    $router->get('fatturazioni/{id}/pdf', [
        'as' => 'admin.commerciale.fatturazione.pdf',
        'uses' => 'FatturazioneController@generaFattura',
        'middleware' => 'can:commerciale.fatturazioni.edit'
    ]);
    // XML fatture
    $router->get('fatturazioni/{id}/xml', [
        'as' => 'admin.commerciale.fatturazione.xml',
        'uses' => 'FatturazioneController@generaXML',
        'middleware' => 'can:commerciale.fatturazioni.edit'
    ]);
    // Excel fatture
    $router->get('fatturazioni/export/excel/fatture', [
        'as' => 'admin.commerciale.fatturazione.export.excel',
        'uses' => 'FatturazioneController@exportExcel',
        'middleware' => 'can:export.exports.fatturazione'
    ]);
    // Excel voci fatture
    $router->get('fatturazioni/export/excel/fatture/voci', [
        'as' => 'admin.commerciale.fatturazione.export.excel.voci',
        'uses' => 'FatturazioneController@exportVociExcel',
        'middleware' => 'can:export.exports.fatturazione'
    ]);
    $router->bind('analisivendita', function ($id) {
        return app('Modules\Commerciale\Repositories\AnalisiVenditaRepository')->find($id);
    });
    $router->get('analisivendite', [
        'as' => 'admin.commerciale.analisivendita.index',
        'uses' => 'AnalisiVenditaController@index',
        'middleware' => 'can:commerciale.analisivendite.index'
    ]);
    $router->get('analisivendite/create', [
        'as' => 'admin.commerciale.analisivendita.create',
        'uses' => 'AnalisiVenditaController@create',
        'middleware' => 'can:commerciale.analisivendite.create'
    ]);
    $router->post('analisivendite', [
        'as' => 'admin.commerciale.analisivendita.store',
        'uses' => 'AnalisiVenditaController@store',
        'middleware' => 'can:commerciale.analisivendite.create'
    ]);
    $router->get('analisivendite/{analisivendita}/read', [
        'as' => 'admin.commerciale.analisivendita.read',
        'uses' => 'AnalisiVenditaController@read',
        'middleware' => 'can:commerciale.analisivendite.read'
    ]);
    $router->get('analisivendite/conversioneJsonUpdate', [
        'as' => 'admin.commerciale.analisivendita.conversionejsonupdate',
        'uses' => 'AnalisiVenditaController@conversioneChecklistJson',
        'middleware' => 'can:commerciale.analisivendite.read'
    ]);
    $router->get('analisivendite/{analisivendita}/edit', [
        'as' => 'admin.commerciale.analisivendita.edit',
        'uses' => 'AnalisiVenditaController@edit',
        'middleware' => 'can:commerciale.analisivendite.edit'
    ]);
    $router->put('analisivendite/{analisivendita}', [
        'as' => 'admin.commerciale.analisivendita.update',
        'uses' => 'AnalisiVenditaController@update',
        'middleware' => 'can:commerciale.analisivendite.edit'
    ]);
    $router->delete('analisivendite/{analisivendita}', [
        'as' => 'admin.commerciale.analisivendita.destroy',
        'uses' => 'AnalisiVenditaController@destroy',
        'middleware' => 'can:commerciale.analisivendite.destroy'
    ]);
    $router->get('analisivendite/export/excel', [
        'as' => 'admin.commerciale.analisivendita.exportexcel',
        'uses' => 'AnalisiVenditaController@exportExcel',
        'middleware' => 'can:commerciale.analisivendite.exportexcel'
    ]);
    $router->bind('censimentocliente', function ($id) {
        return app('Modules\Commerciale\Repositories\CensimentoClienteRepository')->find($id);
    });
    $router->get('censimenticlienti', [
        'as' => 'admin.commerciale.censimentocliente.index',
        'uses' => 'CensimentoClienteController@index',
        'middleware' => 'can:commerciale.censimenticlienti.index'
    ]);
    $router->get('censimenticlienti/create', [
        'as' => 'admin.commerciale.censimentocliente.create',
        'uses' => 'CensimentoClienteController@create',
        'middleware' => 'can:commerciale.censimenticlienti.create'
    ]);
    $router->post('censimenticlienti', [
        'as' => 'admin.commerciale.censimentocliente.store',
        'uses' => 'CensimentoClienteController@store',
        'middleware' => 'can:commerciale.censimenticlienti.create'
    ]);
    $router->get('censimenticlienti/{censimentocliente}/read', [
        'as' => 'admin.commerciale.censimentocliente.read',
        'uses' => 'CensimentoClienteController@read',
        'middleware' => 'can:commerciale.censimenticlienti.read'
    ]);
    $router->post('censimenticlienti/read/offerta/voci', [
        'as' => 'admin.commerciale.censimentocliente.read.offerta.voci',
        'uses' => 'CensimentoClienteController@readOffertaVociModal',
        'middleware' => 'can:commerciale.censimenticlienti.read'
    ]);
    $router->get('censimenticlienti/{censimentocliente}/edit', [
        'as' => 'admin.commerciale.censimentocliente.edit',
        'uses' => 'CensimentoClienteController@edit',
        'middleware' => 'can:commerciale.censimenticlienti.edit'
    ]);
    $router->put('censimenticlienti/{censimentocliente}', [
        'as' => 'admin.commerciale.censimentocliente.update',
        'uses' => 'CensimentoClienteController@update',
        'middleware' => 'can:commerciale.censimenticlienti.edit'
    ]);
    $router->delete('censimenticlienti/{censimentocliente}', [
        'as' => 'admin.commerciale.censimentocliente.destroy',
        'uses' => 'CensimentoClienteController@destroy',
        'middleware' => 'can:commerciale.censimenticlienti.destroy'
    ]);
    $router->get('censimenticlienti/{id}/pdf', [
        'as' => 'admin.commerciale.censimentocliente.pdf',
        'uses' => 'CensimentoClienteController@generaPdf',
        'middleware' => 'can:commerciale.censimenticlienti.read'
    ]);
    $router->bind('segnalazioneopportunita', function ($id) {
        return app('Modules\Commerciale\Repositories\SegnalazioneOpportunitaRepository')->find($id);
    });
    $router->get('segnalazioniopportunita', [
        'as' => 'admin.commerciale.segnalazioneopportunita.index',
        'uses' => 'SegnalazioneOpportunitaController@index',
        'middleware' => 'can:commerciale.segnalazioniopportunita.index'
    ]);
    $router->get('segnalazioniopportunita/create', [
        'as' => 'admin.commerciale.segnalazioneopportunita.create',
        'uses' => 'SegnalazioneOpportunitaController@create',
        'middleware' => 'can:commerciale.segnalazioniopportunita.create'
    ]);
    $router->post('segnalazioniopportunita', [
        'as' => 'admin.commerciale.segnalazioneopportunita.store',
        'uses' => 'SegnalazioneOpportunitaController@store',
        'middleware' => 'can:commerciale.segnalazioniopportunita.create'
    ]);
    $router->get('segnalazioniopportunita/{segnalazioneopportunita}/edit', [
        'as' => 'admin.commerciale.segnalazioneopportunita.edit',
        'uses' => 'SegnalazioneOpportunitaController@edit',
        'middleware' => 'can:commerciale.segnalazioniopportunita.create'
    ]);
  	$router->get('segnalazioniopportunita/{segnalazioneopportunita}/read', [
        'as' => 'admin.commerciale.segnalazioneopportunita.read',
        'uses' => 'SegnalazioneOpportunitaController@read',
        'middleware' => 'can:commerciale.segnalazioniopportunita.read'
    ]);
    $router->put('segnalazioniopportunita/{segnalazioneopportunita}', [
        'as' => 'admin.commerciale.segnalazioneopportunita.update',
        'uses' => 'SegnalazioneOpportunitaController@update',
        'middleware' => 'can:commerciale.segnalazioniopportunita.edit'
    ]);
    $router->delete('segnalazioniopportunita/{segnalazioneopportunita}', [
        'as' => 'admin.commerciale.segnalazioneopportunita.destroy',
        'uses' => 'SegnalazioneOpportunitaController@destroy',
        'middleware' => 'can:commerciale.segnalazioniopportunita.destroy'
    ]);
    $router->post('segnalazioniopportunita/{segnalazioneopportunita}/reject', [
        'as' => 'admin.commerciale.segnalazioneopportunita.reject',
        'uses' => 'SegnalazioneOpportunitaController@reject',
        'middleware' => 'can:commerciale.segnalazioniopportunita.destroy'
    ]);
    $router->post('segnalazioniopportunita/accept', [
        'as' => 'admin.commerciale.segnalazioneopportunita.accept',
        'uses' => 'SegnalazioneOpportunitaController@accept',
        'middleware' => 'can:commerciale.segnalazioniopportunita.edit'
    ]);
    $router->get('segnalazioniopportunita/{id}/restore', [
        'as' => 'admin.commerciale.segnalazioneopportunita.restore',
        'uses' => 'SegnalazioneOpportunitaController@restore',
        'middleware' => 'can:commerciale.segnalazioniopportunita.restore'
    ]);
    $router->post('segnalazioniopportunita/updstato', [
        'as' => 'admin.commerciale.segnalazioneopportunita.updStato',
        'uses' => 'SegnalazioneOpportunitaController@updStato',
        'middleware' => 'can:commerciale.censimenticlienti.edit'
    ]);
  	$router->post('segnalazioniopportunita/updcommerciale', [
        'as' => 'admin.commerciale.segnalazioneopportunita.updCommerciale',
        'uses' => 'SegnalazioneOpportunitaController@updCommerciale',
        'middleware' => 'can:commerciale.censimenticlienti.edit'
    ]);
    $router->get('segnalazioniopportunita/{id}/disconnectcensimento', [
        'as' => 'admin.commerciale.segnalazioneopportunita.disconnectcensimento',
        'uses' => 'SegnalazioneOpportunitaController@disconnectcensimento',
        'middleware' => 'can:commerciale.censimenticlienti.edit'
    ]);
    $router->get('segnalazioniopportunita/export/excel', [
        'as' => 'admin.commerciale.segnalazioniopportunita.export.excel',
        'uses' => 'SegnalazioneOpportunitaController@exportExcel',
        'middleware' => 'can:commerciale.segnalazioniopportunita.index'
    ]);
    $router->bind('simaziendali', function ($id) {
        return app('Modules\Commerciale\Repositories\SimAziendaliRepository')->find($id);
    });
    $router->get('simaziendalis', [
        'as' => 'admin.commerciale.simaziendali.index',
        'uses' => 'SimAziendaliController@index',
        'middleware' => 'can:commerciale.simaziendalis.index'
    ]);
    $router->get('simaziendalis/create', [
        'as' => 'admin.commerciale.simaziendali.create',
        'uses' => 'SimAziendaliController@create',
        'middleware' => 'can:commerciale.simaziendalis.create'
    ]);
    $router->post('simaziendalis', [
        'as' => 'admin.commerciale.simaziendali.store',
        'uses' => 'SimAziendaliController@store',
        'middleware' => 'can:commerciale.simaziendalis.create'
    ]);
    $router->get('simaziendalis/{simaziendali}/read', [
        'as' => 'admin.commerciale.simaziendali.read',
        'uses' => 'SimAziendaliController@read',
        'middleware' => 'can:commerciale.simaziendalis.read'
    ]);
    $router->get('simaziendalis/{simaziendali}/edit', [
        'as' => 'admin.commerciale.simaziendali.edit',
        'uses' => 'SimAziendaliController@edit',
        'middleware' => 'can:commerciale.simaziendalis.edit'
    ]);
    $router->put('simaziendalis/{simaziendali}', [
        'as' => 'admin.commerciale.simaziendali.update',
        'uses' => 'SimAziendaliController@update',
        'middleware' => 'can:commerciale.simaziendalis.edit'
    ]);
    $router->delete('simaziendalis/{simaziendali}', [
        'as' => 'admin.commerciale.simaziendali.destroy',
        'uses' => 'SimAziendaliController@destroy',
        'middleware' => 'can:commerciale.simaziendalis.destroy'
    ]);
// append


});
