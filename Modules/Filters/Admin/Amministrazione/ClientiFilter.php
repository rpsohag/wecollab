<?php

namespace Modules\Filters\Admin\Amministrazione;

use EloquentFilter\ModelFilter;

class ClientiFilter extends ModelFilter
{
    public function ragioneSociale($ragione_sociale)
    {
        if(!empty($ragione_sociale))
            return $this->where('ragione_sociale', 'LIKE', "%$ragione_sociale%");
    }

    public function pIva($p_iva)
    {
        if(!empty($p_iva))
            return $this->where('p_iva', 'LIKE', "%$p_iva%");
    }

    public function tipo($tipo)
    {
        if(!empty($tipo))
            return $this->where('tipo', $tipo);
    }

    public function tipologia($tipologia)
    {
        if(!empty($tipologia))
            return $this->where('tipologia', "$tipologia");
    }

    public function aree($aree)
    {
        foreach ($aree as $key => $area)
            $this->orWhere('aree', 'LIKE', "%$area%");

        return $this;
    }

    public function order($order)
    {
      $by = !empty($order['by']) ? $order['by'] : 'id';
      $sort = !empty($order['sort']) ? $order['sort'] : 'desc';

      return $this->orderBy($by, $sort);
    }
}
