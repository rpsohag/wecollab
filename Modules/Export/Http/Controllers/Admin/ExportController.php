<?php

namespace Modules\Export\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Export\Entities\Export;
use Modules\Export\Http\Requests\CreateExportRequest;
use Modules\Export\Http\Requests\UpdateExportRequest;
use Modules\Export\Repositories\ExportRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;

class ExportController extends AdminBaseController
{
    /**
     * @var ExportRepository
     */
    private $export;

    public function __construct(ExportRepository $export)
    {
        parent::__construct();

        $this->export = $export;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //$exports = $this->export->all();

        return view('export::admin.exports.index', compact(''));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('export::admin.exports.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateExportRequest $request
     * @return Response
     */
    public function store(CreateExportRequest $request)
    {
        $this->export->create($request->all());

        return redirect()->route('admin.export.export.index')
            ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('export::exports.title.exports')]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Export $export
     * @return Response
     */
    public function edit(Export $export)
    {
        return view('export::admin.exports.edit', compact('export'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Export $export
     * @param  UpdateExportRequest $request
     * @return Response
     */
    public function update(Export $export, UpdateExportRequest $request)
    {
        $this->export->update($export, $request->all());

        return redirect()->route('admin.export.export.index')
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('export::exports.title.exports')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Export $export
     * @return Response
     */
    public function destroy(Export $export)
    {
        $this->export->destroy($export);

        return redirect()->route('admin.export.export.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('export::exports.title.exports')]));
    }
}
