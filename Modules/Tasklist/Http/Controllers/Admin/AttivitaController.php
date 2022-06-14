<?php

namespace Modules\Tasklist\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Profile\Entities\Area;
use Modules\Profile\Entities\Procedura;
use Modules\Tasklist\Entities\Attivita;
use Modules\Tasklist\Entities\Timesheet;
use Modules\Tasklist\Http\Requests\CreateAttivitaRequest;
use Modules\Tasklist\Http\Requests\CreateTimesheetRequest;
use Modules\Tasklist\Http\Requests\UpdateAttivitaRequest;
use Modules\Tasklist\Repositories\AttivitaRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Profile\Entities\Profile;
use Modules\Profile\Http\Requests\CreateProfileRequest;
use Modules\Profile\Http\Requests\UpdateProfileRequest;
use Modules\Profile\Repositories\ProfileRepository;
use Cartalyst\Sentinel\Laravel\Facades\Activation;

use Modules\User\Contracts\Authentication;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Modules\User\Entities\Sentinel\User;
use Modules\Wecore\Entities\Meta;
use Modules\Profile\Entities\Gruppo;
use Modules\Amministrazione\Entities\Clienti;
use Modules\Commerciale\Entities\Ordinativo;
use Modules\Tasklist\Entities\AttivitaVoce;
use Spatie\Activitylog\Models\Activity;
use Modules\Commerciale\Entities\SegnalazioneOpportunita;
use Adldap\Laravel\Facades\Adldap;
use Modules\Commerciale\Entities\Offerta;

use Maatwebsite\Excel\Facades\Excel;
use Modules\Export\Entities\AttivitaExport;

use Carbon\Carbon;

class AttivitaController extends AdminBaseController
{
    /**
     * @var AttivitaRepository
     */
    private $attivita;

    /**
     * @var Authentication
     */
    protected $auth;

    public function __construct(AttivitaRepository $attivita, Authentication $auth)
    {
        parent::__construct();

        $this->attivita = $attivita;
        $this->auth = $auth;
    }

    // NOME - INIZIO - FINE - PROGRESSO - REQUISITI

    public function gantt(Request $request)
    {
        if(empty($request->all()))
        {
          $res['order']['by'] = 'percentuale_completamento';
          $res['order']['sort'] = 'desc';
          $res['stato'] = [0];
          $res['lavorabili'] = 1;
          $request->merge($res);
        }

        if(Auth::user()->hasAccess('tasklist.attivita.all') && !empty($request->all))
        {
          $attivita = new Attivita;
        }
        else
        {
          $attivita = Attivita::select('id','data_inizio', 'data_fine', 'requisiti', 'oggetto', 'percentuale_completamento')->where(function($query) {
                                  $query->where('richiedente_id', Auth::id())
                                          ->orWhereHas('users', function($assegnatari) {
                                              $assegnatari->where('users.id', Auth::id());
                                          });
                              })->orWhereJsonContains('supervisori_id->'.Auth::id().'->user_id', (string)Auth::id());
                             
        } 

        if(empty($request->stato))
            $attivita = $attivita->where('stato', [4]);

        $attivita = $attivita->filter($request->all())->orderByDesc('pinned_by')->select('id','data_inizio', 'data_fine', 'requisiti', 'oggetto', 'percentuale_completamento')->get();

        if($request->lavorabili != -1){

          if($request->lavorabili == 1){
            $attivita = $attivita->filter(function($att)
            {
                return $att->hasRequisiti();
            });
          } else {
            $attivita = $attivita->filter(function($att)
            {
                return !$att->hasRequisiti();
            });        
          }
        } else {
          $attivita = $attivita;
        }

        $attivita_array = array();

        foreach($attivita as $att)
        {
          $attivita_array[$att->id]['id'] = "'".$att->id."'";
          $attivita_array[$att->id]['name'] = '"'.$att->oggetto.'"';
          $attivita_array[$att->id]['assegnatari'] = implode(", ", $att->users()->get()->pluck('full_name')->toArray());
          $attivita_array[$att->id]['start'] = $att->data_inizio;
          $attivita_array[$att->id]['end'] = (!empty($att->data_fine) ? $att->data_fine : $att->data_inizio);
          $attivita_array[$att->id]['completed'] = ($att->percentuale_completamento == 100 ? '1' : '0.'.$att->percentuale_completamento);
          
          $attivita_array[$att->id]['start'] = str_replace('-', ' , ', set_date_ita($attivita_array[$att->id]['start']));
          $attivita_array[$att->id]['end'] = str_replace('-', ' , ', set_date_ita($attivita_array[$att->id]['end']));

          if($attivita_array[$att->id]['completed'] == 0.0)
            $attivita_array[$att->id]['completed'] = 0;
        }

        $attivita = collect($attivita_array);

        return view('tasklist::admin.attivita.gantt', compact('attivita'));
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
          $res['order']['by'] = 'percentuale_completamento';
          $res['order']['sort'] = 'desc';
          $res['stato'] = [0];
          $res['lavorabili'] = 1;
          $request->merge($res);
        }

