<?php

namespace Modules\Amministrazione\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Amministrazione\Entities\Clienti;
use Modules\Amministrazione\Http\Requests\CreateClientiRequest;
use Modules\Amministrazione\Http\Requests\UpdateClientiRequest;
use Modules\Amministrazione\Repositories\ClientiRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Amministrazione\Entities\ClienteIndirizzi;
use Modules\Amministrazione\Entities\ClienteReferenti;
use Modules\Amministrazione\Entities\ClienteAmbienti;
use Modules\Commerciale\Entities\Ordinativo;
use Modules\Commerciale\Entities\OrdinativoGiornate;
use Modules\Wecore\Entities\Meta;
use Modules\User\Entities\Sentinel\User;
use Modules\Commerciale\Entities\CensimentoCliente;
use Modules\Commerciale\Entities\SegnalazioneOpportunita;

class ClientiController extends AdminBaseController
{
    /**
     * @var ClientiRepository
     */
    private $clienti;

    public function __construct(ClientiRepository $clienti)
    {
        parent::__construct();

        $this->clienti = $clienti;
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
          $res['order']['by'] = 'ragione_sociale';
          $res['order']['sort'] = 'asc';
          $request->merge($res);
        }

        $clienti = Clienti::withoutGlobalScope('clienti')->filter($request->all())
                            // ->where('azienda', session('azienda'))
                            ->paginateFilter(config('wecore.pagination.limit'));

        $request->flash();

