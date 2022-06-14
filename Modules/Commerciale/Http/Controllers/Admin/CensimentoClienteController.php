<?php

namespace Modules\Commerciale\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\User\Entities\Sentinel\User;

use Modules\Commerciale\Entities\CensimentoCliente;
use Modules\Commerciale\Entities\CensimentoClienteReportViste;
use Modules\Commerciale\Entities\SegnalazioneOpportunita;
use Modules\Commerciale\Entities\Offerta;
use Modules\Commerciale\Http\Requests\CreateCensimentoClienteRequest;
use Modules\Commerciale\Http\Requests\UpdateCensimentoClienteRequest;
use Modules\Commerciale\Repositories\CensimentoClienteRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Profile\Entities\Procedura;

use Illuminate\Support\Facades\Auth;
use Modules\Amministrazione\Entities\Clienti;
use Modules\Profile\Entities\Area; 
use Modules\Profile\Entities\Gruppo; 
use DB;
use stdClass;
use PDF;

class CensimentoClienteController extends AdminBaseController
{
    /**
     * @var CensimentoClienteRepository
     */
    private $censimentocliente;

    public function __construct(CensimentoClienteRepository $censimentocliente)
    {
        parent::__construct();

        $this->censimentocliente = $censimentocliente;
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
          $res['order']['by'] = 'cliente';
          $res['order']['sort'] = 'asc';
          $request->merge($res);
        }

        $censimenticlienti = CensimentoCliente::whereHas('cliente', function($q){
          return $q; })->where('commerciale__censimenticlienti.azienda', session('azienda'))->filter($request->all())->paginateFilter(config('wecore.pagination.limit'));

        $request->flash();

        $clienti = [0 => ''] + Clienti::commerciali()->pluck('ragione_sociale', 'id')->toArray();

        $commerciali_id = User::elencoCommerciali()->pluck('id');
        $commerciali = [''] + User::whereIn('id', $commerciali_id)->get()->pluck('full_name', 'id')->toArray();

