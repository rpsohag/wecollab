<?php

namespace Modules\Commerciale\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Commerciale\Entities\AnalisiVendita;
use Modules\Commerciale\Entities\AnalisiVenditaTemplate;
use Modules\Commerciale\Http\Requests\CreateAnalisiVenditaRequest;
use Modules\Commerciale\Http\Requests\UpdateAnalisiVenditaRequest;
use Modules\Commerciale\Repositories\AnalisiVenditaRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;

use Maatwebsite\Excel\Facades\Excel;
use Modules\Export\Entities\AnalisiVenditaExport;

use Modules\Amministrazione\Entities\Clienti;
use Modules\Commerciale\Entities\CensimentoCliente;
use Modules\Commerciale\Entities\SegnalazioneOpportunita;
use Modules\Profile\Entities\Procedura;
use Modules\Profile\Entities\Gruppo;
use Modules\Profile\Entities\Area;
use Modules\User\Entities\Sentinel\User;
use Modules\Profile\Entities\FiguraProfessionale;

use Modules\Commerciale\Http\Services\AnalisiVenditaService;

class AnalisiVenditaController extends AdminBaseController
{
    /**
     * @var AnalisiVenditaRepository
     */
    private $analisivendita;

    public function __construct(AnalisiVenditaRepository $analisivendita, AnalisiVenditaService $dettaglio)
    {
        parent::__construct();

        $this->analisivendita = $analisivendita;
        $this->dettaglio = $dettaglio;
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
          $res['order']['by'] = 'created_at';
          $res['order']['sort'] = 'desc';
          $request->merge($res);
        }

        $analisivendite = AnalisiVendita::filter($request->all())
                            ->where('azienda', session('azienda'))
                            ->commerciali()
                            ->paginateFilter(config('wecore.pagination.limit'));

        $commerciali_id = User::elencoCommerciali()->pluck('id');
        $commerciali = [''] + User::whereIn('id', $commerciali_id)->get()->pluck('full_name', 'id')->toArray();

        $request->flash();

        $clienti = [0 => ''] + Clienti::commerciali()->pluck('ragione_sociale', 'id')->toArray();