        return view('amministrazione::admin.clienti.index', compact('clienti'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $cliente = new Clienti();

        if($request->filled('ragione_sociale')){
            $cliente->ragione_sociale = $request->ragione_sociale;
        }

        if($request->filled('segnalazione_opportunita')){
            $cliente->segnalazione_opportunita = $request->segnalazione_opportunita;
        }

        $ordinativi = [];
        $commerciali_id = User::elencoCommerciali()->pluck('id');
        $commerciali = [''] + User::whereIn('id', $commerciali_id)->get()->pluck('full_name', 'id')->toArray();
        return view('amministrazione::admin.clienti.create',compact('ordinativi', 'commerciali', 'cliente'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateClientiRequest $request
     * @return Response
     */
    public function store(Request $request)
    {
        $rules = Clienti::getRules();
        $this->validate($request, $rules);

        $rules = ClienteIndirizzi::getRulesStore();
        $this->validate($request, $rules);

        $insert = $request->all();
        $insert['azienda'] = session('azienda');

        $cliente = $this->clienti->create($insert);

        //AGGIORNO HASH
        $cliente->hash_link = md5((now().$cliente->id));
        $cliente->save();

        // Indirizzo
        $indirizzo = new ClienteIndirizzi($insert['indirizzo_base']);
        $cliente->indirizzi()->save($indirizzo);

        // Log
        activity(session('azienda'))
            ->performedOn($cliente)
            ->withProperties($insert)
            ->log('created');

        //Segnalazione Opportunità 
        if($request->filled('segnalazione_opportunita')){
            $segnalazione = SegnalazioneOpportunita::find($request->segnalazione_opportunita);
            if(!empty($segnalazione)){
                $segnalazione->update(['cliente_id' => $cliente->id]);
                return redirect()->route('admin.commerciale.segnalazioneopportunita.edit', $segnalazione->id)->withSuccess('Anagrafica creata e collegata alla segnalazione commerciale.');
            }
        }

        return redirect()->route('admin.amministrazione.clienti.edit', $cliente->id)
            ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('amministrazione::clienti.title.clienti')]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Clienti $clienti
     * @return Response
     */
    public function edit(Clienti $cliente)
    {
        // if($cliente->azienda != session('azienda'))
        //     return $this->index();
        $ordinativi = [""=>""];
        $ordinativi += Ordinativo::where('azienda', session('azienda'))->where('cliente_id', $cliente->id)->pluck('oggetto', 'id')->toArray();

        $commerciali_id = User::elencoCommerciali()->pluck('id');
        $commerciali = [''] + User::whereIn('id', $commerciali_id)->get()->pluck('full_name', 'id')->toArray();

        return view('amministrazione::admin.clienti.edit', compact('cliente','ordinativi', 'commerciali'));
    }



  /**
     * Show the form for read the specified resource.
     *
     * @param  Clienti $clienti
     * @return Response
     */
    public function read(Clienti $cliente)
    {

        $ordinativi_cliente = Ordinativo::where('cliente_id', $cliente->id)->pluck('id')->toArray();

        $giornate_cliente = OrdinativoGiornate::whereIn('ordinativo_id', $ordinativi_cliente)->with('ordinativo')->get()->groupBy('ordinativo.oggetto');

        return view('amministrazione::admin.clienti.read', compact('cliente', 'giornate_cliente'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Clienti $clienti
     * @param  UpdateClientiRequest $request
     * @return Response
     */
    public function update(Clienti $cliente, UpdateClientiRequest $request)
    {
        $rules = Clienti::getRules();
        $this->validate($request, $rules);

        $update = $request->all();

        $update['aree'] = empty($update['aree']) ? NULL : $update['aree'];

        if(empty($cliente->hash_link))
        {
            $update['hash_link'] = md5((now().$cliente->id));
        }

        if(isset($update['elimina']) && $update['elimina']['logo'] == 1)
            $update['logo'] =  NULL;

        $this->clienti->update($cliente, $update);

        save_meta($update['meta'], $cliente);

        // Log
        activity(session('azienda'))
            ->performedOn($cliente)
            ->withProperties($update)
            ->log('updated');

        return redirect()->route('admin.amministrazione.clienti.edit', $cliente->id)
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('amministrazione::clienti.title.clienti')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Clienti $clienti
     * @return Response
     */
    public function destroy(Clienti $cliente)
    {
        $this->clienti->destroy($cliente);

        // Log
        activity(session('azienda'))
            ->performedOn($cliente)
            ->withProperties(json_encode($cliente))
            ->log('destroyed');

        return redirect()->route('admin.amministrazione.clienti.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('amministrazione::clienti.title.clienti')]));
    }

    // Indirizzi
    public function indirizziCreate($cliente_id)
    {
        $cliente = Clienti::findOrFail($cliente_id);

        return view('amministrazione::admin.clienti.indirizzi.fields', compact('cliente'));
    }

    public function indirizziStore($cliente_id, Request $request)
    {
        $rules = ClienteIndirizzi::getRules();

        if($request->ajax())
        {
            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }
        }
        else
        {
            $this->validate($request, $rules);
        }

        $indirizzo = new ClienteIndirizzi($request->all());
        $cliente = Clienti::findOrFail($cliente_id);

        $cliente->indirizzi()->save($indirizzo);

        if($request->ajax())
            return response()->json(['redirect' => route('admin.amministrazione.clienti.edit', $cliente->id)]);
        else
            return redirect()->route('admin.amministrazione.clienti.edit', $cliente->id)->withSuccess('Indirizzo inserito con successo');
    }

    public function indirizziEdit($id)
    {
        $indirizzo = ClienteIndirizzi::findOrFail($id);

        return view('amministrazione::admin.clienti.indirizzi.fields', compact('indirizzo'));
    }

    public function indirizziUpdate($id, Request $request)
    {
        $rules = ClienteIndirizzi::getRules();

        $indirizzo = ClienteIndirizzi::findOrFail($id);

        $validator = Validator::make($request->all(), $rules);
        if($request->ajax())
        {
            if($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }
        }
        else
        {
            if($validator->fails()) {
                return redirect()->route('admin.amministrazione.clienti.edit', $indirizzo->cliente->id)->withError($validator->errors()->first());
            }
        }

        $update = $request->all();

        $indirizzo->update($update);

        if($request->ajax())
            return response()->json(['redirect' => route('admin.amministrazione.clienti.edit', $indirizzo->cliente->id)]);
        else
            return redirect()->route('admin.amministrazione.clienti.edit', $indirizzo->cliente->id)->withSuccess('Indirizzo aggiornato con successo');
    }

    public function indirizziDestroy($id)
    {
        $indirizzo = ClienteIndirizzi::findOrFail($id);

        ClienteIndirizzi::destroy($id);

        return redirect()->route('admin.amministrazione.clienti.edit', $indirizzo->cliente->id)
            ->withSuccess('Indirizzo eliminato con successo');
    }

    // Referenti
    public function referentiCreate($cliente_id)
    {
        $cliente = Clienti::findOrFail($cliente_id);

        return view('amministrazione::admin.clienti.referenti.fields', compact('cliente'));
    }

    public function referentiStore($cliente_id, Request $request)
    {
        $rules = ClienteReferenti::getRules();

        if($request->ajax())
        {
            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }
        }
        else
        {
            $this->validate($request, $rules);
        }

        $referente = new ClienteReferenti($request->all());
        $cliente = Clienti::findOrFail($cliente_id);

        $cliente->referenti()->save($referente);

        if($request->ajax())
            return response()->json(['redirect' => route('admin.amministrazione.clienti.edit', $cliente->id)]);
        else
            return redirect()->route('admin.amministrazione.clienti.edit', $cliente->id)->withSuccess('Referente inserito con successo');
    }

    public function referentiEdit($id)
    {
        $referente = ClienteReferenti::findOrFail($id);

        return view('amministrazione::admin.clienti.referenti.fields', compact('referente'));
    }

    public function referentiUpdate($id, Request $request)
    {
        $rules = ClienteReferenti::getRules();

        if($request->ajax())
        {
            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }
        }
        else
        {
            $this->validate($request, $rules);
        }

        $referente = ClienteReferenti::findOrFail($id);
        $update = $request->all();

        $referente->update($update);

        if($request->ajax())
            return response()->json(['redirect' => route('admin.amministrazione.clienti.edit', $referente->cliente->id)]);
        else
            return redirect()->route('admin.amministrazione.clienti.edit', $referente->cliente->id)->withSuccess('Referente aggiornato con successo');
    }

    public function referentiDestroy($id)
    {
        $referente = ClienteReferenti::findOrFail($id);

        ClienteReferenti::destroy($id);

        return redirect()->route('admin.amministrazione.clienti.edit', $referente->cliente->id)
            ->withSuccess('Referente eliminato con successo');
    }

    public function clienteJson(Request $request)
    {
        $cliente_id = $request->cliente_id;
        $cliente = Clienti::findOrFail($cliente_id)->toJson();
        return response()->json($cliente);
    }

    public function indirizziJson(Request $request)
    {
        $cliente_id = $request->cliente_id;
        $indirizzi = Clienti::findOrFail($cliente_id)->indirizzi->toJson();
        return response()->json($indirizzi);
    }

    public function ambientiEdit($id)
    {
        $ambiente = ClienteAmbienti::findOrFail($id);

        return view('amministrazione::admin.clienti.ambienti.fields', compact('ambiente'));
    }

    public function ambientiUpdate($id, Request $request)
    {
        $rules = ClienteAmbienti::getRules();

        if($request->ajax())
        {
            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }
        }
        else
        {
            $this->validate($request, $rules);
        }

        $ambiente = ClienteAmbienti::findOrFail($id);
        $update = $request->all();
        $ambiente->update($update);

        if($request->ajax())
            return response()->json(['redirect' => route('admin.amministrazione.ambienti.edit', $ambiente->cliente->id)]);
        else
            return redirect()->route('admin.amministrazione.clienti.edit', $ambiente->cliente->id)->withSuccess('Informazioni PA aggiornate con successo');
    }

    public function ambientiCreate($cliente_id)
    {
        $cliente = Clienti::findOrFail($cliente_id);

        return view('amministrazione::admin.clienti.ambienti.fields', compact('cliente'));
    }

    public function ambientiStore($cliente_id, Request $request)
    {
        $rules = ClienteAmbienti::getRules();

        if($request->ajax())
        {
            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }
        }
        else
        {
            $this->validate($request, $rules);
        }

        $ambiente = new ClienteAmbienti($request->all());
        $cliente = Clienti::findOrFail($cliente_id);

        $cliente->referenti()->save($ambiente);

        if($request->ajax())
            return response()->json(['redirect' => route('admin.amministrazione.clienti.edit', $cliente->id)]);
        else
            return redirect()->route('admin.amministrazione.clienti.edit', $cliente->id)->withSuccess('Informazioni PA inserita con successo');
    }

    public function loginUrbi($cliente_id)
    {
    		$urbi_general_pas = config('app.urbi_general_pass');
            $cliente = Clienti::findOrFail($cliente_id);
            $ambiente = $cliente->ambiente()->first();

            $HOSTURBI = $ambiente->ambiente;

            if (substr($ambiente->ambiente, -1) == '/') {
                $HOSTURBI = substr($ambiente->ambiente, 0, strlen($ambiente->ambiente) - 1);
            }
            $HOSTURBI = str_replace('http://', '', $HOSTURBI);
            $HOSTURBI = str_replace('https://', '', $HOSTURBI);

            $DIRURBI = 'urbi';
            $username_full = $ambiente->admin;
            $pass = ((trim($ambiente->password_admin) == '') ? $urbi_general_pas : $ambiente->password_admin);
            $erp = explode('@', $username_full);
            $username = $erp[0];
            $UTEIP = $this->getRealIpAddr();

            $link = "https://$HOSTURBI/$DIRURBI/progs/main/xapirest.sto?WTDK_REQ=IdBuildBae&WTDK_UTE_USERNAME=$username&WTDK_UTE_IP=$UTEIP&WTDK_RESPONSE_FORMAT=JSON";

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $link);
            curl_setopt($curl, CURLOPT_USERPWD, "$username_full:$pass");
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_USERAGENT, "Zucchetti -Php User Agent 1.0");
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $output = json_decode(curl_exec($curl));

            if(!empty($output) && !empty($output->result))
            {
                session(['token_urbi' => $output->result->TOKEN ]);
                $urlsso = str_replace('httpss', 'http', str_replace('http', 'https', $output -> result -> URLSSO));
                return redirect($urlsso);
            }
    }

    public function getRealIpAddr() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
        }

        if(strpos($ip, ",") !== false)
        {
            $splitter = explode(',',$ip);
            $ip = $splitter[1];
        }

		return trim($ip);
	}

