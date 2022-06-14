<?php

namespace Modules\Commerciale\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Assistenza\Entities\RichiesteIntervento;
use Modules\Assistenza\Entities\RichiesteInterventoAzione;
use Modules\Commerciale\Entities\AnalisiVendita;
use Modules\Commerciale\Entities\Ordinativo;
use Modules\Commerciale\Http\Requests\CreateOrdinativoRequest;
use Modules\Commerciale\Http\Requests\UpdateOrdinativoRequest;
use Modules\Commerciale\Repositories\OrdinativoRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Profile\Entities\Area;
use Modules\Tasklist\Entities\Timesheet;
use Modules\Wecore\Entities\Meta;
use Validator;
use Hash;
use Illuminate\Support\Str;

use Modules\Commerciale\Entities\Offerta;
use Modules\Tasklist\Entities\Rinnovo;
use Modules\Tasklist\Entities\Attivita;
use Modules\Tasklist\Entities\RinnovoNotifica;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Profile\Entities\Gruppo;
use Modules\Profile\Entities\Procedura;
use Modules\Amministrazione\Entities\Clienti;
use Modules\Commerciale\Entities\OrdinativoGiornate;
use Modules\Commerciale\Entities\FatturazioneScadenze;

use Modules\User\Entities\Sentinel\User;

use Maatwebsite\Excel\Facades\Excel;
use Modules\Export\Entities\AttivitaExport;
use Modules\Export\Entities\OrdinativiExport;

use Carbon\Carbon;

class OrdinativoController extends AdminBaseController
{
    /**
     * @var OrdinativoRepository
     */
    private $ordinativo;


    public function __construct(OrdinativoRepository $ordinativo)
    {
        parent::__construct();

        $this->ordinativo = $ordinativo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if(empty($request->all()))
        {
          $res['order']['by'] = 'codice';
          $res['order']['sort'] = 'desc';
          $request->merge($res);
        }

        $offerte = [-1 => ''] + Offerta::select(DB::raw("CONCAT(anno,'-',LPAD(numero,3,0)) AS codice"), 'id')
                            ->where('azienda', session('azienda'))
                            ->whereHas('cliente', function($q){
                                $q->commerciali(); })
                            ->pluck('codice', 'id')
                            ->toArray(); 

        $ordinativi = Ordinativo::select('commerciale__ordinativi.*')->filter($request->all())
                            ->where('commerciale__ordinativi.azienda', session('azienda'))
                            ->paginateFilter(config('wecore.pagination.limit'));

        $clienti = [''] + Clienti::commerciali()->pluck('ragione_sociale', 'id')->toArray();

        $request->flash();

        return view('commerciale::admin.ordinativi.index', compact('offerte', 'ordinativi', 'clienti'));
    }

    /**
     * Show the form for editing the specified resource. 
     *
     * @param  Ordinativo $ordinativo
     * @return Response
     */
    public function edit(Ordinativo $ordinativo, Request $request)
    {
        if($ordinativo->azienda != session('azienda'))
            return redirect()->route('admin.commerciale.ordinativo.index')
                    ->withWarning('AVVISO: non puoi accedere a questo ordinativo con l\'azienda ' . session('azienda'));

        if($request->tab == 'attivita' && empty($ordinativo->cliente_id)){
            return redirect()->back()
            ->withError('L\'ordinativo non ha un cliente selezionato.');   
        }

        $offerte = Offerta::with('cliente')->select(DB::raw("CONCAT(anno, '-', LPAD(numero,3,0), ' | ', oggetto, ' - ', importo_iva) AS dati"), 'id')
                            ->where('azienda', session('azienda'))
                            ->whereNull('ordinativo_id')->orWhere('ordinativo_id', '=', 0)->orWhere('ordinativo_id', '=', $ordinativo->id)
                            ->pluck('dati', 'id')
                            ->toArray(); 

        $clienti = Clienti::pluck('ragione_sociale', 'id')->prepend('Seleziona un cliente', '')->toArray();

        //$clienti_offerte = Offerta::with('cliente')->where('ordinativo_id', '=', $ordinativo->id)->get()->pluck('cliente.ragione_sociale', 'cliente.id')->prepend('Seleziona un cliente', '')->toArray();

        $rinnovo = $ordinativo->rinnovo;
        $documenti_tipologie = config('commerciale.ordinativi.documenti');
        $categorie_voci = config('commerciale.ordinativi.voci.categorie');
        $aree_list = [''] + Area::all()->pluck('titolo', 'id')->toArray();

        $utenti = User::select(DB::raw("CONCAT(last_name,' ',first_name) AS nome"), 'id')
                            ->pluck('nome', 'id')
                            ->toArray();

        $procedure = Procedura::all();
        $procedure_list = [''] + $procedure->pluck('titolo', 'id')->toArray();

        $gg_ordinativi2 = Ordinativo::get_giornate($ordinativo->id ,true);
        foreach ($gg_ordinativi2 as $key => $value) {
           $gg_ordinativi[$value->gruppo_id] =  $value;
        }
        $gruppi = Gruppo::all();
        $gruppi_list = [''] + $gruppi->pluck('nome', 'id')->toArray();

        $utenti_list = [''] + User::all()->pluck('full_name', 'id')->toArray();

        $attivita = $ordinativo->attivita;

        $ode = array();
        $clienti_fatturazione = array();

        foreach($ordinativo->offerte()->get() as $offerta)
        {
            array_push($ode, $offerta->oda_determina_ids->first() ?? null);
        }

        if(count($ode) > 0){
            $ode = Meta::find($ode);
        }

        return view('commerciale::admin.ordinativi.edit', compact('ode', 'categorie_voci', 'utenti_list', 'ordinativo', 'clienti', 'offerte', 'rinnovo', 'utenti', 'attivita', 'gruppi','procedure','gg_ordinativi', 'documenti_tipologie', 'aree_list', 'procedure_list', 'gruppi_list'));
    }


