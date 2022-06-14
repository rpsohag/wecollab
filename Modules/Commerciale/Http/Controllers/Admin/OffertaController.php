<?php

namespace Modules\Commerciale\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Commerciale\Entities\Offerta;
use Modules\Commerciale\Http\Requests\CreateOffertaRequest;
use Modules\Commerciale\Http\Requests\UpdateOffertaRequest;
use Modules\Commerciale\Repositories\OffertaRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Profile\Entities\FiguraProfessionale;
use Modules\Commerciale\Entities\Ordinativo;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Modules\Amministrazione\Entities\Clienti;
use Modules\Wecore\Entities\Meta;
use Modules\Commerciale\Entities\OffertaVoce;
use Modules\Commerciale\Entities\AnalisiVendita;
use Modules\Export\Entities\OfferteExport;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Profile\Entities\Gruppo;
use Modules\Profile\Entities\Area;
use Modules\User\Entities\Sentinel\User;

use Modules\Export\Entities\FatturazioneScadenzeExport;

use Modules\Commerciale\Http\Services\AnalisiVenditaService;

class OffertaController extends AdminBaseController
{
    /**
     * @var OffertaRepository
     */
    private $offerta;

    public function __construct(OffertaRepository $offerta)
    {
        parent::__construct();

        $this->offerta = $offerta;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if(empty($request->all()) || empty($request->order['by']) || empty($request->order['sort']))
        {
          $res['order']['by'] = 'codice';
          $res['order']['sort'] = 'desc';
          $request->merge($res);
        }

        if(!empty($request->stato) && $request->stato == -1)
        {
          $offerte = Offerta::filter($request->all())
                            ->with('ordinativo')
                            ->where('commerciale__offerte.azienda', session('azienda'))
                            ->where('stato', -1)
                            ->whereHas('cliente', function($q){
                                $q->commerciali(); })
                            ->orderBy('created_at', 'desc')
                            ->paginateFilter(config('wecore.pagination.limit'));
        }
        else
        {
          $offerte = Offerta::filter($request->all()) 
                          ->with('ordinativo')
                          ->where('commerciale__offerte.azienda', session('azienda'))
                          ->where('stato', '>=', 0)
                          ->whereHas('cliente', function($q){
                            $q->commerciali(); })
                          ->paginateFilter(config('wecore.pagination.limit'));
        }

        $commerciali_id = User::elencoCommerciali()->pluck('id');
        $commerciali = [''] + User::whereIn('id', $commerciali_id)->get()->pluck('full_name', 'id')->toArray();

        $clienti = [0 => ''];
        $clienti = $clienti + Clienti::commerciali()->pluck('ragione_sociale', 'id')->toArray();

        $request->flash();

        return view('commerciale::admin.offerte.index', compact('offerte','clienti', 'commerciali'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(CreateOffertaRequest $request)
    {
        //Nuova offerta
        $offerta = new Offerta(); 
        $offerta->anno = date('Y'); 
        $offerta->numero = Offerta::get_numero_offerta_new();

        //Selects
        $clienti = Clienti::commerciali()->pluck('ragione_sociale', 'id')->toArray();

        /* //Duplica offerta
        if(!empty($request->duplicate_id)){
            $offerta = Offerta::find($request->duplicate_id);
            $offerta->data_offerta = get_date_ita(date("Y-m-d"));
            $offerta->anno = date('Y');
            $offerta->created_at = null;
            $offerta->numero = Offerta::get_numero_offerta_new();
        } */

        //Creazione offerta da un analisi vendita
 		if(!empty($request->analisi_id) && isset($request->analisi_id)) 
        {
            $analisi = AnalisiVendita::find($request -> analisi_id);
            $gruppi = Gruppo::all();
            $aree = Area::all();
            $offerta->cliente_id = optional($analisi->censimento_cliente)->cliente_id;
            $offerta->oggetto = $analisi->titolo;
            $figureprofessionali = FiguraProfessionale::all();

            $analisi_service =  new AnalisiVenditaService();

            $riepilogo['aree'] =  $analisi_service->riepilogoAree($analisi);
            $riepilogo['costi_fissi'] =  $analisi_service->riepilogoCostiFissi($analisi);

            if(!empty($riepilogo['aree'])){
                foreach($riepilogo['aree']['aree'] as $key => $area) {
                    $tmp = new OffertaVoce();

                    $tmp->descrizione = get_if_exist($area, 'nome');
                    $tmp->quantita = 1;
                    $tmp->iva = config('commerciale.offerte.iva'); 
                    $tmp->accettata = 1;
                    $tmp->importo_singolo = get_if_exist($area, 'importo_vendita');
                    $offerta->voci[] = $tmp;
                }
            }

            if(!empty($riepilogo['costi_fissi'])){
                foreach($riepilogo['costi_fissi']['items'] as $key => $item){
                    $tmp = new OffertaVoce();

                    $tmp->descrizione = get_if_exist($item, 'nome');
                    $tmp->quantita = 1;
                    $tmp->iva = config('commerciale.offerte.iva');
                    $tmp->accettata = 1;
                    $tmp->importo_singolo = get_if_exist($item, 'costo_totale');
                    $offerta->voci[] = $tmp;
                }
            }

        }
        
        $iva = config('commerciale.offerte.iva');
        $roles = [];
        $roles['direttore_tecnico']['email'] = user(setting('admin::direttore_tecnico'))->email;
        $roles['direttore_commerciale']['email'] = user(setting('admin::direttore_commerciale'))->email;
        $roles['direttore_pa']['email'] = user(setting('admin::direttore_pa'))->email;
        $roles['amministrazione']['email'] = user(setting('admin::amministrazione'))->email;
        $roles['direttore_tecnico']['full_name'] = user(setting('admin::direttore_tecnico'))->full_name;
        $roles['direttore_commerciale']['full_name'] = user(setting('admin::direttore_commerciale'))->full_name;
        $roles['direttore_pa']['full_name'] = user(setting('admin::direttore_pa'))->full_name;
        $roles['amministrazione']['full_name'] = user(setting('admin::amministrazione'))->full_name;
        $stati = config('commerciale.offerte.stati');

        return view('commerciale::admin.offerte.create', compact('clienti', 'offerta', 'iva', 'roles', 'stati'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateOffertaRequest $request
     * @return Response
     */
    public function store(CreateOffertaRequest $request)
    {
        $rules = Offerta::getRules();
        $this->validate($request, $rules);

        $rules_voci = OffertaVoce::getRules();

        foreach ($request->voci as $key => $voce)
        {
            $collection = json_decode(collect($voce));
            if(isset($collection->descrizione) || isset($collection->quantita)){
                $validator = Validator::make($voce, $rules_voci);

                if ($validator->fails()) {
                    return redirect()
                                ->route('admin.commerciale.offerta.create')
                                ->withError('Una o più voci non sono state inserite correttamente.')
                                ->withInput();
                }
            }
        }

        $insert = $request->all();

        $insert['azienda'] = session('azienda');
        $insert['anno'] = date('Y');
        $insert['stato'] = -1;
        $insert['numero'] = 0;
        $insert['created_user_id'] = Auth::id();
        $insert['updated_user_id'] = Auth::id();

        //Salvare solo le approvazioni richieste
        foreach($insert['approvazioni'] as $ruolo => $boolean){
            if($boolean == 0){
                unset($insert['approvazioni'][$ruolo]);
            } else {
                $insert['approvazioni'][$ruolo] = 0;
            }
        }

        $offerta = $this->offerta->create($insert);

        //Dropzone
        dropzone_files_save('commerciale', $offerta->id, 'Offerta', 'Commerciale', $request);

        // Voci
        if(!empty($request->voci))
        {
            foreach ($request->voci as $key => $voce)
            {
                $validator = Validator::make($voce, $rules_voci);

                if(!$validator->fails())
                {
                    $voce = $offerta->voci()->create($voce);
                }
            }
        }

        // Analisi vendita
        if(!empty($insert['analisi_id']))
        {
          $analisi = AnalisiVendita::find($insert['analisi_id']);
          $analisi->offerta()->associate($offerta);
          $analisi->save();
        }

        // Log
        activity(session('azienda'))
            ->performedOn($offerta)
            ->withProperties($insert)
            ->log('created');

        // Email
        $auth_user = auth_user();

        $assegnatari_email = $offerta->bozzaAssegnatari()->pluck('email')->toArray();

        if($offerta->approvata()){
            $offerta->stato = 0;
            $offerta->numero = Offerta::get_numero_offerta_new();
            $offerta->save();
            //Email alla segreteria commerciale
            $assegnatari_email = user(setting('admin::segreteria_commerciale'))->email;
            $oggetto = 'Nuova offerta approvata - ' . $offerta->oggetto;
            $messaggio = 'Hai una nuova offerta approvata.<br><br>Per visualizzare i dettagli clicca al seguente link:<br><a href="' . route('admin.commerciale.offerta.edit', $offerta->id) . '">' . route('admin.commerciale.offerta.edit', $offerta->id) . '</a>';
            mail_send($assegnatari_email, $oggetto, $messaggio);
    
            return redirect()->route('admin.commerciale.offerta.edit', $offerta->id)
                ->withSuccess('Una nuova offerta e\' stata creata con successo.');          
        } else {
            $oggetto = 'Nuova bozza offerta - ' . $offerta->oggetto;
            $messaggio = 'Hai una nuova bozza offerta assegnata da <strong>' . $auth_user->full_name . '</strong>.<br><br>Per visualizzare i dettagli clicca sul link di seguito:<br><a href="' . route('admin.commerciale.offerta.edit', $offerta->id) . '">' . route('admin.commerciale.offerta.edit', $offerta->id) . '</a><br>Cliente: ' . $offerta->cliente->ragione_sociale;
            mail_send($assegnatari_email, $oggetto, $messaggio);
    
            return redirect()->route('admin.commerciale.offerta.edit', $offerta->id)
                ->withSuccess('Una nuova bozza offerta e\' stata creata con successo.');    
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Offerta $offerta
     * @return Response
     */
    public function edit(Offerta $offerta, Request $request)
    {
        if($offerta->azienda != session('azienda'))
            return redirect()->route('admin.commerciale.offerta.index')
                    ->withWarning('AVVISO: non puoi accedere a questa offerta con l\'azienda ' . session('azienda'));

        /* if(!empty($offerta->ordinativo))
            return redirect()->route('admin.commerciale.offerta.index')->withWarning('L\'offerta non può essere modificata.'); */

        //$offerta_numero = $offerta->numero_offerta();

        //Selects
        $clienti = Clienti::commerciali()->pluck('ragione_sociale', 'id')
                                    ->toArray();

        $iva = config('commerciale.offerte.iva');
        $roles = [];
        $roles['direttore_tecnico']['email'] = user(setting('admin::direttore_tecnico'))->email;
        $roles['direttore_commerciale']['email'] = user(setting('admin::direttore_commerciale'))->email;
        $roles['direttore_pa']['email'] = user(setting('admin::direttore_pa'))->email;
        $roles['amministrazione']['email'] = user(setting('admin::amministrazione'))->email;
        $roles['direttore_tecnico']['full_name'] = user(setting('admin::direttore_tecnico'))->full_name;
        $roles['direttore_commerciale']['full_name'] = user(setting('admin::direttore_commerciale'))->full_name;
        $roles['direttore_pa']['full_name'] = user(setting('admin::direttore_pa'))->full_name;
        $roles['amministrazione']['full_name'] = user(setting('admin::amministrazione'))->full_name;
        $stati = config('commerciale.offerte.stati');
        $azienda = json_decode(setting('wecore::aziende'))->{get_azienda()};
        unset($stati[-1]);
        if($offerta->stato !== 0){
            unset($stati[0]);
        }

        return view('commerciale::admin.offerte.edit', compact('offerta', 'clienti', 'roles', 'stati', 'iva', 'azienda'));
    }

    /**
     * Show   the specified resource.
     *
     * @param  Offerta $offerta
     * @return Response
     */

    public function read(Offerta $offerta, Request $request)
    {
        if($offerta->azienda != session('azienda'))
            return redirect()->route('admin.commerciale.offerta.index')
                    ->withWarning('AVVISO: non puoi accedere a questa offerta con l\'azienda ' . session('azienda'));

        $clienti = Clienti::pluck('ragione_sociale', 'id')->toArray();

        $stati = config('commerciale.offerte.stati');
        $azienda = json_decode(setting('wecore::aziende'))->{get_azienda()};
        $iva = config('commerciale.offerte.iva');

        return view('commerciale::admin.offerte.read', compact('offerta', 'clienti', 'stati', 'azienda', 'iva'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Offerta $offerta
     * @param  UpdateOffertaRequest $request
     * @return Response
     */
    public function update(Offerta $offerta, UpdateOffertaRequest $request)
    {
        if($offerta->azienda != session('azienda'))
            return redirect()->route('admin.commerciale.offerta.index')
                    ->withWarning('AVVISO: non puoi accedere a questa offerta con l\'azienda ' . session('azienda'));

        /* if(!empty($offerta->ordinativo))
            return redirect()->route('admin.commerciale.offerta.index')->withWarning('L\'offerta non può essere modificata.'); */

        $update = $request->all();
        $update['updated_user_id'] = Auth::id();
    
        if(!empty($update['approvazioni'])){
            $offerta->approvazioni = $update['approvazioni'];   
            $offerta->save();      
        }

        if($offerta->approvata()){
            if($offerta->stato !== -1){
                $rules = Offerta::getRules();

                if(empty($offerta->ordinativo)){
                    $this->validate($request, $rules); 
                }

                $rules_voci = OffertaVoce::getRules();

                $offerta->voci()->delete();

                // Voci
                if(!empty($request->voci))
                {
                    foreach ($request->voci as $key => $voce)
                    {
                        $validator = Validator::make($voce, $rules_voci);
    
                        if(!$validator->fails())
                        {
                            $offerta->voci()->create($voce);
                        } else {
                            return redirect()
                            ->route('admin.commerciale.offerta.edit', $offerta->id)
                            ->withError('Una o più voci non sono state inserite e/o modificate correttamente.')
                            ->withInput();
                        }
                    }
                }
            }

            // Email se accettata
            if($request->stato == 1 && $offerta->stato !== 1)
            {
                // Email alla segreteria commerciale && segreteria amministrativa
                $assegnatari_email = [user(setting('admin::segreteria_commerciale'))->email, user(setting('admin::segreteria_amministrativa'))->email];
                $oggetto = 'Offerta n. ' . $offerta->numero_offerta() . ' accettata - ' . $offerta->oggetto;
                $messaggio = 'Hai una nuova offerta accettata.<br><br>Per visualizzare i dettagli clicca al seguente link:<br><a href="' . route('admin.commerciale.offerta.edit', $offerta->id) . '">' . route('admin.commerciale.offerta.edit', $offerta->id) . '</a>';
                mail_send($assegnatari_email, $oggetto, $messaggio);
            }

            // Email se rifiutata
            if($request->stato == 2 && $offerta->stato !== 2)
            {
                // Email alla segreteria commerciale && segreteria amministrativa
                $assegnatari_email = [user(setting('admin::segreteria_commerciale'))->email, user(setting('admin::segreteria_amministrativa'))->email];
                $oggetto = 'Offerta n. ' . $offerta->numero_offerta() . ' rifiutata - ' . $offerta->oggetto;
                $messaggio = 'L\'offerta in oggetto è stata rifiutata.<br><br>Per visualizzare i dettagli clicca al seguente link:<br><a href="' . route('admin.commerciale.offerta.edit', $offerta->id) . '">' . route('admin.commerciale.offerta.edit', $offerta->id) . '</a>';
                mail_send($assegnatari_email, $oggetto, $messaggio);

                if($offerta->analisi_vendita()->first())
                    $offerta->analisi_vendita()->first()->segnalazioni()->update(['stato_id' => 5]);
            }

            $this->offerta->update($offerta, $update);

            //Dropzone
            dropzone_files_save('commerciale', $offerta->id, 'Offerta', 'Commerciale', $request);

            // Approvata => cambio stato da inviare
            if($offerta->stato == -1)
            {
                $offerta->stato = 4;
                $offerta->numero = Offerta::get_numero_offerta_new();
                $offerta->save();

                // Email alla segreteria commerciale
                $assegnatari_email = user(setting('admin::segreteria_commerciale'))->email;
                $oggetto = 'Nuova offerta da inviare - ' . $offerta->oggetto;
                $messaggio = 'Hai una nuova offerta da inviare.<br><br>Per visualizzare i dettagli clicca al seguente link:<br><a href="' . route('admin.commerciale.offerta.edit', $offerta->id) . '">' . route('admin.commerciale.offerta.edit', $offerta->id) . '</a>';
                mail_send($assegnatari_email, $oggetto, $messaggio);

                return redirect()->route('admin.commerciale.offerta.edit', $offerta->id)
                ->withSuccess('L\'offerta è stata approvata con successo.');
            } else {
                return redirect()->route('admin.commerciale.offerta.edit', $offerta->id)
                ->withSuccess('L\'offerta è stata modificata con successo.');                
            }
        }

        // Log
        activity(session('azienda'))
        ->performedOn($offerta)
        ->withProperties($update)
        ->log('updated');

        return redirect()->route('admin.commerciale.offerta.edit', $offerta->id)
        ->withSuccess('Le approvazioni dell\'offerta sono state aggiornate.');   
    }


    public function allegatoDestroy($id)
    {
        $offerta_id = file_destroy($id);

        return redirect()->route('admin.commerciale.offerta.edit', $offerta_id)
            ->withSuccess('Allegato eliminato con successo');
    }

    public function setOffertaDefinitiva($id)
    {
        $allegato = Meta::where('id', $id)->where('name', 'file')->first();

        $offerta_id = $allegato->metagable->metagable_id;

        $offerta = Offerta::findOrFail($offerta_id);

        if($offerta->offerta_definitiva_id == $id)
            $offerta->offerta_definitiva_id = NULL;
        else
            $offerta->offerta_definitiva_id = $id;

        $offerta->save();

        // Log
        activity(session('azienda'))
            ->performedOn($offerta)
            ->withProperties(json_encode($offerta))
            ->log('updated');

        return redirect()->route('admin.commerciale.offerta.edit', $offerta_id)
          ->withSuccess('Allegato associato con successo');
    }

    public function createOrdinativo($offerta_id)
    {
        return view('commerciale::admin.offerte.create', compact('offerta_id'));
    }

    public function setOdaDetermina($id)
    {
        $allegato = Meta::where('id', $id)->where('name', 'file')->first();

        $offerta_id = $allegato->metagable->metagable_id;

        $offerta = Offerta::findOrFail($offerta_id);

        if($offerta->oda_determina_ids->contains($id))
        {
            foreach($offerta->oda_determina_ids as $key => $ids)
            {
                if($ids == $id)
                    $key_remove = $key;
            }

            $offerta->oda_determina_ids = $offerta->oda_determina_ids->forget($key_remove);
        }
        else
            $offerta->oda_determina_ids = $offerta->oda_determina_ids->push($id);

        $offerta->save();

        // Log
        activity(session('azienda'))
            ->performedOn($offerta)
            ->withProperties(json_encode($offerta))
            ->log('updated');

        return redirect()->route('admin.commerciale.offerta.edit', $offerta_id)
          ->withSuccess('Allegato associato con successo');
    }

    public function setOrdineMepa($id)
    {
        $allegato = Meta::where('id', $id)->where('name', 'file')->first();

        $offerta_id = $allegato->metagable->metagable_id;

        $offerta = Offerta::findOrFail($offerta_id);

        if($offerta->ordine_mepa_id == $id)
            $offerta->ordine_mepa_id = NULL;
        else
            $offerta->ordine_mepa_id = $id;

        $offerta->save();

        // Log
        activity(session('azienda'))
            ->performedOn($offerta)
            ->withProperties(json_encode($offerta))
            ->log('updated');

        return redirect()->route('admin.commerciale.offerta.edit', $offerta_id)
          ->withSuccess('Allegato associato con successo');
    } 

    public function generaOrdinativo($id_offerta)
    {
        $offerta = Offerta::findOrFail($id_offerta);

        // genera generaOrdinativo
        $insert['azienda'] = $offerta->azienda;
        $insert['data_inizio'] = date("Y-m-d H:i:s"); 
        $insert['data_fine'] = $offerta->data_fine;
        $insert['oggetto'] = $offerta->oggetto;
        $insert['anno'] = date('Y'); 
        $insert['cliente_id'] = $offerta->cliente_id;
        $insert['numero'] = Ordinativo::get_numero_ordinativo_new();

        //voci economiche
        $voci = array();
        $i = 1;

        if(!empty($offerta->voci) && $offerta->voci->count() > 0){
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

        $insert['voci_economiche'] = !empty($voci) ? $voci : null;

        $ordinativo = Ordinativo::create($insert);

        $offerta->update(['ordinativo_id' => $ordinativo->id]);

        // Log
        activity(session('azienda'))
            ->performedOn($ordinativo)
            ->withProperties($insert)
            ->log('created');

        return redirect()->route('admin.commerciale.ordinativo.edit', $ordinativo->id)
            ->withSuccess('Ordinativo generato con successo');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  Offerta $offerta
     * @return Response
     */
    public function destroy(Offerta $offerta)
    {
        if($offerta->azienda != session('azienda'))
            return redirect()->route('admin.commerciale.offerta.index')
                    ->withWarning('AVVISO: non puoi accedere a questa offerta con l\'azienda ' . session('azienda'));

        $offerta->analisi_vendite()->update(['offerta_id' => 0]);

        $this->offerta->destroy($offerta);

        // Log
        activity(session('azienda'))
            ->performedOn($offerta)
            ->withProperties(json_encode($offerta))
            ->log('destroyed');

        return redirect()->route('admin.commerciale.offerta.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('commerciale::offerte.title.offerte')]));
    }

    // Export excel
    public function exportExcel(Request $request)
    {
      if(!empty($request->stato) && $request->stato == -1)
      {
        $offerte = Offerta::filter($request->all())
                          ->where('azienda', session('azienda'))
                          ->where('stato', -1)
                          ->orderBy('anno', 'desc')
                          ->orderBy('numero', 'desc')
                          ->get();
      }
      else
      {
        $offerte = Offerta::filter($request->all())
                        ->where('azienda', session('azienda'))
                        ->where('stato', '>=', 0)
                        ->orderBy('anno', 'desc')
                        ->orderBy('numero', 'desc')
                        ->get();
      }

      ob_clean();
      return Excel::download(new OfferteExport($offerte), session('azienda') . '_offerte.xlsx');
    }

    // Export excel
    public function exportScadenzeExcel(Request $request)
    {
      if(!empty($request->stato) && $request->stato == -1)
      {
        $offerte = Offerta::filter($request->all())
                          ->where('azienda', session('azienda'))
                          ->where('stato', -1)
                          ->orderBy('anno', 'desc')
                          ->orderBy('numero', 'desc')
                          ->get();
      }
      else
      {
        $offerte = Offerta::filter($request->all())
                        ->where('azienda', session('azienda'))
                        ->where('stato', '>=', 0)
                        ->orderBy('anno', 'desc')
                        ->orderBy('numero', 'desc')
                        ->get();
      }

      ob_clean();
      return Excel::download(new FatturazioneScadenzeExport($offerte), session('azienda') . '_scadenze.xlsx');
    }

}
