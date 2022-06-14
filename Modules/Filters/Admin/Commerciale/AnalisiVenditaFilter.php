<?php

namespace Modules\Filters\Admin\Commerciale;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalisiVenditaFilter extends ModelFilter
{

  public function cliente($cliente)
  {
    if($cliente > 0)
      return $this->whereHas('censimento_cliente.cliente', function ($q) use ($cliente) {
                    $q->where('id', $cliente);
                  });
  }

  public function oggetto($oggetto)
  {
      return $this->where('titolo', 'LIKE', "%$oggetto%");
  }

  public function commerciale($commerciale)
  {
    if($commerciale > 0)
      return $this->where('commerciale_id', $commerciale);
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

  public function withOfferta($with_offerta)
  {
      if($with_offerta == 1)
        return $this->where('offerta_id', '<>', 0);

      if($with_offerta == 2)
        return $this->where('offerta_id', 0)->orWhereNull('offerta_id');
  }

  public function dataCreazione($data_creazione) 
  {
    return $this->whereDate('created_at', '>=', set_date_ita($data_creazione));
  }

  public function order($order)
  {
    $by = !empty($order['by']) ? $order['by'] : 'id';
    $sort = !empty($order['sort']) ? $order['sort'] : 'desc';
 
    switch($by)
    {
      case 'commerciale':
        return $this->join('users', 'commerciale_id', '=', 'users.id')
                    ->orderBy(DB::raw("CONCAT(first_name, ' ', last_name)"), $sort);
        break;
    }

    return $this->orderBy($by, $sort);
  }
}