  /**
     * Show  the specified resource.
     *
     * @param  Ordinativo $ordinativo
     * @return Response
     */
    public function read(Ordinativo $ordinativo, Request $request)
    {
        if($ordinativo->azienda != session('azienda'))
            return redirect()->route('admin.commerciale.ordinativo.index')
                    ->withWarning('AVVISO: non puoi accedere a questo ordinativo con l\'azienda ' . session('azienda'));

        if($request->tab == 'attivita' && empty($ordinativo->cliente_id)){
            return redirect()->back()
            ->withError('L\'ordinativo non ha un cliente selezionato.');   
        }


        if($request->tab == 'quadro_avanzamento') 
        {
            $quadro_avanzamento = array();
            $aree = Area::all();

            $helpdesk_ids = [
                142,
                144,
                146,
                148,
                150,
                152,
                154,
                168
            ];

            $totali = array();
            $totali['timesheets'] = ['previsti_no_hd' => 0, 'previsti_hd' => 0, 'effettuati_no_hd' => 0, 'effettuati_hd' => 0, 'percentuale_hd' => 0, 'percentuale_no_hd' => 0];
            $totali['interventi'] = ['previsti_giornate' => 0, 'previsti_ore' => 0, 'effettuati_giornate' => 0, 'effettuati_ore' => 0, 'percentuale_ore' => 0, 'percentuale_giornate' => 0];    
            $totali['attivita'] = ['totale' => 0, 'completate' => 0, 'percentuale' => 0];   

            // Calcolo previsti dall'analisi vendita

            if($ordinativo->offerte()->count() > 0)
            {
                foreach($ordinativo->offerte()->get() as $offerta)
                {
                    if(!empty($offerta->analisi_vendita()->first()))
                    {
                        if(!empty($offerta->analisi_vendita()->first()->attivita))
                        {
                            $previsti = array();
                            foreach($offerta->analisi_vendita()->first()->attivita as $key => $attivita)
                            {
                                $area_id = Gruppo::find($key)->area_id;
                                if(empty($previsti[$area_id])){
                                    $previsti[$area_id] = array();
                                    $previsti[$area_id]['helpdesk'] = 0;
                                    $previsti[$area_id]['no_helpdesk'] = 0;
                                }
            
                                if(in_array($key, $helpdesk_ids))
                                {
                                    if(!empty($attivita->figure_professionali))
                                    {
                                        foreach($attivita->figure_professionali as $figura)
                                        {
                                            if($figura->ore > 0)
                                            {
                                                $previsti[$area_id]['helpdesk'] += $figura->ore;
                                            }
                                        }
                                    }
                                } else {
                                    if(!empty($attivita->figure_professionali))
                                    {
                                        foreach($attivita->figure_professionali as $figura)
                                        {
                                            if($figura->ore > 0)
                                            {
                                                $previsti[$area_id]['no_helpdesk'] += $figura->ore;
                                            }
                                        } 
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // Calcolo valori attuali

            foreach($aree as $area)
            {
                $quadro_avanzamento[$area->titolo] = array();
                $quadro_avanzamento[$area->titolo]['area_id'] = $area->id;
                $quadro_avanzamento[$area->titolo]['timesheets'] = array();
                $quadro_avanzamento[$area->titolo]['interventi'] = array();
                $quadro_avanzamento[$area->titolo]['attivita'] = array();

                if(empty($previsti[$area->id]))
                {
                    $previsti[$area->id] = array();
                    $previsti[$area->id]['helpdesk'] = 0;
                    $previsti[$area->id]['no_helpdesk'] = 0;                    
                }

                $timesheets_effettuati_hd = Timesheet::withoutGlobalScopes()->where('ordinativo_id', $ordinativo->id)->where('area_id', $area->id)->whereNotNull('ticket_azione_id')->get()->sum->durata_lavorativa();
                $timesheets_effettuati = Timesheet::withoutGlobalScopes()->where('ordinativo_id', $ordinativo->id)->where('area_id', $area->id)->whereNull('ticket_azione_id')->get()->sum->durata_lavorativa();

                $gg_ordinativi = Ordinativo::get_giornate($ordinativo->id);

                $attivita_totale = Attivita::where('ordinativo_id', $ordinativo->id)->where('area_id', $area->id)->count();
                $attivita_completate = Attivita::where('ordinativo_id', $ordinativo->id)->where('area_id', $area->id)->where('percentuale_completamento', 100)->count();
                $attivita_percentuale = Attivita::where('ordinativo_id', $ordinativo->id)->where('area_id', $area->id)->avg('percentuale_completamento');
        
                $gg_totali_ore_hd = collect($gg_ordinativi)->where('tipo', 1)->where('area_id', $area->id)->whereIn('gruppo_id', $helpdesk_ids)->sum('quantita');
                $gg_totali_giorni_hd = collect($gg_ordinativi)->where('tipo', 0)->where('area_id', $area->id)->whereIn('gruppo_id', $helpdesk_ids)->sum('quantita');
                $gg_residui_ore_hd = collect($gg_ordinativi)->where('tipo', 1)->where('area_id', $area->id)->whereIn('gruppo_id', $helpdesk_ids)->sum('quantita_residue');
                $gg_residui_giorni_hd = collect($gg_ordinativi)->where('tipo', 0)->where('area_id', $area->id)->whereIn('gruppo_id', $helpdesk_ids)->sum('quantita_residue');
                $gg_effettuati_ore_hd = collect($gg_ordinativi)->where('tipo', 1)->where('area_id', $area->id)->whereIn('gruppo_id', $helpdesk_ids)->sum('quantita_gia_effettuate') + ($gg_totali_ore_hd - $gg_residui_ore_hd);
                $gg_effettuati_giorni_hd = collect($gg_ordinativi)->where('tipo', 0)->where('area_id', $area->id)->whereIn('gruppo_id', $helpdesk_ids)->sum('quantita_gia_effettuate') + ($gg_totali_giorni_hd - $gg_residui_giorni_hd);
                $gg_totali_hd = ($gg_totali_giorni_hd * 6) +  $gg_totali_ore_hd;

                $gg_totali_ore = collect($gg_ordinativi)->where('tipo', 1)->where('area_id', $area->id)->whereNotIn('gruppo_id', $helpdesk_ids)->sum('quantita');
                $gg_totali_giorni = collect($gg_ordinativi)->where('tipo', 0)->where('area_id', $area->id)->whereNotIn('gruppo_id', $helpdesk_ids)->sum('quantita');
                $gg_residui_ore = collect($gg_ordinativi)->where('tipo', 1)->where('area_id', $area->id)->whereNotIn('gruppo_id', $helpdesk_ids)->sum('quantita_residue');
                $gg_residui_giorni = collect($gg_ordinativi)->where('tipo', 0)->where('area_id', $area->id)->whereNotIn('gruppo_id', $helpdesk_ids)->sum('quantita_residue');
                $gg_effettuati_ore = collect($gg_ordinativi)->where('tipo', 1)->where('area_id', $area->id)->whereNotIn('gruppo_id', $helpdesk_ids)->sum('quantita_gia_effettuate') + ($gg_totali_ore - $gg_residui_ore);
                $gg_effettuati_giorni = collect($gg_ordinativi)->where('tipo', 0)->where('area_id', $area->id)->whereNotIn('gruppo_id', $helpdesk_ids)->sum('quantita_gia_effettuate') + ($gg_totali_giorni - $gg_residui_giorni);
                $gg_totali = ($gg_totali_giorni * 6) + $gg_totali_ore;

                $quadro_avanzamento[$area->titolo]['timesheets']['helpdesk'] = [
                    'previsto' => (int) ($previsti[$area->id]['helpdesk'] ?: 0),
                    'effettuato' => (int) round($timesheets_effettuati_hd / 3600),
                    'percentuale' => (int) ( ( round($timesheets_effettuati_hd / 3600) / ($previsti[$area->id]['helpdesk'] ?: 1) ) * 100 ),
                ];

                $totali['timesheets']['previsti_hd'] += $quadro_avanzamento[$area->titolo]['timesheets']['helpdesk']['previsto'];
                $totali['timesheets']['effettuati_hd'] += $quadro_avanzamento[$area->titolo]['timesheets']['helpdesk']['effettuato'];

                $quadro_avanzamento[$area->titolo]['timesheets']['no_helpdesk'] = [
                    'previsto' => (int) ($previsti[$area->id]['no_helpdesk'] ?: 0),
                    'effettuato' => (int) round($timesheets_effettuati / 3600),
                    'percentuale' => (int) ( ( round($timesheets_effettuati / 3600) / ($previsti[$area->id]['no_helpdesk'] ?: 1) ) * 100 ),
                ];

                $totali['timesheets']['previsti_no_hd'] += $quadro_avanzamento[$area->titolo]['timesheets']['no_helpdesk']['previsto'];
                $totali['timesheets']['effettuati_no_hd'] += $quadro_avanzamento[$area->titolo]['timesheets']['no_helpdesk']['effettuato'];

                $quadro_avanzamento[$area->titolo]['interventi']['ore'] = [
                    'previsto' => (int) ($gg_totali_ore_hd + $gg_totali_ore),
                    'effettuati' => (int) ($gg_effettuati_ore_hd + $gg_effettuati_ore),
                    'percentuale' => (int) ( ( ($gg_effettuati_ore_hd + $gg_effettuati_ore) /  ( ($gg_totali_ore_hd + $gg_totali_ore) ?: 1 ) ) * 100),
                ];

                $totali['interventi']['previsti_ore'] += $quadro_avanzamento[$area->titolo]['interventi']['ore']['previsto'];
                $totali['interventi']['effettuati_ore'] += $quadro_avanzamento[$area->titolo]['interventi']['ore']['effettuati'];

                $quadro_avanzamento[$area->titolo]['interventi']['giornate'] = [
                    'previsto' => (int) ($gg_totali_giorni_hd + $gg_totali_giorni),
                    'effettuati' => (int) ($gg_effettuati_giorni_hd + $gg_effettuati_giorni),
                    'percentuale' => (int) ( ( ($gg_effettuati_giorni_hd + $gg_effettuati_giorni) /  ( ($gg_totali_giorni_hd + $gg_totali_giorni) ?: 1 ) ) * 100),
                ];

                $totali['interventi']['previsti_giornate'] += $quadro_avanzamento[$area->titolo]['interventi']['giornate']['previsto'];
                $totali['interventi']['effettuati_giornate'] += $quadro_avanzamento[$area->titolo]['interventi']['giornate']['effettuati'];

                $quadro_avanzamento[$area->titolo]['attivita'] = [
                    'totale' => (int) $attivita_totale,
                    'completate' => (int) $attivita_completate,
                    'percentuale' => (int) $attivita_percentuale,
                ];

                $totali['attivita']['totale'] += $quadro_avanzamento[$area->titolo]['attivita']['totale'];
                $totali['attivita']['completate'] += $quadro_avanzamento[$area->titolo]['attivita']['completate'];

            }

            // Calcolo percentuale dei totali

            $totali['timesheets']['percentuale_hd'] = (int) ( ( $totali['timesheets']['effettuati_hd'] / ($totali['timesheets']['previsti_hd'] ?: 1) ) * 100 );
            $totali['timesheets']['percentuale_no_hd'] = (int) ( ( $totali['timesheets']['effettuati_no_hd'] / ($totali['timesheets']['previsti_no_hd'] ?: 1) ) * 100 );

            $totali['interventi']['percentuale_giornate'] = (int) ( ( $totali['interventi']['effettuati_giornate'] / ($totali['interventi']['previsti_giornate'] ?: 1) ) * 100 );
            $totali['interventi']['percentuale_ore'] = (int) ( ( $totali['interventi']['effettuati_ore'] / ($totali['interventi']['previsti_ore'] ?: 1) ) * 100 );

            $totali['attivita']['percentuale'] = (int) Attivita::where('ordinativo_id', $ordinativo->id)->avg('percentuale_completamento');

            return view('commerciale::admin.ordinativi.read', compact('quadro_avanzamento', 'totali', 'ordinativo'));

        }

        $offerte = Offerta::select(DB::raw("CONCAT(anno, '-', LPAD(numero,3,0), ' | ', oggetto, ' - ', importo_iva) AS dati"), 'id')
                            ->where('azienda', session('azienda'))
                            ->pluck('dati', 'id')
                            ->toArray();

        $clienti = Clienti::all()->pluck('ragione_sociale', 'id')->toArray();

        $categorie_voci = config('commerciale.ordinativi.voci.categorie');

        $rinnovo = $ordinativo->rinnovo;

        $procedure = (empty($request->procedura)) ? Procedura::all() : Procedura::where('id', $request->procedura)->get();
        $gruppi = Gruppo::all();
        $aree_commessa = (empty($request->area)) ? Area::all() : Area::where('id', $request->area)->get();

        $documenti_tipologie = config('commerciale.ordinativi.documenti');
        $aree_list = [''] + Area::all()->pluck('titolo', 'id')->toArray();
        $procedure_list = [''] + Procedura::all()->pluck('titolo', 'id')->toArray();
        $gruppi_list = [''] + $gruppi->pluck('nome', 'id')->toArray();
        $utenti_list = [''] + User::all()->pluck('full_name', 'id')->toArray();

        if(!empty($request->procedura))
            $aree_commessa = $aree_commessa->where('procedura_id', $request->procedura);

        $timesheets_ordinativo = Timesheet::withoutGlobalScopes()->where('ordinativo_id', $ordinativo->id)->get();

        $tipologie = config('tasklist.timesheets.tipologie');

        if($request->tab == 'riepilogo_ore_commessa')
        {
            $commessa_results = [];
            $commessa_results_aree_totals = [];
            $commessa_results_totals = [
                'previsto' => 0,
                'daitimesheets' => 0,
                'saldo' => 0,
            ];
            $gruppi_ids = $gruppi->pluck('id')->toArray();
            foreach($gruppi_ids as $gruppo_id){
                foreach(['previsto', 'daitimesheets', 'saldo'] as $key){
                    $commessa_results[$gruppo_id][$key] = [];
                    foreach(['ore_remoto', 'ore_cliente', 'ore_configurazione', 'ore_formazione_remoto', 'ore_formazione_cliente', 'totale'] as $value){
                        if(!isset($commessa_results[$gruppo_id][$key][$value]))
                            $commessa_results[$gruppo_id][$key][$value] = 0;
                    }
                }
            }

            if($ordinativo->offerte()->count() > 0)
            {
                foreach($ordinativo->offerte()->get() as $offerta)
                {
                    if(!empty($offerta->analisi_vendita()))
                    {
                        $analisi_vendita = json_decode($offerta->analisi_vendita()->first());
                        $analisi_vendita_attivita = (!empty($analisi_vendita->attivita) ? $analisi_vendita->attivita : null);
                        if(!empty($analisi_vendita_attivita)){
                            foreach($analisi_vendita_attivita as $gruppo_id => $value){
                                if(!empty($value->figure_professionali)){
                                    foreach($value->figure_professionali as $key => $value){
                                        $commessa_results[$gruppo_id]['previsto']['ore_remoto'] += (int) $value->ore;
                                        $commessa_results[$gruppo_id]['previsto']['totale'] += array_sum( (array) $value->ore );
                                    }
                                }
                            }
                        }                    
                    }
                }
            }
            foreach($aree_commessa as $area){
                foreach($timesheets_ordinativo as $timesheet){
                    if($timesheet->area_id == $area->id){
                        switch($timesheet->tipologia){
                            case 0: 
                                $commessa_results[$timesheet->gruppo_id]['daitimesheets']['ore_remoto'] += working_time($timesheet->dataora_inizio, $timesheet->dataora_fine, 'seconds');
                                break;
                            case 1:
                                $commessa_results[$timesheet->gruppo_id]['daitimesheets']['ore_cliente'] += working_time($timesheet->dataora_inizio, $timesheet->dataora_fine, 'seconds');
                                break;
                            case 2:
                                $commessa_results[$timesheet->gruppo_id]['daitimesheets']['ore_configurazione'] += working_time($timesheet->dataora_inizio, $timesheet->dataora_fine, 'seconds');
                                break;
                            case 3:
                                $commessa_results[$timesheet->gruppo_id]['daitimesheets']['ore_formazione_remoto'] += working_time($timesheet->dataora_inizio, $timesheet->dataora_fine, 'seconds');
                                break;
                            case 4:
                                $commessa_results[$timesheet->gruppo_id]['daitimesheets']['ore_formazione_cliente'] += working_time($timesheet->dataora_inizio, $timesheet->dataora_fine, 'seconds');
                                break;

                        }
                        $commessa_results[$timesheet->gruppo_id]['daitimesheets']['totale'] += working_time($timesheet->dataora_inizio, $timesheet->dataora_fine, 'seconds');
                    }
                }
            }

            $aree_commessa_ids = [];
            
            foreach($gruppi as $gruppo){
                if($commessa_results[$gruppo->id]['daitimesheets']['totale'] > 0 || $commessa_results[$gruppo->id]['previsto']['totale'] > 0){
                    foreach(['ore_remoto', 'ore_cliente', 'ore_configurazione', 'ore_formazione_remoto', 'ore_formazione_cliente', 'totale'] as $value){
                        $commessa_results[$gruppo->id]['daitimesheets'][$value] = round($commessa_results[$gruppo->id]['daitimesheets'][$value] / 3600);
                    }
                    $commessa_results_totals['daitimesheets'] += $commessa_results[$gruppo->id]['daitimesheets']['totale'];
                    $commessa_results_totals['previsto'] +=  $commessa_results[$gruppo->id]['previsto']['totale'];
                    foreach(['ore_remoto', 'ore_cliente', 'ore_configurazione', 'ore_formazione_remoto', 'ore_formazione_cliente', 'totale'] as $value){
                        $commessa_results[$gruppo->id]['saldo'][$value] = $commessa_results[$gruppo->id]['previsto'][$value] - $commessa_results[$gruppo->id]['daitimesheets'][$value]; //max(($commessa_results[$gruppo->id]['previsto'][$value] - $commessa_results[$gruppo->id]['daitimesheets'][$value]), 0);
                    }
                    $commessa_results_totals['saldo'] += $commessa_results[$gruppo->id]['saldo']['totale'];
                    $aree_commessa_ids[] = $gruppo->area_id;
                }
            }

            if(!empty($aree_commessa_ids)){
                $aree_commessa_ids_new = [];
                $aree_commessa = $aree_commessa->whereIn('id', $aree_commessa_ids);
                foreach($aree_commessa as $area){
                    $commessa_results_aree_totals[] = $area->id;
                    $commessa_results_aree_totals[$area->id] = [];
                    foreach(['previsto', 'daitimesheets', 'saldo'] as $value)
                        $commessa_results_aree_totals[$area->id][$value] = 0;
                    foreach($area->attivita as $gruppo){
                        if(!empty($commessa_results[$gruppo->id]) && $commessa_results[$gruppo->id]['previsto']['totale'] > 0 || $commessa_results[$gruppo->id]['daitimesheets']['totale']){
                            $commessa_results_aree_totals[$area->id]['previsto'] += $commessa_results[$gruppo->id]['previsto']['totale'];
                            $commessa_results_aree_totals[$area->id]['daitimesheets'] += $commessa_results[$gruppo->id]['daitimesheets']['totale'];
                            $commessa_results_aree_totals[$area->id]['saldo'] += $commessa_results[$gruppo->id]['saldo']['totale'];
                            $aree_commessa_ids_new[] = $gruppo->area_id;
                        }
                    }
                }
            }

            if(!empty($aree_commessa_ids_new)){
                $aree_commessa = $aree_commessa->whereIn('id', $aree_commessa_ids_new);
            }

            if(!empty($request->procedura)){
                $procedure_filters = [0 => ''] +  Procedura::all()->pluck('titolo', 'id')->toArray();
            } else {
                $procedure_filters = [0 => ''] +  $procedure->pluck('titolo', 'id')->toArray();
            }
            if(!empty($request->area)){
                $aree_filters = [0 => ''] +  Area::all()->pluck('titolo', 'id')->toArray();
            } else {
                $aree_filters = [0 => ''] +  $aree_commessa->pluck('titolo', 'id')->toArray();
            } 

        } else {
            $aree_commessa = null;
            $commessa_results = null;
            $commessa_results_totals = null;
            $commessa_results_aree_totals = null;
            $aree_commessa_ids = null;
            $aree_filters = null;
            $procedure_filters = null;
        }

        $utenti = User::select(DB::raw("CONCAT(last_name,' ',first_name) AS nome"), 'id')
                            ->pluck('nome', 'id')
                            ->toArray();

        if($request->tab == 'interventi')
        {
            $gg_ordinativi = Ordinativo::get_giornate($ordinativo->id);
            $gruppi_ordinativi = collect($gg_ordinativi)->pluck('gruppo_id')->toArray();

            $gg_totali_ore = collect($gg_ordinativi)->where('tipo', 1)->sum('quantita');
            $gg_totali_giorni = collect($gg_ordinativi)->where('tipo', 0)->sum('quantita');
            $gg_residui_ore = collect($gg_ordinativi)->where('tipo', 1)->sum('quantita_residue');
            $gg_residui_giorni = collect($gg_ordinativi)->where('tipo', 0)->sum('quantita_residue');
            $gg_effettuati_ore = ($gg_totali_ore - $gg_residui_ore); //collect($gg_ordinativi)->where('tipo', 1)->sum('quantita_gia_effettuate')
            $gg_effettuati_giorni = ($gg_totali_giorni - $gg_residui_giorni); //collect($gg_ordinativi)->where('tipo', 0)->sum('quantita_gia_effettuate')
        } else {
            $gg_ordinativi = null;
            $gruppi_ordinativi = null;
            $gg_totali_ore = null;
            $gg_totali_giorni = null;
            $gg_residui_ore = null;
            $gg_residui_giorni = null;
            $gg_effettuati_ore = null;
            $gg_effettuati_giorni = null;
        }

        $attivita = $ordinativo->attivita;

        $request->flash();
  
        return view('commerciale::admin.ordinativi.read', compact('categorie_voci', 'utenti_list', 'clienti', 'ordinativo', 'offerte', 'rinnovo', 'utenti', 'attivita', 'gruppi' ,'gg_ordinativi', 'gg_totali_ore', 'gg_residui_ore', 'gg_effettuati_ore', 'gg_totali_giorni', 'gg_residui_giorni', 'gg_effettuati_giorni', 'tipologie', 'aree_commessa', 'commessa_results', 'commessa_results_totals', 'commessa_results_aree_totals', 'aree_filters', 'procedure_filters', 'aree_commessa_ids', 'procedure_list', 'gruppi_list', 'aree_list', 'documenti_tipologie'));
    }

    public function importaInterventi($id) 
    {
        $analisi = AnalisiVendita::where('azienda', session('azienda'))->where('offerta_id', $id)->first();
        $ordinativo = Ordinativo::where('offerta_id', $id)->first();
        $ordinativogiornate = OrdinativoGiornate::where('ordinativo_id', $ordinativo->id)->pluck('gruppo_id')->toArray();
        $interventi = collect($analisi->attivita)->where('selected', 1)->toArray();
        $count = 0;
        foreach($interventi as $intervento){
            if(!in_array((int) $intervento->selected, $ordinativogiornate)){
                $commerciale_intervento = new OrdinativoGiornate;
                $commerciale_intervento->gruppo_id = (int) $intervento->selected;
                $commerciale_intervento->ordinativo_id = $ordinativo->id;
                $commerciale_intervento->quantita = (int) $intervento->giornate->gg_rem + (int) $intervento->giornate->gg_cli + (int) $intervento->giornate->configurazione + (int) $intervento->giornate->form_rem + (int) $intervento->giornate->form_cli;
                $commerciale_intervento->quantita_residue = $commerciale_intervento->quantita;
                $commerciale_intervento->quantita_gia_effettuate = 0;
                $commerciale_intervento->tipo = 0;
                $commerciale_intervento->attivita = "0";
                $commerciale_intervento->save();
                $count++;
            }
        }
        if($count == 0){
            return redirect()->route('admin.commerciale.ordinativo.read', $ordinativo->id)->withWarning('Non è presente alcun intervento da importare.');
        } else {
            return redirect()->route('admin.commerciale.ordinativo.read', $ordinativo->id)->withSuccess('Interventi importati con successo.');
        }
    }

 
    /**
     * Update the specified resource in storage.
     *
     * @param  Ordinativo $ordinativo
     * @param  UpdateOrdinativoRequest $request
     * @return Response
     */
    public function update(Ordinativo $ordinativo, UpdateOrdinativoRequest $request)
    {

        if($ordinativo->azienda != session('azienda'))
            return redirect()->route('admin.commerciale.ordinativo.index')
                    ->withWarning('AVVISO: non puoi accedere a questo ordinativo con l\'azienda ' . session('azienda'));

        // Sezione "Documenti"  
        if($request->tab == 'documenti' && $request->hasFile('documento_file')) {
            if(!empty($request->documento_nome)){
                $file = $request->file("documento_file");
                if($file->isValid()){
                    $file_info['azienda'] = session('azienda');
                    $file_info['folder'] = 'uploads/' . get_azienda() . '/' . 'commerciale' . '/' . date('Y') . '/' . date('m');
                    $file_info['client_name'] =$file->getClientOriginalName();
                    $file_info['mime_type'] = $file->getMimeType();
                    $file_info['extension'] = $file->extension();
                    $file_info['size'] = $file->getSize();
                    $file_info['hash_name'] = $file->hashName();
                    $file_info['name'] = request("documento_nome");
                    $file_info['path'] = $file_info['folder'] . '/' . $file_info['hash_name'];
                    $file_info['tipologia_id'] = request("documento_tipologia_id");
                    $file_info['procedura_id'] = request("documento_procedura_id");
                    $file_info['area_id'] = request("documento_area_id");
                    $file_info['gruppo_id'] = request("documento_gruppo_id");
                    $file->store('public/' . $file_info['folder']);
    
                    $meta = new Meta([
                        'name' => 'file',
                        'value' => json_encode($file_info),
                        'created_user_id' => Auth::id(),
                        'updated_user_id' => Auth::id()]
                    );
    
                    $ordinativo->metas()->save($meta);
                    return redirect()->route('admin.commerciale.ordinativo.edit', ['ordinativo' => $ordinativo->id, 'tab' => 'documenti'])
                        ->withSuccess('Documento caricato con successo.');
                } else {
                    return redirect()->route('admin.commerciale.ordinativo.edit', ['ordinativo' => $ordinativo->id, 'tab' => 'documenti'])
                        ->withError('Documento non valido.');                
                }
            } else {
                return redirect()->route('admin.commerciale.ordinativo.edit', ['ordinativo' => $ordinativo->id, 'tab' => 'documenti'])
                ->withError('Il documento non ha un nome valido.');                  
            }
        }  

        // Ordinativo / Assistenza
        if(empty($request->tab) || $request->tab == 'ordinativo')
        {

            $rules = Ordinativo::getRules();
            $this->validate($request, $rules);

            if($request->filled('offerte_ids')) {
                Offerta::whereIn('id', $request->offerte_ids)->update(['ordinativo_id' => $ordinativo->id]);

                $ordinativo->offerte()->whereNotIn('id', $request->offerte_ids)->where('ordinativo_id', $ordinativo->id)->update(['ordinativo_id' => 0]);               
            } else {
                $ordinativo->offerte()->where('ordinativo_id', $ordinativo->id)->update(['ordinativo_id' => 0]);
            }

            if($request->filled('responsabili')){
                $responsabili = User::whereIn('id', $request->responsabili)->get();
                $motivo = $request->motivo_responsabili;
                foreach($responsabili as $responsabile){
                    $attivita = Attivita::create([
                        'oggetto' => "Creazione delle attività per l'ordinativo " . $ordinativo->oggetto,
                        'azienda' => session('azienda'),
                        'descrizione' => $motivo,
                        'ordinativo_id' => $ordinativo->id,
                        'cliente_id' => $ordinativo->offerte()->first()->cliente_id,
                        'richiedente_id' => Auth::id(), 
                        'procedura_id' => 5, 
                        'area_id' => 10,
                        'gruppo_id' => 165,
                        'durata_tipo' => 1,
                        'stato' => 0,
                        'data_inizio' => date('Y-m-d'),
                        'created_user_id' => Auth::id(),
                        'updated_user_id' => Auth::id(),
                    ]);
    
                    $attivita->users()->sync($responsabile->id);
    
                    // Email nuova attività al responsabile
                    $oggetto = 'Nuova attività - ' . $attivita->oggetto . ' (' . $attivita->cliente->ragione_sociale . ')' . ' N.O. #' . $ordinativo->numero_ordinativo();
                    $messaggio = 'Hai una nuova attività assegnata da <strong>' . $attivita->richiedente->full_name . '</strong>.<br><br>Per visualizzare i dettagli clicca sul link di seguito:<br><a href="' . route('admin.tasklist.attivita.read', $attivita->id) . '">' . route('admin.tasklist.attivita.read', $attivita->id) . '</a>';
                    mail_send($responsabile->email, $oggetto, $messaggio);
                }
            }
    
            // Ordinativo
            $update_ordinativo = $request->all();
            $this->ordinativo->update($ordinativo, $update_ordinativo);

            // Log
            activity(session('azienda'))
                ->performedOn($ordinativo)
                ->withProperties($update_ordinativo)
                ->log('updated'); 
        }

        if($request->tab == 'assistenza')
        {

            $update_ordinativo = $request->all();

            $attivita = array();
            $i = 1;

            foreach($update_ordinativo['attivita'] as $act){
                if((int)$act['procedura_id'] != 0){
                    $attivita[$i] = array();
                    $attivita[$i]['id'] = $i;
                    $attivita[$i]['procedura_id'] = !empty($act['procedura_id']) ? $act['procedura_id'] : null;
                    $attivita[$i]['area_id'] = !empty($act['area_id']) ? $act['area_id'] : null;
                    $attivita[$i]['gruppo_id'] = !empty($act['gruppo_id']) ? $act['gruppo_id'] : null;
                    $attivita[$i]['descrizione'] = !empty($act['descrizione']) ? $act['descrizione'] : null;
                    $attivita[$i]['destinatari_ids'] = !empty($act['assegnatari_id']) ? $act['assegnatari_id'] : null;
                    $attivita[$i]['ordine'] = !empty($act['ordine']) ? $act['ordine'] : null;
                    $i++;
                }
            }

            $update_ordinativo['assistenza'] = !empty($attivita) ? $attivita : null;

            $this->ordinativo->update($ordinativo, $update_ordinativo);

            //Hash link
            if(empty($ordinativo->hash_link)){
                $this->ordinativo->update($ordinativo, ['hash_link' => str_random(60)]);
                return redirect()->route('admin.commerciale.ordinativo.edit', ['ordinativo' => $ordinativo->id, 'tab' => 'assistenza'])
                ->withSuccess('Codice univoco creato con successo.');
            }

            // Log
            activity(session('azienda'))
                ->performedOn($ordinativo)
                ->withProperties($update_ordinativo)
                ->log('updated'); 

            return redirect()->route('admin.commerciale.ordinativo.edit', ['ordinativo' => $ordinativo->id, 'tab' => 'assistenza'])
            ->withSuccess('L\'ordinativo è stato aggiornato con successo.');

        }

        if($request->tab == 'vocieconomiche')
        {

            $update_ordinativo = $request->all();

            $voci = array();
            $i = 1;

            if(empty($ordinativo->voci_economiche) && $ordinativo->offerte()->count() > 0){
                foreach($ordinativo->offerte()->get() as $offerta){
                    foreach($offerta->voci as $voce){
                        $voci[$i] = array();
                        $voci[$i]['id'] = $i;
                        $voci[$i]['descrizione'] = !empty($voce->descrizione) ? $voce->descrizione : null;
                        $voci[$i]['anno_di_riferimento'] = !empty($voce->anno_di_riferimento) ? $voce->anno_di_riferimento : null;
                        $voci[$i]['categoria'] = !empty($voce->categoria) ? $voce->categoria : null;
                        $voci[$i]['quantita'] = !empty($voce->quantita) ? $voce->quantita : null;
                        $voci[$i]['importo_singolo'] = !empty($voce->importo_singolo) ? clean_currency($voce->importo_singolo) : null;
                        $voci[$i]['iva'] = !empty($voce->iva) ? $voce->iva : null;
                        $voci[$i]['importo'] = !empty($voce->importo) ? clean_currency($voce->importo) : null;
                        $voci[$i]['importo_iva'] = !empty($voce->importo_iva) ? clean_currency($voce->importo_iva) : null;
                        $voci[$i]['esente_iva'] = !empty($voce->esente_iva) ? 1 : 0;
                        $voci[$i]['costo_fisso'] = !empty($voce->costo_fisso) ? 1 : 0;
                        $voci[$i]['accettata'] = 1;
                        $voci[$i]['offerta_id'] = $offerta->id;
                        $i++;
                    }
                }
            }

            foreach($update_ordinativo['voci'] as $voce){
                if((int)$voce['quantita'] != 0){
                    $voci[$i] = array();
                    $voci[$i]['id'] = $i;
                    $voci[$i]['descrizione'] = !empty($voce['descrizione']) ? $voce['descrizione'] : null;
                    if(empty($voci[$i]['descrizione']))
                    {
                        return redirect()->route('admin.commerciale.ordinativo.edit', ['ordinativo' => $ordinativo->id, 'tab' => 'vocieconomiche'])
                        ->withError('Una o più voci non hanno una descrizione.');
                    }
                    $voci[$i]['anno_di_riferimento'] = !empty($voce['anno_di_riferimento']) ? $voce['anno_di_riferimento'] : null;
                    $voci[$i]['categoria'] = !empty($voce['categoria']) ? $voce['categoria'] : null;
                    $voci[$i]['quantita'] = !empty($voce['quantita']) ? $voce['quantita'] : null;
                    $voci[$i]['importo_singolo'] = !empty($voce['importo_singolo']) ? clean_currency($voce['importo_singolo']) : null;
                    $voci[$i]['iva'] = !empty($voce['iva']) ? $voce['iva'] : null;
                    $voci[$i]['importo'] = !empty($voce['importo']) ? clean_currency($voce['importo']) : null;
                    $voci[$i]['importo_iva'] = !empty($voce['importo_iva']) ? clean_currency($voce['importo_iva']) : null;
                    $voci[$i]['esente_iva'] = !empty($voce['esente_iva']) ? 1 : 0;
                    $voci[$i]['costo_fisso'] = !empty($voce['costo_fisso']) ? 1 : 0;
                    $voci[$i]['accettata'] = 1;
                    $voci[$i]['offerta_id'] = $voce['offerta_id'] ?? 0;
                    $i++;
                }
            }

            $update_ordinativo['voci_economiche'] = !empty($voci) ? $voci : null;

            $this->ordinativo->update($ordinativo, $update_ordinativo);

            // Log
            activity(session('azienda'))
                ->performedOn($ordinativo)
                ->withProperties($update_ordinativo)
                ->log('updated'); 

            return redirect()->route('admin.commerciale.ordinativo.edit', ['ordinativo' => $ordinativo->id, 'tab' => 'vocieconomiche'])
            ->withSuccess('L\'ordinativo è stato aggiornato con successo.');

        }

        // Attività
        if(!empty($request->attivita))
        {
            $rules = Attivita::getOrdinativoRules();
            $this->validate($request, $rules);

            $update_attivita = $request->attivita;

            $update_attivita += [
                'azienda' => session('azienda'),
                'cliente_id' => $ordinativo->cliente_id,
                'created_user_id' => Auth::id(),
                'updated_user_id' => Auth::id()
            ];

            $attivita_id = (!empty($update_attivita['id'])) ? $update_attivita['id'] : 0;

            $attivita = Attivita::updateOrCreate(
                ['id' => $attivita_id],
                $update_attivita
            );

            $ordinativo->attivita()->save($attivita);
            $attivita->users()->sync($update_attivita['assegnatari_id']);

            if($attivita_id == 0)
            {
                $assegnatari_email = $attivita->users()->pluck('email')->toArray();
                $soggetto = 'Nuova attività - ' . $attivita->categoria;
                $messaggio = 'Hai una nuova attività assegnata da <strong>'
                    . $attivita->richiedente->first_name . ' ' . $attivita->richiedente->last_name
                    . '</strong>.<br><br>Per visualizzare i dettagli clicca sul link di seguito:<br><a href="'
                    . route('admin.tasklist.attivita.edit', $attivita->id) . '">'
                    . route('admin.tasklist.attivita.edit', $attivita->id) . '</a>';

                mail_send($assegnatari_email, $soggetto, $messaggio);
            }

            // Log
            activity(session('azienda'))
                ->performedOn($attivita)
                ->withProperties($update_attivita)
                ->log('updated');
        }

        // Rinnovo
        if(!empty($request->rinnovo['titolo']))
        {
            $rules = Rinnovo::getOrdinativoRules();
            $this->validate($request, $rules);

            $update_rinnovo = $request->rinnovo;
            $update_rinnovo += ['created_user_id' => Auth::id() , 'updated_user_id' => Auth::id() ];

            $rinnovo_id = (!empty($ordinativo->rinnovo->id)) ? $ordinativo->rinnovo->id : 0;
            $rinnovo = Rinnovo::updateOrCreate(
                ['id' => $rinnovo_id],
                $update_rinnovo
            );

            $rinnovo_utenti = (!empty($update_rinnovo['utenti'])) ? $update_rinnovo['utenti'] : [];
            $rinnovo->utenti()->sync($rinnovo_utenti);

            foreach ($update_rinnovo['notifiche'] as $key => $notifica)
            {
                if($notifica['notifica'] != '-1' && $notifica['cadenza'] >= 0 && $notifica['tipo'] >= 0)
                {
                    if($key == 'add')
                    {
                        $n = new RinnovoNotifica($notifica);
                        $rinnovo->notifiche()->save($n);
                    }
                    else
                    {
                        $n =  RinnovoNotifica::find($key);
                        $n->update($notifica);
                    }
                }
            }

            // Log
            activity(session('azienda'))
                ->performedOn($rinnovo)
                ->withProperties($update_rinnovo)
                ->log('updated');
        }

        // Giornate
        if(!empty($request->giornate)){
            foreach($request->giornate as $gruppo_id => $giornate)
            {
                $numero_giornate = (int) $giornate['quantita'];

                    $where_giornate = [
                        'ordinativo_id' => $ordinativo->id,
                        'gruppo_id' => $gruppo_id
                    ];

                    $update_giornate['quantita'] = $numero_giornate;
                    $update_giornate['tipo'] = $giornate['tipo'];
                    $update_giornate['attivita'] = $giornate['attivita'];
                    $update_giornate['quantita_gia_effettuate'] = $giornate['quantita_gia_effettuate'];

                    $giornate_effettuate = $ordinativo->interventi_sum_by_gruppo($gruppo_id, $giornate['quantita_gia_effettuate']);
                    $update_giornate['quantita_residue'] = $numero_giornate - $giornate_effettuate;
                    OrdinativoGiornate::updateOrCreate($where_giornate, $update_giornate);

            }
        }

        // Scadenze fatturazioni

        if($request->tab == 'scadenze_ft'){
            if(!empty($request->fatturazioni_scadenze)){
                $ordinativo->fatturazioni_scadenze()->delete();
    
                foreach($request->fatturazioni_scadenze as $scadenza_id => $scadenza)
                {
                    $scadenza['data'] = set_date_ita($scadenza['data']);
                    $scadenza['data_avviso'] = set_date_ita($scadenza['data_avviso']);
                    $scadenza['importo'] = clean_currency($scadenza['importo']);
                    $scadenza['ordinativo_id'] = $ordinativo->id;

                    if(empty($scadenza['descrizione']))
                        $scadenza['descrizione'] = "Campo compilato in automatico.";

                    if(empty($scadenza['data']))
                        $scadenza['data'] = set_date_ita(date('Y-m-d'));

                    if(empty($scadenza['data_avviso']))
                        $scadenza['data_avviso'] = set_date_ita(date('Y-m-d'));

                    if(empty($scadenza['importo']))
                        $scadenza['importo'] = clean_currency(0); 
    
                    $validator = Validator::make($scadenza, FatturazioneScadenze::getRules());
    
                    if(!$validator->fails()){
                        $ordinativo->fatturazioni_scadenze()->create($scadenza);
                    } else {
                        return redirect()->route('admin.commerciale.ordinativo.edit', ['ordinativo' => $ordinativo->id, 'tab' => 'scadenze_ft'])
                            ->withError('Rispettare i campi contrassegnati come obbligatori.')->withErrors($validator)->withInput();                        
                    }
                }

                return redirect()->route('admin.commerciale.ordinativo.edit', ['ordinativo' => $ordinativo->id, 'tab' => 'scadenze_ft'])
                    ->withSuccess('L\'ordinativo è stato aggiornato con successo.');
            }

            return redirect()->route('admin.commerciale.ordinativo.edit', ['ordinativo' => $ordinativo->id, 'tab' => 'scadenze_ft']);

        }

        return redirect()->route('admin.commerciale.ordinativo.edit', $ordinativo->id)
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('Ordinativo')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Ordinativo $ordinativo
     * @return Response
     */
    public function destroy(Ordinativo $ordinativo)
    {
        if($ordinativo->azienda != session('azienda'))
            return redirect()->route('admin.commerciale.ordinativo.index')
                    ->withWarning('AVVISO: non puoi accedere a questo ordinativo con l\'azienda ' . session('azienda'));

        if((int) Attivita::where('ordinativo_id', $ordinativo->id)->avg('percentuale_completamento') != 100)
            return redirect()->route('admin.commerciale.ordinativo.index')
                    ->withWarning('Impossibile eliminare l\'ordinativo: attività in fase di completamento.');


        $this->ordinativo->destroy($ordinativo);

        // Log
        activity(session('azienda'))
            ->performedOn($ordinativo)
            ->withProperties(json_encode($ordinativo))
            ->log('destroyed');

        return redirect()->route('admin.commerciale.ordinativo.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('Ordinativo')]));
    }

    // Create attività
    public function createAttivita($ordinativo_id)
    {

        $ordinativo = Ordinativo::findOrFail($ordinativo_id);

        $users = User::select(DB::raw("CONCAT(first_name,' ',last_name) AS name"), 'id')
                    ->pluck('name', 'id')
                    ->toArray();

        $gruppi = Gruppo::all();

        $clienti = Clienti::pluck('ragione_sociale', 'id')
                            ->toArray();

        return view('commerciale::admin.ordinativi.partials.attivita.fields' , compact('ordinativo','users', 'gruppi', 'clienti'));
    }

    // Edit attività
    public function editAttivita($attivita_id,$ordinativo_id)
    {
        $attivita = Attivita::findOrFail($attivita_id);
        $ordinativo = Ordinativo::findOrFail($ordinativo_id);

        $users = User::select(DB::raw("CONCAT(first_name,' ',last_name) AS name"), 'id')
                    ->pluck('name', 'id')
                    ->toArray();

        $gruppi = Gruppo::all();

        $clienti = Clienti::pluck('ragione_sociale', 'id')
                            ->toArray();

        return view('commerciale::admin.ordinativi.partials.attivita.fields', compact('ordinativo','attivita', 'users', 'gruppi', 'clienti'));
    }

    // Export excel SAL
    public function exportSALExcel(Request $request)
    {
      $attivita = Attivita::where('ordinativo_id', $request->ordinativo_id)
                          ->orderBy('percentuale_completamento', 'desc')
                          ->get();

      if(!empty($attivita) && $attivita->count() > 0){
        ob_clean();
        return Excel::download(new AttivitaExport($attivita), 'SAL.xlsx');          
      } else {
        return redirect()->route('admin.commerciale.ordinativo.edit', ['ordinativo' => $request->ordinativo_id, 'tab' => 'attivita'])
        ->withError('L\'ordinativo non ha alcuna attività.');                           
      }
    }

    // Export excel
    public function exportExcel(Request $request)
    {
        if(empty($request->all()))
        {
          $res['order']['by'] = 'codice_offerta';
          $res['order']['sort'] = 'desc';
          $request->merge($res);
        }

        $ordinativi = Ordinativo::select('commerciale__ordinativi.*')->filter($request->all())
                            ->where('commerciale__ordinativi.azienda', session('azienda'))
                            ->get();

      ob_clean();
      return Excel::download(new OrdinativiExport($ordinativi), 'ordinativi.xlsx');
    }







    public function reportvisteStore(Request $request)
    {
        return $request->all();
        $userid = Auth::id();
       
        DB::table('commerciale_censimenticlienti_report_viste')->insert([
            'data' => $request->data,
            'descrizione' => $request->descrizione,
            'cliente_id' => $request->cliente_id,
            'created_user_id' => $userid
        ]);

        return back();

    }
}
