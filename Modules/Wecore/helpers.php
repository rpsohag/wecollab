<?php

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Modules\Wecore\Entities\Meta;
use Modules\Profile\Entities\Profile;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Modules\User\Entities\Sentinel\User;
use Modules\Wecloud\Entities\File;
use Adldap\Laravel\Facades\Adldap;
use Modules\Profile\Entities\Area;
use Modules\Profile\Entities\Gruppo;
use Illuminate\Support\Arr;

if (!function_exists('read_data_richieste')) {
	function read_data_richieste($data,$tipo)
	{
		$data = new DateTime($data);
		$mese = array("Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre");
		$giorno = array("Domenica", "Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato","Domenica");

		$settimana = $data->format("w");
		$gg = $data->format("d");
		$mm = $data->format("n");
		$anno = $data->format("Y");
		$ore = $data->format("H:i");

		//verifico la  richiesta
		if($tipo == "2")//permesso
		{
			return strtolower($giorno[$settimana] . " " . $gg . " " . $mese[$mm - 1] . " " . $anno . " Ore ".$ore);
		}
		else 
		{
			return strtolower($giorno[$settimana] . " " . $gg . " " . $mese[$mm - 1] . " " . $anno);
		}
	}
}

if (!function_exists('curl_get_contents')) {
    function curl_get_contents($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}

if (!function_exists('get_working_days')) {
    function get_working_days($from, $to) {
        $workingDays = [1, 2, 3, 4, 5];
        $holidayDays = config('holidays.holidays');

        $from = new DateTime($from);
        $to = new DateTime($to);
        //$to->modify('-1 day');
        $interval = new DateInterval('P1D');
        $periods = new DatePeriod($from, $interval, $to);

        $days = 0;
        foreach ($periods as $period) {
            if (!in_array($period->format('N'), $workingDays)) continue;
            if (in_array($period->format('Y-m-d'), $holidayDays)) continue;
            if (in_array($period->format('*-m-d'), $holidayDays)) continue;
            $days++; 
        }
        
        return $days; 
    }
}

// Tempo lavorativo totale senza calcolare quello extra lavorativo o pausa pranzo
if (!function_exists('working_time')) {
    function working_time($ini_str,$end_str, $return = 'seconds'){
        $p1 = get_working_hours_part($ini_str,$end_str,'p1');
        $p2 = get_working_hours_part($ini_str,$end_str,'p2');

        switch($return) {
            case 'days':
                return ($p1 + $p2) / 86400; 
            case 'hours': 
                return ($p1 + $p2) / 3600;
            case 'seconds': 
                return ($p1 + $p2);
            case 'giorni':
                return ($p1 + $p2) / 86400; 
            case 'ore': 
                return ($p1 + $p2) / 3600;
            case 'secondi': 
                return ($p1 + $p2);
        }
    }
}

//Tempo lavorativo suddiviso in pre pausa pranzo & post pausa pranzo
if (!function_exists('get_working_hours_part')) {
    function get_working_hours_part($ini_str,$end_str,$part = 'p1'){

        // Orario lavorativo
        if($part == 'p1'){
            $ini_time = [8,0]; 
            $end_time = [14,0]; 
        } elseif($part == 'p2') {
            $ini_time = [15,0]; 
            $end_time = [18,0];             
        }

        // Date
        $ini = date_create($ini_str);
        $ini_wk = date_time_set(date_create($ini_str),$ini_time[0],$ini_time[1]);
        $end = date_create($end_str);
        $end_wk = date_time_set(date_create($end_str),$end_time[0],$end_time[1]);
        
        // Giorni
        $workdays_arr = get_workdays($ini,$end);
        $workdays_count = count($workdays_arr);
        $workday_seconds = (($end_time[0] * 60 + $end_time[1]) - ($ini_time[0] * 60 + $ini_time[1])) * 60;

        //Differenza
        $ini_seconds = 0;
        $end_seconds = 0;
        if(in_array($ini->format('Y-m-d'),$workdays_arr)) $ini_seconds = $ini->format('U') - $ini_wk->format('U');
        if(in_array($end->format('Y-m-d'),$workdays_arr)) $end_seconds = $end_wk->format('U') - $end->format('U');
        $seconds_dif = $ini_seconds > 0 ? $ini_seconds : 0;
        if($end_seconds > 0) $seconds_dif += $end_seconds;

        //Calcolo finale
        $working_seconds = max(($workdays_count * $workday_seconds) - $seconds_dif, 0);

        return $working_seconds;
    }
}

// Giorni lavorativi
if (!function_exists('get_workdays')) {
    function get_workdays($ini,$end){
        //config
        $skipdays = [6,0]; //saturday:6; sunday:0
        $skipdates = config('holidays.holidays');
        $current = clone $ini;
        $current_disp = $current->format('Y-m-d');
        $end_disp = $end->format('Y-m-d');
        $days_arr = [];
        while($current_disp <= $end_disp){
            if(!in_array($current->format('w'),$skipdays) && !in_array($current_disp,$skipdates)){
                $days_arr[] = $current_disp;
            }
            $current->add(new DateInterval('P1D'));
            $current_disp = $current->format('Y-m-d');
        }
        return $days_arr;
    }
}

if (!function_exists('get_seconds_to_his')) {
  function get_seconds_to_his($seconds)
  {
      $dtF = new \DateTime('@0');
      $dtT = new \DateTime("@$seconds");
      return $dtF->diff($dtT)->format('%a giorni, %h ore, %i minuti e %s secondi');
  }
}

if (!function_exists('get_seconds_to_hi')) {
    function get_seconds_to_hi($seconds)
    {
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");
        return $dtF->diff($dtT)->format('%a giorni, %h ore e %i minuti');
    }
}

if (!function_exists('get_seconds_to_hours')) {
    function get_seconds_to_hours($seconds)
    {
        $timestamp = floor($seconds / 3600) . gmdate(":i", $seconds % 3600);
        $tmp = explode(':', $timestamp);
        $hours = ltrim($tmp[0], '0');
        $minutes =  ltrim($tmp[1], '0');

        $timestamp = $hours . ' ore e ' . $minutes . ' minuti';

        if($hours == '' && $minutes == '')
            $timestamp = 'Meno di 1 minuto';
        elseif($hours == '')
            $timestamp = $minutes . ' minuti';
        elseif($minutes == '')
            $timestamp = $hours . ' ore';
        
        
        return $timestamp;
    }
}

if (!function_exists('secondsToTime')) {
    function secondsToTime($seconds_time)
    {
        if ($seconds_time < 24 * 60 * 60) {
            return gmdate('H:i:s', $seconds_time);
        } else {
            $hours = floor($seconds_time / 3600);
            $minutes = floor(($seconds_time - $hours * 3600) / 60);
            $seconds = floor($seconds_time - ($hours * 3600) - ($minutes * 60));
            if($minutes == 0){ $minutes = "00";}
            if($seconds == 0){ $seconds = "00";}
            return "$hours:$minutes:$seconds";
        }
    }
}

if (!function_exists('is_json'))  {
    function is_json($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

if (! function_exists('get_json_aree')) {
    function get_json_aree()
    {
        $json_aree = base64_encode(Area::all()->toJson());
        return $json_aree;
    }
}

if (! function_exists('get_json_gruppi')) {
    function get_json_gruppi()
    {
        $json_gruppi = base64_encode(Gruppo::all()->toJson());

        return $json_gruppi;
    }
}

if (! function_exists('order_th')) {
  function order_th($field, $name)
  {
    $order_by = !empty(request('order')['by']) ? request('order')['by'] : null;
    $order_sort = !empty(request('order')['sort']) ? request('order')['sort'] : null;


    $class = ($order_by == $field ? ($order_sort == 'desc' ? 'sorting_desc' : 'sorting_asc') : 'sorting');
    $route = route(Route::currentRouteName(), ['order' => ['by' => $field, 'sort' => (($order_by == $field && $order_sort == 'desc') ? 'asc' : 'desc')]] + request()->all());

    $th = '<th class="' . $class . '">
            <a href="' . $route . '">' . $name . '</a>
          </th>';

    return $th;
  }
}

if (! function_exists('gruppi_ldap_user')) {
  function gruppi_ldap_user($id = null, $return_groups = false)
  {
    $selected_groups_ldap = [];
    $profile = ($id > 0) ? get_profile_user($id) : get_profile_user();

    $s_groups_ldap = Adldap::search()->users()
                            ->where('sAMAccountName', $profile->username)
                            //->where('memberOf', config('ldap.connections.default.memberOf'))
                            ->first();

    if(!empty($s_groups_ldap))
    {
      if(!$return_groups)
      {
        $selected_groups_ldap = [];
        foreach ($s_groups_ldap->getGroups() as $gruppo_ldap)
          if($gruppo_ldap->cn[0] != 'Domain Users')
            $selected_groups_ldap[$gruppo_ldap->cn[0]] = $gruppo_ldap->cn[0];
      }
      else
        $selected_groups_ldap = $s_groups_ldap->getGroups();
    }
    return $selected_groups_ldap;
  }
}

if (! function_exists('gruppi_ldap')) {
  function gruppi_ldap()
  {
    $gruppi_ldap = [];
    $g_ldap = Adldap::search()->groups()->in(config('ldap.connections.default.ou'))->get();

    foreach($g_ldap as $gruppo_ldap)
      $gruppi_ldap[$gruppo_ldap->cn[0]] = $gruppo_ldap->cn[0];

    return $gruppi_ldap;
  }
}

if (! function_exists('auth_user')) {
  function auth_user()
  {
    return User::findOrFail(Auth::id());
  }
}

if (! function_exists('user')) {
  function user($id)
  {
    return User::findOrFail($id);
  }
}

if (! function_exists('log_clean_value')) {
  function log_clean_value($val, $type = null, $subtype = null)
  {
    $val = !is_array($val) ? $val : implode( ", ", $val);
    $type = log_clean_column($type);
    $subtype = log_clean_column($subtype);

    switch(true)
    {
      case stristr($type, 'percentuale') || stristr($subtype, 'percentuale'):
        $val .= '%';
        break;

      case stristr($type, 'stato') || stristr($subtype, 'stato'):
        if(!empty(config('tasklist.attivita.stati')[$val]))
          $val = config('tasklist.attivita.stati')[$val];
        break;

      case stristr($type, 'utente') || stristr($subtype, 'utente'):
      case stristr($type, 'assegnatari') || stristr($subtype, 'assegnatari'):
      case stristr($type, 'richiedente') || stristr($subtype, 'richiedente'):
      case stristr($type, 'created user') || stristr($subtype, 'created user'):
      case stristr($type, 'updated user') || stristr($subtype, 'updated user'):
      case stristr($type, 'commerciale') || stristr($subtype, 'commerciale'):
        if(is_numeric($val))
        {
          $profile = Profile::where('user_id', $val)->first();

          if(!empty($profile))
            $val = $profile->user->first_name . ' ' . $profile->user->last_name;
          else
            $val = 'utente non trovato';
        }
        break;

      case stristr($type, 'utenti') || stristr($subtype, 'utenti'):
        if(!empty($val))
        {
          $profiles = Profile::whereIn('user_id', json_decode($val, true))->get();

          $val = '';
          if(!empty($profiles))
          {
            foreach ($profiles as $profile)
            {
              if(!empty($profile->user))
                $val .= $profile->user->first_name . ' ' . $profile->user->last_name . ', ';
            }
            $val = rtrim($val, ', ');
          }
          else
            $val = 'utente non trovato';
        }
        break;
    }

    return $val;
  }
}

if (! function_exists('log_clean_column')) {
  function log_clean_column($col)
  {
    $replace = [
      'voci' => 'lavoro',
      'users_selected' => 'utenti selezionati'
    ];

    if(array_key_exists($col, $replace))
      $col = $replace[$col];

    if(is_numeric($col))
      $col = '---';

    return str_replace(['_id', 'meta', '_'], ['', '', ' '], $col);
  }
}

if (! function_exists('array_undot')) {
  function array_undot($dotted_array)
  {
    $array = [];

    foreach ($dotted_array as $key => $value)
      Arr::set($array, $key, $value);

    return $array;
  }
}

if (! function_exists('array_dot_reverse')) {
    function array_dot_reverse($string)
    {
      $pieces = explode('.', $string);
      $value = array_pop($pieces);

      Arr::set($array, implode('.', $pieces), $value);

      return $array;
  }
}

if (! function_exists('collection_diff')) {
    function collection_diff($collection_1, $collection_2)
    {
      $diff = [];
      $collection_1 = Arr::dot($collection_1->toArray());
      $collection_2 = Arr::dot($collection_2->toArray());

      $array_1 = [];
      foreach($collection_1 as $key => $value)
        if(!is_array($value))
          $array_1[$key] = $value;

      $array_2 = [];
      foreach($collection_2 as $key => $value)
        if(!is_array($value))
          $array_2[$key] = $value;

      $diff = array_diff($array_1, $array_2);

      unset($diff['_token']);
      unset($diff['method']);

      return $diff;
    }
}

if (! function_exists('get_activities')) {
    function get_activities($model)
    {
      $activities = Activity::where('subject_type', get_class($model))
                          ->where('subject_id', $model->id)
                          ->orderBY('created_at', 'desc')
                          ->get();

      return $activities;
    }
}

if (! function_exists('str_random')) {
    function str_random($int = 40)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $random = '';
        for ($i = 0; $i < $int; $i++) {
            $random .= $characters[rand(0, $charactersLength - 1)];
        }       
        return $random;
    }
}

if (! function_exists('debug_query')) {
    function debug_query($query)
    {
        return vsprintf(str_replace('?', '%s', $query->toSql()), collect($query->getBindings())->map(function ($binding) {
            return is_numeric($binding) ? $binding : "'{$binding}'";
        })->toArray());
    }
}

if (! function_exists('getTipologiaNotifica')) {
    function getTipologiaNotifica($path)
    {
        $id = strrchr($path,"/");
        $id = substr($id,1,strlen($id));
        return $id;
    }
}

if (! function_exists('get_nazione_sigla')) {
  function get_nazione_sigla($nazione)
  {
    $nazioni_sigle = config('wecore.nazioni.sigle');

    return $nazioni_sigle[strtoupper($nazione)];
  }
}

if (! function_exists('filemanager_create_or_set_folder')) {
    function filemanager_create_or_set_folder($subfolder = null)
    {
        $folder =  strtolower(((empty($subfolder)) ? get_azienda() . '/' . \Request::path() : $subfolder));

        if(!file_exists(public_path('uploads/' . $folder)))
            mkdir(public_path('uploads/' . $folder), 0777, true);

        session_start();
        $_SESSION['RF']["subfolder"] = $folder;
        session_write_close();
    }
}

if (! function_exists('mail_send')) {
    function mail_send($senders, $subject, $content, $attach = null, $azienda = null, $template = "MailBase")
    {
        $className = '\\Modules\\Wecore\\Mail\\'.$template;
		
		if(is_array($senders))
		{
			$errors = [];
			foreach ($senders as $key => $sender)
			{
				$mail = Mail::to($sender)->queue(new $className($subject, $content, $attach, $azienda));
				if($mail)
                    $errors[$sender] = $mail;
			}
		}
		else
		{
            return Mail::to($senders)->queue(new $className($subject, $content, $attach, $azienda));
        }
    }
}

if (! function_exists('save_meta')) {
    function save_meta($metas, $model)
    {
        foreach ($metas as $key => $value) {
            if(!empty($value) && !is_array($value))
            {
                if($key == 'note'){
                    if(trim($value) != ''){
                        $meta = new Meta([
                            'name' => $key,
                            'value' => $value,
                            'created_user_id' => Auth::id(),
                            'updated_user_id' => Auth::id()]
                        );
                        $model->metas()->save($meta);
                    }
                } else {
                    $meta = new Meta([
                        'name' => $key,
                        'value' => $value,
                        'created_user_id' => Auth::id(),
                        'updated_user_id' => Auth::id()]
                    );
                    $model->metas()->save($meta);                    
                }
            }
        }
    }
}

if (! function_exists('file_icons')) {
    function file_icons($extension)
    {
        switch($extension)
        {
            case stristr($extension, 'doc'):
                $icon = 'fa-file-word-o text-primary';
                break;
            case stristr($extension, 'xls'):
                $icon = 'fa-file-excel-o text-success';
                break;
            case stristr($extension, 'pdf text-danger'):
                $icon = 'fa-file-pdf-o';
                break;
            case stristr($extension, 'png') || stristr($extension, 'jpg') || stristr($extension, 'gif'):
                $icon = 'fa-file-image-o text-info';
                break;
            case stristr($extension, 'zip') || stristr($extension, 'rar'):
                $icon = 'fa-file-zip-o text-warning';
                break;
            default:
                $icon = 'fa-file-o';
                break;
        }

        return $icon;
    }
}


if (! function_exists('mb')) {
    function mb($byte)
    {
        return round($byte/1000000, 3);
    }
}

if (! function_exists('download_file')) {
    function download_file($file_path, $file_name)
    {
        $path = Crypt::encryptString($file_path);
        $name = Crypt::encryptString($file_name);

        return route('admin.wecore.allegato.visualizza', [$path, $name]);
    }
}

if (! function_exists('file_destroy')) {
    function file_destroy($id)
    {
        $allegato = Meta::where('id', $id)->where('name', 'file')->first();

        $file = $allegato->value;
        $metagable_id = $allegato->metagable->metagable_id;

        Meta::destroy($id);
        Storage::delete('public/'.$file->path);

        return $metagable_id;
    }
}

if (! function_exists('wecloud_files_save')) {
    function wecloud_files_save($request)
    {
        if ($request->hasFile('file'))
        {
            $file = $request->file('file');

            if ($file->isValid())
            {
                $file_info = wecloud_files_store($request);

                $user_segreteria = User::find(82); // utente di default: segreteria
                $user_id = (empty(Auth::id()) || Auth::id() == null) ? $user_segreteria->id : Auth::id();

                $file = File::create([
                    'name' => $file->getClientOriginalName(),
                    'value' => json_encode($file_info),
                    'uploaded_user_id' => $user_id
                ]);
                
                return $file;
            }
        }

        return false;
    }
}

if (! function_exists('dropzone_files_save')) {
    function dropzone_files_save($folder, $model_id, $model_name, $model_path, $request)
    {
        if ($request->hasFile('file'))
        {
            $file = $request->file('file');

            if ($file->isValid())
            {
                $client_name = !empty($client_name) ? $client_name . '.' . $file->getClientOriginalExtension() : $file->getClientOriginalName();
                $file_info = dropzone_files_store($folder, $request);

                $user_segreteria = User::find(82); // utente di default: segreteria
                $user_id = (empty(Auth::id()) || Auth::id() == null) ? $user_segreteria->id : Auth::id();

                $meta = new Meta([
                    'name' => 'file',
                    'value' => json_encode($file_info),
                    'created_user_id' => $user_id,
                    'updated_user_id' => $user_id]
                );
                //RichiesteIntervento;
                $className = '\\Modules\\'.$model_path.'\\Entities\\'.$model_name;
                $model_class = new $className;
                $model = $model_class->find($model_id);
                return $model->metas()->save($meta);
            }
        }

        return false;
    }
}

if (! function_exists('file_save')) {
    function file_save($folder, $model, $request, $name = '', $key = 'meta.file.file', $client_name = '')
    {
        if ($request->hasFile($key))

        {
            $file = $request->file($key);

            if ($file->isValid())

            {
                $client_name = !empty($client_name) ? $client_name . '.' . $file->getClientOriginalExtension() : $file->getClientOriginalName();
                $file_info = file_store($folder, $request, $name, $key, $client_name);

                if(!empty($file_info)) {

                    $user_segreteria = User::find(82); // utente di default: segreteria
                    $user_id = (empty(Auth::id()) || Auth::id() == null) ? $user_segreteria->id : Auth::id();
    
                    $meta = new Meta([
                        'name' => 'file',
                        'value' => json_encode($file_info),
                        'created_user_id' => $user_id,
                        'updated_user_id' => $user_id]
                    );
                    return $model->metas()->save($meta);    

                } else {

                    return false;

                }
            }
        }

        return false;
        
    }
}

if (! function_exists('dropzone_files_store')) {
    function dropzone_files_store($folder, $request)
    {
        if ($request->hasFile('file'))
        {
            $file = $request->file('file');

            if ($file->isValid())
            {
                $file_info['azienda'] = session('azienda');
                $file_info['folder'] = 'uploads/' . get_azienda() . '/' . $folder . '/' . date('Y') . '/' . date('m');
                $file_info['client_name'] =$file->getClientOriginalName();
                $file_info['mime_type'] = $file->getMimeType();
                $file_info['extension'] = $file->extension();
                $file_info['size'] = $file->getSize();
                $file_info['hash_name'] = $file->hashName();
                $file_info['name'] = str_replace('.' . $file_info['extension'], '', $file_info['client_name']);
                $file_info['path'] = $file_info['folder'] . '/' . $file_info['hash_name'];
 
                if($file->store('public/' . $file_info['folder']))
                    return $file_info;
            }
        }

        return false;
    }
}

if (! function_exists('wecloud_files_store')) {
    function wecloud_files_store($request)
    {
        if ($request->hasFile('file'))
        {
            $file = $request->file('file');

            if ($file->isValid())
            {
                $file_info['azienda'] = session('azienda');
                $file_info['folder'] = 'uploads/' . get_azienda() . '/' . 'wecloud' . '/' . date('Y') . '/' . date('m');
                $file_info['client_name'] =$file->getClientOriginalName();
                $file_info['mime_type'] = $file->getMimeType();
                $file_info['extension'] = $file->extension();
                $file_info['size'] = $file->getSize();
                $file_info['hash_name'] = $file->hashName();
                $file_info['name'] = str_replace('.' . $file_info['extension'], '', $file_info['client_name']);
                $file_info['path'] = $file_info['folder'] . '/' . $file_info['hash_name'];
 
                if($file->store('public/' . $file_info['folder']))
                    return $file_info;
            }
        }

        return false;
    }
}

if (! function_exists('file_store')) {
    function file_store($folder, $request, $name = '', $key = 'meta.file.file', $client_name = '')
    {
        if ($request->hasFile($key))
        {
            $file = $request->file($key);

            if ($file->isValid())
            {
                $file_info['azienda'] = session('azienda');
                $file_info['folder'] = 'uploads/' . get_azienda() . '/' . $folder . '/' . date('Y') . '/' . date('m');
                $file_info['client_name'] = !empty($client_name) ? $client_name : $file->getClientOriginalName();
                $file_info['mime_type'] = $file->getMimeType();
                $file_info['extension'] = $file->extension();
                $file_info['size'] = $file->getSize();
                $file_info['hash_name'] = $file->hashName();
                $file_info['name'] = (empty($name)) ? str_replace('.' . $file_info['extension'], '', $file_info['client_name']) : $name;
                $file_info['path'] = $file_info['folder'] . '/' . $file_info['hash_name'];
 
                if($file->store('public/' . $file_info['folder']))
                    return $file_info;
            }
        }

        return false;
    }
}

if (! function_exists('sn')) {
    function sn($value)
    {
        return config('wecore.sn')[$value];
    }
}

if (! function_exists('sn_icon')) {
    function sn_icon($value)
    {
        return ($value == 1) ? '<i class="text-success fa fa-check-circle-o fa-2x"><span class="hidden">1</span></i>' : '<i class="text-danger fa fa-times-circle-o fa-2x"><span class="hidden">0</span></i>';
    }
}

if (! function_exists('set_via_placeholder')) {
    function set_via_placeholder($w, $h = null)
    {
        $h = (empty($h)) ? $w : $h;

        return "http://via.placeholder.com/{$w}x{$h}";
    }
}

if (! function_exists('set_blob')) {
    function set_blob($file)
    {
        if(!empty($file))
        {
            $path = $file->getPathname();
            $type = $file->getMimeType();
            $data = file_get_contents($path);
            $base64 = 'data:' . $type . ';base64,' . base64_encode($data);

            return $base64;
        }

        return null;
    }
}

if (! function_exists('get_azienda_class')) {
    function get_azienda_class($class_wc, $class_dc)
    {
        return ((get_azienda() == 'we-com') ? $class_wc : $class_dc);
    }
}

if (! function_exists('get_azienda_dati')) {
    function get_azienda_dati($key = null)
    {
        $key = (!empty($key)) ? $key : get_azienda();
        $aziende = json_decode(setting('wecore::aziende'));
        $azienda = (empty($aziende)) ? [] : $aziende->$key;

        return $azienda;
    }
}

if (! function_exists('set_azienda')) {
    function set_azienda($azienda)
    {
        $aziende = json_decode(setting('profile::aziende'));

        if(empty($azienda))
            session(['azienda' => $aziende[0]]);
        else
            foreach($aziende as $a)
                if(strtolower($a) == strtolower($azienda))
                    session(['azienda' => $a]);
    }
}

if (! function_exists('get_azienda')) {
    function get_azienda()
    {
        $azienda = session('azienda');

        if(empty($azienda))
        {
            $profile = get_profile_user();

            set_azienda($profile->azienda);

            $azienda = session('azienda');
        }

        return Str::slug($azienda, '-');
    }
}

if (! function_exists('get_profile_user')) {
    function get_profile_user($user_id = null)
    {
        $user_id = (empty($user_id)) ? Auth::id() : $user_id;

        return Profile::where('user_id', $user_id)->first();
    }
}

if (! function_exists('get_name_error')) {
    function get_name_error($value)
    {
        return str_replace(['[', ']'], ['.', ''], $value);
    }
}

if (! function_exists('get_if_exist')) {
    function get_if_exist($object, $value = null, $alternative = null)
    {
        if(!empty($object))
        {
            if(is_array($object))
            {
                if(isset($object[$value]))
                    return $object[$value];
            }
            elseif(is_object($object))
            {
                if(isset($object->$value))
                    return $object->$value;
            }
            else
            {
                return $object;
            }
        }
        elseif($alternative)
            return $alternative;

        return null;
    }
}

if (! function_exists('set_check_json')) {
    function set_check_json($values)
    {
        $json = [];

        foreach ($values as $key => $value)
        {
            if($value == 1)
                $json[] = $key;
        }

        return json_encode($json);
    }
}

if (! function_exists('checked')) {
    function checked($array, $value)
    {
        if(empty($array))
            $array = [];

        if(!is_array($array))
            $array = json_decode($array);

        return in_array($value, $array);
    }
}

if (! function_exists('get_currency')) {
    function get_currency($value, $currency = '€')
    {
        if(is_numeric($value))
            return $currency . ' ' . number_format($value, 2, ',', '.');
    }
}

if (! function_exists('clean_currency')) {
    function clean_currency($value, $currency = '€')
    {
        return trim(str_replace([$currency, '.', ','], ['', '', '.'], $value));
    }
}

if (! function_exists('get_date_hour_ita')) {
    function get_date_hour_ita($date)
    {
        $date_time = \DateTime::createFromFormat('Y-m-d H:i:s', $date);

        if($date_time !== false)
            return $date_time->format('d/m/Y - H:i');
        else
            return null;
    }
}

if (! function_exists('set_date_hour_ita')) {
    function set_date_hour_ita($date)
    {
        $format = 'Y-m-d H:i:s';
        $date_time = \DateTime::createFromFormat('d/m/Y - H:i', $date);

        if($date_time !== false)
            return $date_time->format($format);
        else
        {
            $d = \DateTime::createFromFormat($format, $date);

            if($d && $d->format($format) === $date)
                return $date;
            else
                return null;
        }
    }
}

if (! function_exists('set_datetime_ita')) {
    function set_datetime_ita($date)
    {
        $format = 'Y-m-d H:i:s';
        $date_time = \DateTime::createFromFormat('d/m/Y H:i:s', $date);

        if($date_time !== false)
            return $date_time->format($format);
        else
        {
            $d = \DateTime::createFromFormat($format, $date);

            if($d && $d->format($format) === $date)
                return $date;
            else
                return null;
        }
    }
}

if (! function_exists('get_date_ita')) {
    function get_date_ita($date)
    {
        $date_time = \DateTime::createFromFormat('Y-m-d H:i:s', $date);

        if($date_time !== false)
            return $date_time->format('d/m/Y');
        else
            return null;
    }
}

if (! function_exists('get_date_ita_due')) {
    function get_date_ita_due($date)
    {
        $date_time = \DateTime::createFromFormat('Y-m-d', $date);

        if($date_time !== false)
            return $date_time->format('d/m/Y');
        else
            return null;
    }
}

if (! function_exists('get_hour')) {
    function get_hour($date)
    {
        $date_time = \DateTime::createFromFormat('Y-m-d H:i:s', $date);

        if($date_time !== false)
            return $date_time->format('H:i');
        else
            return null;
    }
}

if (! function_exists('set_date_ita')) {
    function set_date_ita($date)
    {
        $format = 'Y-m-d';
        $date_time = \DateTime::createFromFormat('d/m/Y', $date);

        if($date_time !== false)
            return $date_time->format($format);
        else
        {
            $d = \DateTime::createFromFormat($format, $date);

            if($d && $d->format($format) === $date)
                return $date;
            else
                return null;
        }
    }
}

if (! function_exists('set_sql_date')) {
    function set_sql_date($date)
    {
		$format = 'Y-m-d H:i:s';
        $date_time = \DateTime::createFromFormat('d/m/Y H:i:s', $date);

        if($date_time !== false)
            return $date_time->format($format);
        else
        {
            $d = \DateTime::createFromFormat($format, $date);

            if($d && $d->format($format) === $date)
                return $date;
            else
                return null;
        }
    }
}

if (! function_exists('get_sql_date')) {
    function get_sql_date($date)
    {
		$format = 'd/m/Y';
        $date_time = \DateTime::createFromFormat('Y-m-d H:i:s', $date);

        if($date_time !== false)
            return $date_time->format($format);
        else
        {
            $d = \DateTime::createFromFormat($format, $date);

            if($d && $d->format($format) === $date)
                return $date;
            else
                return null;
        }
    }
}

if (! function_exists('resort_array_by_key')) {
	function resort_array_by_key($arr , $key )
	{
		$tmp_bottom_arr = [];

		foreach($arr as $chiave => $v)
		{
			if($chiave == $key)
			{
				$tmp_bottom_arr[$key] = $v;
				unset($arr[$key]);
			}
		}

		return $arr + $tmp_bottom_arr;
	}
}
