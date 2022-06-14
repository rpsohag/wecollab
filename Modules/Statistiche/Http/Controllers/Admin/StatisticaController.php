<?php

namespace Modules\Statistiche\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Statistiche\Entities\Statistica;
use Modules\Statistiche\Entities\ViewRichiesteIntervento;
use Modules\Statistiche\Entities\ViewRichiesteInterventoAzioni;
use Modules\Statistiche\Repositories\StatisticaRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Illuminate\Support\Facades\DB;
use Modules\Profile\Entities\Gruppo;
use Modules\Commerciale\Entities\Fatturazione;
use Modules\Commerciale\Entities\Ordinativo;
use Modules\Amministrazione\Entities\Clienti;
use Illuminate\Support\Facades\Auth;
use Modules\Profile\Entities\Area;
use Modules\Assistenza\Entities\RichiesteIntervento;
use Modules\Assistenza\Entities\RichiesteInterventoAzione;
use Modules\Tasklist\Entities\AttivitaVoce;
use Modules\User\Entities\Sentinel\User;
use Modules\Tasklist\Entities\Timesheet;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Spatie\Activitylog\Models\Activity;
use Cache;

use Modules\Assistenza\Http\Services\StatisticheService;

class StatisticaController extends AdminBaseController
{

    private $statistiche;

    public function __construct(StatisticheService $statistiche)
    {
        parent::__construct();

        $this->statistiche = $statistiche;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $statistica = Statistica::filter($request->all());

        $request->flash();

        return view('statistiche::admin.statistica.index', compact('statistica'));
    }

    // Yo Edd

    public function quadraturaTimesheets(Request $request)
    {

      $mese = $request->filled('mese') ? $request->mese : date("m", strtotime("-1 month"));
      $anno = $request->filled('anno') ? $request->anno : ($mese == 12 ? date("Y", strtotime("-1 year")) : date("Y"));

      $utenti_list = [0 => ''] + User::when(auth_user()->inRole('admin'), function ($query) {
        $query->select(DB::raw("CONCAT(first_name,' ',last_name) AS name"), 'id');
      })->when(!auth_user()->inRole('admin'), function ($query) {
        $query->whereIn('id', Auth::user()->supervisionati()->pluck('id')->toArray())
        ->select(DB::raw("CONCAT(first_name,' ',last_name) AS name"), 'id');
      })->pluck('name', 'id')->toArray();

      $mesi = array('01' => 'Gennaio', '02' => 'Febbraio', '03' => 'Marzo', '04' => 'Aprile', '05' => 'Maggio', '06' => 'Giugno', '07' => 'Luglio', '08' => 'Agosto', '09' => 'Settembre', '10' => 'Ottobre', '11' => 'Novembre', '12' => 'Dicembre');
      $anno_di_partenza = (int) date('Y');
      $anni = array(date('Y') => date('Y'));
      while($anno_di_partenza > 2020){
        $anno_di_partenza--;
        $anni[$anno_di_partenza] = (string) $anno_di_partenza;
      }

      $firstDay = Carbon::parse($anno.'-'.$mese.'-01')->startOfMonth(); 
      $lastDay = Carbon::parse($anno.'-'.$mese.'-01')->endOfMonth(); 

      $working_days = count(get_workdays($firstDay, $lastDay));

      $utenti = User::when(auth_user()->inRole('admin'), function ($query) use ($anno, $mese, $request) {
                      $query->with(['timesheets' => function($q) use ($anno, $mese, $request){
                        $q->whereYear('dataora_inizio', (string)$anno)
                          ->whereMonth('dataora_inizio', (string)$mese);
                        }]);
                      })
                      ->where('timesheets_report', 1)
                      ->when($request->filled('utente') && !empty($request->utente), function ($q) use ($request) {
                        return $q->where('id', $request->utente);
                      })
                      ->when(!auth_user()->inRole('admin'), function ($query) use ($anno, $mese, $request) {
                        $query->whereIn('id', Auth::user()->supervisionati()->pluck('id')->toArray())
                        ->with(['timesheets' => function($q) use ($anno, $mese, $request){
                          $q->whereYear('dataora_inizio', (string)$anno)
                            ->whereMonth('dataora_inizio', (string)$mese);
                          }]);
                        })
                        ->where('timesheets_report', 1)
                        ->when($request->filled('utente') && !empty($request->utente), function ($q) use ($request) {
                          return $q->where('id', $request->utente);
                        })
                    ->get();

      return view('statistiche::admin.quadratura_timesheets.index', compact('anno', 'mese', 'utenti', 'anni', 'mesi', 'utenti_list', 'working_days', 'firstDay', 'lastDay'));
      
    }

