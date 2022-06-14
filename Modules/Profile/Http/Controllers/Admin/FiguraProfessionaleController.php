<?php

namespace Modules\Profile\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Profile\Entities\FiguraProfessionale;
use Modules\Profile\Http\Requests\CreateFiguraProfessionaleRequest;
use Modules\Profile\Http\Requests\UpdateFiguraProfessionaleRequest;
use Modules\Profile\Repositories\FiguraProfessionaleRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;

use Modules\User\Entities\Sentinel\User;

class FiguraProfessionaleController extends AdminBaseController
{
    /**
     * @var FiguraProfessionaleRepository
     */
    private $figuraprofessionale;

    public function __construct(FiguraProfessionaleRepository $figuraprofessionale)
    {
        parent::__construct();

        $this->figuraprofessionale = $figuraprofessionale;
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
          $res['order']['by'] = 'descrizione';
          $res['order']['sort'] = 'asc';
          $request->merge($res);
        }

        $users = User::all()->pluck('full_name', 'id')->toArray();

        $figureprofessionali = FiguraProfessionale::filter($request->all())
                                                ->paginateFilter(config('wecore.pagination.limit'));

        $request->flash();

        return view('profile::admin.figureprofessionali.index', compact('figureprofessionali', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $figuraprofessionale = null;
        $users = User::all()->pluck('full_name', 'id')->toArray();

        return view('profile::admin.figureprofessionali.create', compact('figuraprofessionale', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateFiguraProfessionaleRequest $request
     * @return Response
     */
    public function store(CreateFiguraProfessionaleRequest $request)
    {
        $rules = FiguraProfessionale::getRules();
        $this->validate($request, $rules);

        $create = $request->all();
        $figuraprofessionale = $this->figuraprofessionale->create($create);

        // Log
        activity(session('azienda'))
            ->performedOn($figuraprofessionale)
            ->withProperties($create)
            ->log('created');

        return redirect()->route('admin.profile.figuraprofessionale.edit', $figuraprofessionale->id)
            ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('profile::figureprofessionali.title.figureprofessionali')]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  FiguraProfessionale $figuraprofessionale
     * @return Response
     */
    public function edit(FiguraProfessionale $figuraprofessionale)
    {
        $users = User::all()->pluck('full_name', 'id')->toArray();

        return view('profile::admin.figureprofessionali.edit', compact('figuraprofessionale', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  FiguraProfessionale $figuraprofessionale
     * @param  UpdateFiguraProfessionaleRequest $request
     * @return Response
     */
    public function update(FiguraProfessionale $figuraprofessionale, UpdateFiguraProfessionaleRequest $request)
    {
        $rules = FiguraProfessionale::getRules();
        $this->validate($request, $rules);

        $update = $request->all();
        $this->figuraprofessionale->update($figuraprofessionale, $update);

        // Log
        activity(session('azienda'))
            ->performedOn($figuraprofessionale)
            ->withProperties($update)
            ->log('updated');

        return redirect()->route('admin.profile.figuraprofessionale.edit', $figuraprofessionale->id)
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('profile::figureprofessionali.title.figureprofessionali')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  FiguraProfessionale $figuraprofessionale
     * @return Response
     */
    public function destroy(FiguraProfessionale $figuraprofessionale)
    {
        return abort(404);

        // Log
        activity(session('azienda'))
            ->performedOn($figuraprofessionale)
            ->withProperties(json_encode($figuraprofessionale))
            ->log('destroyed');

        $this->figuraprofessionale->destroy($figuraprofessionale);

        return redirect()->route('admin.profile.figuraprofessionale.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('profile::figureprofessionali.title.figureprofessionali')]));
    }
}
