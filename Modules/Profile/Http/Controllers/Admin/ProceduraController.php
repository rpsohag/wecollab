<?php

namespace Modules\Profile\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Profile\Entities\Procedura;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Profile\Entities\Area;

class ProceduraController extends AdminBaseController
{
    private $procedura;

    public function __construct(Procedura $procedura)
    {
        parent::__construct();

        $this->procedura = $procedura;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $procedure = $this->procedura->paginate(config('wecore.pagination.limit'));

        return view('profile::admin.procedure.index', compact('procedure'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $aree = Area::where('procedura_id', 0)
                      ->pluck('titolo', 'id')
                      ->toArray();

        return view('profile::admin.procedure.create', compact('aree'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateProceduraRequest $request
     * @return Response
     */
    public function store(Request $request)
    {
        $rules = Procedura::getRules();
        $this->validate($request, $rules);

        $create = $request->all();

        $procedura = Procedura::create($create);

        $aree = Area::all();
        $aree_selected = !empty($create['aree']) ? $create['aree'] : [];

        foreach($aree as $area)
        {
          if(in_array($area->id, $aree_selected))
            $area->update(['procedura_id' => $procedura->id]);
        }

        // Log
        activity(session('azienda'))
            ->performedOn($procedura)
            ->withProperties($create)
            ->log('created');

        return redirect()->route('admin.profile.procedura.edit', $procedura->id)
            ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('profile::procedure.title.procedure')]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Gruppo $procedura
     * @return Response
     */
    public function edit(Procedura $procedura)
    {
      $aree = Area::where('procedura_id', 0)
                    ->orWhere('procedura_id', $procedura->id)
                    ->pluck('titolo', 'id')
                    ->toArray();

        return view('profile::admin.procedure.edit', compact('procedura', 'aree'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Procedura $procedura
     * @param  UpdateProceduraRequest $request
     * @return Response
     */
    public function update(Procedura $procedura, Request $request)
    {
        $rules = Procedura::getRules();
        $this->validate($request, $rules);

        $update = $request->all();
        $procedura->update($update);

        $aree = Area::all();
        $aree_selected = !empty($update['aree']) ? $update['aree'] : [];

        foreach($aree as $area)
        {
          if(in_array($area->id, $aree_selected))
            $area->update(['procedura_id' => $procedura->id]);
        }

        // Log
        activity(session('azienda'))
            ->performedOn($procedura)
            ->withProperties($update)
            ->log('updated');

        return redirect()->route('admin.profile.procedura.edit', $procedura->id)
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('profile::procedure.title.procedure')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Procedura $procedura
     * @return Response
     */
    public function destroy(Procedura $procedura)
    {
        $procedura->destroy($procedura->id);

        // Log
        activity(session('azienda'))
            ->performedOn($procedura)
            ->withProperties(json_encode($procedura))
            ->log('destroyed');

        return redirect()->route('admin.profile.procedure.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('profile::procedure.title.procedure')]));
    }
}
