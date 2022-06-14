<?php

namespace Modules\Assistenza\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;

use Modules\Amministrazione\Entities\Clienti;
use Modules\Amministrazione\Entities\ClienteAmbienti;
use Modules\User\Entities\Sentinel\User;
use Illuminate\Support\Facades\Auth;

class AmbientiController extends AdminBaseController
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

    public function index()
    {

        $clienti = Clienti::has('ambiente')->with('ambiente')->get();

        if(!empty($clienti))
          return view('assistenza::admin.ambienti.index', compact('clienti'));
    }

}