    public function creaCensimento($cliente) {

        $cliente = Clienti::withoutGlobalScopes()->find($cliente);

        if($cliente->censimento()->first()){
            return redirect()->route('admin.amministrazione.clienti.edit', $cliente->id)->withSuccess('Il cliente ha già un censimento.');
        }

        $data = array(
            'cliente' => $cliente->ragione_sociale,
            'cap' => optional($cliente->indirizzi->first())->cap,
            'provincia' => optional($cliente->indirizzi->first())->provincia,
            'citta' => optional($cliente->indirizzi->first())->citta,
            'nazione' => optional($cliente->indirizzi->first())->nazione,
            'cliente_id' => $cliente->id,
            'azienda' => session('azienda'),
            'created_user_id' => Auth::id(),
            'updated_user_id' => Auth::id(),
        );

        $rules = CensimentoCliente::getRules();

        $validator = Validator::make($data, $rules);
        if($validator->fails()) {
            return redirect()
                ->route('admin.amministrazione.clienti.edit', $cliente->id)
                ->withErrors($validator)
                ->withInput();
        }

        $censimento = CensimentoCliente::create($data);

        if($censimento){

            activity(session('azienda'))
                ->performedOn($censimento)
                ->withProperties($data)
                ->log('created');

            return redirect()->route('admin.amministrazione.clienti.edit', $cliente->id)->withSuccess('Censimento cliente creato con successo.');
        } else {
            return redirect()->route('admin.amministrazione.clienti.edit', $cliente->id)->withSuccess('Impossibile creare un censimento cliente.');
        }
    }


}
