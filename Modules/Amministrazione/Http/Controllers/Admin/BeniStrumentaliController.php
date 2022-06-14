<?php

namespace Modules\Amministrazione\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;

use Modules\Amministrazione\Entities\Clienti;
use Modules\Amministrazione\Entities\BeneStrumentale;
use Modules\User\Entities\Sentinel\User;
use Illuminate\Support\Facades\Auth;

use Validator;

use PDF;

class BeniStrumentaliController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
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

        $beni = BeneStrumentale::filter($request->all())->paginateFilter(20);

        $utenti = [''] + User::all()->pluck('full_name', 'id')->toArray();

        $tipologie = [''] + config('amministrazione.beni.tipologie');

        return view('amministrazione::admin.benistrumentali.index', compact('beni', 'utenti', 'tipologie'));
    }

    public function storeOrUpdate(Request $request)
    {

        if($request->filled('id')){

            $update = $request->all(); 

            $rules = [
                'tipologia' => 'required',
                'user_id' => 'integer|required',
                'marca' => 'required',
                'modello' => 'required',
                'serial_number' => 'required|unique:beni_strumentali,serial_number,'.$update['id'],
                'imei' => 'nullable|unique:beni_strumentali,imei,'.$update['id'],
                'data_assegnazione' => 'required'
            ];

            $validator = Validator::make($update, $rules);
    
            if ($validator->fails()) {
                return redirect()->route('admin.amministrazione.benistrumentali.index')->withErrors($validator)->withInput();
            }

            $update['imei'] = preg_replace('/\s+/', '', $update['imei']);

            $bene = BeneStrumentale::find($request->id);
            $bene->update($update);

            activity(session('azienda'))
                ->performedOn($bene)
                ->withProperties($update)
                ->log('updated');

            if($bene) {
                return redirect()->route('admin.amministrazione.benistrumentali.index')->withSuccess('Hai modificato con successo il bene strumentale.');            
            } else {
                return redirect()->route('admin.amministrazione.benistrumentali.index')->withError('Impossibile modificare il bene strumentale.');                 
            }

        } else {

            $create = $request->all();

            $rules = [
                'tipologia' => 'required',
                'user_id' => 'integer|required',
                'marca' => 'required',
                'modello' => 'required',
                'serial_number' => 'required|unique:beni_strumentali',
                'imei' => 'nullable|unique:beni_strumentali,imei,',
                'data_assegnazione' => 'required'
            ];
            
            $validator = Validator::make($create, $rules);
    
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $create['imei'] = preg_replace('/\s+/', '', $create['imei']);

            $bene = BeneStrumentale::create($create);     

            activity(session('azienda'))
                ->performedOn($bene)
                ->withProperties($create)
                ->log('created');

            if($bene) {
                return redirect()->route('admin.amministrazione.benistrumentali.index')
                ->withSuccess('Hai creato con successo il bene strumentale.');            
            } else {
                return redirect()->route('admin.amministrazione.benistrumentali.index')
                    ->withError('Impossibile creare il bene strumentale.');                 
            }

        }
    }

    public function informations(Request $request)
    {

        $bene = BeneStrumentale::find($request->id);

        if($bene){

            if($request->type == 'Dettaglio' || $request->type == 'Modifica' ){

                return response()->json(['bene' => $bene]);

            }

        }

    }

    public function destroy(BeneStrumentale $bene)
    {
        if($bene->delete()){

            // Log
            activity(session('azienda'))
                ->performedOn($bene)
                ->withProperties(json_encode($bene))
                ->log('destroyed');

            return redirect()->route('admin.amministrazione.benistrumentali.index')
            ->withSuccess('Hai eliminato con successo il bene strumentale.');            
        } else {
            return redirect()->route('admin.amministrazione.benistrumentali.index')
            ->withError('Impossibile eliminare il bene strumentale.');            
        }
    }

    public function generaFoglioAssegnazione(BeneStrumentale $bene)
    {


        $utente = $bene->assegnatario()->first();

        if(empty($utente->profile()->first()->codice_fiscale) || empty($utente->profile()->first()->titolo) || empty($utente->profile()->first()->data_di_nascita))
        {
            return redirect()->back()->withError('L\'anagrafe dell\'assegnatario non è completa.');
        }


        $pdf = PDF::loadView('amministrazione::admin.benistrumentali.foglio_assegnazione', compact('bene'))->setPaper('a4');

        return $pdf->stream('We-COM - Assegnazione '.$bene->marca.' '.$bene->modello.' - '.$bene->assegnatario()->first()->full_name.'.pdf');
    }

}
