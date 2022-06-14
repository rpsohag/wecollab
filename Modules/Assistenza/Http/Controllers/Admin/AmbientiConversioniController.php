<?php

namespace Modules\Assistenza\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;

use Modules\Amministrazione\Entities\Clienti;
use Modules\Assistenza\Entities\AmbienteConversioni;
use Modules\User\Entities\Sentinel\User;
use Illuminate\Support\Facades\Auth;

class AmbientiConversioniController extends AdminBaseController
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
            $res['chiuso'] = 0;
            $request->merge($res);
        }

        $ambienti = AmbienteConversioni::filter($request->all())->paginateFilter(100);

        $clienti = Clienti::pluck('ragione_sociale', 'id')->toArray();

        $request->flash();

        if(!empty($ambienti))
          return view('assistenza::admin.ambienticonversioni.index', compact('ambienti', 'clienti'));
    }

    public function storeOrUpdate(Request $request)
    {
        $rules = AmbienteConversioni::getRules();

        if($request->filled('id')){

            $update = $request->all(); 
            $update['updated_user_id'] = Auth::id();

            $this->validate($request, $rules);

            $ambiente = AmbienteConversioni::find($request->id);
            $ambiente->update($update);

            activity(session('azienda'))
                ->performedOn($ambiente)
                ->withProperties($update)
                ->log('updated');

            if($ambiente) {
                return redirect()->route('admin.assistenza.ambienticonversioni.index')
                ->withSuccess('Hai modificato con successo l\'ambiente di conversione.');            
            } else {
                return redirect()->route('admin.assistenza.ambienticonversioni.index')
                    ->withError('Impossibile modificare l\'ambiente di conversione.');                 
            }

        } else {

            $create = $request->all();
            $create['azienda'] = session('azienda');
            $create['created_user_id'] = Auth::id();
            $create['updated_user_id'] = Auth::id(); 
            
            $this->validate($request, $rules);

            $ambiente = AmbienteConversioni::create($create);     

            activity(session('azienda'))
                ->performedOn($ambiente)
                ->withProperties($create)
                ->log('created');

            if($ambiente) {
                return redirect()->route('admin.assistenza.ambienticonversioni.index')
                ->withSuccess('Hai creato con successo l\'ambiente di conversione.');            
            } else {
                return redirect()->route('admin.assistenza.ambienticonversioni.index')
                    ->withError('Impossibile creare l\'ambiente di conversione.');                 
            }

        }
    }

    public function informations(Request $request)
    {

        $ambiente = AmbienteConversioni::find($request->id);

        if($ambiente){

            if($request->type == 'Dettaglio' || $request->type == 'Modifica' ){

                return response()->json(['ambiente' => $ambiente]);

            }

        }

    }

    public function destroy(AmbienteConversioni $ambiente)
    {
        if($ambiente->delete()){

            // Log
            activity(session('azienda'))
                ->performedOn($ambiente)
                ->withProperties(json_encode($ambiente))
                ->log('destroyed');

            return redirect()->route('admin.assistenza.ambienticonversioni.index')
            ->withSuccess('Hai eliminato con successo l\'ambiente di conversione');            
        } else {
            return redirect()->route('admin.assistenza.ambienticonversioni.index')
            ->withError('Impossibile eliminare l\'ambiente di conversione');            
        }
    }

}
