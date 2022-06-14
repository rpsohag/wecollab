<?php
namespace Modules\Commerciale\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Commerciale\Entities\Fatturazione;
use Modules\Commerciale\Entities\FatturazioneScadenze;
use Modules\Commerciale\Entities\Ordinativo;
use Modules\Commerciale\Entities\Offerta;
use Modules\Amministrazione\Entities\Clienti;
use Modules\Commerciale\Http\Requests\CreateFatturazioneRequest;
use Modules\Commerciale\Http\Requests\UpdateFatturazioneRequest;
use Modules\Commerciale\Repositories\FatturazioneRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use Validator;
use Modules\Commerciale\Entities\FatturazioneVoce;
use PDF;
use Modules\Export\Entities\FatturazioneExport;
use Modules\Export\Entities\FatturazioneVociExport;
use Modules\Export\Entities\FatturazioneScadenzeExport;
use Maatwebsite\Excel\Facades\Excel;

class FatturazioneController extends AdminBaseController
{
    /**
     * @var FatturazioneRepository
     */
    private $fatturazione;

    public function __construct(FatturazioneRepository $fatturazione)
    {
        parent::__construct();

        $this->fatturazione = $fatturazione;
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
          $res['order']['by'] = 'n_fattura';
          $res['order']['sort'] = 'desc';
          $request->merge($res);
        }

        $fatturazioni = Fatturazione::filter($request->all())
                            ->where('commerciale__fatturazioni.azienda', session('azienda'))
                            ->whereHas('cliente', function($q){
                                $q->commerciali(); })
                            ->paginateFilter(config('wecore.pagination.limit'));

        $clienti = [0 => ''];
        $clienti = $clienti + Clienti::commerciali()->pluck('ragione_sociale', 'id')->toArray();

        $macrocategorie = [0 => ''] + config('commerciale.fatturazioni.macrocategorie');

        $request->flash();

