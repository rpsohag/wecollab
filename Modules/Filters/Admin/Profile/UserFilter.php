<?php

namespace Modules\Filters\Admin\Profile;

use EloquentFilter\ModelFilter;

class UserFilter extends ModelFilter
{
    public function name($name)
    {
        return $this->whereHas('user', function($q) use($name) {
            $q->where('first_name', 'LIKE', "%$name%")
                ->orWhere('last_name', 'LIKE', "%$name%")
                ->orWhere(\DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%$name%");
        });
    }

    public function azienda($azienda)
    {
        $azienda = strtolower($azienda);

        return $this->where('azienda', 'LIKE', "%$azienda%");
    }

    public function username($username)
    {
        return $this->where('username', 'LIKE', "%$username%");
    }

    public function order($order)
    {
      $by = !empty($order['by']) ? $order['by'] : 'id';
      $sort = !empty($order['sort']) ? $order['sort'] : 'desc';

      switch($by)
      {
        case 'id':
          return $this->orderBy('users.id', $sort);
          break;

        case 'created_at':
          return $this->orderBy('users.created_at', $sort);
          break;

        case 'attivo':
          return;
          break;
      }

      return $this->orderBy($by, $sort);
    }
}
