<?php

namespace Modules\Assistenza\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Assistenza\Entities\TicketIntervento;
use Modules\Assistenza\Http\Requests\CreateTicketInterventoRequest;
use Modules\Assistenza\Http\Requests\UpdateTicketInterventoRequest;
use Modules\Assistenza\Repositories\TicketInterventoRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\User\Entities\Sentinel\User;
use Modules\Profile\Entities\Gruppo;
use Modules\Profile\Entities\Area;
use Modules\Amministrazione\Entities\Clienti;
use Modules\Commerciale\Entities\Ordinativo;
use Modules\Commerciale\Entities\OrdinativoGiornate;
use Modules\Assistenza\Entities\TicketInterventoVoci;
use Modules\Assistenza\Entities\RichiesteIntervento;
use Modules\Tasklist\Entities\Attivita;
use Modules\Tasklist\Entities\Timesheet;
use Modules\Profile\Entities\Procedura;

use Modules\Commerciale\Entities\Offerta;

use Maatwebsite\Excel\Facades\Excel;
use Modules\Export\Entities\TicketInterventoExport;

use Carbon\Carbon;
use PDF;

class TicketInterventoController extends AdminBaseController
{
    /**
     * @var TicketInterventoRepository
     */
    private $ticketintervento;

    public function __construct(TicketInterventoRepository $ticketintervento)
    {
        parent::__construct();

        $this->ticketintervento = $ticketintervento;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $res['order']['by'] = 'data_intervento';
        $res['order']['sort'] = 'desc';
        $request->merge($res);

        $ticketinterventi = TicketIntervento::filter($request->all())
                            ->with('voci', 'ordinativo.giornate', 'created_user', 'gruppo', 'cliente')
                            ->where('assistenza__ticketinterventi.azienda', session('azienda'))
                            ->paginateFilter(config('wecore.pagination.limit'));


        $utenti = [0 => 'tutti'] + User::all()->pluck('full_name', 'id')->toArray(); 

        $clienti = [0 => 'tutti'] + Clienti::all()->pluck('ragione_sociale', 'id')->toArray();

        $ordinativi_ids = Offerta::when($request->filled('cliente') && !empty($request->cliente), function ($query) use ($request) {
            $query->where('cliente_id', '=', $request->cliente);
        })->pluck('ordinativo_id')->toArray();
        
        $ordinativi = [0 => ''] + Ordinativo::active()->whereIn('id', $ordinativi_ids)->pluck('oggetto', 'id')->toArray();

        $attivita = [0 => 'tutti'] + Gruppo::all()->pluck('nome', 'id')->toArray();

        $request->flash();

        return view('assistenza::admin.ticketinterventi.index', compact('ticketinterventi','utenti','clienti','ordinativi','attivita'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $clienti = [0 => ''];
        $clienti = $clienti + Clienti::pluck('ragione_sociale', 'id')
                                    ->toArray();

        $ordinativi = [];

        //$aree_di_intervento = Area::all()->pluck('titolo','id')->toArray();
        //$json_aree = base64_encode(Area::all()->toJson());
        //$json_gruppi = base64_encode(Gruppo::all()->toJson());
        //dd($aree_di_intervento);

        //$gruppo_attivita = Gruppo::all()->pluck('nome', 'id') ->toArray();

        $procedure = Procedura::all()->pluck('titolo','id')->toArray();
        $aree_di_intervento = [];
        $gruppo_attivita = [];

        /*$
        if(!empty($request->ordinativo_id))
        {
            $gruppi = $gruppi + Gruppo::whereIn('id', Ordinativo::find($request->ordinativo_id)->giornate->where('quantita_residue', '>', 0)->pluck('gruppo_id')->toArray())
                            ->pluck('nome', 'id')
                            ->toArray();
        }*/

        $ticket = new TicketIntervento;

        return view('assistenza::admin.ticketinterventi.create', compact('clienti','ordinativi','gruppo_attivita','ticket','procedure','aree_di_intervento'));
    }

    public function createRapportino($id_richiestaintervento)
    {
        $richiesta_intervento = RichiesteIntervento::find($id_richiestaintervento);

        //$ticketintervento_fields = TicketIntervento::find($id_richiestaintervento);

        $vuoto = [0 => ''];

        $clienti = $vuoto + Clienti::pluck('ragione_sociale', 'id')->toArray();
        $ordinativi = $vuoto + Ordinativo::pluck('oggetto', 'id')->toArray();
        $procedure = Procedura::all()->pluck('titolo','id')->toArray();
        $aree_di_intervento = $vuoto + Area::pluck('titolo', 'id')->toArray();
        $gruppo_attivita = $vuoto + Gruppo::pluck('nome', 'id')->toArray();

        $ticket = new TicketIntervento;

        return view('assistenza::admin.ticketinterventi.create', compact('clienti','ordinativi','gruppo_attivita','ticket','procedure','aree_di_intervento','richiesta_intervento'));
    }

    public function ajaxRequest(Request $request)
    {
        if($request->ajax())
        {
            $vuoto = [0 => ''];
            $ordinativi = $vuoto;
            $procedure = $vuoto;
            $aree = $vuoto;
            $gruppi = $vuoto;
            $gruppi_result = $vuoto;

            if(!empty($request->cliente_id))
            {

                $ordinativi = [0 => ''] + Ordinativo::where('cliente_id', '=', $request->cliente_id)->pluck('oggetto', 'id')->toArray();

            }

            if(!empty($request->ordinativo_id))
            {
                $gruppi_ids = Gruppo::whereIn('id', Ordinativo::find($request->ordinativo_id)->giornate->where('quantita_residue', '>', 0)->pluck('gruppo_id')->toArray())
                        ->pluck('nome', 'area_id')
                        ->toArray();

                $procedure = Procedura::whereHas('aree', function($query) use ($gruppi_ids) { //aree fa riferimento a un metodo dentro l'entities
                    $query->whereIn('id', array_keys($gruppi_ids));
                })->pluck('titolo', 'id')
                ->toArray();
            }

            if(!empty($request->ordinativo_id) && !empty($request->procedura_id)) // aree
            {
                $gruppi_ids = Gruppo::whereIn('id', Ordinativo::find($request->ordinativo_id)->giornate->where('quantita_residue', '>', 0)->pluck('gruppo_id')->toArray())
                        ->pluck('nome', 'area_id')
                        ->toArray();

                $aree = $aree + Area::whereIn('id', array_keys($gruppi_ids))
                                    ->where('procedura_id', $request->procedura_id)
                                    ->pluck('titolo', 'id')
                                    ->toArray();
            }

            if(!empty($request->ordinativo_id) && !empty($request->area_id)) // gruppi
            {
                $gruppi = $gruppi + Gruppo::whereIn('id', Ordinativo::find($request->ordinativo_id)->giornate->where('quantita_residue', '>', 0)->pluck('gruppo_id')->toArray())
                                    ->where('area_id', $request->area_id)
                                    ->pluck('nome', 'id')
                                    ->toArray();

                ; //db raw concat

                foreach($gruppi as $key => $gruppo)
                {
                    $quantita_tipo = OrdinativoGiornate::where('ordinativo_id', $request->ordinativo_id)
                                                        ->where('gruppo_id', $key)
                                                        ->first();

                    $gruppi_result[$key] = [
                        'titolo' => $gruppo,
                        'quantita_residue' => (get_if_exist($quantita_tipo, 'tipo') !== null) ? get_if_exist($quantita_tipo, 'quantita_residue') : '',
                        'tipo' => (get_if_exist($quantita_tipo, 'tipo') !== null) ? config('commerciale.interventi.tipi')[get_if_exist($quantita_tipo, 'tipo')] : ''
                    ];
                }
            }

            ob_clean();
            return response()->json([
                'ordinativi' => $ordinativi,
                'procedure' => $procedure,
                'aree' => $aree,
                'gruppi' => $gruppi_result
            ]) ;
        }
        else
            return response()->view('404');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateTicketInterventoRequest $request
     * @return Response
     */
    public function store(CreateTicketInterventoRequest $request)
    {
        $rules = TicketIntervento::getRules();
        $this->validate($request, $rules);

        $create = $request->all();
        $create['azienda'] = session('azienda');
        $create['created_user_id'] = Auth::id();
        $create['updated_user_id'] = Auth::id();
        $create['n_di_intervento'] = TicketIntervento::get_next_n_ticket();
        //$create['area_di_intervento'] = Area::id();
       // $create['attivita'] = Gruppo::id();
        //$create['procedura'] = Procedura::id();

       //dd($create);
        $ticketintervento = TicketIntervento::create($create); 

        // Log
        activity(session('azienda'))
            ->performedOn($ticketintervento)
            ->withProperties($create)
            ->log('created');

        return redirect()->route('admin.assistenza.ticketintervento.edit', $ticketintervento->id)
            ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('assistenza::ticketinterventi.title.ticketinterventi')]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  TicketIntervento $ticketintervento
     * @return Response
     */
    public function edit(TicketIntervento $ticketintervento, Request $request)
    {

        if($ticketintervento->azienda != session('azienda'))
            return redirect()->route('admin.assistenza.ticketintervento.index')
                    ->withWarning('AVVISO: non puoi accedere a questo ticket intervento con l\'azienda ' . session('azienda'));

        $clienti = Clienti::pluck('ragione_sociale', 'id')
                            ->toArray();

        $ordinativi = [0 => ''];
        if(!empty($request->cliente_id) || !empty($ticketintervento->cliente_id))
        {
          $cliente_id = !empty($request->cliente_id) ? $request->cliente_id : $ticketintervento->cliente_id;

          $ordinativi = [0 => ''] + Ordinativo::where('cliente_id', '=', $cliente_id)->pluck('oggetto', 'id')->toArray();
        }

       //dd('co');

         $aree_di_intervento = Area::all()->pluck('titolo','id')->toArray();
         $json_aree = base64_encode(Area::all()->toJson());
         $json_gruppi = base64_encode(Gruppo::all()->toJson());
         //dd($aree_di_intervento);

         $gruppo_attivita = Gruppo::all()->pluck('nome', 'id') ->toArray();
         $procedure = Procedura::all()->pluck('titolo','id')->toArray();


        $gruppi = [0 => ''];
        if(!empty($request->ordinativo_id))
        {
            $gruppi = $gruppi + Gruppo::whereIn('id', Ordinativo::find($request->ordinativo_id)->giornate->where('quantita_residue', '>', 0)->pluck('gruppo_id')->toArray())
                            ->pluck('nome', 'id')
                            ->toArray();
        }
        else
        {
            $gruppi = $gruppi + Gruppo::whereIn('id', $ticketintervento->ordinativo->giornate->pluck('gruppo_id')->toArray())
                            ->pluck('nome', 'id')
                            ->toArray();
        }

        $tipologie = config('tasklist.timesheets.tipologie');

        return view('assistenza::admin.ticketinterventi.edit', compact('ticketintervento','clienti','ordinativi','gruppi','aree_di_intervento','gruppo_attivita','procedure','json_aree','json_gruppi', 'tipologie'));
    }





    public function read(TicketIntervento $ticketintervento, Request $request)
    {
        if($ticketintervento->azienda != session('azienda'))
            return redirect()->route('admin.assistenza.ticketintervento.index')
                    ->withWarning('AVVISO: non puoi accedere a questo ticket intervento con l\'azienda ' . session('azienda'));

        $clienti = Clienti::pluck('ragione_sociale', 'id')
                            ->toArray();



        $ordinativi = [0 => ''];
        if(!empty($request->cliente_id) || !empty($ticketintervento->cliente_id))
        {
          $cliente_id = !empty($request->cliente_id) ? $request->cliente_id : $ticketintervento->cliente_id;

          $ordinativi = $ordinativi + Ordinativo::where('azienda', session('azienda'))
                              ->where('cliente_id', $cliente_id)
                              ->pluck('oggetto', 'id')
                              ->toArray();
        }



        $aree_di_intervento = Area::all()->pluck('titolo', 'id') ->toArray();
        $gruppo_attivita = Gruppo::all()->pluck('nome', 'id') ->toArray();
        $procedure = Procedura::all()->pluck('titolo','id')->toArray();




        $gruppi = [0 => ''];
        if(!empty($request->ordinativo_id))
        {
            $gruppi = $gruppi + Gruppo::whereIn('id', Ordinativo::find($request->ordinativo_id)->giornate->where('quantita_residue', '>', 0)->pluck('gruppo_id')->toArray())
                            ->pluck('nome', 'id')
                            ->toArray();
        }
        else
        {
            $gruppi = $gruppi + Gruppo::whereIn('id', $ticketintervento->ordinativo->giornate->pluck('gruppo_id')->toArray())
                            ->pluck('nome', 'id')
                            ->toArray();
        }

       //dd($gruppi);

       $tipologie = config('tasklist.timesheets.tipologie');


        return view('assistenza::admin.ticketinterventi.read', compact('ticketintervento','clienti','ordinativi','gruppi','aree_di_intervento','gruppo_attivita','procedure', 'tipologie'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TicketIntervento $ticketintervento
     * @param  UpdateTicketInterventoRequest $request
     * @return Response
     */
    public function update(TicketIntervento $ticketintervento, UpdateTicketInterventoRequest $request)
    {



        $rules = TicketIntervento::getRules();
        $this->validate($request, $rules);

        $update = $request->all();
        $update['cliente_id'] = Ordinativo::find($update['ordinativo_id'])->cliente_id;
        $update['updated_user_id'] = Auth::id();

        // $intervento_tipi = config('commerciale.interventi.tipi');
        //
        // $area_intervento = $ticketintervento->ordinativo->giornate->where('gruppo_id', $ticketintervento->gruppo_id)->first();
        //
        // $intervento_tipo = !empty($area_intervento) ? $intervento_tipi[$area_intervento->tipo] : '';

        //if(strtolower($intervento_tipo) == 'giornate')
        $ticketintervento->voci = $ticketintervento->giornate();

        foreach ($update['tickets'] as $key => $voce)
        {
            if(!empty($voce['descrizione']) && !empty($voce['data_intervento']) && !empty($voce['quantita']) )
            {
                if($key == 'add')
                {
                    $ordinativo_giornate = $ticketintervento->ordinativo->get_giornate_by_gruppo($ticketintervento->gruppo_id);

                    $numero_giornate = $ordinativo_giornate['quantita'];
                    $giornate_lavorate = $ticketintervento->voci->sum('quantita') + $voce['quantita'];

                    $giornate_residue = $numero_giornate - $giornate_lavorate;

                    if($giornate_residue >= 0)
                    {
                        $n = new TicketInterventoVoci($voce);
                        $ticketintervento->voci()->save($n);
                    }
                }
                else
                {
                    $n = TicketInterventoVoci::find($key);
                    $n->update($voce);
                }
            }
        }

        unset($ticketintervento->voci);
        $this->ticketintervento->update($ticketintervento, $update);

        $this->updateGiornateResidue(TicketIntervento::find($ticketintervento->id));

        // Log
        activity(session('azienda'))
            ->performedOn($ticketintervento)
            ->withProperties($update)
            ->log('updated');

        return redirect()->route('admin.assistenza.ticketintervento.edit',$ticketintervento->id)
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('assistenza::ticketinterventi.title.ticketinterventi')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  TicketIntervento $ticketintervento
     * @return Response
     */
    public function destroy(TicketIntervento $ticketintervento)
    {
        $this->ticketintervento->destroy($ticketintervento);

        // Log
        activity(session('azienda'))
            ->performedOn($ticketintervento)
            ->withProperties(json_encode($ticketintervento))
            ->log('destroyed');

        return redirect()->route('admin.assistenza.ticketintervento.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('assistenza::ticketinterventi.title.ticketinterventi')]));
    }

    public function destroyVoce($id)
    {
        $voce = TicketInterventoVoci::find($id);

        TicketInterventoVoci::destroy($id);

        $this->updateGiornateResidue($voce->ticketIntervento);

        return redirect()->route('admin.assistenza.ticketintervento.edit', $voce->ticket_id)
          ->withSuccess('Voce eliminata con successo');
    }

    public function updateGiornateResidue($ticket_intervento)
    {
        // Giornate/Ore
        $ordinativo_giornate = $ticket_intervento->ordinativo->get_giornate_by_gruppo($ticket_intervento->gruppo_id);

        $numero_giornate = $ordinativo_giornate['quantita'];

        // $intervento_tipi = config('commerciale.interventi.tipi');

        // $area_intervento = $ticket_intervento->ordinativo->giornate->where('gruppo_id', $ticket_intervento->gruppo_id)->first();

        // $intervento_tipo = !empty($area_intervento) ? $intervento_tipi[$area_intervento->tipo] : '';

        // if(strtolower($intervento_tipo) == 'giornate')
        $ticket_intervento->voci = $ticket_intervento->giornate();

        $giornate_lavorate = $ticket_intervento->voci->sum('quantita');

        $giornate_residue = $numero_giornate - $giornate_lavorate;

        $update_giornate = OrdinativoGiornate::find($ordinativo_giornate['id']);

        if(!empty($update_giornate))
        {
            $update_giornate->quantita_residue = $giornate_residue;
            $update_giornate->save();
        }

        // Attività
        $attivita = $ticket_intervento->ordinativo->giornate->where('gruppo_id', $ticket_intervento->gruppo_id)->first();

        $attivita_id = !empty($attivita->attivita) ? $attivita->attivita : null;

        if(!empty($attivita_id) && is_numeric($attivita_id))
        {
            $percentuale = (100 * $giornate_lavorate) / $numero_giornate;
            $attivita = Attivita::find($attivita_id);
            $attivita->percentuale_completamento = $percentuale;
            if($percentuale == 100){
                $attivita->stato = 2; 
                // Creazione nota di completamento
                save_meta(['note' => 'Ho completato l\'attività in data ' . get_date_hour_ita(Carbon::now()) . '.'], $attivita);
                // Data chiusura
                $attivita->data_chiusura =  date('Y-m-d h:i:s');
                $oggetto = 'Attività completata - ' . $attivita->oggetto;
                $messaggio = 'Salve,<br> è stata completata l\'attività in oggetto.<br><br>Puoi visualizzarla al seguente link:<br><a href="' . route('admin.tasklist.attivita.read', $attivita->id) . '">' . route('admin.tasklist.attivita.read', $attivita->id) . '</a>';
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
            $attivita->save();
        }
    }

    public function generaPdf($id)
    {

        $ticket = TicketIntervento::findOrFail($id);

        $fname = 'Ticket ' . session('azienda').' '. $ticket->n_di_intervento;

        $titolo = 'Rapporto Intervento';

        $pdf = PDF::loadView('assistenza::admin.ticketinterventi.genera_pdf', compact('ticket','titolo'))->setPaper('a4');

        //$pdf->setOptions(['isRemoteEnabled' => true]);

        return $pdf->stream($fname.'.pdf');
    }

    public function checkOrdinativo()
    {
        if(!empty($_REQUEST['cliente_id']))
        {
            $cliente_id = $_REQUEST['cliente_id'];
            $ordinativi = Ordinativo::where('azienda', session('azienda'))
                              ->whereHas('offerta', function($q) use($cliente_id) {
                                $q->where('cliente_id', $cliente_id);
                              })->get()->toJson();
            return base64_encode($ordinativi);
        }
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

        $ticket = TicketIntervento::findOrFail($data['ticket_id']);
        if(empty($ticket)){
            return redirect()->back()->withErrors('Ticket inesistente.')->withInput();
        }

        $create = [
            'azienda' => $ticket->azienda,
            'cliente_id' => $ticket->cliente_id,
            'ordinativo_id' => $ticket->ordinativo_id,
            'procedura_id' => $ticket->procedura_id,
            'area_id' => $ticket->area_di_intervento_id,
            'gruppo_id' => $ticket->gruppo_id,
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
        $rapporti = TicketIntervento::filter($request->all())
                                            ->with('voci', 'ordinativo.giornate', 'created_user', 'gruppo', 'cliente')
                                            ->where('assistenza__ticketinterventi.azienda', session('azienda'))
                                            ->paginateFilter(config('wecore.pagination.limit'));

        ob_clean();
        return Excel::download(new TicketInterventoExport($rapporti), 'RapportiIntervento.xlsx');
    }

}