        $procedure = Procedura::pluck('titolo', 'id')->toArray();
        $aree = Area::pluck('titolo', 'id')->toArray();
        $gruppi = Gruppo::pluck('nome', 'id')->toArray();
        $ordinativi = Ordinativo::pluck('oggetto', 'id')->toArray();

        $utenti = User::get()->pluck('full_name', 'id')->toArray();

        $clienti = Clienti::pluck('ragione_sociale', 'id')
                            ->toArray();

        if(Auth::user()->hasAccess('tasklist.attivita.all') && !empty($request->all))
        {
          $attivita = new Attivita;
        }
        else
        {
          $attivita = Attivita::with(['users', 'notes'])->where(function($query) {
                                  $query->where('richiedente_id', Auth::id())
                                          ->orWhereHas('users', function($assegnatari) {
                                              $assegnatari->where('users.id', Auth::id());
                                          });
                              })->orWhereJsonContains('supervisori_id->'.Auth::id().'->user_id', (string)Auth::id());
                             
        } 

        if(empty($request->stato))
            $attivita = $attivita->where('stato', [4]);

        $attivita = $attivita->filter($request->all())->orderByDesc('pinned_by')->get();

        if($request->lavorabili != -1){
          if($request->lavorabili == 1){
            $attivita = $attivita->filter(function($att)
            {
                return $att->hasRequisiti();
            })->paginate(20)->appends(request()->query());
          } else {
            $attivita = $attivita->filter(function($att)
            {
                return !$att->hasRequisiti();
            })->paginate(20)->appends(request()->query());          
          }
        } else {
          $attivita = $attivita->paginate(20)->appends(request()->query());
        }

        //Filtri
        $stati_filter = [-1 => 'Tutte'] + config('tasklist.attivita.stati');
        $stati_icone = config('tasklist.attivita.stati_icone');
        $priorita = config('tasklist.attivita.priorita_testi');
        $priorita_filter = [-1 => ''] + config('tasklist.attivita.priorita');
        $priorita_icone = config('tasklist.attivita.priorita_icone');
        $tipologie = [-1 => ''] + config('tasklist.timesheets.tipologie');

        $request->flash();

