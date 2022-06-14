<?php

namespace Modules\Filters\Admin\Commerciale;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SegnalazioneOpportunitaFilter extends ModelFilter
{
  public function eliminati($del)
  {
      if($del)
        return $this->onlyTrashed();
  }

  public function oggetto($oggetto)
  {
      return $this->where('oggetto', 'LIKE', "%$oggetto%");
  }

  public function cliente($cliente_id)
  {
      if($cliente_id > 0)
        return $this->where('cliente_id', $cliente_id);
  }

  public function utente($utente_id)
  {
      if($utente_id > 0)
        return $this->where('created_user_id', $utente_id);
  }


  public function stato($stato_id)
  {
      if($stato_id > -1)
        return $this->where('stato_id', $stato_id);
  }

  public function range($date)
  {
    if($date)
    {
      $date = explode(' - ', $date);
      $start_array = explode('/', $date[0]);
      $end_array = explode('/', $date[1]);
      $start = Carbon::parse($start_array[2].'-'.$start_array[1].'-'.$start_array[0])->toDateString();
      $end = Carbon::parse($end_array[2].'-'.$end_array[1].'-'.$end_array[0])->toDateString();
      return $this->whereBetween('created_at', [$start, $end]);
    }
  }

  public function order($order)
  {
    $by = !empty($order['by']) ? $order['by'] : 'id';
    $sort = !empty($order['sort']) ? $order['sort'] : 'desc';

    switch($by)
    {
      case 'numero':
        return $this->orderBy(DB::raw("YEAR(created_at)"), $sort)
                    ->orderBy('numero', $sort);
        break;

      case 'cliente':
        return $this->join('amministrazione__clienti', 'cliente_id', '=', 'amministrazione__clienti.id')
                    ->orderBy('ragione_sociale', $sort);
        break;

      case 'user':
        return $this->join('users', 'created_user_id', '=', 'users.id')
                    ->orderBy(DB::raw("CONCAT(first_name, ' ', last_name)"), $sort);
        break;

      case 'stato':
        $stati = "CASE";
        foreach(config('commerciale.segnalazioneopportunita.stati') as $key => $stato)
        {
          $stati .= " WHEN stato_id = $key THEN '$stato' ";
        }
        $stati .= "END as stato_value, commerciale__segnalazioniopportunita.*";

        return $this->select(DB::raw($stati))->orderBy('stato_value', $sort);
        break;
    }

    return $this->orderBy($by, $sort);
  }
}
