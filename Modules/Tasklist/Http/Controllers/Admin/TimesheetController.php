<?php

namespace Modules\Tasklist\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Export\Entities\TimesheetsExport;
use Modules\Tasklist\Entities\Timesheet;
use Modules\Tasklist\Http\Requests\CreateTimesheetRequest;
use Modules\Tasklist\Http\Requests\UpdateTimesheetRequest;
use Modules\Tasklist\Repositories\TimesheetRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Illuminate\Support\Facades\Validator;


use Modules\Profile\Entities\Procedura;
use Modules\Commerciale\Entities\Offerta;
use Modules\Profile\Entities\Area;
use Modules\User\Entities\Sentinel\User;
use Modules\Profile\Entities\Gruppo;
use Modules\Amministrazione\Entities\Clienti;
use Modules\Commerciale\Entities\Ordinativo;
use Modules\Tasklist\Entities\Attivita;
use Auth;
use DB;

class TimesheetController extends AdminBaseController
{
    /**
     * @var TimesheetRepository
     */
    private $timesheet;

    public function __construct(TimesheetRepository $timesheet)
    {
        parent::__construct();

        $this->timesheet = $timesheet;
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
          $res['order']['by'] = 'dataora_fine';
          $res['order']['sort'] = 'desc';
          $request->merge($res);
        } 

        $date = !empty($request->date) ? set_date_ita($request->date) : date('Y-m-d');

        $timesheets = Timesheet::filter($request->all())
                              ->whereDate('dataora_inizio', $date)->get();
                              //->paginateFilter(config('wecore.pagination.limit'));

        //$date = get_date_ita($date . ' 00:00:00');
        $clienti = [''] + Clienti::pluck('ragione_sociale', 'id')->toArray();
        $procedure = [''] + Procedura::pluck('titolo', 'id')->toArray();
        $tipologie = [-1 => ''] + config('tasklist.timesheets.tipologie');
        $aree = [];
        $gruppi = [];
        $ordinativi = [];
        $attivita = [''] + Attivita::whereHas('users', function ($q) {
                                      $q->where('user_id', Auth::id());
                                    })
                                    ->orWhere('richiedente_id', Auth::id())
                                    ->orWhereJsonContains('supervisori_id->'.Auth::id().'->user_id', (string)Auth::id())
                                    ->join('amministrazione__clienti as c', 'c.id', '=', 'tasklist__attivita.cliente_id')
                                    ->select('tasklist__attivita.id as id', DB::raw('CONCAT( tasklist__attivita.oggetto,"  (", c.ragione_sociale, ")") as oggettocompleto'))
                                    ->pluck('oggettocompleto', 'id')
                                    ->toArray();

        $daily_timesheets = Timesheet::whereDate('dataora_inizio', '>=', $date)->count();

