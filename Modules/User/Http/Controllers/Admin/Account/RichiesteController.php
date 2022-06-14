<?php

namespace Modules\User\Http\Controllers\Admin\Account;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\User\Contracts\Authentication;
use Modules\User\Http\Requests\UpdateProfileRequest;
use Modules\User\Repositories\UserRepository;
use Modules\User\Entities\Richieste;
use Modules\User\Entities\Approvazioni;
use Modules\Profile\Entities\Autovettura;
use Auth;
use Modules\User\Entities\Sentinel\User;
use Modules\Profile\Entities\Profile;
use Illuminate\Notifications\Notification;
use App\Notifications\RichiestaInviataNotification;
use Validator;
use Modules\Tasklist\Entities\Attivita;
use DB;
use Modules\User\Entities\VociTrasferte;
use Modules\User\Entities\VociKm;

class RichiesteController extends AdminBaseController
{

	public function approva($idRichiesta)
	{
		$tipologie_richieste = config('wecore.richieste.tipologie_richieste');
		//tipi richieste
		$richiesta = Richieste::find($idRichiesta);
		if($richiesta->tipologia == 1 || $richiesta->tipologia==2 || $richiesta->tipologia == 4 || $richiesta->tipologia==5)//ferie o permesso
		{
			$utente_richiedente = User::find($richiesta->user_id);

			if($richiesta->tipologia == 4 || $richiesta->tipologia==5)
			{
				$approvatori = json_decode($utente_richiedente->profile->approvatori_rimborsi);
			}
			else
			{
				$approvatori = json_decode($utente_richiedente->profile->approvatori_fpm);
			}
	
			if(in_array(Auth::id(),$approvatori))
			{
				//sei un utente che può approvatore
				$approvazione = Approvazioni::whereRichiestaId($idRichiesta)->whereApprovatoreId(Auth::id())->first();
				$approvazione->stato = 1;
				$approvazione->save();

				//verifico se sono l'ultimo
				$test_approvazione = Approvazioni::whereRichiestaId($idRichiesta)->whereStato(0)->count();

				$oggetto = "Richiesta ".$tipologie_richieste[$richiesta->tipologia]." ".ucwords($richiesta->user->full_name)." Approvata da ".ucfirst(Auth::user()->full_name);

				if($test_approvazione == 0)//tutti hanno approvato
				{
					$email_richiedente = $utente_richiedente->email;
					$richiesta->stato = 1;
					$richiesta->save();
					mail_send($email_richiedente, $oggetto, $richiesta,null, null,"MailApprovazioni");
				}
				else//rimanenti
				{
					$richiesta->stato = 0;
					$richiesta->save();
					$approvazione_tmp = Approvazioni::whereRichiestaId($idRichiesta)->whereStato(0)->first();
					$email_altro = $approvazione_tmp->user->email;
					mail_send($email_altro, $oggetto, $richiesta,null, null,"MailApprovazioni");
				}

			}
		}

		return redirect()->route('admin.account.richieste.read',$idRichiesta);
	}

	public function rifiuta($idRichiesta)
	{
		$tipologie_richieste = config('wecore.richieste.tipologie_richieste');

		//tipi richieste
		$richiesta = Richieste::find($idRichiesta);
		if($richiesta->tipologia == 1 || $richiesta->tipologia==2 || $richiesta->tipologia == 4 || $richiesta->tipologia==5)//ferie o permesso
		{
			$utente_richiedente = User::find($richiesta->user_id);
			
			if($richiesta->tipologia == 4 || $richiesta->tipologia==5)
			{
				$approvatori = json_decode($utente_richiedente->profile->approvatori_rimborsi);
			}
			else
			{
				$approvatori = json_decode($utente_richiedente->profile->approvatori_fpm);
			}

			$oggetto = "Richiesta ".$tipologie_richieste[$richiesta->tipologia]." ".ucwords($richiesta->user->full_name)." Bocciata da ".ucfirst(Auth::user()->full_name);

			if(in_array(Auth::id(),$approvatori))
			{
				//sei un utente che può approvatore
				$approvazione = Approvazioni::whereRichiestaId($idRichiesta)->whereApprovatoreId(Auth::id())->first();
				$approvazione->stato = 2;
				$approvazione->save();

				$email_richiedente = $utente_richiedente->email;
				$richiesta->stato = 2;
				$richiesta->save();
				mail_send($email_richiedente, $oggetto, $richiesta,null, null,"MailApprovazioni");
			}
		}

		return redirect()->route('admin.account.richieste.read',$idRichiesta);
	}

