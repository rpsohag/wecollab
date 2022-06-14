<?php

namespace Modules\Commerciale\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Commerciale\Entities\SegnalazioneOpportunita;
use Modules\Commerciale\Entities\AnalisiVendita;
use Modules\Commerciale\Entities\CensimentoCliente;
use Modules\Commerciale\Http\Requests\CreateSegnalazioneOpportunitaRequest;
use Modules\Commerciale\Http\Requests\UpdateSegnalazioneOpportunitaRequest;
use Modules\Commerciale\Repositories\SegnalazioneOpportunitaRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;

use Modules\Amministrazione\Entities\Clienti;
use Modules\Profile\Entities\Procedura;
use Modules\Profile\Entities\Area;
use Modules\Profile\Entities\Gruppo;
use Modules\User\Entities\Sentinel\User;
use Modules\Tasklist\Entities\Attivita;
use Modules\Export\Entities\SegnalazioneOpportunitaExport;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use DB;

class SegnalazioneOpportunitaController extends AdminBaseController
{
    /**
     * @var SegnalazioneOpportunitaRepository
     */
    private $segnalazioneopportunita;

    public function __construct(SegnalazioneOpportunitaRepository $segnalazioneopportunita)
    {
        parent::__construct();

        $this->segnalazioneopportunita = $segnalazioneopportunita;
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
          $res['order']['by'] = 'numero';
          $res['order']['sort'] = 'desc';
          $request->merge($res);
        }

        if(Auth::user()->inRole('admin') || Auth::user()->inRole('commerciale')){
            $segnalazioniopportunita = SegnalazioneOpportunita::where('azienda', session('azienda'))->filter($request->all())->paginateFilter(config('wecore.pagination.limit'));
        } else {
            $segnalazioniopportunita = SegnalazioneOpportunita::where('azienda', session('azienda'))->where('created_user_id', Auth::id())->filter($request->all())->paginateFilter(config('wecore.pagination.limit'));
        }

        $clienti = [''] + Clienti::commerciali()->pluck('ragione_sociale', 'id')->toArray();
        $utenti = [''] + User::select(DB::raw("CONCAT(first_name, ' ', last_name) AS name"),'id')->pluck('name', 'id')->toArray();

        $auth_user = auth_user();

        $request->flash();