        return view('tasklist::admin.attivita.index', compact('attivita', 'utenti', 'clienti', 'aree', 'procedure', 'ordinativi', 'gruppi', 'stati_filter', 'tipologie', 'stati_icone', 'priorita', 'priorita_filter', 'priorita_icone'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
      //Crea nuova attività (Serve per il collegamento le voci)
      $attivita = new Attivita();

      //Utenti
      $utenti = User::select(DB::raw("CONCAT(first_name,' ',last_name) AS name"), 'id')
                  ->pluck('name', 'id')
                  ->toArray();

      //Procedura, Area, Gruppo, Cliente
      $procedure = Procedura::pluck('titolo', 'id')->toArray();
      $aree = Area::pluck('titolo', 'id')->toArray();
      $gruppi_users = Gruppo::orderBy('nome')->get();
      $gruppi = $gruppi_users->pluck('nome', 'id')->toArray();
      $clienti = Clienti::pluck('ragione_sociale', 'id')->toArray();

      //Requisiti
      $requisiti = Attivita::select(DB::raw("CONCAT(oggetto,' ( ',azienda,' ) - [',percentuale_completamento,'%]' ) AS oggetto"),'id')
                          ->where('id', '<>', $attivita->id)
                          ->when($request->filled('cliente_id'), function ($query) use ($request) {
                            $query->where('cliente_id', $request->cliente_id);
                          })
                          ->pluck('oggetto', 'id', 'cliente_id')
                          ->toArray();

      //Ordinativi
      $ordinativi = array() + [''];
      if($request->filled('cliente_id')) {
        $attivita->cliente_id = $request->cliente_id;

        $ordinativi_obj = Ordinativo::active()->where('cliente_id',$request->cliente_id)->orderBy('azienda', 'desc')->get();

        foreach ($ordinativi_obj as $key => $ordinativo) {
          $ordinativi[$ordinativo->azienda][$ordinativo->id] = $ordinativo->oggetto;
        }
      }

      if($request->filled('ordinativo_id')) 
        $attivita->ordinativo_id = $request->ordinativo_id;

      //Requisito: Attività Padre
      $attivita_padre = null;
      if($request->filled('attivita_padre')) {
        $attivita_padre = Attivita::where('id', $request->attivita_padre)->first();
        $ordinativi = Ordinativo::find(optional($attivita_padre)->ordinativo_id)->pluck('oggetto', 'id')->toArray();
        $attivita->data_inizio = optional($attivita_padre)->data_fine;
        $attivita->cliente_id = optional($attivita_padre)->cliente_id;
        $attivita->ordinativo_id = optional($attivita_padre)->ordinativo_id;
      }

      $priorita = config('tasklist.attivita.priorita');
      $durata_tipo =  config('tasklist.attivita.durata_tipo');
      $stati = config('tasklist.attivita.stati');

      $request->flash();

      return view('tasklist::admin.attivita.create',compact('attivita', 'utenti', 'procedure', 'aree', 'gruppi', 'gruppi_users', 'clienti', 'requisiti', 'ordinativi', 'attivita_padre', 'priorita', 'durata_tipo', 'stati'));
    }

    public function multiCreate(Request $request) 
    {
      if($request->filled('cliente_id') && $request->filled('ordinativo_id')) {
        //Utenti
        $utenti = User::select(DB::raw("CONCAT(first_name,' ',last_name) AS name"), 'id')
                    ->pluck('name', 'id')
                    ->toArray();

        //Procedura, Area, Gruppo, Cliente
        $procedure = [''] + Procedura::pluck('titolo', 'id')->toArray();
        $aree = [''] + Area::pluck('titolo', 'id')->toArray();
        $gruppi_users = Gruppo::orderBy('nome')->get();
        $gruppi = [''] + $gruppi_users->pluck('nome', 'id')->toArray();
        $clienti = Clienti::pluck('ragione_sociale', 'id')->toArray();

        $ordinativo_id = $request->ordinativo_id;
        $cliente_id = $request->cliente_id;

        //Requisiti
        $requisiti = Attivita::select(DB::raw("CONCAT(oggetto,' ( ',azienda,' ) - [',percentuale_completamento,'%]' ) AS oggetto"),'id')
                            ->when($request->filled('cliente_id'), function ($query) use ($request) {
                              $query->where('cliente_id', $request->cliente_id);
                            })
                            ->pluck('oggetto', 'id', 'cliente_id')
                            ->toArray();

        //Ordinativi
        $ordinativi_ids = Offerta::where('cliente_id', '=', $request->cliente_id)->pluck('ordinativo_id')->toArray();
        $ordinativi_obj = Ordinativo::active()->whereIn('id', $ordinativi_ids)->orderBy('azienda', 'desc')->get();
          
        foreach ($ordinativi_obj as $key => $ordinativo) {
            $ordinativi[$ordinativo->azienda][$ordinativo->id] = $ordinativo->oggetto;
        }    

        $priorita = config('tasklist.attivita.priorita');
        $durata_tipo =  config('tasklist.attivita.durata_tipo');
        $stati = config('tasklist.attivita.stati');

        $ordinativo = Ordinativo::find($ordinativo_id);
        $cliente = Clienti::find($cliente_id);

        $request->flash();

        return view('tasklist::admin.attivita.multicreate',compact('ordinativo', 'cliente', 'request', 'utenti', 'procedure', 'aree', 'ordinativo_id', 'cliente_id', 'gruppi', 'gruppi_users', 'clienti', 'requisiti', 'ordinativi', 'priorita', 'durata_tipo', 'stati'));
      } else {
        return redirect()->back()->withError('La creazione multipla è abilitata solo dall\'ordinativo.');
      }
    }

    public function requisitiAttivita(Request $request)
    {

        $attivita = Attivita::find($request->id);
        $requisiti = $attivita->requisiti();

        if($requisiti){

          return response()->json(['requisiti' => $requisiti]);

        }

    }

    /**
     * Show the resource.
     *
     * @return Response
     */
    public function read(Attivita $attivita, Request $request)
    {
      //Partecipanti

      $partecipanti_all = $attivita->partecipanti();
      $partecipanti = array();

      foreach($partecipanti_all as $partecipante){
        $partecipanti[$partecipante->id] = [];
        $partecipanti[$partecipante->id]['nome'] = $partecipante->full_name;
        if($attivita->richiedente->id == $partecipante->id)
          $partecipanti[$partecipante->id]['ruolo'] = 'Richiedente ';
        if($attivita->supervisori() && $attivita->supervisori()->contains('id', $partecipante->id))
          empty($partecipanti[$partecipante->id]['ruolo']) ? ($partecipanti[$partecipante->id]['ruolo'] = 'Supervisore') : ($partecipanti[$partecipante->id]['ruolo'] .= ' & Supervisore');
        if($attivita->users->contains('id', $partecipante->id))
          empty($partecipanti[$partecipante->id]['ruolo']) ? ($partecipanti[$partecipante->id]['ruolo'] = 'Assegnatario') : ($partecipanti[$partecipante->id]['ruolo'] .= ' & Assegnatario');
        $partecipanti[$partecipante->id]['email'] = $partecipante->email;
      }

      $data_fine = !empty($attivita->data_fine) ? $attivita->data_fine : date('Y-m-d H:i:s');

      $timesheets = Timesheet::withoutGlobalScopes()
                            //->whereDate('dataora_inizio', '>=', $attivita->data_inizio)
                            ->where('attivita_id', $attivita->id)
                            ->select(['created_user_id', DB::raw('SUM(TIMESTAMPDIFF(SECOND, dataora_inizio, dataora_fine)) as diff'), DB::raw('count(*) AS count') ])
                            ->groupBy('created_user_id')
                            ->get();

      //Lavoratori

      $lavoratori = array();

      foreach($attivita->users as $lavoratore){
        $lavoratori[$lavoratore->id] = [];
        $lavoratori[$lavoratore->id]['nome'] = $lavoratore->full_name;
        $timesheets_collegati = optional($timesheets->where('created_user_id', $lavoratore->id)->first())->count;
        $secondi_lavorati = 0;
        $secondi_lavorati = optional($timesheets->where('created_user_id', $lavoratore->id)->first())->diff;
        $lavoratori[$lavoratore->id]['timesheets'] = $timesheets_collegati;
        $lavoratori[$lavoratore->id]['tempo_lavorato'] = get_seconds_to_hours($secondi_lavorati);
      }

      $tipologie = [-1 => ''] + config('tasklist.timesheets.tipologie');
      $priorita = config('tasklist.attivita.priorita');
      $durata_tipo =  config('tasklist.attivita.durata_tipo');
      $stati = config('tasklist.attivita.stati');

      return view('tasklist::admin.attivita.read',compact('attivita', 'priorita', 'durata_tipo', 'stati', 'partecipanti', 'lavoratori', 'tipologie'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateAttivitaRequest $request
     * @return Response
     */
    public function store(CreateAttivitaRequest $request)
    {
        $rules = Attivita::getRules();

        $this->validate($request, $rules);

        $rules_voci = AttivitaVoce::getRules();

        $create = $request->all();
        $create['created_user_id'] = Auth::id();
        $create['updated_user_id'] = Auth::id();
        $create['azienda'] = session('azienda');

        if(empty($request->supervisori_id))
          $create['supervisori_id'] = [];

        $attivita = Attivita::create($create);

        //Dropzone
        dropzone_files_save('tasklist', $attivita->id, 'Attivita', 'Tasklist', $request);

        $attivita->users()->sync($create['assegnatari_id']);

        // Segnalazioni opportunità
        if(!empty($request->segnalazioneopportunita_id))
        {
          $segnalazione = SegnalazioneOpportunita::findOrFail($request->segnalazioneopportunita_id);

          $attivita->attivitable()->associate($segnalazione)->save();
        }

        // Log
        activity(session('azienda'))
            ->performedOn($attivita)
            ->withProperties($create)
            ->log('created');

        // Email nuova attività agli assegnatari
        $oggetto = 'Nuova attività - ' . $attivita->oggetto . ' (' . $attivita->cliente->ragione_sociale . ')';
        $messaggio = 'Hai una nuova attività assegnata da <strong>' . $attivita->richiedente->full_name . '</strong>.<br><br>Per visualizzare i dettagli clicca sul link di seguito:<br><a href="' . route('admin.tasklist.attivita.read', $attivita->id) . '">' . route('admin.tasklist.attivita.read', $attivita->id) . '</a>';
        mail_send($attivita->users()->pluck('email')->toArray(), $oggetto, $messaggio);

        return redirect()->route('admin.tasklist.attivita.edit', $attivita->id)
            ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('Attività')]));
    }

    public function multiStore(Request $request)
    {
      $rules = Attivita::getRules();
      $res = $request->all();
      if(!empty($res['attivita']) && count($res['attivita']) > 0){
        $err_count = 0;
        $pos_count = 0;
        foreach($res['attivita'] as $key => $attivita){
          $store = $attivita;
          $store['fatturazione'] = !empty($attivita['fatturazione']) ? 1 : 0;
          $store['cliente_id'] = $request->cliente_id;
          $store['ordinativo_id'] = $request->ordinativo_id;
          $store['created_user_id'] = Auth::id();
          $store['updated_user_id'] = Auth::id();
          $store['azienda'] = session('azienda');
          $store['stato'] = 0;
          $store['priorita'] = 0;
          $store['durata_tipo'] = 0;
          $store['durata_valore'] = 0;
          $store['opzioni'] = [
            'prese_visioni' => !empty($attivita['prese_visioni']) ? 1 : 0,
            'multi_presa_in_carico' => !empty($attivita['multi_presa_in_carico']) ? 1 : 0,
          ];
          unset($store['prese_visioni']);
          unset($store['multi_presa_in_carico']);
          $validator = Validator::make($store, $rules);
          if ($validator->fails()) {
            $err_count++;
          } else {
            $pos_count++;
  
            $attivita = Attivita::create($store);
  
            $attivita->users()->sync($store['assegnatari_id']);
  
            // Email nuova attività agli assegnatari
            $oggetto = 'Nuova attività - ' . $attivita->oggetto . ' (' . $attivita->cliente->ragione_sociale . ')';
            $messaggio = 'Hai una nuova attività assegnata da <strong>' . $attivita->richiedente->full_name . '</strong>.<br><br>Per visualizzare i dettagli clicca sul link di seguito:<br><a href="' . route('admin.tasklist.attivita.read', $attivita->id) . '">' . route('admin.tasklist.attivita.read', $attivita->id) . '</a>';
            mail_send($attivita->users()->pluck('email')->toArray(), $oggetto, $messaggio);
          }
        }
      }
      if($pos_count > 0) {
        return redirect()->back()->withSuccess('Hai creato '.$pos_count. ' attività con successo. (Errori: '.$err_count.')');
      } else {
        return redirect()->back()->withError('Nessuna attivita creata con successo. (Errori: '.$err_count.')');
      }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  Attivita $attivita
     * @return Response
     */
    public function edit(Attivita $attivita, Request $request)
    {

        //Controllare se l'utente che prova ad accedere è un partecipante
        if(!$attivita->partecipanti()->contains('id', Auth::id()) && !auth_user()->hasAccess('tasklist.attivita.admin')){
          return redirect()->route('admin.tasklist.attivita.read', ['attivita' => $attivita->id])
          ->withError('Non puoi lavorare questa attività, solo la lettura è concessa.');          
        }

        //Se l'attività è completata non permettere l'edit se non si è supervisori.
        if($attivita->percentuale_completamento == 100 && $attivita->supervisori() && !$attivita->supervisori()->contains('id', Auth::id())){
          return redirect()->route('admin.tasklist.attivita.read', $attivita->id)
            ->withError('Non è possibile effettuare modifiche su un\' attività completata.');    
        }

        //Utenti
        $utenti = User::withTrashed()->get()->pluck('full_name', 'id')->toArray();

        //Procedura, Area, Gruppo, Cliente
        $procedure = Procedura::pluck('titolo', 'id')->toArray();
        $aree = Area::pluck('titolo', 'id')->toArray();
        $gruppi = Gruppo::pluck('nome', 'id')->toArray();
        $clienti = Clienti::pluck('ragione_sociale', 'id')->toArray();

        //Requisiti (Attività propedeutiche)
        $requisiti = Attivita::select(DB::raw("CONCAT(oggetto,' ( ',azienda,' ) - [',percentuale_completamento,'%]' ) AS oggetto"),'id')
                            ->where('id', '<>', $attivita->id)
                            ->where('cliente_id', $attivita->cliente_id)
                            ->pluck('oggetto', 'id')
                            ->toArray();

        $ordinativi = Ordinativo::where('cliente_id', $attivita->cliente_id)->orderBy('azienda', 'desc')->pluck('oggetto', 'id')->toArray();

        $tipologie = [-1 => ''] + config('tasklist.timesheets.tipologie');
        $priorita = config('tasklist.attivita.priorita');
        $durata_tipo =  config('tasklist.attivita.durata_tipo');
        $stati = config('tasklist.attivita.stati');

        $request->flash();

        return view('tasklist::admin.attivita.edit', compact('attivita', 'utenti', 'procedure', 'aree', 'gruppi', 'clienti', 'requisiti', 'ordinativi', 'priorita', 'durata_tipo', 'stati', 'tipologie'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Attivita $attivita
     * @param  UpdateAttivitaRequest $request
     * @return Response
     */
    public function update(Attivita $attivita, UpdateAttivitaRequest $request)
    {
        $rules = Attivita::getRules();

        $this->validate($request, $rules);

        $rules_voci = AttivitaVoce::getRules();

        $attivita_pre_update = $attivita->toArray();

        $update = $request->all();
        $update['updated_user_id'] = Auth::id();

        //Stato Annullata 
        if($request->stato == 3) {
          $update['percentuale_completamento'] = 100;
        }

        //Supervisori
        if(empty($request->supervisori_id))
          $update['supervisori_id'] = [];

        //Requisiti per la lavorazione
        if(empty($request->requisiti))
          $update['requisiti'] = [];


        if (Auth::id() !== $attivita->richiedente_id){
            unset($update['richiedente_id']);
            unset($update['durata_valore']);
            unset($update['durata_tipo']);
        }

        $requisiti_pre_update = $attivita->hasRequisiti();

        $this->attivita->update($attivita, $update);
        $attivita->users()->sync($update['assegnatari_id']);

        save_meta($update['meta'], $attivita);

        // Voci
        $attivita->voci()->delete();

        if(!empty($request->voci))
        {
            foreach ($request->voci as $key => $voce)
            {
                $validator = Validator::make($voce, $rules_voci);

                if(!$validator->fails())
                {
                    $v = $attivita->voci()->create($voce);

                    if(!empty($voce['meta']))
                  	 	save_meta($voce['meta'], $v);

                    if(!empty($voce['meta']['file']))
                      file_save('tasklist', $v, $request, $voce['meta']['file']['name'], "voci.$key.meta.file.file");
                }
            }
        }

        //Dropzone
        dropzone_files_save('tasklist', $attivita->id, 'Attivita', 'Tasklist', $request);

        // Log
        activity(session('azienda'))
            ->performedOn($attivita)
            ->withProperties($update)
            ->log('updated');

        //EMAILS AGGIORNAMENTI DI STATO AI PARTECIPANTI

        // Tutti i requisiti per iniziare la lavorazione sono rispettati.
        if(empty($requisiti_pre_update) && $attivita->hasRequisiti()){
          $oggetto = 'L\'attività "'. $attivita->oggetto . '" rispetta i requisiti per la lavorazione';
          $messaggio = 'Salve,<br> l\'attività rispetta i requisiti per la lavorazione.<br><br>Puoi visualizzarla al seguente link:<br><a href="' . route('admin.tasklist.attivita.read', $attivita->id) . '">' . route('admin.tasklist.attivita.read', $attivita->id) . '</a>';
          mail_send($attivita->partecipanti()->pluck('email')->toArray(), $oggetto, $messaggio);
        }

        // Completamento attività al 100%
        if($attivita_pre_update['percentuale_completamento'] != 100 && $attivita->percentuale_completamento == 100)
        {
          // Creazione nota di completamento
          save_meta(['note' => 'Ho completato l\'attività in data ' . get_date_hour_ita(Carbon::now()) . '.'], $attivita);

          // Data chiusura
          $attivita->update(['data_chiusura' => date('Y-m-d h:i:s')]);

          $oggetto = 'Attività completata - ' . $attivita->oggetto;
          $messaggio = 'Salve,<br> è stata completata l\'attività in oggetto per il cliente ' . $attivita->cliente()->first()->ragione_sociale . ' .<br><br>Puoi visualizzarla al seguente link:<br><a href="' . route('admin.tasklist.attivita.read', $attivita->id) . '">' . route('admin.tasklist.attivita.read', $attivita->id) . '</a>';
          if($attivita->fatturazione == 1){
            $partecipanti_fatturazione = array_unique(array_merge($attivita->partecipanti()->pluck('email')->toArray(), json_decode(setting('tasklist::attivita_email_notifica'), true)));
            mail_send($partecipanti_fatturazione, $oggetto, $messaggio);
          } else {
            mail_send($attivita->partecipanti()->pluck('email')->toArray(), $oggetto, $messaggio);
          }

          //Controllo se ora può lavorare nuove attività
          $attivita_figlie = Attivita::whereJsonContains('requisiti->'.$attivita->id.'->attivita_id', (string)$attivita->id)->get(); 
          if(!empty($attivita_figlie)){
            foreach($attivita_figlie as $figlia){
              if($figlia->hasRequisiti()){
                $oggetto = 'L\'attività "'. $figlia->oggetto . '" rispetta i requisiti per la lavorazione';
                $messaggio = 'Salve,<br> l\'attività rispetta i requisiti per la lavorazione.<br><br>Puoi visualizzarla al seguente link:<br><a href="' . route('admin.tasklist.attivita.read', $figlia->id) . '">' . route('admin.tasklist.attivita.read', $figlia->id) . '</a>';
                mail_send($figlia->partecipanti()->pluck('email')->toArray(), $oggetto, $messaggio);                
              }
            }
          }

        }

        // Email note
        if(!empty($request->meta['note']))
        {
          $partecipanti = $attivita->partecipanti()->pluck('email', 'email')->toArray();
          //unset($partecipanti[$attivita->richiedente->email]);
          unset($partecipanti[Auth::user()->email]);
          $oggetto = 'Nuova nota attività da ' . Auth::user()->full_name . ' - ' . $attivita->oggetto . ' (' . $attivita->cliente->ragione_sociale . ')';
          $messaggio = 'Salve,<br> è stata inserita una nuova nota per l\'attività in oggetto:<br>' . $request->meta['note'] . '<br><br>Puoi visualizzarla al seguente link:<br><a href="' . route('admin.tasklist.attivita.read', $attivita->id) . '">' . route('admin.tasklist.attivita.read', $attivita->id) . '</a>';
          mail_send($partecipanti, $oggetto, $messaggio);
        }

        if($attivita->percentuale_completamento != 100){
          return redirect()->route('admin.tasklist.attivita.edit', $attivita->id)
          ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('Attività')]));
        } else {
          return redirect()->route('admin.tasklist.attivita.read', $attivita->id)
          ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('Attività')]));          
        }
    }

    public function presaVisione(Attivita $attivita)
    {
      if($attivita->partecipanti()->contains('id', Auth::id())){
        if(!$attivita->hasPresoVisione()){
          if($attivita->opzioni['multi_presa_in_carico'] == 1 || !$attivita->preseVisioni()){
            $prese_visioni = $attivita->prese_visioni;
            $prese_visioni[Auth::id()] = ["user_id" => Auth::id(), "data" => date("Y-m-d H:i:s")];
            $attivita->prese_visioni = $prese_visioni;
            $attivita->save();

            // Creazione nota presa in carico
            save_meta(['note' => 'Ho preso in carico l\'attività.'], $attivita);

            //Send email to supervisori
            if($attivita->supervisori()){
              $oggetto = 'Presa In Carico - ' . $attivita->oggetto . ' (' . $attivita->cliente->ragione_sociale . ')';
              $messaggio = 'Salve,<br>'. Auth::user()->full_name .' ha preso in carico l\'attività in oggetto.<br><br>Puoi visualizzarla al seguente link:<br><a href="' . route('admin.tasklist.attivita.read', $attivita->id) . '">' . route('admin.tasklist.attivita.read', $attivita->id) . '</a>';
              mail_send($attivita->supervisori()->pluck('email')->toArray(), $oggetto, $messaggio);          
            }

            // Log
            activity(session('azienda'))
            ->performedOn($attivita)
            ->withProperties(json_encode($attivita))
            ->log('update');

            return redirect()->route('admin.tasklist.attivita.edit', $attivita->id)
                ->withSuccess('Hai preso in carico l\'attività.');
          } else {
            return redirect()->route('admin.tasklist.attivita.edit', $attivita->id)
                ->withError('L\'attività è stata già presa in carico.');         
          }
        } else {
          return redirect()->route('admin.tasklist.attivita.edit', $attivita->id)
              ->withError('L\'attività è stata già presa in carico.');        
        }
      } else {
        return redirect()->route('admin.tasklist.attivita.edit', $attivita->id)
            ->withError('Non puoi prendere in carico l\'attività.');          
      }
    }

    public function clearPreseVisioni(Attivita $attivita)
    {
      if($attivita->supervisori() && $attivita->supervisori()->contains('id', Auth::id())){
        if($attivita->preseVisioni()){
          if($attivita->opzioni['multi_presa_in_carico'] != 1){

            $attivita->prese_visioni = [];
            $attivita->save();

            // Log
            activity(session('azienda'))
            ->performedOn($attivita)
            ->withProperties(json_encode($attivita))
            ->log('update');

            $partecipanti = $attivita->partecipanti()->pluck('email', 'email')->toArray();
            unset($partecipanti[$attivita->richiedente->email]);

            $oggetto = 'L\'attività "'. $attivita->oggetto . '" è da prendere in carico.';
            $messaggio = 'Salve,<br> l\'attività deve essere ripresa in carico da un assegnatario.<br><br>Puoi visualizzarla al seguente link:<br><a href="' . route('admin.tasklist.attivita.read', $attivita->id) . '">' . route('admin.tasklist.attivita.read', $attivita->id) . '</a>';
            mail_send($partecipanti, $oggetto, $messaggio);

            // Creazione nota di annullamento prese in carico
            save_meta(['note' => 'Ho annullato le prese in carico dell\'attività.'], $attivita);

            return redirect()->route('admin.tasklist.attivita.edit', $attivita->id)
                ->withSuccess('Hai annullato le prese in carico dell\'attività.');

          } else {
            return redirect()->route('admin.tasklist.attivita.edit', $attivita->id)
                ->withError('Non puoi annullare le prese in carico poichè la presa in carico multipla è abilitata.');               
          }
        } else {
          return redirect()->route('admin.tasklist.attivita.edit', $attivita->id)
              ->withError('L\'attività non presenta alcuna presa in carico.');         
        }
      } else {
        return redirect()->route('admin.tasklist.attivita.edit', $attivita->id)
            ->withError('Non sei un supervisore dell\'attività.');          
      }
    }

    public function pinAttivita(Attivita $attivita)
    {
      if($attivita->partecipanti()->contains('id', Auth::id())){
        if($attivita->pinnedBy() && $attivita->pinnedBy()->contains('id', Auth::id())){
          $pinned_by = $attivita->pinned_by;
          unset($pinned_by[Auth::id()]);
          $attivita->pinned_by = $pinned_by;
          $attivita->save();

          // Log
          activity(session('azienda'))
          ->performedOn($attivita)
          ->withProperties(json_encode($attivita))
          ->log('update');

          return redirect()->back()
              ->withSuccess('L\'attività non è più in risalto.');
        }

        if($attivita->pinnedBy() && !$attivita->pinnedBy()->contains('id', Auth::id()) || !$attivita->pinnedBy()){
          $pinned_by = $attivita->pinned_by;
          $pinned_by[Auth::id()] = ["user_id" => Auth::id(), "data" => date("Y-m-d H:i:s")];
          $attivita->pinned_by = $pinned_by;
          $attivita->save();

          // Log
          activity(session('azienda'))
          ->performedOn($attivita)
          ->withProperties(json_encode($attivita))
          ->log('update');

          return redirect()->back()
              ->withSuccess('Hai messo l\'attività in risalto.');
        }
      } else {
        return redirect()->back()
        ->withError('Non puoi mettere in risalto l\'attività.');        
      }
    }

    public function getAssegnatari(Request $request){
      $gruppo = $request->gruppo;
      $gruppo_sel = Gruppo::find($gruppo);

      if(!empty($gruppo_sel))
        $assegnatari = $gruppo_sel->users->pluck('full_name','id')->toJson();
      else
        return "";

      ob_clean();
      return $assegnatari;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Attivita $attivita
     * @return Response
     */
    public function destroy(Attivita $attivita)
    {
        $this->attivita->destroy($attivita);

        // Log
        activity(session('azienda'))
            ->performedOn($attivita)
            ->withProperties(json_encode($attivita))
            ->log('destroyed');


        //Attività / Ordinativo
        if(str_contains(url()->current(), '/commerciale/ordinativi')){
          return redirect()->back()
          ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('Attività')]));
        }

        return redirect()->route('admin.tasklist.attivita.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('Attività')]));
    }

    public function sollecitaAssegnatari(Request $request)
    {
      $data = $request->all();
      $attivita = Attivita::findOrFail($data['attivita_id']);
      if(empty($attivita)){
          return redirect()->back()->withErrors('Attività inesistente.')->withInput();
      }

      if(!empty($attivita->supervisori()) && $attivita->supervisori()->count() > 0){
        if (!$attivita->supervisori()->contains('id', Auth::id())){
          return redirect()->back()->withErrors('Non sei un supervisore di questa attività!')->withInput();
        }
      }
      
      $validator = Validator::make($data, [
        'attivita_id' => 'required|integer|min:1',
        'nota' => 'string|required',
      ]);

      if ($validator->fails()) {
          return redirect()->back()
              ->withErrors($validator)
              ->withInput();
      }

      // Email Assegnatari
      $assegnatari_email = $attivita->users()->pluck('email')->toArray();

      $oggetto = 'Sollecito attività - ' . $attivita->oggetto . ' (' . $attivita->cliente->ragione_sociale . ')';
      $messaggio = '<br>L\'attività <strong class="text-primary">' . $attivita->oggetto . '</strong> è stata sollecitata da <strong>' .  auth_user()->full_name . '</strong>.<br><br>Nota:<br>' . $request->nota . '<br><br>' . 'Cliente:<br> ' . $attivita->cliente->ragione_sociale . 'Ordinativo:<br> ' . $attivita->ordinativo->oggetto . 'Procedura:<br> ' . $attivita->procedura->titolo . 'Area D\'Intervento:<br> ' . $attivita->area->titolo . 'Gruppo:<br> ' . $attivita->gruppo->nome . '<br><br>Link: <a href="' . route('admin.tasklist.attivita.edit', $attivita->id) . '">premi qui per aprire l\'attività.</a>';
      mail_send($assegnatari_email, $oggetto, $messaggio);

      // Log
      activity(session('azienda'))
      ->performedOn($attivita)
      ->withProperties($data)
      ->log('updated');

      return redirect()->back()->withSuccess("Assegnatari sollecitati con successo.");

    }

    /**
     * $request->only['nota', 'durata', 'data']
     *
     * @return Response
     */
    public function storeTimesheet(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'attivita_id' => 'required|integer|min:1',
            'tipologia' => 'required|integer|min:0',
            'data' => 'required',
            'ora_inizio' => 'required',
            'ora_fine' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $attivita = Attivita::findOrFail($data['attivita_id']);
        if(empty($attivita)){
            return redirect()->back()->withErrors('Attività inesistente.')->withInput();
        }

        $create = [
            'azienda' =>$attivita->azienda,
            'attivita_id' => $attivita->id,
            'cliente_id' => $attivita->cliente_id,
            'ordinativo_id' => $attivita->ordinativo_id,
            'procedura_id' => $attivita->procedura_id,
            'area_id' => $attivita->area_id,
            'gruppo_id' => $attivita->gruppo_id,
            'tipologia' => $data['tipologia'],
            'dataora_inizio' => $data['data'] . ' ' . $data['ora_inizio'],
            'dataora_fine' => $data['data'] . ' ' . $data['ora_fine'],
            'nota' => $data['nota'],
            'created_user_id' => Auth::id(),
            'updated_user_id' => Auth::id()
        ];

        $rules = Timesheet::getRules();
        unset($rules['ora_inizio']);
        unset($rules['ora_fine']);
        $rules['dataora_inizio'] = 'required';
        $rules['dataora_fine'] = 'required';

        $validator = Validator::make($create, $rules);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator);
        }

        $timesheet = Timesheet::create($create);

        // Log
        activity(session('azienda'))
            ->performedOn($timesheet)
            ->withProperties(json_encode($create))
            ->log('created');

        return redirect()->back()->withSuccess("Timesheet creato con successo.");
    }

    // Export excel
    public function exportExcel(Request $request)
    {
      $auth = $this->auth;
      if($auth->hasAccess('tasklist.attivita.all') && !empty($request->all))
      {
        $attivita = new Attivita;
      }
      else
      {
        $attivita = Attivita::where(function($query) {
                                $query->where('richiedente_id', Auth::id())
                                        ->orWhereHas('users', function($assegnatari) {
                                            $assegnatari->where('users.id', Auth::id());
                                        });
                            })->orWhere('supervisori_id', 'LIKE', '%"' . Auth::id() . '"%');
                           
      }

      if(empty($request->stato))
          $attivita = $attivita->whereNotIn('stato', [4]);

      $attivita = $attivita->filter($request->all())
                          //->whereIn('stato', [0, 1])
                          ->orderBy('percentuale_completamento', 'desc')
                          ->get();
      ob_clean();
      return Excel::download(new AttivitaExport($attivita), 'Attivita.xlsx');
    }

}
