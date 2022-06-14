<?php

namespace Modules\Tasklist\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Tasklist\Entities\Rinnovo;
use Modules\Tasklist\Http\Requests\CreateRinnovoRequest;
use Modules\Tasklist\Http\Requests\UpdateRinnovoRequest;
use Modules\Tasklist\Repositories\RinnovoRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Amministrazione\Entities\Clienti;
use Modules\Tasklist\Entities\RinnovoNotifica;

use Modules\User\Entities\Sentinel\User;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel;
use Modules\Export\Entities\RinnoviExport;

class RinnovoController extends AdminBaseController
{
    /**
     * @var RinnovoRepository
     */
    private $rinnovo;

    public function __construct(RinnovoRepository $rinnovo)
    {
        parent::__construct();

        $this->rinnovo = $rinnovo;
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
          $res['order']['by'] = 'data';
          $res['order']['sort'] = 'desc';
          $request->merge($res);
        }

        $clienti = Clienti::pluck('ragione_sociale', 'id')
                             ->toArray();

        $rinnovi = Rinnovo::filter($request->all())
                            ->whereHas('cliente', function ($q) {
                                $q->commerciali();
                            })
                            ->where('tasklist__rinnovi.azienda', session('azienda'))
                            ->paginateFilter(config('wecore.pagination.limit'));

        $request->flash();

        return view('tasklist::admin.rinnovi.index', compact('rinnovi','clienti'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $clienti = Clienti::pluck('ragione_sociale', 'id')
                             ->toArray();

        $utenti = User::select(DB::raw("CONCAT(last_name,' ',first_name) AS nome"), 'id')
                             ->pluck('nome', 'id')
                             ->toArray();

        return view('tasklist::admin.rinnovi.create', compact('clienti','utenti'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateRinnovoRequest $request
     * @return Response
     */
    public function store(CreateRinnovoRequest $request)
    {
        $rules = Rinnovo::getRules();
        $this->validate($request, $rules);


        $create = $request->all();
        $create['created_user_id'] = Auth::id();
        $create['updated_user_id'] = Auth::id();
        $create['azienda'] = session('azienda');

        $rinnovo = Rinnovo::create($create);

        $rinnovo_utenti = (!empty($create['utenti'])) ? $create['utenti'] : [];
        $rinnovo->utenti()->sync($rinnovo_utenti);

        if($create['notifiche']['add']['notifica'] != '-1' && $create['notifiche']['add']['cadenza'] > 0 && $create['notifiche']['add']['tipo'] > 0)
        {
            $notifica = new RinnovoNotifica($create['notifiche']['add']);
            $rinnovo->notifiche()->save($notifica);
        }

        // Log
        activity(session('azienda'))
            ->performedOn($rinnovo)
            ->withProperties($create)
            ->log('created');

        return redirect()->route('admin.tasklist.rinnovo.index')
            ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('tasklist::rinnovi.title.rinnovi')]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Rinnovo $rinnovo
     * @return Response
     */
    public function edit(Rinnovo $rinnovo)
    {
        if($rinnovo->azienda != session('azienda'))
            return redirect()->route('admin.tasklist.rinnovo.index')
                    ->withWarning('AVVISO: non puoi accedere a questo rinnovo con l\'azienda ' . session('azienda'));

        $clienti = Clienti::pluck('ragione_sociale', 'id')
                             ->toArray();

        $utenti = User::select(DB::raw("CONCAT(last_name,' ',first_name) AS nome"), 'id')
                         ->pluck('nome', 'id')
                         ->toArray();

        return view('tasklist::admin.rinnovi.edit', compact('rinnovo', 'clienti','utenti'));
    }




    /**
     * Show the specified resource.
     *
     * @param  Rinnovo $rinnovo
     * @return Response
     */
    public function read(Rinnovo $rinnovo)
    {
        if($rinnovo->azienda != session('azienda'))
            return redirect()->route('admin.tasklist.rinnovo.index')
                    ->withWarning('AVVISO: non puoi accedere a questo rinnovo con l\'azienda ' . session('azienda'));

        $clienti = Clienti::pluck('ragione_sociale', 'id')
                             ->toArray();

        $utenti = User::select(DB::raw("CONCAT(last_name,' ',first_name) AS nome"), 'id')
                         ->pluck('nome', 'id')
                         ->toArray();

        return view('tasklist::admin.rinnovi.read', compact('rinnovo', 'clienti','utenti' ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Rinnovo $rinnovo
     * @param  UpdateRinnovoRequest $request
     * @return Response
     */
    public function update(Rinnovo $rinnovo, UpdateRinnovoRequest $request)
    {
        if($rinnovo->azienda != session('azienda'))
            return redirect()->route('admin.tasklist.rinnovo.index')
                    ->withWarning('AVVISO: non puoi accedere a questo rinnovo con l\'azienda ' . session('azienda'));

        $rules = Rinnovo::getRules();
        $this->validate($request, $rules);

        $update = $request->all();

        $rinnovo_utenti = (!empty($update['utenti'])) ? $update['utenti'] : [];
        $rinnovo->utenti()->sync($rinnovo_utenti);

        foreach ($update['notifiche'] as $key => $notifica)
        {
            if($notifica['notifica'] != '-1' && $notifica['cadenza'] >= 0 && $notifica['tipo'] >= 0)
            {
                if($key == 'add')
                {
                    $n = new RinnovoNotifica($notifica);
                    $rinnovo->notifiche()->save($n);
                }
                else
                {
                    $n =  RinnovoNotifica::find($key);
                    $n->update($notifica);
                }
            }
        }

        $this->rinnovo->update($rinnovo, $update);

        // Log
        activity(session('azienda'))
            ->performedOn($rinnovo)
            ->withProperties($update)
            ->log('updated');

        return redirect()->route('admin.tasklist.rinnovo.edit', $rinnovo->id)
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('tasklist::rinnovi.title.rinnovi')]));
    }

    public function destroyNotifica($id)
    {
        $notifica = RinnovoNotifica::find($id);
        RinnovoNotifica::destroy($id);

        return redirect()->route('admin.tasklist.rinnovo.edit', $notifica->rinnovo_id)
          ->withSuccess('Notifica eliminata con successo');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Rinnovo $rinnovo
     * @return Response
     */
    public function destroy(Rinnovo $rinnovo)
    {
        if($rinnovo->azienda != session('azienda'))
            return redirect()->route('admin.tasklist.rinnovo.index')
                    ->withWarning('AVVISO: non puoi accedere a questo rinnovo con l\'azienda ' . session('azienda'));

        $this->rinnovo->destroy($rinnovo);

        // Log
        activity(session('azienda'))
            ->performedOn($rinnovo)
            ->withProperties(json_encode($rinnovo))
            ->log('destroyed');

        return redirect()->route('admin.tasklist.rinnovo.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('tasklist::rinnovi.title.rinnovi')]));
    }

    // Export excel
    public function exportExcel(Request $request)
    {
        if(empty($request->all()))
        {
          $res['order']['by'] = 'data';
          $res['order']['sort'] = 'desc';
          $request->merge($res);
        }

        $rinnovi = Rinnovo::filter($request->all())
                            ->whereHas('cliente', function ($q) {
                                $q->commerciali();
                            })
                            ->where('tasklist__rinnovi.azienda', session('azienda'))
                            ->get();

      ob_clean();
      return Excel::download(new RinnoviExport($rinnovi), 'Rinnovi.xlsx');
    }

}
