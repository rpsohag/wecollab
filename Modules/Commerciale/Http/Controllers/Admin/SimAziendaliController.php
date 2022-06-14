<?php

namespace Modules\Commerciale\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Commerciale\Entities\SimAziendali;
use Modules\Commerciale\Http\Requests\CreateSimAziendaliRequest;
use Modules\Commerciale\Http\Requests\UpdateSimAziendaliRequest;
use Modules\Commerciale\Repositories\SimAziendaliRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\User\Entities\Sentinel\User;
use Illuminate\Notifications\Notification;
use App\Notifications\newTicketAssegnatari;
use DB;
use Auth;

class SimAziendaliController extends AdminBaseController
{
    /**
     * @var SimAziendaliRepository
     */
    private $simaziendali;

    public function __construct(SimAziendaliRepository $simaziendali)
    {
        parent::__construct();

        $this->simaziendali = $simaziendali;
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
          $res['order']['by'] = 'numero_contratto';
          $res['order']['sort'] = 'asc';
          $request->merge($res);
        }

        $operatori_telefonici = SimAziendali::distinct('operatore')->pluck('operatore','operatore')->toArray();
        $utenti = [''] + User::select(DB::raw("CONCAT(first_name, ' ', last_name) AS name"),'id')->pluck('name', 'id')->toArray();
        $simaziendalis = SimAziendali::filter($request->all())
                          ->where('azienda', session('azienda'))
                          ->paginateFilter(config('wecore.pagination.limit'));

        $contratti = SimAziendali::all()->prepend(['' => ''])->pluck('numero_contratto', 'numero_contratto')->toArray();

        $request->flash();

        return view('commerciale::admin.simaziendalis.index', compact('simaziendalis','operatori_telefonici','utenti', 'contratti'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $utenti = [''] + User::select(DB::raw("CONCAT(first_name, ' ', last_name) AS name"),'id')->pluck('name', 'id')->toArray();
        $operatori_telefonici = SimAziendali::distinct('operatore')->pluck('operatore','operatore')->toArray();

        return view('commerciale::admin.simaziendalis.create',compact('utenti','operatori_telefonici'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateSimAziendaliRequest $request
     * @return Response
     */
    public function store(CreateSimAziendaliRequest $request)
    {
        $insert = $request->all();

        $rules = SimAziendali::getRules();

        $this->validate($request, $rules);

        $insert['azienda'] = session('azienda');

        $simaziendali = $this->simaziendali->create($insert);


        // Log
        activity(session('azienda'))
            ->performedOn($simaziendali)
            ->withProperties($insert)
            ->log('created');

        return redirect()->route('admin.commerciale.simaziendali.index')
            ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('commerciale::simaziendalis.title.simaziendalis')]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  SimAziendali $simaziendali
     * @return Response
     */
    public function edit(SimAziendali $simaziendali)
    {
        $operatori_telefonici = SimAziendali::distinct('operatore')->pluck('operatore','operatore')->toArray();

        $utenti = [''] + User::select(DB::raw("CONCAT(first_name, ' ', last_name) AS name"),'id')->pluck('name', 'id')->toArray();

        return view('commerciale::admin.simaziendalis.edit', compact('simaziendali','utenti','operatori_telefonici'));
    }

    public function read(SimAziendali $simaziendali)
    {
        $operatori_telefonici = SimAziendali::distinct('operatore')->pluck('operatore','operatore')->toArray();

        $utenti = [''] + User::select(DB::raw("CONCAT(first_name, ' ', last_name) AS name"),'id')->pluck('name', 'id')->toArray();

        return view('commerciale::admin.simaziendalis.read', compact('simaziendali','utenti','operatori_telefonici'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  SimAziendali $simaziendali
     * @param  UpdateSimAziendaliRequest $request
     * @return Response
     */
    public function update(SimAziendali $simaziendali, UpdateSimAziendaliRequest $request)
    {
        $update = $request->all();
        $update['updated_user_id'] = Auth::id();

        $this->simaziendali->update($simaziendali, $update);

        // Log
        activity(session('azienda'))
            ->performedOn($simaziendali)
            ->withProperties($update)
            ->log('updated');

        return redirect()->route('admin.commerciale.simaziendali.index')
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('commerciale::simaziendalis.title.simaziendalis')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  SimAziendali $simaziendali
     * @return Response
     */
    public function destroy(SimAziendali $simaziendali)
    {
        // Log
        activity(session('azienda'))
            ->performedOn($simaziendali)
            ->withProperties(json_encode($simaziendali))
            ->log('destroyed');

        $this->simaziendali->destroy($simaziendali);

        return redirect()->route('admin.commerciale.simaziendali.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('commerciale::simaziendalis.title.simaziendalis')]));
    }
}
