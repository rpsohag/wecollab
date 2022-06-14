<?php

namespace Modules\Wecore\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Wecore\Entities\Core;
use Modules\Wecore\Http\Requests\CreateCoreRequest;
use Modules\Wecore\Http\Requests\UpdateCoreRequest;
use Modules\Wecore\Repositories\CoreRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;

use Illuminate\Support\Facades\Crypt;

use Spatie\Activitylog\Models\Activity;
use Modules\Tasklist\Entities\RinnovoNotifica;
use Modules\Commerciale\Entities\FatturazioneScadenze;
use Modules\Commerciale\Entities\Offerte;
use Modules\Tasklist\Entities\Attivita;

class CoreController extends AdminBaseController
{
    /**
     * @var CoreRepository
     */
    private $core;

    public function __construct(CoreRepository $core)
    {
        parent::__construct();

        $this->core = $core;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //$cores = $this->core->all();

        return view('wecore::admin.cores.index', compact(''));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('wecore::admin.cores.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateCoreRequest $request
     * @return Response
     */
    public function store(CreateCoreRequest $request)
    {
        $this->core->create($request->all());

        return redirect()->route('admin.wecore.core.index')
            ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('wecore::cores.title.cores')]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Core $core
     * @return Response
     */
    public function edit(Core $core)
    {
        return view('wecore::admin.cores.edit', compact('core'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Core $core
     * @param  UpdateCoreRequest $request
     * @return Response
     */
    public function update(Core $core, UpdateCoreRequest $request)
    {
        $this->core->update($core, $request->all());

        return redirect()->route('admin.wecore.core.index')
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('wecore::cores.title.cores')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Core $core
     * @return Response
     */
    public function destroy(Core $core)
    {
        $this->core->destroy($core);

        return redirect()->route('admin.wecore.core.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('wecore::cores.title.cores')]));
    }


    /* Functions */
    public function allegatoVisualizza($path, $name)
    {

          $path_decript = Crypt::decryptString($path);
          $name_decript = Crypt::decryptString($name);

          $headers = [
                  'Content-Type' => mime_content_type($path_decript) ,
               ]; 

          $risposta = response()->download($path_decript, $name_decript);

          ob_end_clean();

          return $risposta;

    }

    /* Functions */
    public function caricaFiles(Request $request)
    {
        dropzone_files_save(request('type'), request('model_id'), request('model_name'), request('model_path'), $request);
        ob_end_clean();
        return true;
    }

    public function allegatoDestroy($id)
    {
        $offerta_id = file_destroy($id);

        return redirect()->back()
            ->withSuccess('Allegato eliminato con successo');

    }

    // Get currency by ajax request
    public function getCurrencyAjax($value, $currency = '€')
    {
      if(\Request::ajax())
      {
        ob_clean();
        echo get_currency($value, $currency);
      }
      else
        abort(404);
    }

    public function test()
    {
      $fatturazioni = FatturazioneScadenze::where('data_avviso', date('Y-m-d'))->get();

      if(!empty($fatturazioni))
      {
        foreach($fatturazioni as $key => $ft)
        {
          $senders = json_decode(setting('commerciale::fatturazione::scadenze_notifica'));
          $oggetto = 'SCADENZA FATTURAZIONE - ' . $ft->ordinativo->oggetto;
          $testo = '<h3>' . $ft->ordinativo->oggetto . '</h3>'
                  . 'Cliente: <strong>' . nl2br(get_if_exist($ft->ordinativo->offerta->cliente, 'ragione_sociale')) . '</strong>'
                  . 'Descrizione: <strong>' . nl2br($ft->descrizione) . '</strong>'
                  . '<br>IMPORTO: <strong>' . get_currency($ft->importo) . '</strong>'
                  . '<br><br><br>La data fissata per la fatturazione è per il giorno '
                  . '<strong>' . $ft->data . '<strong>'
                  . '<br><br>Puoi visualizzare i dettagli al seguente link: <a href="' . route('admin.commerciale.ordinativo.edit', $ft->ordinativo_id) . '">' . route('admin.commerciale.ordinativo.edit', $ft->ordinativo_id) . '</a>';

          $result['data'] = [
            'senders' => $senders,
            'oggetto' => $oggetto,
            'testo' => $testo
          ];

          dd($result);
        }
      }
    }
}
