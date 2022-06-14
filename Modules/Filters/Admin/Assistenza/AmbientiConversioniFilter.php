<?php

namespace Modules\Filters\Admin\Assistenza;

use EloquentFilter\ModelFilter;
use DB;
use Modules\Assistenza\Entities\AmbienteConversioni;
use Modules\Amministrazione\Entities\Clienti;

class AmbientiConversioniFilter extends ModelFilter
{

    public function cliente($cliente_id)
    {
      if($cliente_id != 81)
        return $this->where('cliente_id', $cliente_id);
    }

    public function admin($admin)
    {

        return $this->where('user_admin', 'LIKE', "%$admin%");
    }

    public function chiuso($chiuso)
    {
      if($chiuso != -1)
        return $this->where('chiuso', $chiuso);
    }

    public function order($order)
    {
      $by = !empty($order['by']) ? $order['by'] : 'id';
      $sort = !empty($order['sort']) ? $order['sort'] : 'desc';

      return $this->orderBy($by, $sort);
    }
}
