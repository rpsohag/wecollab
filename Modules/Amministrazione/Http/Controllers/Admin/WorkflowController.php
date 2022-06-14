<?php

namespace Modules\Amministrazione\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;

use Modules\Amministrazione\Entities\Workflow;
use Modules\User\Entities\Sentinel\User;
use Illuminate\Support\Facades\Auth;

use Validator;

class WorkflowController extends AdminBaseController
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

}