        return view('tasklist::admin.timesheets.index', compact('timesheets', 'date', 'clienti', 'procedure', 'aree', 'gruppi', 'ordinativi', 'attivita', 'tipologie', 'daily_timesheets'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function manage(Request $request)
    {
        if(auth_user()->hasAccess('tasklist.timesheets.manage')){
                if(empty($request->all())){
                    $res['order']['by'] = 'dataora_fine';
                    $res['order']['sort'] = 'desc';
                    $request->merge($res); 
                }

                  $timesheets = Timesheet::when(auth_user()->inRole('admin'), function ($query) use ($request){
                                        $query->withoutGlobalScope('user');
                                        })->when(!auth_user()->inRole('admin'), function ($query) use ($request){
                                          $query->withoutGlobalScope('user')->whereIn('created_user_id', Auth::user()->supervisionati()->pluck('id')->toArray());
                                        });

                $timesheets = $timesheets->filter($request->all())->paginateFilter(10);

                $clienti = [0 => ''] + Clienti::pluck('ragione_sociale', 'id')->toArray();
                $utenti = [0 => ''] + User::when(auth_user()->inRole('admin'), function ($query) {
                                            $query->select(DB::raw("CONCAT(first_name,' ',last_name) AS name"), 'id');
                                          })->when(!auth_user()->inRole('admin'), function ($query) {
                                            $query->whereIn('id', Auth::user()->supervisionati()->pluck('id')->toArray())
                                            ->select(DB::raw("CONCAT(first_name,' ',last_name) AS name"), 'id');
                                          })->pluck('name', 'id')->toArray();

                $ordinativi = [0 => ''] + Ordinativo::pluck('oggetto', 'id')->toArray();
                $tipologie = config('tasklist.timesheets.tipologie');

                $request->flash();

                return view('tasklist::admin.timesheets.manage', compact('timesheets', 'ordinativi', 'clienti', 'utenti', 'tipologie'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
     public function create()
     {
         return view('tasklist::admin.timesheets.create');
     }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateTimesheetRequest $request
     * @return Response
     */
    public function store(CreateTimesheetRequest $request)
    {
      $rules = Timesheet::getRules();

      $insert = $request->all();

      $this->validate($request, $rules);

      $insert['azienda'] = session('azienda');
      $insert['dataora_inizio'] = $insert['date'] . ' ' . $insert['ora_inizio'];
      $insert['dataora_fine'] = $insert['date'] . ' ' . $insert['ora_fine'];
      $insert['created_user_id'] = Auth::id();
      $insert['updated_user_id'] = Auth::id();

      $timesheet = Timesheet::create($insert);

      // Log
      activity(session('azienda'))
          ->performedOn($timesheet) 
          ->withProperties($insert)
          ->log('created');

        return redirect()->route('admin.tasklist.timesheet.index', ['date' => $insert['date']])
            ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('tasklist::timesheets.title.timesheets')]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Timesheet $timesheet
     * @return Response
     */
    public function edit(Timesheet $timesheet, $date)
    {
      $daily_timesheets = Timesheet::whereDate('dataora_inizio', '>=', $date)->count();
      if($daily_timesheets == 0) {
        return redirect()->back()->withError('Non è presente alcun timesheet da modificare.');
      }
      $clienti = [''] + Clienti::pluck('ragione_sociale', 'id')->toArray();
      $procedure = [''] + Procedura::pluck('titolo', 'id')->toArray();
      $aree = [''] + Area::pluck('titolo', 'id')->toArray();
      $gruppi = [''] + Gruppo::pluck('nome', 'id')->toArray();
      $ordinativi = [''] + Ordinativo::pluck('oggetto', 'id')->toArray();
      $tipologie = [-1 => ''] + config('tasklist.timesheets.tipologie');
      $attivita = [''] + Attivita::where(function($query) {
        $query->where('richiedente_id', Auth::id());
        $query->orWhereHas('users', function($q) {
          $q->where('user_id', Auth::id());
        });
      })->join('amministrazione__clienti as c', 'c.id', '=', 'tasklist__attivita.cliente_id')
      ->select('tasklist__attivita.id as id', DB::raw('CONCAT( tasklist__attivita.oggetto,"  (", c.ragione_sociale, ")") as oggettocompleto'))
      ->pluck('oggettocompleto', 'id')
      ->toArray();
      $date = (!empty($date) ? $date : Carbon::now());
      $timesheets = Timesheet::whereDate('dataora_inizio', $date)->get();

      $latest_timesheet_id = Timesheet::withoutGlobalScopes()->latest()->first()->id;

      return view('tasklist::admin.timesheets.edit', compact('timesheets', 'clienti', 'procedure', 'aree', 'gruppi', 'ordinativi', 'attivita', 'tipologie', 'latest_timesheet_id'));
    }

    public function show($id)
    {
        $clienti = [''] + Clienti::pluck('ragione_sociale', 'id')->toArray();
        $procedure = [''] + Procedura::pluck('titolo', 'id')->toArray();
        $aree = [''] + Area::pluck('titolo', 'id')->toArray();
        $gruppi = [''] + Gruppo::pluck('nome', 'id')->toArray();
        $ordinativi = [''] + Ordinativo::pluck('oggetto', 'id')->toArray();
        $attivita = [''] + Attivita::pluck('oggetto', 'id')->toArray();
        $tipologie = config('tasklist.timesheets.tipologie');

        $timesheets = [Timesheet::find($id)];

        return view('tasklist::admin.timesheets.edit', compact('timesheets', 'clienti', 'procedure', 'aree', 'gruppi', 'ordinativi', 'attivita', 'tipologie'));
    }


    public function timesheetsAjaxRequest(Request $request)
    {

      if($request->ajax()) {

        if($request->filled('cliente')) {

          $ordinativi = [0 => ''] + Ordinativo::active()->where('cliente_id', '=', $request->cliente)->pluck('oggetto', 'id')->toArray();

          $tasklistAttivita = [0 => ''] + Attivita::when(!empty($request->cliente), function($q) use ($request) {
                                                    $q->whereHas('cliente', function($q) use ($request) {
                                                      $q->whereId($request->cliente);
                                                    });
                                                  })
                                                  ->where(function($query) {
                                                    $query->where('richiedente_id', Auth::id());
                                                    $query->orWhereHas('users', function($q) {
                                                      $q->where('user_id', Auth::id());
                                                    });
                                                  })
                                                  ->pluck('oggetto', 'id')
                                                  ->toArray();

          ob_clean();

          return response()->json([ 'ordinativi' => $ordinativi , 'tasklistAttivita' => $tasklistAttivita ]);

        }

        if($request->filled('reset')) {

          $clienti = [''] + Clienti::pluck('ragione_sociale', 'id')->toArray();
          $procedure = [''] + Procedura::pluck('titolo', 'id')->toArray();
          $attivita = [''] + Attivita::whereHas('users', function ($q) {
                                        $q->where('user_id', Auth::id());
                                      })
                                      ->orWhere('richiedente_id', Auth::id())
                                      ->orWhereJsonContains('supervisori_id->'.Auth::id().'->user_id', (string)Auth::id())
                                      ->join('amministrazione__clienti as c', 'c.id', '=', 'tasklist__attivita.cliente_id')
                                      ->select('tasklist__attivita.id as id', DB::raw('CONCAT( tasklist__attivita.oggetto,"  (", c.ragione_sociale, ")") as oggettocompleto'))
                                      ->pluck('oggettocompleto', 'id')
                                      ->toArray(); 

            ob_clean();

            return response()->json(['clienti' => $clienti, 'procedure' => $procedure, 'attivita' => $attivita ]);

        }

        if($request->filled('tasklist_attivita')) {

          $attivita = Attivita::find($request->tasklist_attivita);
          if($attivita){
            $nota = ' ';//$attivita->oggetto;
            $ordinativo = optional($attivita->ordinativo())->pluck('oggetto', 'id')->toArray();
            $procedura = optional($attivita->procedura())->pluck('titolo', 'id')->toArray();
            $area = optional($attivita->area())->pluck('titolo', 'id')->toArray();
            $gruppo = optional($attivita->gruppo())->pluck('nome', 'id')->toArray();
            $cliente = optional($attivita->cliente())->pluck('ragione_sociale', 'id')->toArray();

            ob_clean();

            return response()->json([ 'nota' => $nota , 'ordinativo' => $ordinativo, 'procedura' => $procedura, 'area' => $area, 'gruppo' => $gruppo, 'cliente' => $cliente ]);

          }

        }

      }

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  Timesheet $timesheet
     * @param  UpdateTimesheetRequest $request
     * @return Response
     */
    public function update(Timesheet $timesheet, Request $request, $date)
    {
        $timesheet_ids = Timesheet::whereDate('dataora_inizio', $date)->pluck('id', 'id')->toArray();
        $res = $request->all();
        $rules = Timesheet::getRulesListEdit();

        // Edit
        if(!empty($res['timesheet']['edit']))
        {
          foreach($res['timesheet']['edit'] as $key => $ts)
          {
            $update = $ts;

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            }

            $update['dataora_inizio'] = $date . ' ' . $ts['ora_inizio'];
            $update['dataora_fine'] = $date . ' ' . $ts['ora_fine'];
            $update['updated_user_id'] = Auth::id();

            Timesheet::updateOrCreate(
              ['id' => $update['id'], 'created_user_id' => $update['updated_user_id']],
              $update
          );

            if(in_array($key, $timesheet_ids))
              unset($timesheet_ids[$key]);
          }
        }

        // Delete
        if(!empty($timesheet_ids))
          Timesheet::destroy($timesheet_ids);

        // Log
        activity(session('azienda'))
            ->performedOn($timesheet)
            ->withProperties($res)
            ->log('updated');

        return redirect()->route('admin.tasklist.timesheet.index', ['date' => $date])
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('tasklist::timesheets.title.timesheets')]));
    }

    // Export excel
    public function exportExcel(Request $request)
    {
      if(empty($request->all())){
        $res['order']['by'] = 'dataora_fine';
        $res['order']['sort'] = 'desc';
        $request->merge($res);
      }

      $timesheets = Timesheet::withoutGlobalScope('user')->filter($request->all())
                            ->get();

      $clienti = [0 => ''] + Clienti::pluck('ragione_sociale', 'id')->toArray();
      $utenti = [0 => ''] + User::select(DB::raw("CONCAT(first_name,' ',last_name) AS name"), 'id')->pluck('name', 'id')->toArray();
      $ordinativi = [0 => ''] + Ordinativo::pluck('oggetto', 'id')->toArray();
      $tipologie = config('tasklist.timesheets.tipologie');
      ob_clean();
      return Excel::download(new TimesheetsExport($timesheets), 'Timesheets.xlsx');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Timesheet $timesheet
     * @return Response
     */
    // public function destroy(Timesheet $timesheet)
    // {
    //     $this->timesheet->destroy($timesheet);
    //
    //     return redirect()->route('admin.tasklist.timesheet.index')
    //         ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('tasklist::timesheets.title.timesheets')]));
    // }
}
