<?php

namespace Modules\Filters\Admin\Wecloud;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Facades\DB;
use Modules\Wecloud\Entities\File;

class FileFilter extends ModelFilter
{
    public function cerca($cerca)
    {
        return $this->where('name', 'LIKE', "%$cerca%")
                    ->orWhere('value', 'LIKE', "%$cerca%");
    }

    public function procedura($procedura)
    {
      if (!empty($procedura))
        return $this->where('value->procedura_id', $procedura);
    }

    public function area($area)
    {
      if (!empty($area))
        return $this->where('value->area_id', $area);
    }

    public function gruppo($gruppo)
    {
      if (!empty($gruppo))
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