        return view('commerciale::admin.segnalazioniopportunita.index', compact('segnalazioniopportunita', 'clienti', 'utenti', 'auth_user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $segnalazione = new SegnalazioneOpportunita;
        $clienti = [''] + Clienti::pluck('ragione_sociale', 'id')->toArray();
        $procedure = Procedura::all();
        $aree = [''] + Area::all()->pluck('titolo', 'id')->toArray(); 
        $attivita = [''] + Gruppo::all()->pluck('nome', 'id')->toArray();
        $utenti = [''] + User::all()->pluck('full_name', 'id')->toArray();

        if($request->filled('cliente_id'))
            $segnalazione->cliente_id = $request->cliente_id;

        return view('commerciale::admin.segnalazioniopportunita.create', compact('segnalazione', 'clienti', 'procedure', 'aree', 'attivita', 'utenti'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateSegnalazioneOpportunitaRequest $request
     * @return Response
     */
    public function store(CreateSegnalazioneOpportunitaRequest $request)
    {
        $rules = SegnalazioneOpportunita::getRules();

        if($request->filled("cliente_id") && !$request->filled("cliente")){
            $request->merge(['cliente' => Clienti::find($request->cliente_id)->ragione_sociale]);
        }

        $this->validate($request, $rules);

        $insert = $request->all();
        $cliente = Clienti::whereId($request->cliente_id)->first();

        $insert['azienda'] = session('azienda');
        $insert['numero'] = SegnalazioneOpportunita::get_numero_new();
        $insert['stato_id'] = 0;

        if(!empty($insert['crea_per'])){
            $insert['created_user_id'] = $insert['crea_per'];
            $insert['updated_user_id'] = $insert['crea_per'];  
            unset($insert['crea_per']);
        } else {
            $insert['created_user_id'] = Auth::id();
            $insert['updated_user_id'] = Auth::id();            
        }

        $segnalazione = SegnalazioneOpportunita::create($insert);

        //Dropzone
        dropzone_files_save('commerciale', $segnalazione->id, 'SegnalazioneOpportunita', 'Commerciale', $request);

        // Log
        activity(session('azienda'))
            ->performedOn($segnalazione)
            ->withProperties($insert)
            ->log('created');

        // Email
        $id_direttore_commerciale = setting('admin::direttore_commerciale');
        $id_segreteria_commerciale = setting('admin::segreteria_commerciale');
        if(!empty($id_direttore_commerciale) && !empty($id_segreteria_commerciale)){
            $direttore_commerciale = User::find($id_direttore_commerciale);
            $segreteria_commerciale = User::find($id_segreteria_commerciale);
            $oggetto = 'Nuova segnalazione da ' . $segnalazione->created_user->full_name . ' - ' . $segnalazione->oggetto;
            $messaggio = 'Salve ' . $direttore_commerciale->full_name . ' la informiamo che è stata inviata una nuova segnalazione di opportunità commerciale da ' . $segnalazione->created_user->full_name . '.' . '<br><strong>Oggetto</strong>: ' . $segnalazione->oggetto . '<br><strong>Cliente</strong>: ' . $segnalazione->cliente . '<br><br>Per visualizzare i dettagli clicca sul link di seguito:<br><a href="' . route('admin.commerciale.segnalazioneopportunita.edit', $segnalazione->id) . '">' . route('admin.commerciale.segnalazioneopportunita.edit', $segnalazione->id) . '</a>';
            mail_send([$direttore_commerciale->email, $segreteria_commerciale->email], $oggetto, $messaggio);
        }

        return redirect()->route('admin.commerciale.segnalazioneopportunita.edit', $segnalazione->id)
            ->withSuccess('Segnalazione commerciale creata con successo.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  SegnalazioneOpportunita $segnalazioneopportunita
     * @return Response
     */
    public function edit(SegnalazioneOpportunita $segnalazione)
    {
        if($segnalazione->stato_id == 0 || $segnalazione->stato_id == 2)
        {
            $clienti = Clienti::all()->pluck('ragione_sociale', 'id')->toArray();
            $procedure = Procedura::all();
            $aree = Area::all()->pluck('titolo', 'id')->toArray(); 
            $attivita = Gruppo::all()->pluck('nome', 'id')->toArray();

            $spesa_totale['totale'] = 0;

            if(!empty($segnalazione->checklist)){
                foreach($segnalazione->checklist as $key => $value){
                  if(empty($spesa_totale[$value->procedura_id]))
                    $spesa_totale[$value->procedura_id] = 0;
                  $spesa_totale['totale']+= (float) clean_currency($value->spesa);
                  $spesa_totale[$value->procedura_id] += (float) clean_currency($value->spesa);
                }
            }

            $censimenti = [''] + CensimentoCliente::pluck('cliente', 'id')->toArray();

            $commerciali = [''] + User::whereIn('id', User::elencoCommerciali()->pluck('id')->toArray())->get()->pluck('full_name', 'id')->toArray();

            return view('commerciale::admin.segnalazioniopportunita.edit', compact('segnalazione', 'clienti', 'procedure', 'censimenti', 'aree', 'attivita', 'spesa_totale', 'commerciali'));
        }
        else
            return redirect()->route('admin.commerciale.segnalazioneopportunita.index')->withError('La segnalazione d\'opportunità è stata rifiutata/approvata.');
    }

	 /**
     * Show  the specified resource.
     *
     * @param  SegnalazioneOpportunita $segnalazioneopportunita
     * @return Response
     */
    public function read(SegnalazioneOpportunita $segnalazione)
    {
        $clienti = Clienti::pluck('ragione_sociale', 'id')->toArray();
        $procedure = Procedura::all();

        $spesa_totale['totale'] = 0;

        if(!empty($segnalazioneopportunita->checklist)){
            foreach($segnalazioneopportunita->checklist as $key => $value){
              if(empty($spesa_totale[$value->procedura_id]))
                $spesa_totale[$value->procedura_id] = 0;
              $spesa_totale['totale']+= (float) clean_currency($value->spesa);
              $spesa_totale[$value->procedura_id] += (float) clean_currency($value->spesa);
            }
        }

        $censimenti = CensimentoCliente::pluck('cliente', 'id')->toArray();

        return view('commerciale::admin.segnalazioniopportunita.read', compact('segnalazione', 'clienti', 'procedure', 'censimenti', 'spesa_totale'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  SegnalazioneOpportunita $segnalazioneopportunita
     * @param  UpdateSegnalazioneOpportunitaRequest $request
     * @return Response
     */
    public function update(SegnalazioneOpportunita $segnalazione, UpdateSegnalazioneOpportunitaRequest $request)
    {
        $rules = SegnalazioneOpportunita::getRules();
        $this->validate($request, $rules);

        $update = $request->all();

        $cliente = Clienti::whereId($request->cliente_id)->first();

        $update['updated_user_id'] = Auth::id();

        if(!empty($update['checklist'])){
            foreach($update['checklist'] as $key => $value){
                if(!$update['checklist'][$key]['nome'] && !$update['checklist'][$key]['telefono'] && !$update['checklist'][$key]['email'] && !$update['checklist'][$key]['spesa'] && !$update['checklist'][$key]['note'] && !$update['checklist'][$key]['area_id'] && !$update['checklist'][$key]['attivita_id'] && !$update['checklist'][$key]['procedura_id']){
                    unset($update['checklist'][$key]);
                }
            }
        }  

        $segnalazione->update($update);

        //Dropzone
        dropzone_files_save('commerciale', $segnalazione->id, 'SegnalazioneOpportunita', 'Commerciale', $request);

        // Log
        activity(session('azienda'))
            ->performedOn($segnalazione)
            ->withProperties($update)
            ->log('updated');

        return redirect()->route('admin.commerciale.segnalazioneopportunita.edit', $segnalazione->id)
            ->withSuccess('Segnalazione commerciale aggiornata con successo.');
    }

    /**
     * accept the specified resource.
     *
     * @param  SegnalazioneOpportunita $segnalazioneopportunita
     * @return Response
     */
    public function accept(Request $request)
    {
        $update['stato_id'] = 1;
        $update['commerciale_id'] = (!empty($request->commerciale_id) ? $request->commerciale_id : 0);
        $segnalazioneopportunita = SegnalazioneOpportunita::find($request->id);
        if(empty($segnalazioneopportunita->censimento())){
            return redirect()->route('admin.commerciale.segnalazioneopportunita.edit', $segnalazioneopportunita->id)
            ->withError('La segnalazione non ha un censimento cliente collegato.');            
        }
        $segnalazioneopportunita->update($update);

       // Log
        activity(session('azienda'))
            ->performedOn($segnalazioneopportunita)
            ->withProperties(json_encode($segnalazioneopportunita))
            ->log('accepted');

        return redirect()->route('admin.commerciale.analisivendita.create', ['censimentocliente_id' => $segnalazioneopportunita->censimento_id, 'segnalazioni_id' => $segnalazioneopportunita->id,'commerciale_id' => $segnalazioneopportunita->commerciale_id ])
            ->withSuccess('La segnalazione è stata accettata con successo. Crea un\' analisi vendita da collegare alla segnalazione.');
    }

    /**
     * reject the specified resource.
     *
     * @param  SegnalazioneOpportunita $segnalazioneopportunita
     * @return Response
     */
    public function reject(SegnalazioneOpportunita $segnalazioneopportunita)
    {
      //  $segnalazioneopportunita->delete();
        //dd($segnalazioneopportunita);

        $update['stato_id'] = 3;// rifiutata

        $segnalazioneopportunita = $this->segnalazioneopportunita->update($segnalazioneopportunita, $update);

       // Log
        activity(session('azienda'))
            ->performedOn($segnalazioneopportunita)
            ->withProperties(json_encode($segnalazioneopportunita))
            ->log('rejected');

        return redirect()->route('admin.commerciale.segnalazioneopportunita.index')
            ->withSuccess(trans('core::core.messages.resource reject', ['name' => trans('commerciale::segnalazioniopportunita.title.segnalazioniopportunita')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  SegnalazioneOpportunita $segnalazioneopportunita
     * @return Response
     */
    public function destroy(SegnalazioneOpportunita $segnalazioneopportunita)
    {
        $segnalazioneopportunita->delete();

        // Log
        activity(session('azienda'))
            ->performedOn($segnalazioneopportunita)
            ->withProperties(json_encode($segnalazioneopportunita))
            ->log('softDelete');

        return redirect()->route('admin.commerciale.segnalazioneopportunita.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('commerciale::segnalazioniopportunita.title.segnalazioniopportunita')]));
    }

 	public function updStato(Request $request)
    {
      // $rules = SegnalazioneOpportunita::getRules();
        //$this->validate($request, $rules);

 	//	dd($request->all());
		$segnalazioneopportunita =  SegnalazioneOpportunita::findOrFail($request->segnalazioneid);


	 	$gg['stato_id'] = $request ->statoid;
		$segnalazioneopportunita =  $this->segnalazioneopportunita->update($segnalazioneopportunita, $gg);

        activity(session('azienda'))
            ->performedOn($segnalazioneopportunita)
            ->withProperties($gg)
            ->log('updated');


        return  null ;/* redirect()->route('admin.commerciale.segnalazioneopportunita.edit', $segnalazioneopportunita->id)
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('commerciale::segnalazioniopportunita.title.segnalazioniopportunita')]));*/
    }

  	public function updCommerciale(Request $request)
    {
        // $rules = SegnalazioneOpportunita::getRules();
        //$this->validate($request, $rules);

        $segnalazioneopportunita =  SegnalazioneOpportunita::findOrFail($request->segnalazioneid);
        $gg['commerciale_id'] = $request ->commercialeid;
        $segnalazioneopportunita =  $this->segnalazioneopportunita->update($segnalazioneopportunita, $gg);
//->where('stato', '<>', 3)
      /*  $attivita = $segnalazioneopportunita->attivita()->first();

        $attivita->stato = 3;
        $attivita->save();
 */
        $attivita_collegata = $segnalazioneopportunita->attivita()->first();
        activity(session('azienda'))
            ->performedOn($segnalazioneopportunita)
            ->withProperties($gg)
            ->log('updated');

        //controllo se vi è già un attività collegata. in base al valore mandato il js capisce se aggiornare l'attività o crearla
        if(is_null($attivita_collegata )){
            $ret_value = 1 ;
        }else{
            //se vi è l'attività sostituisco il destinatario
            $attivita_collegata->users()->sync([$request ->commercialeid]);
            $pp['assegnatario_id'] = [$request ->commercialeid];

            activity(session('azienda'))
            ->performedOn($attivita_collegata)
            ->withProperties($pp)
            ->log('updated');


            $ret_value = 2;


        }

        return  json_encode(array('response'=> $ret_value ,'titolo'=> $segnalazioneopportunita-> oggetto ,'cliente'=> $segnalazioneopportunita-> cliente,'cliente_id'=> $segnalazioneopportunita-> cliente_id)) ;


    }

    public function restore($id)
    {
        $segnalazioneopportunita = SegnalazioneOpportunita::withTrashed()
                                                          ->where('id', $id)
                                                          ->first();
        $segnalazioneopportunita->restore();

        // Log
        activity(session('azienda'))
            ->performedOn($segnalazioneopportunita)
            ->withProperties(json_encode($segnalazioneopportunita))
            ->log('restored');

        return redirect()->route('admin.commerciale.segnalazioneopportunita.index')
            ->withSuccess('Segnalazione ripristinata con successo.');
    }

    // Export excel
    public function exportExcel(Request $request)
    {
        if(empty($request->all()))
        {
          $res['order']['by'] = 'numero';
          $res['order']['sort'] = 'desc';
          $request->merge($res);
        }

        if(Auth::user()->inRole('admin') || Auth::user()->inRole('commerciale')){
            $segnalazioniopportunita = SegnalazioneOpportunita::where('azienda', session('azienda'))->filter($request->all())->get();
        } else {
            $segnalazioniopportunita = SegnalazioneOpportunita::where('azienda', session('azienda'))->where('created_user_id', Auth::id())->filter($request->all())->get();
        }

      ob_clean();
      return Excel::download(new SegnalazioneOpportunitaExport($segnalazioniopportunita), session('azienda') . '_segnalazioni.xlsx');
    }

}
