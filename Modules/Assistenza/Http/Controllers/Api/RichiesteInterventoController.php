<?php

namespace Modules\Assistenza\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Modules\Assistenza\Entities\RichiesteIntervento;

use Modules\Assistenza\Entities\RichiesteInterventoAzione;
use Modules\Assistenza\Http\Requests\CreateTicketInterventoRequest;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Amministrazione\Entities\ClienteAmbienti;
use Modules\Amministrazione\Entities\Clienti;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use Modules\Assistenza\Repositories\RichiesteInterventoRepository;
use Modules\Profile\Entities\Procedura;
use Modules\Profile\Entities\Area;
use Modules\Profile\Entities\Gruppo;
use Modules\Commerciale\Entities\Ordinativo;

class RichiesteInterventoController extends Controller {
	/**
	 * @var PageRepository
	 */
	private $richiesteintervento;

	public function __construct(RichiesteInterventoRepository $richiesteintervento) {
		//  parent::__construct();

		$this -> richiesteintervento = $richiesteintervento;
	}

	/** TODO
	 * dato l'id di un tkt nel campo  $request->all()['richiesta_id'] restituisce tutto la riga del db comprensiva di attività svolte e lo stato dell tkt
	 *
	 * @param  CreateRichiesteInterventoRequest $request->all()['richiesta_id']
	 * @return
	 */
	public function getDettaglioTkt(Request $request) {

		$ticket_id = $request -> richiesta_id;
		$richiesta = RichiesteIntervento::findOrFail($ticket_id);
		$azioni = RichiesteInterventoAzione::where("ticket_id", $ticket_id) -> get();
		$ordinativo = $richiesta->ordinativo;

		if (empty($richiesta)) {
			$azioni = null;
		} else {
			$cliente = Clienti::findOrFail($richiesta -> cliente_id);
			if (empty($cliente)) {
				$area = null;
			}
			$area = $richiesta -> area;
			if (empty($area)) {
				$area = null;
			}
		}
		if (!count($azioni) > 0) {
			$azioni = null;
		}

		$stato = $richiesta->stato;

		if (!empty($stato)) {
			if ($stato == 1 || $stato == 2 || $stato == 5 || $stato == 6 || $stato == 7) { $stato_string = "In Lavorazione";
			}
			if ($stato == 3) { $stato_string = "Chiuso";
			}
			if ($stato == 4) { $stato_string = "Sospeso";
			}
		} else {
			$stato_string = "Aperto";
		}
		return response() -> json(['richiesta' => $richiesta, 'area' => $area, 'cliente' => $cliente, 'azioni' => $azioni, 'stato' => $stato_string, 'ordinativo' => $ordinativo]);
	}

	/** TODO
	 *   restituisce l'elenco dei tkt dati i filtri.
	 *
	 * @param   $request->all()['cliente_id']
	 * @param   $request->all()['richiedente'],
	 * @param   $request->all()['stato'] ( 0 = in lavorazione ,1 chiuso)
	 * @param   $request->all()['numero']
	 * @param   $request->all()['gruppo_id']
	 * @param   $request->all()['procedura_id']
	 * 	 * @return Response
	 */
	public function getElencoTkt(Request $request) {

		$richieste = RichiesteIntervento::where('cliente_id', $request -> cliente_id);

		if(!empty($request -> richiedente)){
			$richieste = $richieste -> where('richiedente', $request -> richiedente);
		}
		if(!empty($request -> numero)){
			$richieste = $richieste -> where('numero', $request -> numero);
		}
		if (!empty($request -> gruppo_id)){
			$richieste = $richieste -> where('gruppo_id', $request -> gruppo_id);
		}
		if (!empty($request -> procedura_id)){
			$richieste = $richieste -> where('procedura_id', $request -> procedura_id);
		}
		if (isset($request -> stato)){
			if($request->stato == 0){
				$richieste = $richieste->whereDoesntHave('azioni', function ($q) {
					$q->where('tipo', 3);
				});
			} else {
				$richieste = $richieste->whereHas('azioni', function ($q) {
					$q->where('tipo', 3);
				});
			}
		}
		$richieste = $richieste->get();
		$richiests = array();
		if($richieste->count() > 0 ){
			foreach(  $richieste  as $keys =>   $richiesta){
				$richiests[$keys] = $richiesta->toArray();
				$richiests[$keys]['stato'] = $richiesta->get_stato_text();
			}
		}

		return response() -> json($richiests);
	}

	/** TODO
	 * dato il numero db restituisce il record del cliente relativo, di seguito query corrispondente
	 * SELECT c.id, c.ragione_sociale  FROM amministrazione__clienti_ambienti a left join amministrazione__clienti c on c.id = a.cliente_id WHERE a.n_db LIKE '%{$ndb}%'
	 * @param  CreateRichiesteInterventoRequest  $request->all()['ndb']
	 * @return Response
	 */
	public function getCliente(Request $request)
	{
		$cliente_ambienti = ClienteAmbienti::where('n_db', 'like', "%" . $request -> ndb . "%") -> first();
		$cliente = Clienti::find($cliente_ambienti -> cliente_id);

		return response() -> json(['cliente' => $cliente, 'cliente_ambienti' => $cliente_ambienti]);
	}

