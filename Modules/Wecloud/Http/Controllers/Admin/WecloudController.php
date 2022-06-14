<?php

namespace Modules\Wecloud\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Modules\User\Entities\Sentinel\User;
use Modules\Wecloud\Entities\File;
use Modules\Profile\Entities\Area;
use Modules\Profile\Entities\Gruppo;
use Modules\Profile\Entities\Procedura;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;

class WecloudController extends AdminBaseController
{
    /**
     * @var Authentication
     */
    protected $auth;

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $users = User::select(DB::raw("CONCAT(first_name,' ',last_name) AS name"), 'id')
                        ->pluck('name', 'id')
                        ->toArray();

        $procedure = [''] + Procedura::pluck('titolo', 'id')->toArray();
        $aree = [''] + Area::pluck('titolo', 'id')->toArray();
        $gruppi = [''] + Gruppo::pluck('nome', 'id')->toArray();

        $files = File::filter($request->all())->with('user')->paginateFilter(config('wecore.pagination.limit'));

        $request->flash();

        return view('wecloud::admin.files.index', compact( 'users' , 'files' , 'aree' , 'procedure' , 'gruppi' ));
    }

    /* Functions */
    public function uploadFile(Request $request)
    {
        if($request->hasFile('file')) {
            if(!empty($request->file)){ 
                $file = $request->file("file");
                if($file->isValid()){
                    $file_info['azienda'] = session('azienda');
                    $file_info['folder'] = 'uploads/' . get_azienda() . '/' . 'wecloud' . '/' . date('Y') . '/' . date('m');
                    $file_info['client_name'] =$file->getClientOriginalName();
                    $file_info['mime_type'] = $file->getMimeType();
                    $file_info['extension'] = $file->extension();
                    $file_info['size'] = $file->getSize();
                    $file_info['hash_name'] = $file->hashName();
                    $file_info['name'] = request("file_nome");
                    $file_info['path'] = $file_info['folder'] . '/' . $file_info['hash_name'];
                    $file_info['procedura_id'] = request("file_procedura_id");
                    $file_info['area_id'] = request("file_area_id");
                    $file_info['gruppo_id'] = request("file_gruppo_id");
                    $file->store('public/' . $file_info['folder']);
    
                    $file = new File([
                        'name' => 'file',
                        'value' => json_encode($file_info),
                        'uploaded_user_id' => Auth::id(),
                        ]
                    );

                    $file->save();

                    return redirect()->route('admin.wecloud.file.index')
                        ->withSuccess('File caricato con successo.');
                } else {
                    return redirect()->route('admin.wecloud.file.index')
                        ->withError('File non valido.');                
                }
            } else {
                return redirect()->route('admin.wecloud.file.index')
                ->withError('Il file non ha un nome valido.');                  
            }
        }  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  File $file
     * @return Response
     */
    public function destroy(File $file)
    {
        $file->delete();

        // Log
        activity(session('azienda'))
            ->performedOn($file)
            ->withProperties(json_encode($file))
            ->log('destroyed');

        return redirect()->route('admin.wecloud.file.index')
            ->withSuccess('File eliminato con successo.');
    }

}
