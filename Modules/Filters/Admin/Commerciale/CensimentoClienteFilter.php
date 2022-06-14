<?php

namespace Modules\Filters\Admin\Commerciale;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Facades\DB;

class CensimentoClienteFilter extends ModelFilter
{

  public function cliente($cliente)
  {
    if($cliente > 0)
      return $this->where('cliente_id' , $cliente);
  }

  public function commerciale($commerciale_id)
  {
    if($commerciale_id > 0)
      return $this->whereHas('cliente' , function ($q) use ($commerciale_id) {
                  $q->where('commerciale_id', $commerciale_id);
      });
  }
  
  public function order($order)
  {
    $by = !empty($order['by']) ? $order['by'] : 'id';
    $sort = !empty($order['sort']) ? $order['sort'] : 'desc';

    switch($by)
    {
      case 'created_user':
        return $this->join('users', 'created_user_id', '=', 'users.id')
                    ->orderBy(DB::raw("CONCAT(first_name, ' ', last_name)"), $sort);
        break;

      case 'commerciale_id':
        return $this->join('amministrazione__clienti', 'cliente_id', '=', 'amministrazione__clienti.id')
                    ->join('users', 'amministrazione__clienti.commerciale_id', '=', 'users.id')
                    ->orderBy(DB::raw("CONCAT(first_name, ' ', last_name)"), $sort);
        break;
    }

    return $this->orderBy($by, $sort);
  }
}