    /** TODO
     * dato il gruppo id restituisce il record degli utenti relativi al gruppo
     * @param  CreateRichiesteInterventoRequest  $request->all()['gruppo_id']
     * @return Response
     */
	public function getUtenti(Request $request)
	{
		//dd($request->gruppo_id);
        $gruppo = Gruppo::findOrFail($request->gruppo_id);
        $utenti = [];
        if(!empty($gruppo)){
            $utenti = $gruppo->users;
        }

        return response() -> json($utenti);
    }

	/** TODO
	 * dato il numero db restituisce il record del cliente relativo, di seguito query corrispondente
	 * @param  CreateRichiesteInterventoRequest  $request->all()['hash_link']
	 * @return Response
	 */
	public function gethashCliente(Request $request) {

		$cliente = Clienti::where('hash_link', $request -> hash_link) -> first();

		return response() -> json($cliente);
	}

	public function gethashOrdinativo(Request $request) {

		$ordinativo = Ordinativo::where('hash_link', $request->hash_link)->first();

		$assistenza_per = $ordinativo->assistenza_per;
		$ordinativo_id = $ordinativo->id;
		$cliente_id = $ordinativo->cliente_id;
		$attivita = $ordinativo->assistenza;

		$result = [
			"assistenza_per" => $assistenza_per,
			"ordinativo_id" => $ordinativo_id,
			"cliente_id" => $cliente_id,
			"attivita" => $attivita
		];

		return response()->json($result);
	}

	public function getRichiesteOrdinativo(Request $request) {

		$ordinativo = Ordinativo::where('hash_link', $request->hash_link)->first();

		$limit = ($request->filled('limit') ? $request->limit : 20);
		$page = ($request->filled('page') ? $request->page : 1);

		if($ordinativo->api_password == $request->password){

			$aree = Area::all();
			$gruppi = Gruppo::all();

			$richieste = RichiesteIntervento::orderBy('numero', 'DESC')->where('ordinativo_id', $ordinativo->id)->filter($request->all())->paginateFilter($limit, ['*'], 'page', $page);
			
			if($richieste->count() > 0 ){
				foreach(  $richieste  as $keys =>   $richiesta){
					$richiesta->stato = $richiesta->get_stato_text();
					$richiesta->area = $aree->where('id', $richiesta->area_id)->first->titolo;
					$richiesta->gruppo = $gruppi->where('id', $richiesta->gruppo_id)->first->nome;
				}
			}
	
			return response() -> json(['richieste' => $richieste, 'cliente' => $ordinativo->cliente()->ragione_sociale]);

		}
	}

	/** TODO
	 *
	 * @param  CreateRichiesteInterventoRequest  $request->all()['id']
	 * @return Response
	 */
	public function getIdCliente(Request $request) {
		$cliente = Clienti::where('id', $request -> cliente_id) -> first();
		return response() -> json($cliente);
	}

	/** TODO
	 *
	 * @param  CreateRichiesteInterventoRequest  $request->all()['gruppo_id']
	 * @return Response
	 */
	public function getGruppoId(Request $request) {
		$gruppo = Gruppo::where('id', $request -> gruppo_id) -> first();
		return response() -> json($gruppo);
	}

	/** TODO
	 *
	 * @param  CreateRichiesteInterventoRequest  $request->all()['area_id']
	 * @return Response
	 */
	public function getAreaId(Request $request) {
		$area = Area::where('id', $request -> area_id) -> first();
		return response() -> json($area);
	}

	/** TODO
	 * restituisce l'alberatura delle aree
	 * SELECT pa.id as area_id , g.id as gruppo_id , pa.titolo as area_di_intervento, g.nome as gruppo FROM profile__aree pa left join profile__gruppi g on g.area_id = pa.id   WHERE pa.procedura_id = {4} and g.visibile_web = 1 order by  g.area_id,g.id
	 * @param  CreateRichiesteInterventoRequest $request  $request->all()['procedura_id']  (default 4 )
	 * @return Response
	 */
	public function getAree(Request $request) {
		$result = [];
		$procedura_id = !empty($request -> procedura_id) ? $request -> procedura_id : 4;
		$procedura = Procedura::findOrFail($procedura_id);

		foreach ($procedura->aree as $area) {
			foreach ($area->attivita as $gruppo) {
				$result[] = ['area_id' => $area -> id, 'gruppo_id' => $gruppo -> id, 'area_di_intervento' => $area -> titolo, 'gruppo' => $gruppo -> nome, 'gruppo_visibile_web' => $gruppo->visibile_web];
			}
		}

		return response() -> json($result);
	}

	/**
	 * apertura tkt da web
	 *
	 * @param  CreateRichiesteInterventoRequest $request
	 * @return Response
	 */
	public function ticketWeb(Request $request) {
		$oggetto = $request->all()['message_id'];
		if(!empty($oggetto)){
			$verifica = RichiesteIntervento::where('message_id', $oggetto)->first();
			if(empty($verifica)){
				$resp = RichiesteIntervento::create_ticket_web($request);
				return response() -> json($resp); 
			}
		} else {
			$resp = RichiesteIntervento::create_ticket_web($request);
			return response() -> json($resp); 			
		}
	}

}