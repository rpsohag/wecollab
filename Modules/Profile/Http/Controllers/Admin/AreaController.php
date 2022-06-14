<?php

namespace Modules\Profile\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Profile\Entities\Area;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Profile\Entities\Procedura;
use Modules\Profile\Entities\Gruppo;

class AreaController extends AdminBaseController
{
    private $area;

    public function __construct(Area $area)
    {
        parent::__construct();

        $this->area = $area;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $aree = $this->area->paginate(config('wecore.pagination.limit'));

        return view('profile::admin.aree.index', compact('aree'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $procedure = Procedura::pluck('titolo', 'id')->toArray();
        $attivita = Gruppo::where('area_id', 0)
                      ->pluck('nome', 'id')
                      ->toArray();

        return view('profile::admin.aree.create', compact('procedure', 'attivita'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateAreaRequest $request
     * @return Response
     */
    public function store(Request $request)
    {
        $rules = Area::getRules();
        $this->validate($request, $rules);

        $create = $request->all();

        $area = Area::create($create);

        $attivita = Gruppo::all();
        $attivita_selected = !empty($create['gruppi']) ? $create['gruppi'] : [];

        foreach($attivita as $at)
        {
          if(in_array($at->id, $attivita_selected))
            $at->update(['area_id' => $area->id]);
        }

        // Log
        activity(session('azienda'))
            ->performedOn($area)
            ->withProperties($create)
            ->log('created');

        return redirect()->route('admin.profile.area.edit', $area->id)
            ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('profile::aree.title.aree')]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Gruppo $area
     * @return Response
     */
    public function edit(Area $area)
    {
      $procedure = Procedura::pluck('titolo', 'id')->toArray();
      $attivita = Gruppo::where('area_id', 0)
                    ->orWhere('area_id', $area->id)
                    ->pluck('nome', 'id')
                    ->toArray();

        return view('profile::admin.aree.edit', compact('area', 'attivita', 'procedure'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Area $area
     * @param  UpdateAreaRequest $request
     * @return Response
     */
    public function update(Area $area, Request $request)
    {
        $rules = Area::getRules();
        $this->validate($request, $rules);

        $update = $request->all();

        $area->update($update);

        $attivita = Gruppo::all();
        $attivita_selected = !empty($update['gruppi']) ? $update['gruppi'] : [];

        foreach($attivita as $at)
        {
          if(in_array($at->id, $attivita_selected))
            $at->update(['area_id' => $area->id]);
        }

        // Log
        activity(session('azienda'))
            ->performedOn($area)
            ->withProperties($update)
            ->log('updated');

        return redirect()->route('admin.profile.area.edit', $area->id)
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('profile::aree.title.aree')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Area $area
     * @return Response
     */
    public function destroy(Area $area)
    {
        $area->destroy($area->id);

        // Log
        activity(session('azienda'))
            ->performedOn($area)
            ->withProperties(json_encode($area))
            ->log('destroyed');

        return redirect()->route('admin.profile.aree.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('profile::aree.title.aree')]));
    }
}
