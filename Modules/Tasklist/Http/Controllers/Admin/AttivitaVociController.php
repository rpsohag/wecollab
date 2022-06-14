<?php

namespace Modules\Tasklist\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Tasklist\Entities\AttivitaVoci;
use Modules\Tasklist\Http\Requests\CreateAttivitaVociRequest;
use Modules\Tasklist\Http\Requests\UpdateAttivitaVociRequest;
use Modules\Tasklist\Repositories\AttivitaVociRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;

class AttivitaVociController extends AdminBaseController
{
    // /**
    //  * @var AttivitaVociRepository
    //  */
    // private $attivitavoci;
    //
    // public function __construct(AttivitaVociRepository $attivitavoci)
    // {
    //     parent::__construct();
    //
    //     $this->attivitavoci = $attivitavoci;
    // }
    //
    // /**
    //  * Display a listing of the resource.
    //  *
    //  * @return Response
    //  */
    // public function index()
    // {
    //     //$attivitavoci = $this->attivitavoci->all();
    //
    //     return view('tasklist::admin.attivitavoci.index', compact(''));
    // }
    //
    // /**
    //  * Show the form for creating a new resource.
    //  *
    //  * @return Response
    //  */
    // public function create()
    // {
    //     return view('tasklist::admin.attivitavoci.create');
    // }
    //
    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  CreateAttivitaVociRequest $request
    //  * @return Response
    //  */
    // public function store(CreateAttivitaVociRequest $request)
    // {
    //     $this->attivitavoci->create($request->all());
    //
    //     return redirect()->route('admin.tasklist.attivitavoci.index')
    //         ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('tasklist::attivitavoci.title.attivitavoci')]));
    // }
    //
    // /**
    //  * Show the form for editing the specified resource.
    //  *
    //  * @param  AttivitaVoci $attivitavoci
    //  * @return Response
    //  */
    // public function edit(AttivitaVoci $attivitavoci)
    // {
    //     return view('tasklist::admin.attivitavoci.edit', compact('attivitavoci'));
    // }
    //
    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param  AttivitaVoci $attivitavoci
    //  * @param  UpdateAttivitaVociRequest $request
    //  * @return Response
    //  */
    // public function update(AttivitaVoci $attivitavoci, UpdateAttivitaVociRequest $request)
    // {
    //     $this->attivitavoci->update($attivitavoci, $request->all());
    //
    //     return redirect()->route('admin.tasklist.attivitavoci.index')
    //         ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('tasklist::attivitavoci.title.attivitavoci')]));
    // }
    //
    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  AttivitaVoci $attivitavoci
    //  * @return Response
    //  */
    // public function destroy(AttivitaVoci $attivitavoci)
    // {
    //     $this->attivitavoci->destroy($attivitavoci);
    //
    //     return redirect()->route('admin.tasklist.attivitavoci.index')
    //         ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('tasklist::attivitavoci.title.attivitavoci')]));
    // }
}