        return view('commerciale::admin.censimenticlienti.index', compact(['censimenticlienti', 'clienti', 'commerciali']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
	     if(empty($request->segnalazione_opportunita))
          return redirect()->back()
                          ->withWarning('AVVISO: per creare una nuovo Censimento Cliente deve essere creata prima una Segnalazione di Opportunità.');

        $enti = [''] + Clienti::pluck('ragione_sociale', 'id')->toArray();

        $censimentocliente = new CensimentoCliente();
        $censimentocliente -> fascia_abitanti = null;
     //   $censimentocliente = [];

        if(!empty($request))
        {
		      if($request->cliente_id > 0)
          {//aggiorno l'indirizzo partendo dal cliente se il cliente già esiste

            $ente = Clienti::findOrFail($request->cliente_id);

            if($ente){
              $censimentocliente->cliente = optional($ente)->ragione_sociale;
              $censimentocliente->indirizzo = optional($ente->sedeLegale())->indirizzo;
              $censimentocliente->citta = optional($ente->sedeLegale())->citta;
              $censimentocliente->provincia = optional($ente->sedeLegale())->provincia;
              $censimentocliente->cap = optional($ente->sedeLegale())->cap;
              $censimentocliente->nazione = optional($ente->sedeLegale())->nazione;
            }
          }

        }
        $procedure = Procedura::all();
        $procedure_list = $procedure->pluck('titolo', 'id')->toArray();
        $aree_foreach = Area::all();
        $aree = $aree_foreach->pluck('titolo', 'id')->toArray(); 
        $attivita = Gruppo::all()->pluck('nome', 'id')->toArray();

    		$id_segnalazione = -1 ;

    		if(!is_null($request->segnalazione_opportunita) )
    		{
    			if($request->segnalazione_opportunita > 0 )
    			{
            $id_segnalazione = $request->segnalazione_opportunita;
            $segnalazioniopportunita = SegnalazioneOpportunita::findOrFail($id_segnalazione);
            $censimentocliente->cliente = $segnalazioniopportunita -> cliente;
            $censimentocliente->cliente_id = $segnalazioniopportunita -> cliente_id;
            $censimentocliente -> referenti = [];
            foreach($aree_foreach as $key => $area)
            {
              foreach($segnalazioniopportunita->checklist as $checklist){
                if(!empty(collect($checklist)->referente) && collect($checklist)->referente !== null){
                  $rr[$area->id ]['nome']  =  get_if_exist($segnalazioniopportunita->checklist->{$area->id}, 'referente');
                  $rr[$area->id ]['note']   =  get_if_exist($segnalazioniopportunita->checklist->{$area->id}, 'note');
                  $rr[$area->id ]['telefono']   =  get_if_exist($segnalazioniopportunita->checklist->{$area->id}, 'telefono');
                  $rr[$area->id ]['email']   =  get_if_exist($segnalazioniopportunita->checklist->{$area->id}, 'email');
                  $rr[$area->id ]['costo']   =  get_if_exist($segnalazioniopportunita->checklist->{$area->id}, 'spesa_attuale');
                  $censimentocliente->setReferentiAttribute($rr);                  
                }
              }
              
            }
    			}
        }
        return view('commerciale::admin.censimenticlienti.create', compact('censimentocliente', 'enti', 'procedure', 'id_segnalazione', 'procedure_list', 'aree', 'attivita'));
      }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateCensimentoClienteRequest $request
     * @return Response
     */
    public function store(CreateCensimentoClienteRequest $request)
    {
        $rules = CensimentoCliente::getRules();
        $this->validate($request, $rules);

        $insert = $request->all();
        $insert['azienda'] = session('azienda');
        $insert['created_user_id'] = Auth::id();
        $insert['updated_user_id'] = Auth::id();

        $censimentocliente = $this->censimentocliente->create($insert);

 	/*	unset($insert['stato_id']);
		unset($insert['commerciale_id']);
	 */
	 	activity(session('azienda'))
            ->performedOn($censimentocliente)
            ->withProperties($insert)
            ->log('created');


	 	if(!empty($request))
        	{
		  	if($request->cliente_id > 0)
          	{//aggiorno l'indirizzo del cliente se presente
		      	$ente = Clienti::findOrFail($request->cliente_id);
	         	$sedeLegale =  $ente-> sedeLegale();
				$newsede['denominazione' ]=  'SEDE LEGALE';
				$newsede['indirizzo' ]= $censimentocliente ->indirizzo;
				$newsede['citta' ]= $censimentocliente -> citta;
				$newsede['provincia' ]= $censimentocliente ->provincia;
				$newsede['cap' ]= $censimentocliente ->cap;
				$newsede['nazione' ]= $censimentocliente ->nazione;

			 	$ente-> setSedeLegale($newsede);
				// Log
		        activity(session('azienda'))
		            ->performedOn($sedeLegale)
		            ->withProperties($newsede)
		            ->log('updated');
  		  	}
		}


		if(!is_null($request->id_segnalazione) )
		{
			if($request->id_segnalazione > 0 )
			{
				$id_segnalazione = $request->id_segnalazione;
			   	$segnalazione = SegnalazioneOpportunita::findOrFail($request->id_segnalazione);
				$agg['censimento_id'] = $censimentocliente->id;
				//$agg['stato_id'] = 1;
				if(empty($segnalazione->cliente_id)  && !empty($censimentocliente ->cliente_id) ){
					$agg['cliente_id'] = $censimentocliente ->cliente_id;
				}
	         	$segnalaziones  =  $segnalazione-> update(  $agg);

				 activity(session('azienda'))
		            ->performedOn($segnalazione)
		            ->withProperties($segnalaziones)
		            ->log('updated');
			}
		}

        // Log
        return redirect()->route('admin.commerciale.censimentocliente.edit', $censimentocliente->id)
            ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('commerciale::censimenticlienti.title.censimenticlienti')]));
    }

    /**
     * Show the reading the specified resource.
     *
     * @param  CensimentoCliente $censimentocliente
     * @return Response
     */
    public function read(CensimentoCliente $censimentocliente)
    {
      if($censimentocliente->azienda != session('azienda'))
          return redirect()->route('admin.commerciale.censimentocliente.index')
                  ->withWarning('AVVISO: non puoi accedere a questo censimentocliente con l\'azienda ' . session('azienda'));

      $procedure = Procedura::all(); 
      if(!empty($censimentocliente->referenti)){
        $spesa_totale['totale'] = 0;
        foreach($censimentocliente->referenti as $key => $value){
          if(empty($spesa_totale[$value->procedura_id]))
            $spesa_totale[$value->procedura_id] = 0;
          $spesa_totale['totale']+= (float) clean_currency($value->spesa);
          $spesa_totale[$value->procedura_id] += (float) clean_currency($value->spesa);
        }
    } else {
      $spesa_totale['totale'] = 0;
    }
      
      $offerte = collect($censimentocliente->offerte)->paginate(config('wecore.pagination.limit'));

      $ordinativi = $censimentocliente->ordinativi()->paginate(config('wecore.pagination.limit'));

		  $segnalazioniopportunita = $censimentocliente->segnalazioni_oppotunita()->get();

		  $analisivendite = $censimentocliente->analisivendita()->get();

      $commerciali_id = User::elencoCommerciali()->pluck('id');
		  $commerciali = [''] + User::whereIn('id', $commerciali_id)->get()->pluck('full_name', 'id')->toArray();

    	$activities = get_activities($censimentocliente);

      return view('commerciale::admin.censimenticlienti.read', compact('censimentocliente', 'segnalazioniopportunita', 'procedure', 'analisivendite', 'commerciali','activities', 'offerte', 'ordinativi', 'spesa_totale'));
    }

    public function readOffertaVociModal(Request $request)
    {
        if($request->ajax()){

            $offerta = Offerta::find($request->offerta_id);
            $voci = $offerta->voci;

            $data = ['empty' => 1];
            if(!empty($voci) && $voci->count() > 0){
                foreach($voci as $voce)
                {
                    $data['empty'] = 0;

                    $data['values'][$voce->id]['descrizione'] = get_if_exist($voce, 'descrizione');
                    $data['values'][$voce->id]['importo_singolo'] = get_if_exist($voce, 'importo_singolo');
                    $data['values'][$voce->id]['iva'] = get_if_exist($voce, 'iva');
                    $data['values'][$voce->id]['importo'] = get_if_exist($voce, 'importo');
                    $data['values'][$voce->id]['importo_iva'] = get_if_exist($voce, 'importo_iva');
                    $data['values'][$voce->id]['esente_iva'] = ((get_if_exist($voce, 'esente_iva')==1 ) ? 'SI' :'NO');
                    $data['values'][$voce->id]['accettata'] = ((get_if_exist($voce, 'accettata')==1 ) ? 'SI' :'NO');
                }
            }
            
          return response()->json(json_encode($data));
    
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  CensimentoCliente $censimentocliente
     * @return Response
     */
    public function edit(CensimentoCliente $censimentocliente, Request $request)
    {
      if($censimentocliente->azienda != session('azienda'))
          return redirect()->route('admin.commerciale.censimentocliente.index')
                  ->withWarning('AVVISO: non puoi accedere a questo censimentocliente con l\'azienda ' . session('azienda'));

      $enti = [''] + Clienti::pluck('ragione_sociale', 'id')->toArray();

      if(!empty($request))
      {
        if($request->cliente_id > 0)
        {
          $ente = Clienti::findOrFail($request->cliente_id);

          $censimentocliente->cliente = $ente->ragione_sociale;
          $censimentocliente->indirizzo = $ente->sedeLegale()->indirizzo;
          $censimentocliente->citta = $ente->sedeLegale()->citta;
          $censimentocliente->provincia = $ente->sedeLegale()->provincia;
          $censimentocliente->cap = $ente->sedeLegale()->cap;
          $censimentocliente->nazione = $ente->sedeLegale()->nazione; 
        }
      }

      $procedure = Procedura::all();
      $procedure_list = $procedure->pluck('titolo', 'id')->toArray();
      $aree = Area::all()->pluck('titolo', 'id')->toArray(); 
      $attivita = Gruppo::all()->pluck('nome', 'id')->toArray();

      if(!empty($censimentocliente->referenti)){
          $spesa_totale['totale'] = 0;
          foreach($censimentocliente->referenti as $key => $value){
            if(empty($spesa_totale[$value->procedura_id]))
              $spesa_totale[$value->procedura_id] = 0;
            $spesa_totale['totale']+= (float) clean_currency($value->spesa);
            $spesa_totale[$value->procedura_id] += (float) clean_currency($value->spesa);
          }
      } else {
        $spesa_totale['totale'] = 0;
      }

		  $segnalazioniopportunita = $censimentocliente->segnalazioni_oppotunita()->get();

		  $analisivendite = $censimentocliente->analisivendita()->get();

      $commerciali_id = User::elencoCommerciali()->pluck('id');
		  $commerciali = [''] + User::whereIn('id', $commerciali_id)->get()->pluck('full_name', 'id')->toArray();

      $activities = get_activities($censimentocliente);

      return view('commerciale::admin.censimenticlienti.edit', compact('censimentocliente', 'segnalazioniopportunita', 'enti', 'procedure', 'analisivendite', 'commerciali','activities', 'aree', 'procedure_list', 'spesa_totale', 'attivita'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CensimentoCliente $censimentocliente
     * @param  UpdateCensimentoClienteRequest $request
     * @return Response
     */
    public function update(CensimentoCliente $censimentocliente, UpdateCensimentoClienteRequest $request)
    {
        if($censimentocliente->azienda != session('azienda'))
            return redirect()->route('admin.commerciale.censimentocliente.index')
                    ->withWarning('AVVISO: non puoi accedere a questo censimentocliente con l\'azienda ' . session('azienda'));

        $rules = CensimentoCliente::getRules();
        $this->validate($request, $rules);

        $update = $request->all();
        $update['updated_user_id'] = Auth::id();

        unset($update['stato_id']);
        unset($update['commerciale_id']);
    
        if(!empty($update['referenti'])){
          foreach($update['referenti'] as $key => $value){
              if(!$update['referenti'][$key]['nome'] && !$update['referenti'][$key]['telefono'] && !$update['referenti'][$key]['email'] && !$update['referenti'][$key]['spesa'] && !$update['referenti'][$key]['area_id']){
                unset($update['referenti'][$key]);
              }
          }
        } 
        $this->censimentocliente->update($censimentocliente, $update);



	   	activity(session('azienda'))
            ->performedOn($censimentocliente)
            ->withProperties($update)
            ->log('updated');



		if(!empty($request))
    	{
		  	if($request->cliente_id > 0)
          	{//aggiorno l'indirizzo del cliente se presente
		      	$ente = Clienti::findOrFail($request->cliente_id);
	         	$sedeLegale =  $ente-> sedeLegale();
				$newsede['denominazione' ]=  'SEDE LEGALE';
				$newsede['indirizzo' ]= $censimentocliente ->indirizzo;
				$newsede['citta' ]= $censimentocliente -> citta;
				$newsede['provincia' ]= $censimentocliente ->provincia;
				$newsede['cap' ]= $censimentocliente ->cap;
				$newsede['nazione' ]= $censimentocliente ->nazione;

			 	$ente-> setSedeLegale($newsede);
			   // Log
		        activity(session('azienda'))
		            ->performedOn($sedeLegale)
		            ->withProperties($newsede)
		            ->log('updated');

  		  	}
		}

        return redirect()->route('admin.commerciale.censimentocliente.edit', $censimentocliente->id)
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('commerciale::censimenticlienti.title.censimenticlienti')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  CensimentoCliente $censimentocliente
     * @return Response
     */
    public function destroy(CensimentoCliente $censimentocliente)
    {
        if($censimentocliente->azienda != session('azienda'))
            return redirect()->route('admin.commerciale.censimentocliente.index')
                    ->withWarning('AVVISO: non puoi accedere a questo censimentocliente con l\'azienda ' . session('azienda'));

        $this->censimentocliente->destroy($censimentocliente);

        // Log 
        activity(session('azienda'))
            ->performedOn($censimentocliente)
            ->withProperties(json_encode($censimentocliente))
            ->log('destroyed');

        return redirect()->route('admin.commerciale.censimentocliente.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('commerciale::censimenticlienti.title.censimenticlienti')]));
    }

    public function generaPdf($id)
    {

        $censimentocliente = CensimentoCliente::findOrFail($id);

        $procedure = Procedura::all();

        if(!empty($censimentocliente->referenti)){
          $spesa_totale['totale'] = 0;
          foreach($censimentocliente->referenti as $key => $value){
            if(empty($spesa_totale[$value->procedura_id]))
              $spesa_totale[$value->procedura_id] = 0;
            $spesa_totale['totale']+= (float) clean_currency($value->spesa);
            $spesa_totale[$value->procedura_id] += (float) clean_currency($value->spesa);
          }
        } else {
          $spesa_totale['totale'] = 0;
        }

        $fname = 'Censimento ' . session('azienda').' '. $censimentocliente->cliente;

        $titolo = 'Censimento Cliente';

        $pdf = PDF::loadView('commerciale::admin.censimenticlienti.genera_pdf', compact('censimentocliente','titolo', 'spesa_totale', 'procedure'))->setPaper('a4');

        return $pdf->stream($fname.'.pdf');
    }


    
    public function reportvisteStore(Request $request)
    {
        $dataStore = CensimentoClienteReportVistec::create($request->all());
        return back();
    }
}
