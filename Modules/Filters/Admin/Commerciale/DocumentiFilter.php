<?php

namespace Modules\Filters\Admin\Commerciale;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Facades\DB;

class DocumentiFilter extends ModelFilter
{
    public function nome($nome)
    {
        return $this->where('value->name', 'LIKE', "%$nome%");
    }

    public function tipologia($tipologia)
    {
        if($tipologia > -1)
            return $this->where('value->tipologia_id', $tipologia);
    }

    public function procedura($procedura)
    {
        if($procedura != 0)
            return $this->where('value->procedura_id', $procedura);
    }

    public function area($area)
    {
        if($area != 0)
            return $this->where('value->area_id', $area);
    }

    public function gruppo($gruppo)
    {
        if($gruppo != 0)
            return $this->where('value->gruppo_id', $gruppo);
    }

    public function order($order)
    {
      $by = !empty($order['by']) ? $order['by'] : 'id';
      $sort = !empty($order['sort']) ? $order['sort'] : 'desc';

      switch($by)
      {
        case 'utente':
          return $this->select(DB::raw("CONCAT(first_name, ' ', last_name) as full_name, wecloud__files.*"))
                      ->join('users', 'uploaded_user_id', '=', 'users.id')
                      ->orderBy('full_name', $sort);
          break;
      }

      return $this->orderBy($by, $sort);
    }
}
