<?php
namespace Modules\Assistenza\Http\Controllers\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Assistenza\Entities\RichiesteIntervento;
use Modules\Assistenza\Http\Requests\CreateRichiesteInterventoRequest;
use Modules\Assistenza\Http\Requests\UpdateRichiesteInterventoRequest;
use Modules\Assistenza\Repositories\RichiesteInterventoRepository;
use Modules\Commerciale\Entities\Fatturazione;
use Modules\Export\Entities\RichiesteInterventoExport;
use Modules\Export\Entities\RichiesteInterventoDipendenteExport;
use Modules\Export\Entities\RichiesteInterventoAreaExport;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Assistenza\Entities\RichiesteInterventoAzione;

use Modules\Tasklist\Entities\Timesheet;
use Modules\Amministrazione\Entities\Clienti;
use Modules\Amministrazione\Entities\ClienteReferenti;
use Modules\Amministrazione\Entities\ClienteIndirizzi;
use Modules\Commerciale\Entities\Ordinativo;
use Modules\Profile\Entities\Procedura;
use Modules\Profile\Entities\Area;
use Modules\Profile\Entities\Gruppo;
use Modules\User\Entities\Sentinel\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File; 

use Notification;
use App\Notifications\newTicketAssegnatari;
use App\Notifications\newTicketCliente;
use App\Notifications\ticketChiusuraCliente;
use App\Notifications\ticketTentatoContatto;


use Session;

use Modules\Commerciale\Entities\Offerta;

use Modules\Assistenza\Http\Services\StatisticheService;

class RichiesteInterventoController extends AdminBaseController
{
    /**
     * @var RichiesteInterventoRepository
     */
    private $richiesteintervento;
    private $statistiche;

    public function __construct(RichiesteInterventoRepository $richiesteintervento, StatisticheService $statistiche)
    {
        parent::__construct();

        $this->richiesteintervento = $richiesteintervento;
        $this->statistiche = $statistiche;
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
            $res['stato'] = 1;
            $request->merge($res);
        }

        $clienti = [0 => ''] + Clienti::pluck('ragione_sociale', 'id')->toArray();
        $aree = [0 => ''] + Area::pluck('titolo', 'id')->toArray();
        $gruppi = [0 => ''] + Gruppo::pluck('nome', 'id')->toArray();
        $destinatari = [0 => ''] + User::all()->pluck('full_name', 'id')->toArray();

        //PARTNERS
        $partners = Auth::user()->profile->partner;
        $partners = json_decode($partners);

        if(empty($partners))
        {
            $richieste = RichiesteIntervento::filter($request->all())->get();

            $richieste_ids = [];
            if(auth_user()->hasAccess('assistenza.richiesteinterventi.admin')){
                $richieste_ids += $richieste->pluck('id')->toArray();
            }
            if(auth_user()->hasAccess('assistenza.richiesteinterventi.urbismart')){
                $richieste_ids += $richieste->where('procedura_id', 4)->pluck('id')->toArray();       
            }
            if(auth_user()->hasAccess('assistenza.richiesteinterventi.areatecnica')){
                $richieste_ids += $richieste->where('procedura_id', 5)->pluck('id')->toArray();                  
            }

            $richieste_ids += RichiesteIntervento::filter($request->all())
                ->whereHas('destinatari', function($q) {
                    $q->where('user_id', Auth::id());
                })->pluck('id')->toArray();

            $richiesteinterventi = RichiesteIntervento::filter($request->all())->orderByDesc('livello_urgenza')->orderbyDesc('created_at')->whereIn('id', $richieste_ids)->paginateFilter(config('wecore.pagination.limit'));
        }
        else
        { 
            $richiesteinterventi = [];
            $richiesteinterventi_raw = RichiesteIntervento::filter($request->all())->get();
                //->paginateFilter(config('wecore.pagination.limit'));

            if(!empty($richiesteinterventi_raw) && count($richiesteinterventi_raw) > 0) {
                foreach($richiesteinterventi_raw as $richiesta) {
                    $partner_cliente = $richiesta->cliente->aree;
                    $utenti_gruppo = $richiesta->destinatari->pluck('id')->toArray();

                    if(in_array(Auth::id(), $utenti_gruppo)) {
                        $richiesteinterventi[] = $richiesta;
                    }
                    elseif(!empty($partner_cliente)) {
                        foreach ($partner_cliente as $cliente_partner)  {
                            if(in_array($cliente_partner, $partners) ) {
                                if(!in_array($richiesta, $richiesteinterventi)) {
                                    $richiesteinterventi[] = $richiesta;
                                }
                            }
                        }
                    }
                }
            }

            $richieste_ids = collect($richiesteinterventi)->pluck('id')->toArray();
            $richiesteinterventi = RichiesteIntervento::filter($request->all())
                                    ->orderByDesc('livello_urgenza')
                                    ->orderbyDesc('created_at')
                                    ->whereIn('id', $richieste_ids)
                                    ->paginateFilter(config('wecore.pagination.limit'));
        }
        