        return view('commerciale::admin.analisivendite.index', compact(['analisivendite', 'clienti', 'commerciali']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {

        //Duplica Analisi Vendita
        if(!empty($request->duplicate_id)){
            $analisivendita = AnalisiVendita::find($request->duplicate_id);
            $analisivendita->created_at = null;  
            $analisivendita->updated_at = null;
        } else {

            $analisivendita = new AnalisiVendita();

            if(empty($request->censimentocliente_id)) {
                return redirect()->back()->withWarning('AVVISO: per creare una nuova analisi di vendita devi accedere al censimento cliente.');
            } else {
                $analisivendita->censimento_cliente = $analisivendita->censimento_cliente($request->censimentocliente_id);
            }

        }

        $analisivendita->commerciale_id = (!empty($request->commerciale_id) ? $request->commerciale_id : 0);
		$segnalazioni_selected = [];
        if(!empty($request->segnalazioni_id)){
          $segnalazioni_selected = [$request->segnalazioni_id];
          $analisivendita->titolo = SegnalazioneOpportunita::find($request->segnalazioni_id)->oggetto;
        }

        $segnalazioni = $analisivendita->elencoSegnalazioniVista();

        $procedure = Procedura::all();

        $commerciali_id = User::elencoCommerciali()->pluck('id');
        $commerciali = [''] + User::whereIn('id', $commerciali_id)->get()->pluck('full_name', 'id')->toArray();
          
        $figureprofessionali = FiguraProfessionale::all();
        $figureprofessionali_select = [''] + $figureprofessionali->pluck('descrizione', 'id')->toArray();

        $templates = [0 => ''] + AnalisiVenditaTemplate::all()->pluck('nome', 'id')->toArray();

        if(!empty($request->template_caricato_id)){
            $template_caricato = AnalisiVenditaTemplate::find($request->template_caricato_id);
            $analisivendita->attivita = $template_caricato->attivita;
        }

        return view('commerciale::admin.analisivendite.create', compact('analisivendita', 'segnalazioni', 'procedure', 'segnalazioni_selected', 'commerciali', 'figureprofessionali', 'figureprofessionali_select', 'templates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateAnalisiVenditaRequest $request
     * @return Response
     */
    public function store(CreateAnalisiVenditaRequest $request)
    {
        $rules = AnalisiVendita::getRules();
        $this->validate($request, $rules);

        $insert = $request->all();
        $insert['azienda'] = session('azienda');

        if(!empty($insert['template_nome'])){
            $template = AnalisiVenditaTemplate::create([
                'nome' => $insert['template_nome'],
                'attivita' => $insert['attivita']
            ]);
            unset($insert['template_nome']);
        }

        $analisivendita = AnalisiVendita::create($insert);

        foreach($insert['segnalazione'] as $segnalazione_id) 
        {
           $segnalazione = SegnalazioneOpportunita::find($segnalazione_id);
           $segnalazione->analisi_vendita()->associate($analisivendita);
           $segnalazione->save();
           $censimento_id = $segnalazione->censimento->id;
        }

        $analisivendita->update(['censimento_id' => $censimento_id]);

        // Log
        activity(session('azienda'))
            ->performedOn($analisivendita)
            ->withProperties($insert)
            ->log('created');

        return redirect()->route('admin.commerciale.analisivendita.edit', $analisivendita->id)
            ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('commerciale::analisivendite.title.analisivendite')]));
    }

    public function conversioneChecklistJson()
    {
        $junior = [8, 11, 14, 17];
        $medium = [16, 7, 9, 1, 12];  
        $senior = [3, 4, 5, 6, 10, 15];   
        $responsabile = [18, 19]; 
        $helpdesk = [2, 13];

        $analisis = AnalisiVendita::all();

        //Remove the unselected things

        foreach($analisis as $analisi){
            if(!is_null($analisi->attivita)){
                $attivita_new = json_decode(json_encode($analisi->attivita), true);
                foreach($attivita_new as $key => $attivita){
                    if($attivita['selected'] == 0){
                        unset($attivita_new[$key]);
                    }
                }
                $analisi->update(['attivita' => $attivita_new]);
            }
        }

        //Change figure ids & calcolo ore

        foreach($analisis as $analisi){
            if(!is_null($analisi->attivita)){
                $attivita_new = json_decode(json_encode($analisi->attivita), true);
                foreach($attivita_new as $key => $attivita){
                    if(!is_null($attivita['figure_professionali'])){
                        foreach($attivita['figure_professionali'] as $figura_key => $figura){
                            if(in_array($figura['figura_professionale_id'], $junior))
                            {
                                $attivita_new[$key]['figure_professionali'][$figura_key]['figura_professionale_id'] = (int)20;
                            }
        
                            if(in_array($figura['figura_professionale_id'], $medium))
                            {
                                $attivita_new[$key]['figure_professionali'][$figura_key]['figura_professionale_id'] = (int)21;
                            }
        
                            if(in_array($figura['figura_professionale_id'], $senior))
                            {
                                $attivita_new[$key]['figure_professionali'][$figura_key]['figura_professionale_id'] = (int)22;
                            }
        
                            if(in_array($figura['figura_professionale_id'], $responsabile))
                            {
                                $attivita_new[$key]['figure_professionali'][$figura_key]['figura_professionale_id'] = (int)23;
                            }
        
                            if(in_array($figura['figura_professionale_id'], $helpdesk))
                            {
                                $attivita_new[$key]['figure_professionali'][$figura_key]['figura_professionale_id'] = (int)24;
                            }  
                            
                            $ore_nuove = 0;
                            foreach($figura['ore'] as $ore_key => $ore)
                            {
                                if(!is_null($ore)) {
                                    if(!empty($ore['remoto']))
                                    {
                                        $ore_nuove += (int)$ore['remoto'];
                                    }
                
                                    if(!empty($ore['cliente']))
                                    {
                                        $ore_nuove += (int)$ore['cliente'];
                                    }
                
                                    if(!empty($ore['configurazione']))
                                    {
                                        $ore_nuove += (int)$ore['configurazione'];
                                    }
                
                                    if(!empty($ore['formazione_remoto']))
                                    {
                                        $ore_nuove += (int)$ore['formazione_remoto'];
                                    }
                
                                    if(!empty($ore['formazione_cliente']))
                                    {
                                        $ore_nuove += (int)$ore['formazione_cliente'];
                                    }
                                    unset($attivita_new[$key]['figure_professionali'][$figura_key]['ore']);
                                }
                            }
                            $attivita_new[$key]['figure_professionali'][$figura_key]['ore'] = $ore_nuove;    
                        }
                    }
                }

                $analisi->update(['attivita' => $attivita_new]);

            }
        }
    }

    public function read(AnalisiVendita $analisivendita)
    {
       if($analisivendita->azienda != session('azienda'))
            return redirect()->route('admin.commerciale.analisivendita.index')
                    ->withWarning('AVVISO: non puoi accedere a questo analisivendita con l\'azienda ' . session('azienda'));
         
        $segnalazioni_selected = implode(',', $analisivendita->segnalazioni()->pluck('oggetto')->toArray());

        $segnalazioni = $analisivendita->elencoSegnalazioniVista();

        $activities = get_activities($analisivendita);

        $commerciali_id = User::elencoCommerciali()->pluck('id');
        $commerciali = [''] + User::whereIn('id', $commerciali_id)->get()->pluck('full_name', 'id')->toArray();

        // RIEPILOGO

        $gruppi = Gruppo::all();
        $procedure = Procedura::all();
        $aree = Area::all();
        $figureprofessionali = FiguraProfessionale::all();

        $riepilogo['figure'] =  $this->dettaglio->riepilogoFigure($analisivendita);
        
        $riepilogo['aree'] =  $this->dettaglio->riepilogoAree($analisivendita);

        $riepilogo['costi_fissi'] = $this->dettaglio->riepilogoCostiFissi($analisivendita);

        $riepilogo['attivita'] = $this->dettaglio->riepilogoAttivita($analisivendita);

        return view('commerciale::admin.analisivendite.read', compact('figureprofessionali', 'aree', 'gruppi', 'procedure', 'analisivendita', 'segnalazioni_selected', 'segnalazioni', 'activities', 'commerciali', 'riepilogo'));
 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  AnalisiVendita $analisivendita
     * @return Response
     */
    public function edit(AnalisiVendita $analisivendita, Request $request)
    {
        if($analisivendita->azienda != session('azienda'))
            return redirect()->route('admin.commerciale.analisivendita.index')
                    ->withWarning('AVVISO: non puoi accedere a questo analisivendita con l\'azienda ' . session('azienda'));

        /* if(!empty($analisivendita->offerta) && !empty($analisivendita->offerta->ordinativo)){
            return redirect()->route('admin.commerciale.analisivendita.read', $analisivendita->id)->withWarning('L\'analisi vendita non può essere modificata.');
        } */

 	    $segnalazioni_selected = $analisivendita->segnalazioni()->pluck('id')->toArray();

        $segnalazioni = $analisivendita->elencoSegnalazioniVista();

        $procedure = Procedura::all();
        $activities = get_activities($analisivendita);

        $commerciali_id = User::elencoCommerciali()->pluck('id');
        $commerciali = [''] + User::whereIn('id', $commerciali_id)->get()->pluck('full_name', 'id')->toArray();
        
        $figureprofessionali = FiguraProfessionale::all();
        $figureprofessionali_select = [''] + $figureprofessionali->pluck('descrizione', 'id')->toArray();

        $templates = [0 => ''] + AnalisiVenditaTemplate::all()->pluck('nome', 'id')->toArray();

        if(!empty($request->template_caricato_id)){
            $template_caricato = AnalisiVenditaTemplate::find($request->template_caricato_id);
            $analisivendita->attivita = $template_caricato->attivita;
        }

        return view('commerciale::admin.analisivendite.edit', compact('analisivendita', 'segnalazioni_selected', 'segnalazioni', 'procedure', 'activities', 'commerciali', 'figureprofessionali', 'figureprofessionali_select', 'templates'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  AnalisiVendita $analisivendita
     * @param  UpdateAnalisiVenditaRequest $request
     * @return Response 
     */
    public function update(AnalisiVendita $analisivendita, UpdateAnalisiVenditaRequest $request)
    {
        if($analisivendita->azienda != session('azienda'))
            return redirect()->route('admin.commerciale.analisivendita.index')
                    ->withWarning('AVVISO: non puoi accedere a questo analisivendita con l\'azienda ' . session('azienda'));

        /* if(!empty($analisivendita->offerta) && !empty($analisivendita->offerta->ordinativo)){
            return redirect()->route('admin.commerciale.analisivendita.read', $analisivendita->id)->withWarning('L\'analisi vendita non può essere modificata.');
        } */

        $rules = AnalisiVendita::getRules();

        $this->validate($request, $rules);

        $update = $request->all();

        if(!empty($update['template_nome'])){
            $template = AnalisiVenditaTemplate::create([
                'nome' => $update['template_nome'],
                'attivita' => $update['attivita']
            ]);
            unset($update['template_nome']);
        }

        $this->analisivendita->update($analisivendita, $update);

    	// Associo le segnalazioni
        $analisivendita->segnalazioni()->update(['analisivendita_id' => 0]);

        foreach($update['segnalazione'] as $segnalazione_id)
        {
            $segnalazione = SegnalazioneOpportunita::find($segnalazione_id);
            $segnalazione->analisi_vendita()->associate($analisivendita);
            $segnalazione->save();
        }

        // Log
        activity(session('azienda'))
            ->performedOn($analisivendita)
            ->withProperties($update)
            ->log('updated');

        return redirect()->route('admin.commerciale.analisivendita.edit', $analisivendita->id)
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('commerciale::analisivendite.title.analisivendite')]));
    }

    // Export excel
    public function exportExcel(Request $request)
    {

        $gruppi = Gruppo::all();
        $aree = Area::all();
        $analisivendita = AnalisiVendita::find($request->analisivendita_id);
        $figureprofessionali = FiguraProfessionale::all();

        //RIEPILOGO
        $riepilogo = [];
        $riepilogo['figure'] = [];
        foreach($analisivendita->attivita as $id_attivita => $attivita){
            $area = $aree->find($gruppi->find($id_attivita)->area_id);
            if(!empty($attivita->figure_professionali)){ 
                foreach($attivita->figure_professionali as $figura_professionale){
                    if(get_if_exist($figura_professionale, 'figura_professionale_id')){
                        $fp = $figureprofessionali->find($figura_professionale->figura_professionale_id);
                        if(empty($riepilogo['figure'][$figura_professionale->figura_professionale_id]['area_titolo'])){
                            $riepilogo['figure'][$figura_professionale->figura_professionale_id]['area_titolo'] = $area->titolo;
                            $riepilogo['figure'][$figura_professionale->figura_professionale_id]['gruppo_nome'] = $gruppi->find($id_attivita)->nome;
                            $riepilogo['figure'][$figura_professionale->figura_professionale_id]['risorsa_nome'] = $fp->descrizione;
                        }

                        $riepilogo['figure'][$figura_professionale->figura_professionale_id]['ore_remoto'] = get_if_exist($figura_professionale->ore, 'remoto');
                        $riepilogo['figure'][$figura_professionale->figura_professionale_id]['ore_cliente'] = get_if_exist($figura_professionale->ore, 'cliente');
                        $riepilogo['figure'][$figura_professionale->figura_professionale_id]['ore_configurazione'] = get_if_exist($figura_professionale->ore, 'configurazione');
                        $riepilogo['figure'][$figura_professionale->figura_professionale_id]['ore_formazione_remoto'] = get_if_exist($figura_professionale->ore, 'formazione_remoto');
                        $riepilogo['figure'][$figura_professionale->figura_professionale_id]['ore_formazione_cliente'] = get_if_exist($figura_professionale->ore, 'formazione_cliente');
                    }
                }
            } 
        }
        ob_clean();
        return Excel::download(new AnalisiVenditaExport($riepilogo['figure']), 'AnalisiVendite.xlsx');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  AnalisiVendita $analisivendita
     * @return Response
     */
    public function destroy(AnalisiVendita $analisivendita)
    {
        if($analisivendita->azienda != session('azienda'))
            return redirect()->route('admin.commerciale.analisivendita.index')
                    ->withWarning('AVVISO: non puoi accedere a questo analisivendita con l\'azienda ' . session('azienda'));

        foreach($analisivendita->censimenticlienti as $p)
          $p->update(['analisivendita_id' => 0]);

        $this->analisivendita->destroy($analisivendita);

        // Log
        activity(session('azienda'))
            ->performedOn($analisivendita)
            ->withProperties(json_encode($analisivendita))
            ->log('destroyed');

        return redirect()->route('admin.commerciale.analisivendita.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('commerciale::analisivendite.title.analisivendite')]));
    }
}