        return view('commerciale::admin.fatturazioni.index', compact('macrocategorie','clienti','fatturazioni'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */ 
    public function create(Request $request)
    {
        $fatturazione = new Fatturazione();

        $ordinativi = [0 => ''];
        $ordinativi = $ordinativi + Ordinativo::where('azienda', session('azienda'))
                                    ->orderBy('id','desc')
                                    ->pluck('oggetto', 'id')
                                    ->toArray();

        // FRP
        $fattura_numero_next = Fatturazione::get_next_numero_fattura(true);

        $fattura_numero = New Fatturazione();
        $fattura_numero = $fattura_numero->get_numero_fattura($fattura_numero_next, true);

        // FPA
        $fattura_numero_fepa_next = Fatturazione::get_next_numero_fattura();

        $fattura_numero_fepa = New Fatturazione();
        $fattura_numero_fepa = $fattura_numero_fepa->get_numero_fattura($fattura_numero_fepa_next);

        // Nota di credito interna
        $fattura_numero_nota_di_credito_interna = Fatturazione::get_next_numero_nota_di_credito_interna();
        $fattura_numero_nota_di_credito_interna_codice = $fatturazione->get_numero_fattura($fattura_numero_nota_di_credito_interna, false, true);

        $macrocategorie = [0 => 'Seleziona una categoria'] + config('commerciale.fatturazioni.macrocategorie');

        // ORDINATIVO
        $ordinativo_voci = null;
        if(!empty($request->ordinativo_id) && is_numeric($request->ordinativo_id))
        {
            $ordinativo = Ordinativo::find($request->ordinativo_id);

            $clienti = Offerta::with('cliente')->where('ordinativo_id', '=', $request->ordinativo_id)->get()->pluck('cliente.ragione_sociale', 'cliente.id')->prepend('Seleziona un cliente', '')->toArray();

            if(!empty($request->fatturazione_scadenza_id) && is_numeric($request->fatturazione_scadenza_id))
            {
                $fatturazione_scadenza = FatturazioneScadenze::find($request->fatturazione_scadenza_id);

                if(!empty($fatturazione_scadenza))
                {
                    $ordinativo_voci = new \stdClass();
                    $ordinativo_voci->{0} = new \stdClass();
                    $ordinativo_voci->{0}->descrizione = $fatturazione_scadenza->descrizione;
                    $ordinativo_voci->{0}->quantita = 1;
                    $ordinativo_voci->{0}->iva = 22;
                    $ordinativo_voci->{0}->importo_singolo = $fatturazione_scadenza->importo;
                }
            }
            else
              $ordinativo_voci = !empty($ordinativo->offerta->voci) ? $ordinativo->offerta->voci()->where('accettata', 1)->get() : '';
        }
        elseif(!empty($fatturazione))
            $ordinativo = $fatturazione->ordinativo;

        if(empty($request->ordinativo_id))
            $clienti = Clienti::commerciali()->pluck('ragione_sociale', 'id')->prepend('Seleziona un cliente', '')->toArray();

        if(!empty($fatturazione_scadenza)){
            return view('commerciale::admin.fatturazioni.create' , compact('macrocategorie','clienti','ordinativi','fattura_numero','fattura_numero_next','fattura_numero_fepa','fattura_numero_fepa_next', 'ordinativo', 'ordinativo_voci', 'fatturazione_scadenza', 'fattura_numero_nota_di_credito_interna', 'fattura_numero_nota_di_credito_interna_codice'));
        } 
        return view('commerciale::admin.fatturazioni.create' , compact('macrocategorie','clienti','ordinativi','fattura_numero','fattura_numero_next','fattura_numero_fepa','fattura_numero_fepa_next', 'ordinativo', 'ordinativo_voci', 'fattura_numero_nota_di_credito_interna', 'fattura_numero_nota_di_credito_interna_codice'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateFatturazioneRequest $request
     * @return Response
     */
    public function store(CreateFatturazioneRequest $request)
    {
        // Fatturazione
        $ctrl_voce = false;
        $ctrl_voce_msg = false;

        $rules = Fatturazione::getRules();
        $this->validate($request, $rules);

        $fattura_pr = $request['fattura_pa'] == 1 ? false : true;

        // echo $request['nota_di_credito_interna'] . ' - ' . Fatturazione::get_next_numero_nota_di_credito_interna();
        // exit;
        if(($request['nota_di_credito_interna'] == 0 && $request['n_fattura'] != Fatturazione::get_next_numero_fattura($fattura_pr)) || ($request['nota_di_credito_interna'] == 1 && $request['n_fattura'] != Fatturazione::get_next_numero_nota_di_credito_interna()))
        {
            return back()->withInput()
                    ->withError('ATTENZIONE: Il numero di fatturazione non è corretto, riprovare!');
        }

        $rules_voci = FatturazioneVoce::getRules();

        foreach ($request->voci as $key => $voce) {

            $validator = Validator::make($voce, $rules_voci);

            if(!$validator->fails())
            {
                $ctrl_voce = true;
            }

        }

        if(!$ctrl_voce)
        {
            return back()->withInput()
                    ->withError('ATTENZIONE: inserire almeno una voce');
        }


        $insert = $request->all();
        $insert['azienda'] = session('azienda');
        $insert['created_user_id'] = Auth::id();
        $insert['updated_user_id'] = Auth::id();

        $fatturazione = Fatturazione::create($insert);

        // Voci
        foreach ($request->voci as $key => $voce)
        {
            $validator = Validator::make($voce, $rules_voci);

            if(!$validator->fails())
                $fatturazione->voci()->create($voce);
            else
                $ctrl_voce_msg = true;
        }

        // Fatturazione scadenza
        if(!empty($request->fatturazione_scadenza_id) && is_numeric($request->fatturazione_scadenza_id))
          FatturazioneScadenze::findOrFail($request->fatturazione_scadenza_id)
                              ->update(['fattura_id' => $fatturazione->id]);

        // // Fattura
        // if(!empty($fatturazione->ordinativo))
        // {
        //   $fatturazione->ordinativo->offerta->update(['fatturata' => 1]);
        // }

        // Log
        activity(session('azienda'))
            ->performedOn($fatturazione)
            ->withProperties($insert)
            ->log('created');

        // Return
        if($ctrl_voce_msg)
        {
            return redirect()->route('admin.commerciale.fatturazione.edit', $fatturazione->id)
                ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('commerciale::fatturazioni.title.fatturazioni')]))
                ->withWarning('Attenzione: una o più voci non sono state salvate perchè non compilate correttamnete');
        }

        return redirect()->route('admin.commerciale.fatturazione.edit', $fatturazione->id)
            ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('commerciale::fatturazioni.title.fatturazioni')]));
    }

    public function read(Fatturazione $fatturazione)
    {
        if($fatturazione->azienda != session('azienda'))
            return redirect()->route('admin.commerciale.fatturazione.index')
                    ->withWarning('AVVISO: non puoi accedere a questa fattura con l\'azienda ' . session('azienda'));

        $clienti = [0 => ''];
        $clienti = $clienti + Clienti::pluck('ragione_sociale', 'id')->toArray();

        $macrocategorie = [0 => 'Nessuna Macrocategoria'] + config('commerciale.fatturazioni.macrocategorie');

        $ordinativi = [0 => ''];
        $ordinativi = $ordinativi + Ordinativo::where('azienda', session('azienda'))
                                    ->pluck('oggetto', 'id')
                                    ->toArray();

        return view('commerciale::admin.fatturazioni.read', compact('macrocategorie', 'fatturazione','ordinativi','clienti'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Fatturazione $fatturazione
     * @return Response
     */
    public function edit(Fatturazione $fatturazione)
    {
        if($fatturazione->azienda != session('azienda'))
            return redirect()->route('admin.commerciale.fatturazione.index')
                    ->withWarning('AVVISO: non puoi accedere a questa fattura con l\'azienda ' . session('azienda'));

        $clienti = [0 => ''];
        $clienti = $clienti + Clienti::commerciali()->pluck('ragione_sociale', 'id')->toArray();

        $macrocategorie = [0 => 'Seleziona una categoria'] + config('commerciale.fatturazioni.macrocategorie');

        $ordinativi = [0 => ''];
        $ordinativi = $ordinativi + Ordinativo::where('azienda', session('azienda'))
                                    ->pluck('oggetto', 'id')
                                    ->toArray();

        return view('commerciale::admin.fatturazioni.edit', compact('fatturazione','ordinativi','clienti', 'macrocategorie'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Fatturazione $fatturazione
     * @param  UpdateFatturazioneRequest $request
     * @return Response
     */
    public function update(Fatturazione $fatturazione, UpdateFatturazioneRequest $request)
    {
        if($fatturazione->azienda != session('azienda'))
            return redirect()->route('admin.commerciale.fatturazione.index')
                    ->withWarning('AVVISO: non puoi accedere a questa fattura con l\'azienda ' . session('azienda'));

        $rules = Fatturazione::getRules();
        unset($rules['n_fattura']);
        $this->validate($request, $rules);

        $ctrl_voce = false;

        $update = $request->all();
        $update['updated_user_id'] = Auth::id();

        $rules_voci = FatturazioneVoce::getRules();

        foreach ($request->voci as $key => $voce)
        {
            $validator = Validator::make($voce, $rules_voci);

            if(!$validator->fails())
            {
                $ctrl_voce = true;
            }

        }

        if(!$ctrl_voce)
        {
            return back()->withInput()
                    ->withError('ATTENZIONE: inserire almeno una voce');
        }

        $fatturazione->update($update);

        // Voci
        $fatturazione->voci()->delete();

        if(!empty($request->voci))
        {
            foreach ($request->voci as $key => $voce)
            {
                $validator = Validator::make($voce, $rules_voci);

                if(!$validator->fails())
                {
                    $fatturazione->voci()->create($voce);
                }
            }
        }

        // Log
        activity(session('azienda'))
            ->performedOn($fatturazione)
            ->withProperties($update)
            ->log('updated');

        return redirect()->route('admin.commerciale.fatturazione.edit', $fatturazione->id)
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('commerciale::fatturazioni.title.fatturazioni')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Fatturazione $fatturazione
     * @return Response
     */
    public function destroy(Fatturazione $fatturazione)
    {
        if($fatturazione->azienda != session('azienda'))
            return redirect()->route('admin.commerciale.fatturazione.index')
                    ->withWarning('AVVISO: non puoi accedere a questa fattura con l\'azienda ' . session('azienda'));

        $this->fatturazione->destroy($fatturazione);

        // Log
        activity(session('azienda'))
            ->performedOn($fatturazione)
            ->withProperties(json_encode($fatturazione))
            ->log('destroyed');

        return redirect()->route('admin.commerciale.fatturazione.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('commerciale::fatturazioni.title.fatturazioni')]));
    }

    public function generaFattura($id)
    {
        $fatturazione = Fatturazione::findOrFail($id);

        if($fatturazione->azienda != session('azienda'))
            return redirect()->route('admin.commerciale.fatturazione.index')
                    ->withWarning('AVVISO: non puoi accedere a questa fattura con l\'azienda ' . session('azienda'));

        $titolo = !empty($fatturazione->nota_di_credito_interna) ? 'NOTA DI CREDITO' : 'FATTURA';

        $anno = '';
        if($fatturazione->fepa)
        {
            $anno = new Carbon($fatturazione->created_at);
            $anno = ' / ' . $anno->year;
        }

        $pdf = PDF::loadView('commerciale::admin.fatturazioni.genera_fattura', compact('titolo', 'fatturazione', 'anno'))->setPaper('a4');

        return $pdf->stream('Fattura ' . session('azienda') . ' - ' . $fatturazione->get_numero_fattura() . $anno . ' - ' . $fatturazione->oggetto . '.pdf');
    }

    public function generaXML($id)
    {
        $fattura = Fatturazione::findOrFail($id);

        if($fattura->azienda != session('azienda'))
            return redirect()->route('admin.commerciale.fatturazione.index')
                    ->withWarning('AVVISO: non puoi accedere a questa fattura con l\'azienda ' . session('azienda'));

        $azienda = get_azienda_dati();
        $iva_0 = config('commerciale.fatturazioni.iva_tipi');

        $data = set_date_ita($fattura->data);

        $anno = date('Y', strtotime($data));
        $formato_trasmissione = ($fattura->fattura_pa == 1) ? 'FPA12' : 'FPR12';
        $Body_tipodoc = ($fattura->nota_di_credito == 1) ? 'TD04' : 'TD01';
        $EsigibilitaIVA = ($fattura->fattura_pa == 1) ? 'S' : (($fattura->iva_esigibile == 1) ? 'I' : '');
        $CondizioniPagamento = 'TP02';
        $ModalitaPagamento = 'MP05';

        switch($fattura->tipo_pagamento)
        {
        	case '1' :
        		$DataScadenzaPagamento = date('Y-m-d', strtotime('+' . $fattura->n_giorni . ' days', strtotime($data)));
        		break;
        	case '2' :
        		$l_mese = date('m', strtotime('+1 month', strtotime($data)));
        		$l_anno = date('y', strtotime('+1 month', strtotime($data)));
        		$DataScadenzaPagamento =  date('Y-m-d', strtotime('+' . $fattura->n_giorni . ' days', strtotime($l_anno . '-' . $l_mese . '-01')));
        		break;
        	case '3' :
        		$DataScadenzaPagamento = $data;
                break;
        	default :
        		$DataScadenzaPagamento = $data;
        		break;
        }

        $IBAN = str_replace(' ', '', $fattura->iban);
        $ABI = substr($IBAN, 6, 5);
        $CAB = substr($IBAN, 11, 5);
        $video = false;

        // GENERAZIONE FILE XML
        define('XMLNS_WS', 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2');

        $FatturaElettronica = new \SimpleXMLElement("<q1:FatturaElettronica xmlns:q1='http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2'></q1:FatturaElettronica>", 0, false, 'q1', true);
        $FatturaElettronica->addAttribute('versione', $formato_trasmissione);

        $FatturaElettronicaHeader = $FatturaElettronica -> addChild('FatturaElettronicaHeader', null, '');

        $DatiTrasmissione = $FatturaElettronicaHeader -> addChild('DatiTrasmissione');

        $IdTrasmittente = $DatiTrasmissione -> addChild('IdTrasmittente');
        $IdTrasmittente -> addChild('IdPaese', $azienda->id_paese);
        $IdTrasmittente -> addChild('IdCodice', $azienda->id_codice);
        $DatiTrasmissione -> addChild('ProgressivoInvio', $fattura->id);
        $DatiTrasmissione -> addChild('FormatoTrasmissione', $formato_trasmissione);
        $DatiTrasmissione -> addChild('CodiceDestinatario', $fattura->codice_univoco);
        $ContattiTrasmittente = $DatiTrasmissione -> addChild('ContattiTrasmittente');
        //$ContattiTrasmittente -> addChild('Telefono');
        //$ContattiTrasmittente -> addChild('Email');
        if($fattura->cliente->pec != '')
            $DatiTrasmissione -> addChild('PECDestinatario', $fattura->cliente->pec);

        $CedentePrestatore = $FatturaElettronicaHeader -> addChild('CedentePrestatore');
        $DatiAnagrafici = $CedentePrestatore -> addChild('DatiAnagrafici');
        //if(($fattura['fattura_pa'] != '1') ){
        	$IdFiscaleIVA = $DatiAnagrafici -> addChild('IdFiscaleIVA');
        	$IdFiscaleIVA -> addChild('IdPaese', $azienda->id_paese);
        	$IdFiscaleIVA -> addChild('IdCodice', $azienda->p_iva);
        //}

        $DatiAnagrafici -> addChild('CodiceFiscale', $azienda->p_iva);

        $Anagrafica = $DatiAnagrafici -> addChild('Anagrafica');

        $Anagrafica -> addChild('Denominazione', $azienda->ragione_sociale);
        /*
        $Anagrafica -> addChild('Nome');
        $Anagrafica -> addChild('Cognome');
        $Anagrafica -> addChild('Titolo');*/
        //$Anagrafica -> addChild('CodEORI');
        /*
        $DatiAnagrafici -> addChild('AlboProfessionale');
        $DatiAnagrafici -> addChild('ProvinciaAlbo');
        $DatiAnagrafici -> addChild('NumeroIscrizioneAlbo');
        $DatiAnagrafici -> addChild('DataIscrizioneAlbo');*/
        $DatiAnagrafici -> addChild('RegimeFiscale', $azienda->regime_fiscale);
        $Sede = $CedentePrestatore -> addChild('Sede');
        $Sede -> addChild('Indirizzo', $azienda->indirizzo);
        $Sede -> addChild('CAP', $azienda->cap);
        $Sede -> addChild('Comune', $azienda->citta);
        $Sede -> addChild('Provincia', $azienda->provincia);
        $Sede -> addChild('Nazione', get_nazione_sigla($azienda->nazione));/*
        $StabileOrganizzazione = $CedentePrestatore -> addChild('StabileOrganizzazione');
        $StabileOrganizzazione -> addChild('Indirizzo');
        $StabileOrganizzazione -> addChild('NumeroCivico');
        $StabileOrganizzazione -> addChild('CAP');
        $StabileOrganizzazione -> addChild('Comune');
        $StabileOrganizzazione -> addChild('Provincia');
        $StabileOrganizzazione -> addChild('Nazione');*/
        $IscrizioneREA = $CedentePrestatore -> addChild('IscrizioneREA');
        $IscrizioneREA -> addChild('Ufficio', $azienda->rea_ufficio);
        $IscrizioneREA -> addChild('NumeroREA', $azienda->rea_numero);
        $IscrizioneREA -> addChild('CapitaleSociale', number_format(clean_currency($azienda->rea_capitale_sociale), 2, '.', ''));
        //$IscrizioneREA -> addChild('SocioUnico');
        $IscrizioneREA -> addChild('StatoLiquidazione', $azienda->rea_stato_liquidazione);
        $Contatti = $CedentePrestatore -> addChild('Contatti');
        $Contatti -> addChild('Telefono', $azienda->rl_telefono);
        //$Contatti -> addChild('Fax');
        $Contatti -> addChild('Email', $azienda->rl_email);
        /*
        $CedentePrestatore -> addChild('RiferimentoAmministrazione');
        $RappresentanteFiscale = $FatturaElettronicaHeader -> addChild('RappresentanteFiscale');
        $DatiAnagrafici = $RappresentanteFiscale -> addChild('DatiAnagrafici');
        $IdFiscaleIVA = $DatiAnagrafici -> addChild('IdFiscaleIVA');
        $IdFiscaleIVA -> addChild('IdPaese');
        $IdFiscaleIVA -> addChild('IdCodice');
        $DatiAnagrafici -> addChild('CodiceFiscale');
        $Anagrafica = $DatiAnagrafici -> addChild('Anagrafica');
        $Anagrafica -> addChild('Denominazione');
        $Anagrafica -> addChild('Nome');
        $Anagrafica -> addChild('Cognome');
        $Anagrafica -> addChild('Titolo');
        $Anagrafica -> addChild('CodEORI');
        */
        $CessionarioCommittente = $FatturaElettronicaHeader -> addChild('CessionarioCommittente');
        $DatiAnagrafici = $CessionarioCommittente -> addChild('DatiAnagrafici');
        $IdFiscaleIVA = $DatiAnagrafici -> addChild('IdFiscaleIVA');
        $IdFiscaleIVA -> addChild('IdPaese', $azienda->id_paese);
        $IdFiscaleIVA -> addChild('IdCodice', $fattura->cliente->p_iva);
        if($fattura->cliente->cod_fiscale != '' )
        {
            $DatiAnagrafici -> addChild('CodiceFiscale', $fattura->cliente->cod_fiscale);
        }
        $Anagrafica = $DatiAnagrafici -> addChild('Anagrafica');
        $Anagrafica -> addChild('Denominazione', htmlspecialchars($fattura->cliente->ragione_sociale));
        /*$Anagrafica -> addChild('Nome');
        $Anagrafica -> addChild('Cognome');
        $Anagrafica -> addChild('Titolo');
        $Anagrafica -> addChild('CodEORI');*/
        $Sede = $CessionarioCommittente -> addChild('Sede');
        $Sede -> addChild('Indirizzo', $fattura->cliente->sede()->indirizzo);
        //$Sede -> addChild('NumeroCivico');
        $Sede -> addChild('CAP', $fattura->cliente->sede()->cap);
        $Sede -> addChild('Comune', $fattura->cliente->sede()->citta);
        $Sede -> addChild('Provincia' ,  $fattura->cliente->sede()->provincia);
        $Sede -> addChild('Nazione', get_nazione_sigla($fattura->cliente->sede()->nazione));
        /*
        $StabileOrganizzazione = $CessionarioCommittente -> addChild('StabileOrganizzazione');
        $StabileOrganizzazione -> addChild('Indirizzo');
        $StabileOrganizzazione -> addChild('NumeroCivico');
        $StabileOrganizzazione -> addChild('CAP');
        $StabileOrganizzazione -> addChild('Comune');
        $StabileOrganizzazione -> addChild('Provincia');
        $StabileOrganizzazione -> addChild('Nazione');
        $RappresentanteFiscale = $CessionarioCommittente -> addChild('RappresentanteFiscale');
        $IdFiscaleIVA = $RappresentanteFiscale -> addChild('IdFiscaleIVA');
        $IdFiscaleIVA -> addChild('IdPaese');
        $IdFiscaleIVA -> addChild('IdCodice');
        $RappresentanteFiscale -> addChild('Denominazione');
        $RappresentanteFiscale -> addChild('Nome');
        $RappresentanteFiscale -> addChild('Cognome');*/
        /*
        $TerzoIntermediarioOSoggettoEmittente = $FatturaElettronicaHeader -> addChild('TerzoIntermediarioOSoggettoEmittente');
        $DatiAnagrafici = $TerzoIntermediarioOSoggettoEmittente -> addChild('DatiAnagrafici');
        $IdFiscaleIVA = $DatiAnagrafici -> addChild('IdFiscaleIVA');
        $IdFiscaleIVA -> addChild('IdPaese');
        $IdFiscaleIVA -> addChild('IdCodice');
        $DatiAnagrafici -> addChild('CodiceFiscale');
        $Anagrafica = $DatiAnagrafici -> addChild('Anagrafica');
        $Anagrafica -> addChild('Denominazione');
        $Anagrafica -> addChild('Nome');
        $Anagrafica -> addChild('Cognome');
        $Anagrafica -> addChild('Titolo');
        $Anagrafica -> addChild('CodEORI');
        $FatturaElettronicaHeader -> addChild('SoggettoEmittente');
        */
        //Body

        $FatturaElettronicaBody = $FatturaElettronica -> addChild('FatturaElettronicaBody', null, '');
        $DatiGenerali = $FatturaElettronicaBody -> addChild('DatiGenerali');
        $DatiGeneraliDocumento = $DatiGenerali -> addChild('DatiGeneraliDocumento');
        $DatiGeneraliDocumento -> addChild('TipoDocumento', $Body_tipodoc);
        $DatiGeneraliDocumento -> addChild('Divisa', $azienda->divisa);
        $DatiGeneraliDocumento -> addChild('Data', $data);
        $DatiGeneraliDocumento -> addChild('Numero', $fattura->get_numero_fattura());
        /*
        $DatiRitenuta = $DatiGeneraliDocumento -> addChild('DatiRitenuta');
        $DatiRitenuta -> addChild('TipoRitenuta');
        $DatiRitenuta -> addChild('ImportoRitenuta');
        $DatiRitenuta -> addChild('AliquotaRitenuta');
        $DatiRitenuta -> addChild('CausalePagamento');
        $DatiBollo = $DatiGeneraliDocumento -> addChild('DatiBollo');
        $DatiBollo -> addChild('BolloVirtuale');
        $DatiBollo -> addChild('ImportoBollo');
        $DatiCassaPrevidenziale = $DatiGeneraliDocumento -> addChild('DatiCassaPrevidenziale');
        $DatiCassaPrevidenziale -> addChild('TipoCassa');
        $DatiCassaPrevidenziale -> addChild('AlCassa');
        $DatiCassaPrevidenziale -> addChild('ImportoContributoCassa');
        $DatiCassaPrevidenziale -> addChild('ImponibileCassa');
        $DatiCassaPrevidenziale -> addChild('AliquotaIVA');
        $DatiCassaPrevidenziale -> addChild('Ritenuta');
        $DatiCassaPrevidenziale -> addChild('Natura');
        $DatiCassaPrevidenziale -> addChild('RiferimentoAmministrazione');*/
        /*
        $ScontoMaggiorazione = $DatiGeneraliDocumento -> addChild('ScontoMaggiorazione');
        $ScontoMaggiorazione -> addChild('Tipo');
        $ScontoMaggiorazione -> addChild('Percentuale');
        $ScontoMaggiorazione -> addChild('Importo');*/
        $DatiGeneraliDocumento -> addChild('ImportoTotaleDocumento', clean_currency($fattura->totale_fattura));
        //$DatiGeneraliDocumento -> addChild('Arrotondamento');
        if($fattura->note != '')
        {
            $DatiGeneraliDocumento -> addChild('Causale', $fattura->note);
        }
        //$DatiGeneraliDocumento -> addChild('Art73');
        if($fattura->cig != '' || $fattura->rda != '')
        {
        	$DatiContratto = $DatiGenerali -> addChild('DatiContratto');
        	$DatiContratto -> addChild('RiferimentoNumeroLinea', 1);
        	if($fattura->rda != '')
            {
        		$DatiContratto -> addChild('IdDocumento', $fattura->rda);
        	}
            else
            {
        		$DatiContratto -> addChild('IdDocumento', $anno . '-' . $fattura->cig);
        	}
        	if($fattura->rda_data != '')
            {
        		$DatiContratto -> addChild('Data', set_date_ita($fattura->rda_data));
        	}
            else
            {
        		$DatiContratto -> addChild('Data', $anno . '-01-01');
        	}
        	//$DatiContratto -> addChild('NumItem');
        	//$DatiContratto -> addChild('CodiceCommessaConvenzione');
        	//$DatiContratto -> addChild('CodiceCUP');
        	if($fattura->cig != '')
            {
                $DatiContratto -> addChild('CodiceCIG', $fattura->cig);
            }
        }

        /*
        $DatiGenerali -> addChild('DatiContratto');
        $DatiGenerali -> addChild('DatiConvenzione');
        $DatiGenerali -> addChild('DatiRicezione');
        $DatiGenerali -> addChild('DatiFattureCollegate');*/
        /*
        $DatiSAL = $DatiGenerali -> addChild('DatiSAL');
        $DatiSAL -> addChild('RiferimentoFase');*/
        /*
        $DatiDDT = $DatiGenerali -> addChild('DatiDDT');
        $DatiDDT -> addChild('NumeroDDT');
        $DatiDDT -> addChild('DataDDT');
        $DatiDDT -> addChild('RiferimentoNumeroLinea');

        $DatiTrasporto = $DatiGenerali -> addChild('DatiTrasporto');
        $DatiAnagraficiVettore = $DatiTrasporto -> addChild('DatiAnagraficiVettore');
        $IdFiscaleIVA = $DatiAnagraficiVettore -> addChild('IdFiscaleIVA');
        $IdFiscaleIVA -> addChild('IdPaese');
        $IdFiscaleIVA -> addChild('IdCodice');
        $DatiAnagraficiVettore -> addChild('CodiceFiscale');
        $Anagrafica = $DatiAnagraficiVettore -> addChild('Anagrafica');
        $Anagrafica -> addChild('Denominazione');
        $Anagrafica -> addChild('Nome');
        $Anagrafica -> addChild('Cognome');
        $Anagrafica -> addChild('Titolo');
        $Anagrafica -> addChild('CodEORI');
        $DatiAnagraficiVettore -> addChild('NumeroLicenzaGuida');
        $DatiTrasporto -> addChild('MezzoTrasporto');
        $DatiTrasporto -> addChild('CausaleTrasporto');
        $DatiTrasporto -> addChild('NumeroColli');
        $DatiTrasporto -> addChild('Descrizione');
        $DatiTrasporto -> addChild('UnitaMisuraPeso');
        $DatiTrasporto -> addChild('PesoLordo');
        $DatiTrasporto -> addChild('PesoNetto');
        $DatiTrasporto -> addChild('DataOraRitiro');
        $DatiTrasporto -> addChild('DataInizioTrasporto');
        $DatiTrasporto -> addChild('TipoResa');
        $IndirizzoResa = $DatiTrasporto -> addChild('IndirizzoResa');
        $IndirizzoResa -> addChild('Indirizzo');
        $IndirizzoResa -> addChild('NumeroCivico');
        $IndirizzoResa -> addChild('CAP');
        $IndirizzoResa -> addChild('Comune');
        $IndirizzoResa -> addChild('Provincia');
        $IndirizzoResa -> addChild('Nazione');
        $DatiTrasporto -> addChild('DataOraConsegna');*/
        /*
        $FatturaPrincipale = $DatiGenerali -> addChild('FatturaPrincipale');
        $FatturaPrincipale -> addChild('NumeroFatturaPrincipale');
        $FatturaPrincipale -> addChild('DataFatturaPrincipale');
        */
        // $ctrl_tipi_iva = [];
        $ctrl_bollo = '';
        $DatiBeniServizi = $FatturaElettronicaBody -> addChild('DatiBeniServizi');

        foreach ($fattura->voci as $key => $voce)
        {
        	$l_NumeroLinea = $key+1;
        	$l_Descrizione = $voce->descrizione;
        	$l_quantita = ($voce->quantita > 0) ? $voce->quantita : 1;
        //	$l_DataInizioPeriodo = $anno . '-01-01';
        	//$l_DataFinePeriodo = $anno . '-12-31';
        	$l_PrezzoUnitario = clean_currency(!empty($voce->importo_singolo) ? $voce->importo_singolo : $fattura->totale_netto);
        	$l_PrezzoTotale = $l_quantita * $l_PrezzoUnitario;
        	$l_AliquotaIVA = $voce->iva;
        	$l_natura = ($voce->iva_tipo != '0') ? substr($voce->iva_tipo, 0, 2) : "";

          // if(array_search($voce->iva_tipo, $ctrl_tipi_iva) === false)
          // {
            // $ctrl_tipi_iva[] = $voce->iva_tipo;
            $iva_ar = ($voce->iva_tipo == '0') ? 1.22 : 1;
            $tipi_iva_key = substr($voce->iva_tipo, 0, 2);
            $tipi_iva_imposta = ($l_PrezzoTotale * $iva_ar) - $l_PrezzoTotale;

            $tipi_iva[$tipi_iva_key] = [
              'AliquotaIVA' => number_format($l_AliquotaIVA, 2, '.', ''),
              'Natura' => $l_natura,
              'ImponibileImporto' => (!empty($tipi_iva[$tipi_iva_key]['ImponibileImporto']) ? $tipi_iva[$tipi_iva_key]['ImponibileImporto'] + $l_PrezzoTotale : $l_PrezzoTotale),
              'Imposta' => (!empty($tipi_iva[$tipi_iva_key]['Imposta']) ? $tipi_iva[$tipi_iva_key]['Imposta'] + $tipi_iva_imposta : $tipi_iva_imposta)
            ];

            if($ctrl_bollo !== true && $voce->iva_tipo != '0')
              $ctrl_bollo = true;
          // }

            $DettaglioLinee = $DatiBeniServizi -> addChild('DettaglioLinee');
            $DettaglioLinee -> addChild('NumeroLinea', $l_NumeroLinea);
            //$DettaglioLinee -> addChild('TipoCessionePrestazione');
            /*$CodiceArticolo = $DettaglioLinee -> addChild('CodiceArticolo');
            $CodiceArticolo -> addChild('CodiceTipo');
            $CodiceArticolo -> addChild('CodiceValore');*/
            $DettaglioLinee -> addChild('Descrizione', $l_Descrizione);
            $DettaglioLinee -> addChild('Quantita', number_format($l_quantita, 2 , '.', ''));
            //$DettaglioLinee -> addChild('UnitaMisura');
            //$DettaglioLinee -> addChild('DataInizioPeriodo', $l_DataInizioPeriodo);
            //$DettaglioLinee -> addChild('DataFinePeriodo', $l_DataFinePeriodo);
            $DettaglioLinee -> addChild('PrezzoUnitario' , number_format($l_PrezzoUnitario, 2, '.', ''));
            /*$ScontoMaggiorazione = $DettaglioLinee -> addChild('ScontoMaggiorazione');
            $ScontoMaggiorazione -> addChild('Tipo');
            $ScontoMaggiorazione -> addChild('Percentuale');
            $ScontoMaggiorazione -> addChild('Importo');
            */
            $DettaglioLinee -> addChild('PrezzoTotale' , number_format($l_PrezzoTotale, 2, '.', ''));
            $DettaglioLinee -> addChild('AliquotaIVA', number_format($l_AliquotaIVA, 2, '.', ''));
            //$DettaglioLinee -> addChild('Ritenuta');
            if($l_natura!= '')
                $DettaglioLinee -> addChild('Natura', $l_natura);
            //$DettaglioLinee -> addChild('RiferimentoAmministrazione');

        }

        if($ctrl_bollo)
        {
          // $DettaglioLinee = $DatiBeniServizi -> addChild('DettaglioLinee');
          // $DettaglioLinee -> addChild('NumeroLinea', ++$l_NumeroLinea);
          // $DettaglioLinee -> addChild('Descrizione', 'Imposta di bollo assolta in modo virtuale ai sensi del D.M. 17.6.2014');
          // $DettaglioLinee -> addChild('Quantita', '1.00');
          // $DettaglioLinee -> addChild('PrezzoUnitario', '2.00');
          // $DettaglioLinee -> addChild('PrezzoTotale', '2.00');
          // $DettaglioLinee -> addChild('AliquotaIVA', '0.00');
          //
          // $DatiBollo = $DatiGeneraliDocumento -> addChild('DatiBollo');
          // $DatiBollo -> addChild('BolloVirtuale');
          // $DatiBollo -> addChild('ImportoBollo', '2.00');

          // $DatiGeneraliDocumento -> addChild('Art73');
        }

        /*
        $AltriDatiGestionali = $DettaglioLinee -> addChild('AltriDatiGestionali');
        $AltriDatiGestionali -> addChild('TipoDato');
        $AltriDatiGestionali -> addChild('RiferimentoTesto');
        $AltriDatiGestionali -> addChild('RiferimentoNumero');
        $AltriDatiGestionali -> addChild('RiferimentoData');*/

        foreach ($tipi_iva as $natura => $tipo_iva)
        {
          $DatiRiepilogo = $DatiBeniServizi -> addChild('DatiRiepilogo');
          $DatiRiepilogo -> addChild('AliquotaIVA', $tipo_iva['AliquotaIVA']);
          if($natura != '0')
              $DatiRiepilogo -> addChild('Natura' , $tipo_iva['Natura']);
          //$DatiRiepilogo -> addChild('SpeseAccessorie');
          //$DatiRiepilogo -> addChild('Arrotondamento');
          $DatiRiepilogo -> addChild('ImponibileImporto', number_format($tipo_iva['ImponibileImporto'], 2, '.', ''));
          $DatiRiepilogo -> addChild('Imposta' , number_format($tipo_iva['Imposta'], 2, '.', ''));
          if($EsigibilitaIVA != '')
              $DatiRiepilogo -> addChild('EsigibilitaIVA', $EsigibilitaIVA);
          if($fattura->riferimento_normativo != '')
              $DatiRiepilogo -> addChild('RiferimentoNormativo', $fattura->riferimento_normativo);
        }
        /*$DatiVeicoli = $FatturaElettronicaBody -> addChild('DatiVeicoli');
        $DatiVeicoli -> addChild('Data');
        $DatiVeicoli -> addChild('TotalePercorso');*/
        $DatiPagamento = $FatturaElettronicaBody -> addChild('DatiPagamento');
        $DatiPagamento -> addChild('CondizioniPagamento', $CondizioniPagamento);
        $DettaglioPagamento = $DatiPagamento -> addChild('DettaglioPagamento');
        //$DettaglioPagamento -> addChild('Beneficiario');
        $DettaglioPagamento -> addChild('ModalitaPagamento' , $ModalitaPagamento);
        $DettaglioPagamento -> addChild('DataRiferimentoTerminiPagamento', $data);
        //$DettaglioPagamento->addChild('GiorniTerminiPagamento');
        $DettaglioPagamento -> addChild('DataScadenzaPagamento', $DataScadenzaPagamento);
        $DettaglioPagamento -> addChild('ImportoPagamento', number_format(clean_currency($fattura->totale_importo_dovuto), 2, '.', ''));
        $DettaglioPagamento -> addChild('IBAN', $IBAN);
        $DettaglioPagamento -> addChild('ABI', $ABI);
        $DettaglioPagamento -> addChild('CAB', $CAB);
        //$DettaglioPagamento->addChild('DataLimitePagamentoAnticipato');
        //$DettaglioPagamento->addChild('PenalitaPagamentiRitardati');
        //$DettaglioPagamento->addChild('DataDecorrenzaPenale');
        //$DettaglioPagamento->addChild('CodicePagamento');

        $headers = [
            'Content-Type' => 'application/xml'
        ];
        if(!$video)
            $headers['Content-Disposition'] = 'attachment; filename=' . uniqid().'.xml';

        ob_clean();
        return response($FatturaElettronica -> asXML(), 200, $headers);
    }

    // Export excel
    public function exportExcel(Request $request)
    {
      $fatture = Fatturazione::filter($request->all())
                            ->where('azienda', session('azienda'))
                            ->orderBy('id', 'desc')
                            ->get();

      ob_clean();
      return Excel::download(new FatturazioneExport($fatture), session('azienda') . '_fatturazione.xlsx');
    }

    // Export voci excel
    public function exportVociExcel(Request $request)
    {
        $fatture_voci = Fatturazione::filter($request->all())
                        ->where('azienda', session('azienda'))
                        ->with('voci')
                        ->orderBy('id', 'desc')
                        ->get()
                        ->pluck('voci')
                        ->flatten();
        ob_clean();
        return Excel::download(new FatturazioneVociExport($fatture_voci), session('azienda') . '_voci_fatturazione.xlsx');
    }

}
