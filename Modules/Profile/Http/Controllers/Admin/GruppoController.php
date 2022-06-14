<?php

namespace Modules\Profile\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Filters\Admin\Profile\GruppoFilter;
use Modules\Profile\Entities\Gruppo;
use Modules\Profile\Http\Requests\CreateGruppoRequest;
use Modules\Profile\Http\Requests\UpdateGruppoRequest;
use Modules\Profile\Repositories\GruppoRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\User\Entities\Sentinel\User;
use Modules\Profile\Entities\Area;

class GruppoController extends AdminBaseController
{
    /**
     * @var GruppoRepository
     */
    private $gruppo;

    public function __construct(GruppoRepository $gruppo)
    {
        parent::__construct();

        $this->gruppo = $gruppo;
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
        $res['order']['by'] = 'id';
        $res['order']['sort'] = 'asc';
        $request->merge($res);
      }

      $gruppi = Gruppo::with('users')->filter($request->all())->paginateFilter(config('wecore.pagination.limit'));
      $aree = [0 => ''] + Area::pluck('titolo', 'id')->toArray();
      
      $request->flash();

      return view('profile::admin.gruppi.index', compact('gruppi', 'aree'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $gruppi = $this->gruppo->all();
        $users = User::select(DB::raw("CONCAT(first_name,' ',last_name) AS name"), 'id')
                    ->pluck('name', 'id')
                    ->toArray();
        $aree = [0 => ''] + Area::pluck('titolo', 'id')->toArray();

        return view('profile::admin.gruppi.create', compact('gruppi', 'users', 'aree'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateGruppoRequest $request
     * @return Response
     */
    public function store(CreateGruppoRequest $request)
    {
        $rules = Gruppo::getRules();
        $this->validate($request, $rules);

        $create = $request->all();

        $gruppo = Gruppo::create($create);
        $utenti = (!empty($create['utenti'])) ? $create['utenti'] : [];
        $gruppo->users()->sync($utenti);

        // Log
        activity(session('azienda'))
            ->performedOn($gruppo)
            ->withProperties($create)
            ->log('created');

        return redirect()->route('admin.profile.gruppo.index')
            ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('profile::gruppi.title.gruppi')]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Gruppo $gruppo
     * @return Response
     */
    public function edit(Gruppo $gruppo)
    {
        $users = User::select(DB::raw("CONCAT(first_name,' ',last_name) AS name"), 'id')
                    ->pluck('name', 'id')
                    ->toArray();
        $aree = [0 => ''] + Area::pluck('titolo', 'id')->toArray();

        return view('profile::admin.gruppi.edit', compact('gruppo', 'users', 'aree'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Gruppo $gruppo
     * @param  UpdateGruppoRequest $request
     * @return Response
     */
    public function update(Gruppo $gruppo, UpdateGruppoRequest $request)
    {
        $rules = Gruppo::getRules();
        $this->validate($request, $rules);

        $update = $request->all();

        $this->gruppo->update($gruppo, $update);
        $utenti = (!empty($update['utenti'])) ? $update['utenti'] : [];
        $gruppo->users()->sync($utenti);

        // Log
        activity(session('azienda'))
            ->performedOn($gruppo)
            ->withProperties($update)
            ->log('updated');

        return redirect()->route('admin.profile.gruppo.index')
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('profile::gruppi.title.gruppi')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Gruppo $gruppo
     * @return Response
     */
    public function destroy(Gruppo $gruppo)
    {
        $this->gruppo->destroy($gruppo);

        // Log
        activity(session('azienda'))
            ->performedOn($gruppo)
            ->withProperties(json_encode($gruppo))
            ->log('destroyed');

        return redirect()->route('admin.profile.gruppo.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('profile::gruppi.title.gruppi')]));
    }

    public function users(Request $request)
    {
        if($request->ajax())
        {
            $users = Gruppo::findOrFail($request->gruppo_id)->users->toJson();

            return $users;
        }
        else
        {
            return redirect()->back()->withError('Non hai accesso a questa pagina');
        }
    }
}