        if(!$request->ajax())
        {
            $request->flash();

            return view('assistenza::admin.richiesteinterventi.index', compact('richiesteinterventi','clienti','aree','destinatari', 'gruppi'));
        }
        else
        {
            return view('assistenza::admin.richiesteinterventi.partials.table_index', compact('richiesteinterventi','clienti','aree','destinatari', 'gruppi'));
        }
    }

    public function ajaxrequestdestinatari(Request $request){
      $gruppo = $request->gruppo;
      $gruppo_sel = Gruppo::find($gruppo);

      if(!empty($gruppo_sel))
        $destinatari = $gruppo_sel->users->pluck('full_name','id')->toJson();
      else
        return "";

      ob_clean();
      return $destinatari;
    }

    public function ajaxrequestcontatti(Request $request){
      $data = [];
      $data[4] ='no';
      if($request->ajax() && !empty($request->cliente_id)){
          $id = $request->cliente_id;
          $cliente = Clienti::find($id);
          $referenti = $cliente->referenti;
          $numeri = [];
          $nomi = [];
          $emails = [];
          $ids = [];
          foreach($referenti as $referente){

              if(!empty($referente->telefono))
                $numeri[] = $referente->telefono;

              $nomi[] = (!empty($referente->nome) ? $referente->nome : '') . ' ' . (!empty($referente->cognome) ? $referente->cognome : '');
              $emails[] = $referente->email;
              $ids[] = $referente->id;
          }

          $data[0] = $numeri;
          $data[1] = $nomi;
          $data[2] = $emails;
          $data[3] = $ids;
          $data[4] ='ok';

      }

      return $data;
    }

    public function ajaxrequestrichiesta(Request $request)
    {
        if($request->ajax())
        {
          $vuoto = [0 => ''];
          $ordinativi = $vuoto;
          $ordinativo_sel = '';
          $indirizzi = $vuoto;
          if(!empty($request->cliente_id))
          {
            //TROVO IL CLIENTE E VERIFICO SE HA IL DEFAULT
            $cliente = Clienti::where('id',$request->cliente_id)->first();
            if(!empty($cliente) && !empty($cliente->default_ordinativo))
            {
              $ordinativo_sel = $cliente->default_ordinativo;
            }
            $indirizzi_query = ClienteIndirizzi::where('cliente_id', $request->cliente_id)->get();
            $final_indirizzi = [];
            if(!empty($indirizzi_query)){
              foreach($indirizzi_query as $indirizzo){

                $final_indirizzi[0] = (!empty($indirizzo->denominazione) ? $indirizzo->denominazione . ' | ' : '') . $indirizzo->citta . ' - ' . $indirizzo->indirizzo . ' (' . $indirizzo->cap . ' ' . $indirizzo->provincia . ')';
                $final_indirizzi[1] = $indirizzo->id;

                $indirizzi[] = $final_indirizzi;
              }
            }

            $ordinativi = [0 => ''] + Ordinativo::active()->where('cliente_id', $request->cliente_id)->pluck('oggetto', 'id')->toArray();

          }
            ob_clean();
            //$indirizzi = $final_indirizzi;
            return response()->json([
                'default_ordinativo'=> $ordinativo_sel,
                'indirizzi' => $indirizzi,
                'ordinativi' => $ordinativi
            ]);

        }else{
               return response()->view('404');
        }
    }

    public function getclienteLogs(Request $request)
    {
        if($request->ajax()){
            $richiestaintervento = RichiesteIntervento::find($request->ticketID);
            $cliente = $richiestaintervento->cliente;
            $ordinativo = Ordinativo::findOrFail($cliente->default_ordinativo);
            $gruppi = Gruppo::all();
            //$gg_ordinativi = Ordinativo::get_giornate(28);
            $gg_ordinativi = Ordinativo::get_giornate($ordinativo->id);
            if(empty($gg_ordinativi)){ $gg_ordinativi = [];}
            return response()->json([
                'ordinativo' => $ordinativo,
                'gg_ordinativi' => $gg_ordinativi,
                'gruppi' => $gruppi,
            ]);
        }
    }

    public function startLavorazione(Request $request)
    {
        // Controllo che l'utente non abbia in carica altri tickets
        $actions_auth = RichiesteInterventoAzione::where('created_user_id', Auth::id())->where('tipo', 1)->get();
        if(!empty($actions_auth) && count($actions_auth) > 0){
            $ticket_id = RichiesteInterventoAzione::where('created_user_id', Auth::id())->where('tipo', 1)->pluck('ticket_id')->first();
            $msg = 'Hai già in carica un ticket. <a href="'. route('admin.assistenza.richiesteintervento.edit', $ticket_id) . '"> Premi qui  </a> per aprirlo.';
            return redirect()->route('admin.assistenza.richiesteintervento.read', $request->ticketID)->withError($msg);
        }
        // Controllo che il ticket non sia in carica ad un altro utente
        $actions_auth = RichiesteInterventoAzione::where('ticket_id', $request->ticketID)->where('tipo', 1)->get();
        if(!empty($actions_auth) && count($actions_auth) > 0){
            return redirect()->route('admin.assistenza.richiesteintervento.read', $request->ticketID)->withError('Ticket in lavorazione da un altro utente.');
        }
        // Controllo che il ticket abbia un cliente valido
        $ticket = RichiesteIntervento::find($request->ticketID);
        if($ticket->cliente->id == 81){
          return redirect()->route('admin.assistenza.richiesteintervento.read', $request->ticketID)->withError('Impossibile lavorare il ticket: cliente non valido.');
        }

        if($ticket->ordinativo_id == null){
            return redirect()->route('admin.assistenza.richiesteintervento.read', $request->ticketID)->withError('Il ticket non ha un ordinativo.');
        }

        if($ticket->get_stato_integer() == 4){
          $nuova_richiesta = new RichiesteInterventoAzione();
          $nuova_richiesta->tipo = 5;
          $nuova_richiesta->ticket_id = $request->ticketID;
          $nuova_richiesta->tipologia_intervento = 2;
          $nuova_richiesta->created_user_id = Auth::id();
          $nuova_richiesta->updated_user_id = Auth::id();
          $nuova_richiesta->save();
          $ticket->stato = 5;
          $ticket->save();

          // Log
          activity(session('azienda'))
              ->performedOn($nuova_richiesta)
              ->withProperties(json_encode($nuova_richiesta))
              ->log('created');
        }

        if($ticket->stato != 3) {
            $nuova_richiesta = new RichiesteInterventoAzione();
            $nuova_richiesta->tipo = 1;
            $nuova_richiesta->ticket_id = $request->ticketID;
            $nuova_richiesta->tipologia_intervento = 2;
            $nuova_richiesta->created_user_id = Auth::id();
            $nuova_richiesta->updated_user_id = Auth::id();
            $nuova_richiesta->save();

            // Log
            activity(session('azienda'))
                ->performedOn($nuova_richiesta)
                ->withProperties(json_encode($nuova_richiesta))
                ->log('created');
        }

        return redirect()->route('admin.assistenza.richiesteintervento.edit', $ticket->id);
    }

    public function conversione() 
    {
        $richiesteintervento = RichiesteIntervento::whereHas('azioni')->whereNull('stato')->get();
        $azioni = RichiesteInterventoAzione::all();

        foreach($richiesteintervento as $richiesta)
        {
            $azioni_richiesta = $azioni->where('ticket_id', $richiesta->id);

            $statistiche = array();

            $statistiche['tempo_risoluzione_con_sospensioni'] = 0;
            $statistiche['tempo_risoluzione'] = 0;

            if($azioni_richiesta->contains('tipo', 3))
            {
                $statistiche['tempo_risoluzione_con_sospensioni'] = working_time($richiesta->created_at, $azioni_richiesta->where('tipo', 3)->sortByDesc('id')->first()->updated_at, 'seconds');
                $statistiche['tempo_risoluzione'] = working_time($richiesta->created_at, $azioni_richiesta->first()->created_at, 'seconds');
            }

            $statistiche['tempo_lavorazione_totale'] = 0;
            $statistiche['tempo_lavorazione_operatori'] = array();

            foreach($azioni_richiesta as $a)
            {
                $totale_secondi_azione = working_time($a->created_at, $a->updated_at, 'seconds'); 
                
                if($a->tipo != 4)
                {
                    $statistiche['tempo_lavorazione_totale'] += $totale_secondi_azione;

                    if(empty($statistiche['tempo_lavorazione_operatori'][$a->created_user_id]))
                    {
                        $statistiche['tempo_lavorazione_operatori'][$a->created_user_id] = 0;
                    }

                    if($azioni_richiesta->contains('tipo', 3)){
                        $statistiche['tempo_risoluzione'] += $totale_secondi_azione;
                    }

                    $statistiche['tempo_lavorazione_operatori'][$a->created_user_id] += $totale_secondi_azione;
                }
            }

            $richiesta->update(['statistiche' => $statistiche, 'stato' => $azioni_richiesta->sortByDesc('id')->first()->tipo]);
        }
    }

    public function iniziolavoro(Request $request)
    {
        $edit_richiesta = RichiesteInterventoAzione::find($request->id_azione);
        $time_richiesta = RichiesteIntervento::find($edit_richiesta->ticket_id);

        if($time_richiesta->stato != 3) {
            $edit_richiesta->tipo = $request->tipo;
            $edit_richiesta->tipologia_intervento = $request->id_tip;
            $edit_richiesta->descrizione = $request->descr;
            $edit_richiesta->save(); 

            // Log
            activity(session('azienda'))
                ->performedOn($edit_richiesta)
                ->withProperties(json_encode($edit_richiesta))
                ->log('updated');

            //timesheets
            $timesheet = new Timesheet();
            $timesheet->azienda = session('azienda');
            $timesheet->cliente_id = $time_richiesta->cliente_id;
            $timesheet->procedura_id = $time_richiesta->procedura_id;
            $timesheet->area_id = $time_richiesta->area_id;
            $timesheet->gruppo_id = $time_richiesta->gruppo_id;
            $timesheet->ordinativo_id = $time_richiesta->ordinativo_id;
            $timesheet->attivita_id = null;
            $timesheet->ticket_azione_id = $edit_richiesta->id;
            $timesheet->dataora_inizio = $edit_richiesta->created_at;
            $timesheet->dataora_fine = $edit_richiesta->updated_at;
            $timesheet->nota = $time_richiesta->oggetto . ' ( '. config('assistenza.richieste_intervento.azioni.tipo')[$edit_richiesta->tipo] .' ) : '. $edit_richiesta->descrizione;
            $timesheet->tipologia = $edit_richiesta->tipologia_intervento == 2 ? 0 : $edit_richiesta->tipologia_intervento;
            $timesheet->created_user_id = Auth::id();
            $timesheet->updated_user_id = Auth::id();
            $timesheet->save();

            // Log
            activity(session('azienda'))
                ->performedOn($timesheet)
                ->withProperties(json_encode($timesheet))
                ->log('created');

            //Aggiorno il conteggio lavorato

            $azioni_richiesta = RichiesteInterventoAzione::where('ticket_id',  $time_richiesta->id)->get();

            $statistiche = array();

            $statistiche['tempo_risoluzione_con_sospensioni'] = 0;
            $statistiche['tempo_risoluzione'] = 0;

            if($azioni_richiesta->contains('tipo', 3))
            {
                $statistiche['tempo_risoluzione_con_sospensioni'] = working_time($time_richiesta->created_at, $azioni_richiesta->where('tipo', 3)->sortByDesc('id')->first()->updated_at, 'seconds');
                $statistiche['tempo_risoluzione'] = working_time($time_richiesta->created_at, $azioni_richiesta->first()->created_at, 'seconds');
            }

            $statistiche['tempo_lavorazione_totale'] = 0;
            $statistiche['tempo_lavorazione_operatori'] = array();

            foreach($azioni_richiesta as $a)
            {
                $totale_secondi_azione = working_time($a->created_at, $a->updated_at, 'seconds'); 
                
                if($a->tipo != 4)
                {
                    $statistiche['tempo_lavorazione_totale'] += $totale_secondi_azione;

                    if(empty($statistiche['tempo_lavorazione_operatori'][$a->created_user_id]))
                    {
                        $statistiche['tempo_lavorazione_operatori'][$a->created_user_id] = 0;
                    }

                    if($azioni_richiesta->contains('tipo', 3)){
                        $statistiche['tempo_risoluzione'] += $totale_secondi_azione;
                    }

                    $statistiche['tempo_lavorazione_operatori'][$a->created_user_id] += $totale_secondi_azione;
                }
            }

            $time_richiesta->update(['statistiche' => $statistiche, 'stato' => $request->tipo]);

            // Log
            activity(session('azienda'))
                ->performedOn($time_richiesta)
                ->withProperties(json_encode($time_richiesta))
                ->log('updated');

            //Email contatto
            $status = $time_richiesta->stato;
            $contatto_email = $time_richiesta->email;
            if($status == 3) {
                $time_richiesta->email_oggetto = 'Chiusura ticket assistenza - ' . $time_richiesta->codice;
                $time_richiesta->messaggio = $request->descr;
                Notification::route('mail', $contatto_email)->notify(new ticketChiusuraCliente($time_richiesta));
            }

            if($status == 7) {
                $time_richiesta->email_oggetto = 'Contatto ticket assistenza - ' . $time_richiesta->codice;
                $time_richiesta->messaggio = $request->descr;
                Notification::route('mail', $contatto_email)->notify(new ticketTentatoContatto($time_richiesta));
            }

            return route('admin.assistenza.richiesteintervento.edit', $edit_richiesta->id); 

        } else {
            return route('admin.assistenza.richiesteintervento.read', $edit_richiesta->id)->withError('Impossibile lavorare il ticket: ticket chiuso.');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $richiesteintervento = new RichiesteIntervento();
        $clienti = [0 => ''] + Clienti::where('tipo', '!=', 2)->pluck('ragione_sociale', 'id')->toArray();
        $richiedenti = Clienti::distinct()->pluck('ragione_sociale')->toArray();
        $numeri = RichiesteIntervento::distinct()->pluck('numero_da_richiamare')->toArray();
        $emails = RichiesteIntervento::distinct()->pluck('email')->toArray();
        $ordinativi = [];
        $procedure = [0 => ''] + Procedura::pluck('titolo', 'id')->toArray();
        $aree = [0 => ''] + Area::pluck('titolo', 'id')->toArray();
        $gruppi = [0 => ''] + Gruppo::pluck('nome', 'id')->toArray();
        $destinatari = [];
        $listautenti = User::all();

        foreach($listautenti as $utente)
        {
          $destinatari += [$utente->id => $utente->full_name];
        }
        $richiesteintervento_azioni = $richiesteintervento->azioni()->where('ticket_id', $richiesteintervento->id)->get();
        $destinatari_sel = null;

        /*Tutti GLI UTENTI
          foreach(User::all() as $k => $value)
          {
            $destinatari += [$value->id => $value->full_name];
          }
        */
        return view('assistenza::admin.richiesteinterventi.create', compact('richiesteintervento', 'clienti', 'ordinativi',
        'procedure', 'destinatari','aree','gruppi','destinatari_sel','richiesteintervento_azioni', 'richiedenti', 'numeri', 'emails'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateRichiesteInterventoRequest $request
     * @return Response
     */
    public function store(CreateRichiesteInterventoRequest $request)
    {
        $rules = RichiesteIntervento::getRules();
        $this->validate($request, $rules);

        $create = $request->all();
        $create['azienda'] = session('azienda');
        $create['numero'] = RichiesteIntervento::get_next_numero();
        $create['created_user_id'] = Auth::id();
        $create['updated_user_id'] = Auth::id();

        $richiestaintervento = RichiesteIntervento::create($create);

        //SALVO I DESTINATARI
        $richiestaintervento->destinatari()->sync($create['destinatario_id']);

        //Dropzone
        dropzone_files_save('assistenza', $richiestaintervento->id, 'RichiesteIntervento', 'Assistenza', $request);

        // Files
        //if(!empty($create['meta']['file']))
        //    file_save('assistenza', $richiestaintervento, $request, $create['meta']['file']['name']);

        // Email destinatari
        if((bool) $richiestaintervento->gruppo->notifiche) {
            foreach($richiestaintervento->destinatari()->get() as $destinatario)
            {
                $richiestaintervento->email_oggetto = 'Nuovo ticket assistenza - ' . $richiestaintervento->cliente->ragione_sociale . ' | ' . $richiestaintervento->codice;
                $richiestaintervento->assegnato_da = Auth::user()->full_name;
                $destinatario->notify(new newTicketAssegnatari($richiestaintervento));
            }
        }

        // Email contatto
        $contatto_email = $create['email'];

        $richiestaintervento->email_oggetto = 'Nuovo ticket assistenza - ' . $richiestaintervento->cliente->ragione_sociale . ' | ' . $richiestaintervento->codice;        
        Notification::route('mail', $contatto_email)->notify(new newTicketCliente($richiestaintervento));

        // Log
        activity(session('azienda'))
            ->performedOn($richiestaintervento)
            ->withProperties($create)
            ->log('created');

        return redirect()->route('admin.assistenza.richiesteintervento.read', $richiestaintervento->id)
            ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('assistenza::richiesteinterventi.title.richiesteinterventi')]));
    }

    /**
     * Show the form for read the specified resource.
     *
     * @param  RichiesteIntervento $richiesteintervento
     * @return Response
     */
    public function read(RichiesteIntervento $richiesteintervento)
    {
        $richiesteintervento_azioni = $richiesteintervento->azioni()->where('ticket_id', $richiesteintervento->id)->get();
        $cliente = $richiesteintervento->cliente;
        $gg_ordinativi = [];
        $ordinativo = Ordinativo::find($richiesteintervento->ordinativo_id);
        $gruppi = Gruppo::all();
        //$gg_ordinativi = Ordinativo::get_giornate(28);

        if(!empty($ordinativo)){
          $gg_ordinativi = Ordinativo::get_giornate($ordinativo->id);
        }

        return view('assistenza::admin.richiesteinterventi.read', compact('richiesteintervento','richiesteintervento_azioni', 'gruppi', 'gg_ordinativi', 'ordinativo'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  RichiesteIntervento $richiesteintervento
     * @return Response
     */
    public function edit(RichiesteIntervento $richiesteintervento)
    {
        //se il ticket è chiuso
        if($richiesteintervento->get_stato_integer() == 3)
            return redirect()->route('admin.assistenza.richiesteintervento.read',$richiesteintervento->id);

        // verifico se posso accedere
        if(!auth_user()->hasAccess('assistenza.richiesteinterventi.admin') && !$richiesteintervento->possoLavorarlo() && empty($richiesteintervento->checkinLavorazione()))
            return redirect()->route('admin.assistenza.richiesteintervento.read',$richiesteintervento->id);

        $utente_corrente = Auth::id();

        $clienti = [0 => ''] + Clienti::where('tipo', '!=', 2)->pluck('ragione_sociale', 'id')->toArray();
        $richiedenti = Clienti::pluck('ragione_sociale')->toArray();
        $numeri = RichiesteIntervento::pluck('numero_da_richiamare')->toArray();
        $emails = RichiesteIntervento::pluck('email')->toArray();
        $ordinativi = [0 => ''] + Ordinativo::where('cliente_id', $richiesteintervento->cliente_id)->pluck('oggetto', 'id')->toArray();
        $procedure = [0 => ''] + Procedura::pluck('titolo', 'id')->toArray();
        $aree = [0 => ''] + Area::pluck('titolo', 'id')->toArray();
        $gruppi = [0 => ''] + Gruppo::pluck('nome', 'id')->toArray();

        $destinatari_sel = $richiesteintervento->destinatari->pluck('id')->toArray();
        $destinatari = [];

        $listautenti = User::all();

        foreach($listautenti as $utente)
        {
          $destinatari += [$utente->id => $utente->full_name];
        }

        /*$gruppo_sel = Gruppo::find($richiesteintervento->gruppo_id);

        foreach($gruppo_sel->users as $value)
        {
          $destinatari += [$value->id => $value->full_name];
        }

         /*Tutti GLI UTENTI
            foreach(User::all() as $k => $value)
            {
              $destinatari += [$value->id => $value->full_name];
            }
        */
        $richiesteintervento_azioni = $richiesteintervento->azioni()->where('ticket_id', $richiesteintervento->id)->get();

        return view('assistenza::admin.richiesteinterventi.edit',
        compact('richiesteintervento', 'clienti', 'ordinativi', 'procedure', 'aree', 'gruppi',
        'destinatari','destinatari_sel','richiesteintervento_azioni','utente_corrente', 'richiedenti', 'numeri', 'emails'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  RichiesteIntervento $richiesteintervento
     * @param  UpdateRichiesteInterventoRequest $request
     * @return Response
     */
    public function update(RichiesteIntervento $richiesteintervento, UpdateRichiesteInterventoRequest $request)
    {
        $rules = RichiesteIntervento::getRules();
        $this->validate($request, $rules);

        $update = $request->all();
        $update['updated_user_id'] = Auth::id();

        //Dropzone
        dropzone_files_save('assistenza', $richiesteintervento->id, 'RichiesteIntervento', 'Assistenza', $request); 

        //Controllo rimozione destinatari (chi sta lavorando il ticket);
        $id_operatore = RichiesteInterventoAzione::where('ticket_id', $richiesteintervento->id)->where('tipo', 1)->orderBy('id', 'desc')->first();

        $destinatari_email = [];
        $destinatari_new = $request->destinatario_id;
        $destinatari_old = $richiesteintervento->destinatari->pluck('id')->toArray();

        if(!empty($id_operatore) && !in_array($id_operatore->created_user_id, $destinatari_new) && in_array($id_operatore->created_user_id, $destinatari_old)){
            return back()->withError('Prima di rimuovere i destinatari salvare la lavorazione in corso.');
        }

        // Verifico cambio destinatari e che il ticket non sia chiuso
        $diff_destinatari = array_diff($destinatari_new, $destinatari_old);
        $destinatari_email = User::whereIn('id', $diff_destinatari)->get();
        $destinatari_names = implode(", ", User::whereIn('id', $destinatari_new)->get()->pluck('full_name')->toArray());

        if(count($diff_destinatari) > 0 && $richiesteintervento->stato !== 3 || count($destinatari_new) != count($destinatari_old) && $richiesteintervento->stato !== 3)
        {
          $insert_cambio_destinatari = new RichiesteInterventoAzione();
          $insert_cambio_destinatari->descrizione = 'Cambio destinatari: ' . $destinatari_names;
          $insert_cambio_destinatari->tipo = 6;
          $insert_cambio_destinatari->tipologia_intervento = 2;
          $insert_cambio_destinatari->ticket_id = $richiesteintervento->id;
          $insert_cambio_destinatari->created_user_id = Auth::id();
          $insert_cambio_destinatari->updated_user_id = Auth::id();

          $insert_cambio_destinatari->save();

        } else {
            $update['destinatario_id'] = $destinatari_old;
        }

        $richiestaintervento = $this->richiesteintervento->update($richiesteintervento, $update);
        $richiestaintervento->destinatari()->sync($update['destinatario_id']);

        if(count($diff_destinatari) > 0 && $richiesteintervento->stato !== 3 || count($destinatari_new) != count($destinatari_old) && $richiesteintervento->stato !== 3)
        {
          // Email nuovi destinatari
          foreach($destinatari_email as $destinatario)
          {
              $richiestaintervento->email_oggetto = 'Ticket assistenza - ' . $richiestaintervento->cliente->ragione_sociale . ' | ' . $richiestaintervento->codice;
              $richiestaintervento->assegnato_da = Auth::user()->full_name;
              $destinatario->notify(new newTicketAssegnatari($richiestaintervento));
          }            
        }

        // Log
        activity(session('azienda'))
            ->performedOn($richiestaintervento)
            ->withProperties($update)
            ->log('updated');

        return redirect()->route('admin.assistenza.richiesteintervento.edit', $richiestaintervento->id)
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('assistenza::richiesteinterventi.title.richiesteinterventi')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  RichiesteIntervento $richiesteintervento
     * @return Response
     */
    public function destroy(RichiesteIntervento $richiesteintervento)
    {
        // Log
        activity(session('azienda'))
            ->performedOn($richiesteintervento)
            ->withProperties(json_encode($richiesteintervento))
            ->log('destroyed');

        $this->richiesteintervento->destroy($richiesteintervento);

        return redirect()->route('admin.assistenza.richiesteintervento.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('assistenza::richiesteinterventi.title.richiesteinterventi')]));
    }

    //Riapri ticket chiuso
    public function riapriTicket(RichiesteIntervento $richiesteintervento)
    {
        if($richiesteintervento->stato == 3 && auth_user()->hasAccess('assistenza.richiesteinterventi.edit')){
            $azione_chiusura = RichiesteInterventoAzione::where('ticket_id', $richiesteintervento->id)->where('tipo', 3)->first();
            $azione_chiusura->tipo = 2;
            $azione_chiusura->save();
            $richiesteintervento->stato = 2;
            $richiesteintervento->save();
            return redirect()->route('admin.assistenza.richiesteintervento.edit', $richiesteintervento->id)
            ->withSuccess('Hai riaperto il ticket con successo.');
        } else {
            return redirect()->route('admin.assistenza.richiesteintervento.read', $richiesteintervento->id)
            ->withError('Impossibile eseguire la riapertura del ticket.');
        }
    }

    //Aggiungi referente
    public function aggiungiReferente(Request $request)
    {
        if($request->ajax()){
            $cliente = Clienti::find($request->cliente_id);
            if(!empty($cliente) && $request->contatto_email !== null && $request->contatto_numero !== null && $request->contatto_nome !== null){
                $full_name = explode(" ", $request->contatto_nome);
                $create = [
                    'nome' => (!empty($full_name[0]) ? $full_name[0] : ''),
                    'cognome' => (!empty($full_name[1]) ? $full_name[1] : ''),
                    'telefono' => $request->contatto_numero,
                    'email' => $request->contatto_email,
                    'mansione' => null,
                    'cliente_id' => $request->cliente_id
                ];

                $referente = ClienteReferenti::create($create);

                // Log
                activity(session('azienda'))
                    ->performedOn($referente)
                    ->withProperties($create)
                    ->log('created');

                return response()->json(['success' => $referente->email]);
            }
        }
    }

    // Export excel
    public function exportExcel(Request $request)
    {
        if(empty($request->all()))
        {
            $res['stato'] = 1;
            $res['order']['by'] = 'created_at';
            $res['order']['sort'] = 'desc';
            $request->merge($res);
        }

        $clienti = [0 => ''] + Clienti::all()->pluck('ragione_sociale', 'id')->toArray();
        $aree = [0 => ''] + Area::pluck('titolo', 'id')->toArray();
        $destinatari = [0 => ''] + User::all()->pluck('full_name', 'id')->toArray();

        // Visualizzo tutti i ticket se sono admin
        if(auth_user()->hasAccess('assistenza.richiesteinterventi.exportexcel'))
        {
            $richiesteinterventi = RichiesteIntervento::filter($request->all())->get();
        }
        else // Visualizzo solo i miei ticket oppure quelli del partner
        {
            //PARTNERS
            $partners = Auth::user()->profile->partner;
            $partners = json_decode($partners);

            if(empty($partners))
            {
                $richiesteinterventi = RichiesteIntervento::filter($request->all())
                    ->whereHas('destinatari', function($q) {
                        $q->where('user_id', Auth::id());
                    });
            }
            else
            {
                $richiesteinterventi = [];
                $richiesteinterventi_raw = RichiesteIntervento::filter($request->all())->get();
                //->paginateFilter(config('wecore.pagination.limit'));

                if(!empty($richiesteinterventi_raw) && count($richiesteinterventi_raw) > 0) {
                    foreach($richiesteinterventi_raw as $richiesta) {
                        $partner_cliente = $richiesta->cliente->aree;
                        $utenti_gruppo = $richiesta->destinatari->pluck('id')->toArray();

                        if(in_array(Auth::id(), $utenti_gruppo)) {
                            $richiesteinterventi[] = $richiesta;
                        }
                        elseif(!empty($partner_cliente)) {
                            foreach ($partner_cliente as $cliente_partner)  {
                                if(in_array($cliente_partner, $partners) ) {
                                    if(!in_array($richiesta, $richiesteinterventi)) {
                                        $richiesteinterventi[] = $richiesta;
                                    }
                                }
                            }
                        }
                    }
                }

                $richieste_ids = collect($richiesteinterventi)->pluck('id')->toArray();
                $richiesteinterventi = RichiesteIntervento::filter($request->all())
                    ->whereIn('id', $richieste_ids)->get();
            }
        }
        ob_clean();
        return Excel::download(new RichiesteInterventoExport($richiesteinterventi), 'RichiesteIntervento.xlsx');
    }

    // Export excel
    public function exportPerDipendente(Request $request)
    {
        $dettaglio = $this->statistiche->statsPerDipendente($request);

        ob_clean();
        return Excel::download(new RichiesteInterventoDipendenteExport($dettaglio), 'Assistenza_Statistiche_Per_Dipendente.xlsx');
    }

    public function exportPerArea(Request $request)
    {
        $dettaglio = $this->statistiche->statsPerArea($request);

        ob_clean();
        return Excel::download(new RichiesteInterventoAreaExport($dettaglio), 'Assistenza_Statistiche_Per_Area.xlsx');
    }

    // Export excel
    public function exportMultiExcel(Request $request)
    {
        

        if(empty($request->all()))
        {
            $res['stato'] = 4;
            $res['data_apertura'] = '01/02/2021';
            $res['data_chiusura'] = '31/05/2021';
            $res['order']['by'] = 'created_at';
            $res['order']['sort'] = 'desc';
            $request->merge($res);
        }

        $clienti = Clienti::all();

        $zipname = public_path().'/uploads/'.uniqid().'.zip';
        $zip = new \ZipArchive;
        $zip->open($zipname, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        foreach($clienti as $cliente){
            $richiesteinterventi = RichiesteIntervento::filter($request->all())->where('cliente_id', $cliente->id)->get();
            Excel::store(new RichiesteInterventoExport($richiesteinterventi), 'public/uploads/'.$cliente->ragione_sociale.'.xlsx');
            if(file_exists(public_path().'/uploads/'.$cliente->ragione_sociale.'.xlsx')){
                $zip->addFile(public_path().'/uploads/'.$cliente->ragione_sociale.'.xlsx',$cliente->ragione_sociale.'.xlsx');  
            }
        }

        $zip->close();

        foreach($clienti as $cliente){
            File::delete(public_path().'/uploads/'.$cliente->ragione_sociale.'.xlsx');   
        }

        return response()->download($zipname);
    }

    // Export excel
    public function exportStatsMultiExcel(Request $request)
    {      
        if(empty($request->all()))
        {
            $res['data_apertura'] = '01/04/2021';
            $res['data_chiusura'] = '31/06/2021';
            $res['order']['by'] = 'created_at';
            $res['order']['sort'] = 'desc';
            $request->merge($res);
        }

        $clienti = Clienti::all();

        $dettaglio = array();

        $dipendenti = User::all();
        $aree = Area::all();

        $zipname = public_path().'/exports/'.uniqid().'.zip';
        $zip = new \ZipArchive;
        $zip->open($zipname, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        foreach($clienti as $cliente){
            $richiesteintervento = RichiesteIntervento::filter($request->all())->where('cliente_id', $cliente->id)->get();
            if($richiesteintervento->count() > 0)
            {
            
                //Dipendenti
                foreach($dipendenti as $dipendente) {
                    foreach($aree as $area){

                        if($richiesteintervento->where('area_id', $area->id)->count() == 0)
                            continue;

                        if(empty($dettaglio[$dipendente->id]))
                            $dettaglio[$dipendente->id] = array(); 

                        $dettaglio[$dipendente->id][$area->id] = array(); 
                        $dettaglio[$dipendente->id][$area->id]['tempo_lavorazione'] = 0;
                        $dettaglio[$dipendente->id][$area->id]['tickets'] = 0;
                        foreach($richiesteintervento->where('area_id', $area->id) as $richiesta)
                        {
                            if(!empty($richiesta->statistiche['tempo_lavorazione_operatori'][$dipendente->id]))
                            {
                                $dettaglio[$dipendente->id][$area->id]['tempo_lavorazione'] += (int) $richiesta->statistiche['tempo_lavorazione_operatori'][$dipendente->id];
                                $dettaglio[$dipendente->id][$area->id]['tickets']++;
                            }
                        }
                        if(empty($dettaglio[$dipendente->id][$area->id]['tempo_lavorazione']))
                        {
                            unset($dettaglio[$dipendente->id][$area->id]);
                        } else {
                            $dettaglio[$dipendente->id][$area->id]['titolo'] = $area->titolo;
                            $dettaglio[$dipendente->id][$area->id]['dipendente'] = $dipendente->full_name;  
                            $dettaglio[$dipendente->id][$area->id]['tempo_lavorazione_media'] = round($dettaglio[$dipendente->id][$area->id]['tempo_lavorazione'] / $dettaglio[$dipendente->id][$area->id]['tickets']);         
                        }
                        if(empty($dettaglio[$dipendente->id]))
                        {
                            unset($dettaglio[$dipendente->id]);
                        }
                    }
                }

                if(!empty($dettaglio))
                {
                    Excel::store(new RichiesteInterventoDipendenteExport($dettaglio), 'public/exports/'.$cliente->ragione_sociale.'_dipendenti.xlsx');
                    if(file_exists(public_path().'/exports/'.$cliente->ragione_sociale.'_dipendenti.xlsx')){
                        $zip->addFile(public_path().'/exports/'.$cliente->ragione_sociale.'_dipendenti.xlsx',$cliente->ragione_sociale.'_dipendenti.xlsx');  
                    }
                }

                $dettaglio = array();

                //Aree
                foreach($aree as $area)
                {
                    if($richiesteintervento->where('area_id', $area->id)->count() == 0)
                        continue;
        
                    if(empty($dettaglio[$area->id]))
                        $dettaglio[$area->id] = array();
        
                    $dettaglio[$area->id]['titolo'] = $area->titolo;
                    $dettaglio[$area->id]['aperti'] = $richiesteintervento->where('area_id', $area->id)->where('stato', '<>', 3)->count();
                    $dettaglio[$area->id]['chiusi'] = $richiesteintervento->where('area_id', $area->id)->where('stato', 3)->count();
                    $dettaglio[$area->id]['tickets'] = $richiesteintervento->where('area_id', $area->id)->count();
                    $dettaglio[$area->id]['tempo_lavorazione_totale'] = 0;
                    $dettaglio[$area->id]['tempo_risoluzione_totale'] = 0;
                    $dettaglio[$area->id]['tempo_risoluzione_con_sospensioni'] = 0;
                    $dettaglio[$area->id]['tempo_lavorazione_media'] = 0;
                    $dettaglio[$area->id]['tempo_risoluzione_media'] = 0;
                    $dettaglio[$area->id]['tempo_risoluzione_con_sospensioni_media'] = 0;
        
                    foreach($richiesteintervento->where('area_id', $area->id) as $richiesta)
                    {
                        if(!empty($richiesta->statistiche))
                        {
                            $dettaglio[$area->id]['tempo_lavorazione_totale'] += (int) $richiesta->statistiche['tempo_lavorazione_totale'];
        
                            if($dettaglio[$area->id]['chiusi'] > 0)
                                $dettaglio[$area->id]['tempo_risoluzione_totale'] += (int) $richiesta->statistiche['tempo_risoluzione'];
                                $dettaglio[$area->id]['tempo_risoluzione_con_sospensioni'] += (int) $richiesta->statistiche['tempo_risoluzione_con_sospensioni'];
                        }
                    }
        
                    if($dettaglio[$area->id]['tickets'] > 0)
                        $dettaglio[$area->id]['tempo_lavorazione_media'] = round($dettaglio[$area->id]['tempo_lavorazione_totale'] / $dettaglio[$area->id]['tickets']);
        
                    
                    if($dettaglio[$area->id]['chiusi'] > 0)
                        $dettaglio[$area->id]['tempo_risoluzione_media'] = round($dettaglio[$area->id]['tempo_risoluzione_totale'] / $dettaglio[$area->id]['chiusi']);
        
                    if($dettaglio[$area->id]['chiusi'] > 0 && $dettaglio[$area->id]['tempo_risoluzione_con_sospensioni'] > 0)            
                        $dettaglio[$area->id]['tempo_risoluzione_con_sospensioni_media'] = round($dettaglio[$area->id]['tempo_risoluzione_con_sospensioni'] / $dettaglio[$area->id]['chiusi']);
        
                    if($dettaglio[$area->id]['tickets'] == 0)
                        unset($dettaglio[$area->id]);
                }

                if(!empty($dettaglio))
                {
                    Excel::store(new RichiesteInterventoAreaExport($dettaglio), 'public/exports/'.$cliente->ragione_sociale.'_aree.xlsx');
                    if(file_exists(public_path().'/exports/'.$cliente->ragione_sociale.'_aree.xlsx')){
                        $zip->addFile(public_path().'/exports/'.$cliente->ragione_sociale.'_aree.xlsx',$cliente->ragione_sociale.'_aree.xlsx');  
                    }                    
                }
            }
        }

        $zip->close();

        return response()->download($zipname);
    }

}
