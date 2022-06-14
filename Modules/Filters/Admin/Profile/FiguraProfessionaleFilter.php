<?php

namespace Modules\Filters\Admin\Profile;

use EloquentFilter\ModelFilter;

class FiguraProfessionaleFilter extends ModelFilter
{
    public function descrizione($descrizione)
    {
        return $this->where('descrizione', 'LIKE', "%$descrizione%");
    }

    public function users($users)
    {
        return $this->whereJsonContains('users', $users);
    }

    public function order($order)
    {
      $by = !empty($order['by']) ? $order['by'] : 'id';
      $sort = !empty($order['sort']) ? $order['sort'] : 'desc';

      return $this->orderBy($by, $sort);
    }
}