    public function reports(Request $request)
    {

      if(!$request->filled('reports')){
        $request->merge(['reports' => 'assistenza']);
      }

      if(!$request->filled('data_inizio') && !$request->filled('data_fine')) {
        $request->merge(['data_inizio' => date('d-m-Y', strtotime('-7 days'))]);
        $request->merge(['data_fine' => date('d-m-Y')]);
      }

      if($request->reports == 'timesheets'){
        $utenti = User::with('timesheets')->get();        
      } else {
        $utenti = User::get();
      }
      $gruppi = Gruppo::all();
      $clienti = [0 => ''] + Clienti::pluck('ragione_sociale', 'id')->toArray();
      $aree = [0 => ''] + Area::pluck('titolo', 'id')->toArray();

      $request->flash();

      return view('statistiche::admin.reports.index', compact('utenti', 'clienti', 'aree'));
    }

    public function reportsModal(Request $request)
    {
      $request->flash();

      if($request->ajax()){

        if($request->type == 'logs'){
          $utente = User::find($request->utente_id);
          $logs = $utente->logs($request->data)->get();
          $weekly_logs = $utente->logs_weekly_reports_collection($logs);

          $wl = ['empty' => 1];
          foreach($weekly_logs as $key => $w_log)
          {
            $wl['empty'] = 0;

            $wl['values'][$key]['data'] = ucfirst(utf8_encode(strftime('%A %e %B', $w_log->first()->created_at->timestamp)));
            $wl['values'][$key]['count'] = $w_log->count();
            $wl['values'][$key]['first']['data'] = get_date_hour_ita($w_log->first()->created_at);
            $wl['values'][$key]['first']['description'] = $w_log->first()->description;
            $wl['values'][$key]['last']['data'] = get_date_hour_ita($w_log->last()->created_at);
            $wl['values'][$key]['last']['description'] = $w_log->last()->description;
          }
          
          return response()->json(json_encode($wl));
        }

        if($request->type == 'timesheets'){
          
          $utente = User::find($request->utente_id); 
          $tempo_timesheets = $utente->tempo_timesheets($request->utente_id, $request->cliente_id, $request->area_id, $request->data_inizio, $request->data_fine)->toArray();
          
          if(!empty($tempo_timesheets)){
            $last_day_printed_carbon = new \Carbon\Carbon(end($tempo_timesheets)['date']);
            $last_day_printed = $last_day_printed_carbon->subDay(1)->format('Y-m-d');   
            $wl['ultima_data'] = $last_day_printed;          
          }

          $wl['empty'] = 1;
          $wl['utente'] = $utente->full_name;
          $wl['data_inizio'] = ucfirst(utf8_encode(strftime('%A %e %B', strtotime($request->data_inizio))));
          $wl['data_fine'] = ucfirst(utf8_encode(strftime('%A %e %B', strtotime($request->data_fine))));
          if(!empty($tempo_timesheets)){
            foreach($tempo_timesheets as $keyy => $w_log)
            { 
              $wl['empty'] = 0;
              $data = date("Y-m-d", strtotime($w_log['date'])); 
              $timesheets = $utente->timesheets($data, $request->cliente_id, $request->area_id)->get();

              $wl['values'][$data]['data'] = ucfirst(utf8_encode(strftime('%A %e %B', strtotime($data))));
              $temp_durata = 0;
              foreach($timesheets as $timesheet){
                $gg['nota'] = !empty($timesheet->nota) ? $timesheet->nota : '';
/*                 if(strpos($gg['nota'], '( Azione ) :')){
                  $gg['tipologia'] = 'Automatico';
                } else {
                  $gg['tipologia'] = 'Manuale';
                } */
                if(is_null($timesheet->ticket_azione_id)) 
                {
                  $gg['tipologia'] = 'Manuale';
                } else {
                  $gg['tipologia'] = 'Automatico';                  
                }
                $gg['attivita'] = (!empty($timesheet->attivita) ? $timesheet->attivita->oggetto : '');
                $gg['durata'] = $timesheet->durata();
                $temp_durata = $temp_durata + $timesheet->durata_time();
                $gg['area'] = $timesheet->area->titolo;
                $gg['gruppo'] = $timesheet->gruppo->nome;
                $wl['values'][$data]['timesheets'][] = $gg;
              }
              $wl['values'][$data]['durata'] = get_seconds_to_hi($temp_durata);
              if($temp_durata > 32400 || $temp_durata < 25200){
                $wl['values'][$data]['alert'] = true;
              } else {
                $wl['values'][$data]['alert'] = false;
              }
            }
          }
          
          return response()->json(json_encode($wl));
        }

        if($request->type == 'rapporti'){
          $utente = User::find($request->utente_id);
          $rapporti = $utente->rapporti($request->data, null, $request->cliente_id, $request->area_id)->get();
          $reports = $utente->rapporti_weekly_reports($request->data, $utente->id, $request->cliente_id, $request->area_id);

          $wl = ['empty' => 1];
          $gruppi = Gruppo::all();

           foreach($gruppi as $gruppo){
            if($utente->rapporti($request->data, $gruppo->id, $request->cliente_id, $request->area_id)->get()->count() > 0){
              $wl['gruppi'][] = $gruppo->nome;
            }
          } 

          foreach($reports as $data => $totale)
          {
            $wl['empty'] = 0;
            $wl['values'][$data]['data'] = ucfirst(utf8_encode(strftime('%A %e %B', strtotime($data))));
            $wl['values'][$data]['count'] = $totale;
            foreach($gruppi as $gruppo){
              $utente_rapporti = $utente->rapporti($data, $gruppo->id, $request->cliente_id, $request->area_id)->get();
              if($utente_rapporti->count() > 0){
                foreach($utente_rapporti as $rapporto){
                    $ordinativo_giornate = $rapporto->ordinativo->giornate;
                    if(!empty($ordinativo_giornate)){
                      foreach($ordinativo_giornate as $giornata){
                        if(empty($giornate_residue)){ $giornate_residue = 0; }
                        if(empty($giornate_effettuate)){ $giornate_effettuate = 0; }
                        $giornate_residue = $giornate_residue + $giornata->quantita_residue;
                        $giornate_effettuate = $giornate_effettuate + $giornata->quantita_gia_effettuate;   
                      }
                      $wl['values'][$data]['gruppi'][$gruppo->nome]['giornate_residue'] =$giornate_residue;
                      $wl['values'][$data]['gruppi'][$gruppo->nome]['giornate_effettuate'] = $giornate_effettuate; 
                      $wl['values'][$data]['gruppi'][$gruppo->nome]['totali'] = $utente->rapporti($data, $gruppo->id, $request->cliente_id, $request->area_id)->get()->count(); 
                    }
                }
              }
            }
          }
          return response()->json(json_encode($wl));
        }   
      }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */

    public function fatturazione(Request $request)
    {
      $fatturazione = Fatturazione::filter($request->all())
                                  ->where('azienda', session('azienda'));

      $fatturazione_foreach = $fatturazione->get();

      $fatturazione_riepilogo_scaduto = 0;
      $fatturazione_riepilogo_scaduto_anticipato = 0;
                                
      foreach($fatturazione_foreach as $fatturazione_fe){
        if($fatturazione_fe->scaduta() && $fatturazione_fe->pagata !== 1){
          $fatturazione_riepilogo_scaduto += clean_currency($fatturazione_fe->totale_netto);
        }
        if($fatturazione_fe->scaduta() && $fatturazione_fe->anticipata == 1 && $fatturazione_fe->pagata !== 1){
          $fatturazione_riepilogo_scaduto_anticipato += clean_currency($fatturazione_fe->totale_netto);
        }
      }

      $clienti = [0 => ''] + Clienti::pluck('ragione_sociale', 'id')->toArray();

      $request->flash();

      return view('statistiche::admin.statistica.fatturazione', compact('fatturazione', 'clienti', 'fatturazione_riepilogo_scaduto', 'fatturazione_riepilogo_scaduto_anticipato'));
    }

    // Richieste intervento
    public function richiesteIntervento(Request $request) 
    {
      if(!$request->filled('stats'))
        $request->merge(['stats' => 'dipendenti']);

      if(!$request->filled('data_inizio') && !$request->filled('data_fine'))
          $request->merge(['data_inizio' => date('Y-m-d', strtotime('-1 month'))]);
          $request->merge(['data_fine' => date('Y-m-d')]);

      if($request->stats == 'dipendenti')
        $dettaglio = $this->statistiche->statsPerDipendente($request);
      
      if($request->stats == 'aree')
        $dettaglio = $this->statistiche->statsPerArea($request);

      $clienti = [''] + Clienti::all()->pluck('ragione_sociale', 'id')->toArray();
      $ordinativi = [''] + Ordinativo::all()->pluck('oggetto', 'id')->toArray();

      return view('statistiche::admin.richieste_intervento.index', compact('dettaglio', 'clienti', 'ordinativi'));
    }
}