    public function index(Request $request)
    {
		$tipologie_richieste = config('wecore.richieste.tipologie_richieste');

		$utenti_controllabili = [];		
		
		$utente_corrente = Auth::id();

		$visuliazzatore_pers = Profile::whereRaw('visualizzatori LIKE \'%"'.$utente_corrente.'"%\' OR approvatori_fpm  LIKE \'%"'.$utente_corrente.'"%\' OR approvatori_rimborsi LIKE \'%"'.$utente_corrente.'"%\'  ')->get();
		
		foreach ($visuliazzatore_pers as $k => $v)
		{
			$utenti_controllabili[$v->user_id] = $v->user->full_name;
		}

		if(!empty($utenti_controllabili))
		{
			$utenti_controllabili = [Auth::id() => Auth::user()->full_name] + $utenti_controllabili;
			$utenti_controllabili = ["-1"=>"Tutti"] + $utenti_controllabili;
		}


		$res['order']['by'] = 'draft,id';
		$res['order']['sort'] = 'desc';
		$request->merge($res);
		$richieste = Richieste::whereUserId(Auth::id())->orderByDesc("draft")->orderByDesc("id")->paginateFilter(config('wecore.pagination.limit')); 	

		if($request->filled('tab'))
		{
			$richieste = Richieste::filter($request->all())->where('tipologia',$request->tab)->orderByDesc("draft")->orderByDesc("id")->paginateFilter(config('wecore.pagination.limit'));
		}

		$stato_richiesta = [-1=>'Tutte',0=>'Attesa',1=>'Approvata',2=>'Rifiutata'];

		$macchine_possedute = Auth::user()->autovetture()->get()->pluck('full_name','id')->toArray();

		$utenti = ["-1"=>"Tutti"] + User::all()->pluck('full_name', 'id')->toArray();

		return view('user::admin.account.richieste.index',compact('richieste','tipologie_richieste','stato_richiesta','macchine_possedute','request','utenti','utenti_controllabili','utente_corrente'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
		$mese_help = null;
		$macchina_sel = null;

		$tipologie_richieste = config('wecore.richieste.tipologie_richieste');

		$tipologie_permessi = config('wecore.richieste.tipologie_permessi');

		$mesi = config('wecore.richieste.mesi');

		$anni = [date('Y')=>date('Y'),date('Y')-1=>date('Y')-1];

		$tipi_trasferte = config('wecore.richieste.tipi_trasferte');

		$valori_sedi = config('wecore.richieste.sedi');

		$tipologia_sel = $request->tipologia;

		$attivita = [''] + Attivita::whereHas('users', function ($q) {
			$q->where('user_id', Auth::id());
		  })
		  ->orWhere('richiedente_id', Auth::id())
		  ->orWhereJsonContains('supervisori_id->'.Auth::id().'->user_id', (string)Auth::id())
		  ->join('amministrazione__clienti as c', 'c.id', '=', 'tasklist__attivita.cliente_id')
		  ->select('tasklist__attivita.id as id', DB::raw('CONCAT( tasklist__attivita.oggetto,"  (", c.ragione_sociale, ")") as oggettocompleto'))
		  ->pluck('oggettocompleto', 'id')
		  ->toArray();

		if($tipologia_sel == 4 || $tipologia_sel == 5)
		{
			$controllo_mese = date('d') <= 5 ? date('n')-1 : date('n');
			$mese_help = $controllo_mese ;

			$macchina_sel = Autovettura::find($request->autovettura);
			$bozza = Richieste::whereUserId(Auth::id())->whereDraft(1)->whereTipologia($tipologia_sel)->first();

			if(!empty($bozza))
			{
				return redirect()->route('admin.account.richieste.index',['tab'=>$tipologia_sel])->withError("Hai ancora una Richiesta Da Inviare");
			}
		}

        return view('user::admin.account.richieste.create',compact('attivita','mese_help','tipologie_richieste','tipologie_permessi','mesi','anni','tipologia_sel','tipi_trasferte','valori_sedi','macchina_sel'));
    }


	public function redirectError($tipologia)
	{
		$macchina_sel = null;

		$mese_help = null;

		$tipologie_richieste = config('wecore.richieste.tipologie_richieste');

		$tipologie_permessi =  config('wecore.richieste.tipologie_permessi');

		$mesi = config('wecore.richieste.mesi');

		$anni = [date('Y')=>date('Y'),date('Y')-1=>date('Y')-1];

		$tipi_trasferte = config('wecore.richieste.tipi_trasferte');

		$valori_sedi = config('wecore.richieste.sedi');

		$tipologia_sel = $tipologia;

		if($tipologia_sel == 4 || $tipologia_sel == 5)
		{
			$controllo_mese = date('d') <= 5 ? date('n')-1 : date('n');
			$mese_help = $controllo_mese ;
		}

		if($tipologia_sel == 5)
		{
			$macchina_sel = Autovettura::find(session("macchina"));
			$bozza = Richieste::whereUserId(Auth::id())->whereDraft(1)->first();

			if(!empty($bozza))
			{
				return redirect()->route('admin.account.richieste.index')->withError("Hai una Bozza da Eliminare o Inviare");
			}
		}

        return view('user::admin.account.richieste.create',compact('mese_help','tipologie_richieste','tipologie_permessi','mesi','anni','tipologia_sel','tipi_trasferte','valori_sedi','macchina_sel'));
	}

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateUserRequest $request
     * @return Response
     */
    public function store(Request $request)
    {
		$tipologie_richieste = config('wecore.richieste.tipologie_richieste');

		//verifico se sto salvando una bozza
		if($request->has('bozza'))
		{
			$imp_rimborso = 0;
			//verifico in che tipologia sono se km o trasferte
			if($request->tipologia_sel == 4)//trasferte
			{
				foreach($request->trasferte as $key => $value)
				{
					if($key > 0)
						$imp_rimborso += clean_currency($value['importo']);
				}

				$nuova_richiesta = new Richieste;
				$nuova_richiesta->user_id = Auth::id();
				$nuova_richiesta->tipologia = $request->tipologia_sel;
				$nuova_richiesta->stato = 0;
				$nuova_richiesta->meta = null;
				$nuova_richiesta->from = null;
				$nuova_richiesta->to = null;
				$nuova_richiesta->note = $request->note;
				$nuova_richiesta->mese = $request->mese;
				$nuova_richiesta->anno = $request->anno;
				$nuova_richiesta->targa = null;
				$nuova_richiesta->modello = null;
				$nuova_richiesta->costo_km = 0;
				$nuova_richiesta->totale = $imp_rimborso;
				$nuova_richiesta->draft = 1;

				$nuova_richiesta->save();

				foreach($request->trasferte as $key => $value)
				{
					if($key > 0)
					{
						$voce_trasf = new VociTrasferte;
						$voce_trasf->richiesta_id = $nuova_richiesta->id;
						$voce_trasf->data = $value['data'];
						$voce_trasf->tipologia = $value['id_tipo'];
						$voce_trasf->importo = clean_currency($value['importo']);
						$voce_trasf->attivita_id = $value['attivita'];
						$voce_trasf->ordinativo_id = !empty(Attivita::find($value['attivita'])) ? Attivita::find($value['attivita'])->ordinativo_id : 0;
						$voce_trasf->note = $value['motivazione'];

						$voce_trasf->save();
					}
				}
				
			}
			 
			if($request->tipologia_sel == 5)//km
			{
				
				$macchina = Autovettura::find($request->macchina_selezionata);

				foreach($request->km as $key => $value)
				{
					if($key > 0)
					{
						$imp_rimborso += intval($value['km']) * floatval(clean_currency($macchina->costo_km));
					}
				}

				$nuova_richiesta = new Richieste;
				$nuova_richiesta->user_id = Auth::id();
				$nuova_richiesta->tipologia = $request->tipologia_sel;
				$nuova_richiesta->stato = 0;
				$nuova_richiesta->meta = null;
				$nuova_richiesta->from = null;
				$nuova_richiesta->to = null;
				$nuova_richiesta->note = $request->note;
				$nuova_richiesta->mese = $request->mese;
				$nuova_richiesta->anno = $request->anno;
				$nuova_richiesta->targa = $macchina->targa;
				$nuova_richiesta->modello = $macchina->modello;
				$nuova_richiesta->costo_km = clean_currency($macchina->costo_km);
				$nuova_richiesta->totale = $imp_rimborso;
				$nuova_richiesta->draft = 1;

				$nuova_richiesta->save();

				foreach($request->km as $key => $value)
				{
					if($key > 0)
					{
						$voce_km = new VociKm;
						$voce_km->richiesta_id = $nuova_richiesta->id;
						$voce_km->data = $value['data'];
						$voce_km->partenza = $value['partenza'];
						$voce_km->arrivo = $value['arrivo'];
						$voce_km->km = $value['km'];
						$voce_km->ar = (isset($value['ar']) ? 1 : 0);
						$voce_km->attivita_id = $value['attivita'];
						$voce_km->ordinativo_id = !empty(Attivita::find($value['attivita'])) ? Attivita::find($value['attivita'])->ordinativo_id : 0;
						$voce_km->note = $value['motivazione'];

						$voce_km->save();
					}
				}


			}

			return redirect()->route('admin.account.richieste.index',['tab'=>$request->tipologia_sel])->withSuccess("Richiesta Salvata");
		}

		if($request->has('bozzaupdate'))
		{
			if($request->tipologia_sel == 4)
			{
				$imp_rimborso =0;

				foreach($request->trasferte as $key => $value)
				{
					$imp_rimborso += clean_currency($value['importo']);		
				}			

				$aggiorna_richiesta = Richieste::find($request->id_richiesta);
				$aggiorna_richiesta->note = $request->note;
				$aggiorna_richiesta->mese = $request->mese;
				$aggiorna_richiesta->anno = $request->anno;
				$aggiorna_richiesta->stato = 0;
				$aggiorna_richiesta->user_id = Auth::id();
				$aggiorna_richiesta->totale = $imp_rimborso;
				$aggiorna_richiesta->tipologia = $request->tipologia_sel;
				$aggiorna_richiesta->save();

				//elimino collegamenti e risalvo tutto
				$aggiorna_richiesta->vociTrasferte()->delete();

				foreach($request->trasferte as $key => $value)
				{
					$voce_trasf = new VociTrasferte;
					$voce_trasf->richiesta_id = $aggiorna_richiesta->id;
					$voce_trasf->data = $value['data'];
					$voce_trasf->tipologia = $value['id_tipo'];
					$voce_trasf->importo = clean_currency($value['importo']);
					$voce_trasf->attivita_id = $value['attivita'];
					$voce_trasf->ordinativo_id = !empty(Attivita::find($value['attivita'])) ? Attivita::find($value['attivita'])->ordinativo_id : 0;
					$voce_trasf->note = $value['motivazione'];

					$voce_trasf->save();
				}
			}

			if($request->tipologia_sel == 5)
			{
				$macchina = Autovettura::find($request->macchina_selezionata);

				$imp_rimborso =0;

				foreach($request->km as $key => $value)
				{
					if($key > 0)
					{
						$imp_rimborso += intval($value['km']) * floatval(clean_currency($macchina->costo_km));
					}
				}

				$aggiorna_richiesta = Richieste::find($request->id_richiesta);
				$aggiorna_richiesta->user_id = Auth::id();
				$aggiorna_richiesta->tipologia = $request->tipologia_sel;
				$aggiorna_richiesta->stato = 0;
				$aggiorna_richiesta->meta = null;
				$aggiorna_richiesta->from = null;
				$aggiorna_richiesta->to = null;
				$aggiorna_richiesta->note = $request->note;
				$aggiorna_richiesta->mese = $request->mese;
				$aggiorna_richiesta->anno = $request->anno;
				$aggiorna_richiesta->targa = $macchina->targa;
				$aggiorna_richiesta->modello = $macchina->modello;
				$aggiorna_richiesta->costo_km = clean_currency($macchina->costo_km);
				$aggiorna_richiesta->totale = $imp_rimborso;
				$aggiorna_richiesta->draft = 1;

				//elimino collegamenti e risalvo tutto
				$aggiorna_richiesta->vociKm()->delete();

				foreach($request->km as $key => $value)
				{
					if($key > 0)
					{
						$voce_km = new VociKm;
						$voce_km->richiesta_id = $aggiorna_richiesta->id;
						$voce_km->data = $value['data'];
						$voce_km->partenza = $value['partenza'];
						$voce_km->arrivo = $value['arrivo'];
						$voce_km->km = $value['km'];
						$voce_km->ar = (isset($value['ar']) ? 1 : 0);
						$voce_km->attivita_id = $value['attivita'];
						$voce_km->ordinativo_id = !empty(Attivita::find($value['attivita'])) ? Attivita::find($value['attivita'])->ordinativo_id : 0;
						$voce_km->note = $value['motivazione'];

						$voce_km->save();
					}
				}
			}

			return redirect()->route("admin.account.richieste.bozza",$aggiorna_richiesta)->withSuccess("Modifica Effettuata");
		}

		if($request->tipologia_sel == 1 || $request->tipologia_sel == 2 || $request->tipologia_sel == 3)//Ferie Malattia Permesso
		{
			if($request->tipologia_sel == 2)//se Permesso
			{
				$tipologie_permessi =  config('wecore.richieste.tipologie_permessi');

				$validator = Validator::make($request->all(), [
					'from' => 'required',
					'ora_inizio' => 'required',
					'ora_fine' => 'required',
					'tipo_permesso' => 'required'
				]);

				if ($validator->fails()) {
					return redirect()->route('admin.account.richieste.seleziona.create.error',$request->tipologia_sel)->withErrors($validator)->withInput();
				}

				$data_sql_from = "$request->from $request->ora_inizio:00";
				$data_sql_to = "$request->from $request->ora_fine:00";
				$meta = [$request->tipo_permesso => $tipologie_permessi[$request->tipo_permesso]];
			}
			else
			{
				
				$data_sql_from = $request->from." 00:00:00";
				$data_sql_to = $request->to." 00:00:00";
				$meta = null;

				$validator = Validator::make($request->all(), [
					'from' => 'required',
					'to' => 'required'
				]);
			
				if ($validator->fails()) {
					return redirect()->route('admin.account.richieste.seleziona.create.error',$request->tipologia_sel)->withErrors($validator)->withInput();
				}

			}

			$nuova_richiesta = new Richieste;
			$nuova_richiesta->user_id = Auth::id();
			$nuova_richiesta->tipologia = $request->tipologia_sel;
			$nuova_richiesta->meta = !empty($meta) ? $meta : null;
			$nuova_richiesta->from = set_sql_date($data_sql_from);
			$nuova_richiesta->to = set_sql_date($data_sql_to);
			$nuova_richiesta->note = $request->note;
			$nuova_richiesta->stato = 0;
			$nuova_richiesta->save();
			if($request->tipologia_sel == 3)//se malattia
			{
				$nuova_richiesta->stato = 1;//Approvata
				$nuova_richiesta->save();
				foreach(json_decode(Auth::user()->profile->approvatori_fpm) as $k => $appr)
				{
					$new_approv = new Approvazioni;
					$new_approv->richiesta_id = $nuova_richiesta->id;
					$new_approv->approvatore_id = $appr;
					$new_approv->stato = 1;
					$new_approv->save();
				}
			}
			$tipo_richiesta = $tipologie_richieste[$request->tipologia_sel];

			//Creo i record per le Approvazioni
			$count_mail = 0;

			foreach(json_decode(Auth::user()->profile->approvatori_fpm) as $k => $appr)
			{
				//Invia Mail Principale
				if($count_mail < 1)
				{
					$senders = User::find($appr)->email;
					$oggetto = "Richiesta ".$tipologie_richieste[$nuova_richiesta->tipologia]." ".ucwords($nuova_richiesta->user->full_name);
					mail_send($senders, $oggetto, $nuova_richiesta,null, null,"MailApprovazioni");
				}

				$new_approv = new Approvazioni;
				$new_approv->richiesta_id = $nuova_richiesta->id;
				$new_approv->approvatore_id = $appr;
				$new_approv->stato = 0;
				$new_approv->save();

				$count_mail++;
			}


			return redirect()->route('admin.account.richieste.index',['tab'=>$request->tipologia_sel])->withSuccess("Richiesta $tipo_richiesta Inviata");
		}


		if($request->tipologia_sel == 4)//Trasferte
		{
			//verifico se c'è almeno una trasferta
			if(count($request->trasferte) < 1)
			{
				return redirect()->route('admin.account.richieste.seleziona.create.error',$request->tipologia_sel)->withError("Devi Almeno inserire 1 trasferta");
			}

			
			$validator = Validator::make($request->all(), [
				'mese' =>'required',
				'anno' => 'required'
			]);

			if ($validator->fails()) {
				return redirect()->route('admin.account.richieste.seleziona.create.error',$request->tipologia_sel)->withErrors($validator)->withInput();
			}

			$imp_rimborso =0;

			foreach($request->trasferte as $key => $value)
			{
				if($request->has("id_richiesta"))
				{
					$imp_rimborso += clean_currency($value['importo']);
				}
				else
				{
					if($key > 0)
					{
						$imp_rimborso += clean_currency($value['importo']);
					}
					
				}
			}

			if($request->has("id_richiesta"))
			{
				$nuova_richiesta = Richieste::find($request->id_richiesta);
			}
			else
			{
				$nuova_richiesta = new Richieste;
			}

			$nuova_richiesta->note = $request->note;
			$nuova_richiesta->user_id = Auth::id();
			$nuova_richiesta->mese = $request->mese;
			$nuova_richiesta->anno = $request->anno;
			$nuova_richiesta->totale = $imp_rimborso;
			$nuova_richiesta->tipologia = $request->tipologia_sel;
			$nuova_richiesta->stato = 0;
			$nuova_richiesta->draft = 0;
			$nuova_richiesta->save();

			//elimino collegamenti e risalvo tutto
			$nuova_richiesta->vociTrasferte()->delete();

			foreach($request->trasferte as $key => $value)
			{
				if($request->has("id_richiesta"))
				{
					$voce_trasf = new VociTrasferte;
					$voce_trasf->richiesta_id = $nuova_richiesta->id;
					$voce_trasf->data = $value['data'];
					$voce_trasf->tipologia = $value['id_tipo'];
					$voce_trasf->importo = clean_currency($value['importo']);
					$voce_trasf->attivita_id = $value['attivita'];
					$voce_trasf->ordinativo_id = !empty(Attivita::find($value['attivita'])) ? Attivita::find($value['attivita'])->ordinativo_id : 0;
					$voce_trasf->note = $value['motivazione'];

					$voce_trasf->save();
				}
				else
				{
					if($key > 0)
					{
						$voce_trasf = new VociTrasferte;
						$voce_trasf->richiesta_id = $nuova_richiesta->id;
						$voce_trasf->data = $value['data'];
						$voce_trasf->tipologia = $value['id_tipo'];
						$voce_trasf->importo = clean_currency($value['importo']);
						$voce_trasf->attivita_id = $value['attivita'];
						$voce_trasf->ordinativo_id = !empty(Attivita::find($value['attivita'])) ? Attivita::find($value['attivita'])->ordinativo_id : 0;
						$voce_trasf->note = $value['motivazione'];

						$voce_trasf->save();
					}
				}
			}

			//Creo i record per le Approvazioni
			$count_mail = 0;

			foreach(json_decode(Auth::user()->profile->approvatori_rimborsi) as $k => $appr)
			{
				//Invia Mail Principale
				if($count_mail < 1)
				{
					$senders = User::find($appr)->email;
					$oggetto = "Richiesta ".$tipologie_richieste[$nuova_richiesta->tipologia]." ".ucwords($nuova_richiesta->user->full_name);
					mail_send($senders, $oggetto, $nuova_richiesta,null, null,"MailApprovazioni");
				}

				$new_approv = new Approvazioni;
				$new_approv->richiesta_id = $nuova_richiesta->id;
				$new_approv->approvatore_id = $appr;
				$new_approv->stato = 0;
				$new_approv->save();

				$count_mail++;
			}

			$tipo_richiesta  = $tipologie_richieste[$nuova_richiesta->tipologia];

			return redirect()->route('admin.account.richieste.index',['tab'=>$request->tipologia_sel])->withSuccess("Richiesta $tipo_richiesta Inviata");
		}

		if($request->tipologia_sel == 5)//Rimborso
		{
			session(['macchina' => $request->macchina_selezionata]);

			if(count($request->km) <= 1)
			{
				return redirect()->route('admin.account.richieste.seleziona.create.error',$request->tipologia_sel)->withError("Devi Almeno inserire 1 Percorso");
			}

			//verifico se c'è almeno un percorso
			$validator = Validator::make($request->all(), [
				'mese' =>'required',
				'anno' => 'required'
			]);

			if ($validator->fails()) {
				return redirect()->route('admin.account.richieste.seleziona.create.error',$request->tipologia_sel)->withErrors($validator)->withInput();
			}

			$macchina = Autovettura::find($request->macchina_selezionata);

			$validator = Validator::make($request->all(), [
				'mese' =>'required',
				'anno' => 'required'
			]);

			if ($validator->fails()) {
				return redirect()->route('admin.account.richieste.seleziona.create.error',$request->tipologia_sel)->withErrors($validator)->withInput();
			}

			$imp_rimborso =0;

			foreach($request->km as $key => $value)
			{
				if($request->has("id_richiesta"))
				{
					$imp_rimborso += intval($value['km']) * floatval(clean_currency($macchina->costo_km));
				}
				else
				{
					if($key > 0)
					{
						$imp_rimborso += intval($value['km']) * floatval(clean_currency($macchina->costo_km));
					}
					
				}
			}

			if($request->has("id_richiesta"))
			{
				$nuova_richiesta = Richieste::find($request->id_richiesta);
			}
			else
			{
				$nuova_richiesta = new Richieste;
			}

			$nuova_richiesta->user_id = Auth::id();
			$nuova_richiesta->tipologia = $request->tipologia_sel;
			$nuova_richiesta->stato = 0;
			$nuova_richiesta->meta = null;
			$nuova_richiesta->from = null;
			$nuova_richiesta->to = null;
			$nuova_richiesta->note = $request->note;
			$nuova_richiesta->mese = $request->mese;
			$nuova_richiesta->anno = $request->anno;
			$nuova_richiesta->targa = $macchina->targa;
			$nuova_richiesta->modello = $macchina->modello;
			$nuova_richiesta->costo_km = clean_currency($macchina->costo_km);
			$nuova_richiesta->totale = $imp_rimborso;
			$nuova_richiesta->draft = 0;

			$nuova_richiesta->save();

			//elimino collegamenti e risalvo tutto
			$nuova_richiesta->vociKm()->delete();

			foreach($request->km as $key => $value)
			{
				if($request->has("id_richiesta"))
				{
					if($key > 0)
					{
						$voce_km = new VociKm;
						$voce_km->richiesta_id = $nuova_richiesta->id;
						$voce_km->data = $value['data'];
						$voce_km->partenza = $value['partenza'];
						$voce_km->arrivo = $value['arrivo'];
						$voce_km->km = $value['km'];
						$voce_km->ar = (isset($value['ar']) ? 1 : 0);
						$voce_km->attivita_id = $value['attivita'];
						$voce_km->ordinativo_id = !empty(Attivita::find($value['attivita'])) ? Attivita::find($value['attivita'])->ordinativo_id : 0;
						$voce_km->note = $value['motivazione'];

						$voce_km->save();
					}
				}
				else
				{
					if($key > 0)
					{
						$voce_km = new VociKm;
						$voce_km->richiesta_id = $nuova_richiesta->id;
						$voce_km->data = $value['data'];
						$voce_km->partenza = $value['partenza'];
						$voce_km->arrivo = $value['arrivo'];
						$voce_km->km = $value['km'];
						$voce_km->ar = (isset($value['ar']) ? 1 : 0);
						$voce_km->attivita_id = $value['attivita'];
						$voce_km->ordinativo_id = !empty(Attivita::find($value['attivita'])) ? Attivita::find($value['attivita'])->ordinativo_id : 0;
						$voce_km->note = $value['motivazione'];

						$voce_km->save();
					}
				}
			}

			$tipo_richiesta = $tipologie_richieste[$request->tipologia_sel];

			$count_mail = 0;

			foreach(json_decode(Auth::user()->profile->approvatori_rimborsi) as $k => $appr)
			{
				//Invia Mail Principale
				if($count_mail < 1)
				{
					$senders = User::find($appr)->email;
					$oggetto = "Richiesta ".$tipologie_richieste[$nuova_richiesta->tipologia]." ".ucwords($nuova_richiesta->user->full_name);
					mail_send($senders, $oggetto, $nuova_richiesta,null, null,"MailApprovazioni");
				}

				$new_approv = new Approvazioni;
				$new_approv->richiesta_id = $nuova_richiesta->id;
				$new_approv->approvatore_id = $appr;
				$new_approv->stato = 0;
				$new_approv->save();

				$count_mail++;
			}

			return redirect()->route('admin.account.richieste.index',['tab'=>$request->tipologia_sel])->withSuccess("Richiesta $tipo_richiesta Inviata");
		}

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function bozza(Richieste $richiesta)
    {
		$macchina_sel = Autovettura::whereTarga($richiesta->targa)->first();

		$attivita = [''] + Attivita::whereHas('users', function ($q) {
			$q->where('user_id', Auth::id());
		  })
		  ->orWhere('richiedente_id', Auth::id())
		  ->orWhereJsonContains('supervisori_id->'.Auth::id().'->user_id', (string)Auth::id())
		  ->join('amministrazione__clienti as c', 'c.id', '=', 'tasklist__attivita.cliente_id')
		  ->select('tasklist__attivita.id as id', DB::raw('CONCAT( tasklist__attivita.oggetto,"  (", c.ragione_sociale, ")") as oggettocompleto'))
		  ->pluck('oggettocompleto', 'id')
		  ->toArray();

		$mesi = config('wecore.richieste.mesi');

		$anni = [date('Y')=>date('Y'),date('Y')-1=>date('Y')-1];

		$valori_sedi = config('wecore.richieste.sedi');

		$tipologia_sel = $richiesta->tipologia;

		$tipi_trasferte = config('wecore.richieste.tipi_trasferte');

	return view('user::admin.account.richieste.bozza',compact('attivita','tipi_trasferte','richiesta','macchina_sel','mesi','anni','valori_sedi','tipologia_sel'));
    }

    public function destroyBozzaTrasferte($id)
    {
		$richi = Richieste::find($id);
		$richi->delete();
		$richi->vociTrasferte()->delete();
		return redirect()->route('admin.account.richieste.index',['tab'=>4])->withSuccess("Richiesta Eliminata");
    }

	public function destroyBozzaKm($id)
    {
		$richi = Richieste::find($id);
		$richi->delete();
		$richi->vociKm()->delete();
		return redirect()->route('admin.account.richieste.index',['tab'=>5])->withSuccess("Richiesta Eliminata");
    }

	public function read($id)
	{
		$tipi_trasferte = config('wecore.richieste.tipi_trasferte');
		$tipologie_richieste = config('wecore.richieste.tipologie_richieste');

		$richiesta = Richieste::find($id);

		$macchina_sel = null;

		if($richiesta->tipologia == 5)
		{
			$macchina_sel = Autovettura::whereTarga($richiesta->targa)->first();
		}
		
		$utente = Auth::user();

		$current_user = Auth::id();

		$utenti = User::all()->pluck('full_name', 'id')->toArray();

		$attivita = [''] + Attivita::whereHas('users', function ($q) {
			$q->where('user_id', Auth::id());
		  })
		  ->orWhere('richiedente_id', Auth::id())
		  ->orWhereJsonContains('supervisori_id->'.Auth::id().'->user_id', (string)Auth::id())
		  ->join('amministrazione__clienti as c', 'c.id', '=', 'tasklist__attivita.cliente_id')
		  ->select('tasklist__attivita.id as id', DB::raw('CONCAT( tasklist__attivita.oggetto,"  (", c.ragione_sociale, ")") as oggettocompleto'))
		  ->pluck('oggettocompleto', 'id')
		  ->toArray();

		return view('user::admin.account.richieste.read',compact('attivita','richiesta','tipologie_richieste','macchina_sel','current_user','tipi_trasferte','utente','utenti'));
	}

	public function update($id,Request $request)
	{
		$richiesta = Richieste::find($id);

		if($richiesta->tipologia == 3)//solo se malattia
		{
			$richiesta->note = $request->note;
			$richiesta->save();
		}

		return redirect()->route('admin.account.richieste.read',$id)->withSuccess("Malattia Aggiornata");
	}

}
