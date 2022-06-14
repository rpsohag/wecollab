<?php

namespace Modules\Filters\Admin\Commerciale;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Facades\DB;

class SimAziendaliFilter extends ModelFilter
{
    public function operatore($operatore)
    {
      if(!empty($operatore))
        return $this->where('operatore', 'LIKE', '%' . $operatore . '%');
    }

    public function telefono($telefono)
    {
      if(!empty($telefono))
        return $this->where('telefono', 'LIKE', '%' . $telefono . '%');
    }

    public function iccid($iccid)
    {
      if(!empty($iccid))
        return $this->where('iccid', 'LIKE', '%' . $iccid . '%');
    }

    public function profilo($profilo)
    {
      if(!empty($profilo))
        return $this->where('profilo', 'LIKE', '%' . $profilo . '%');
    }

    public function assegnatario($assegnatario)
    {
      if($assegnatario > 0)
      {
        return $this->whereHas('applicato', function($q) use ($assegnatario) {
            $q->where('assegnatario', $assegnatario);
        });
      }
    }

    public function numerocontratto($numero_contratto)
    {
      if($numero_contratto > 0)
        return $this->where('numero_contratto', $numero_contratto);
    }

    public function tiposim($tipo_sim)
    {
        return $this->where('tipo_sim', $tipo_sim);
    }

    public function order($order)
    {
      $by = !empty($order['by']) ? $order['by'] : 'id';
      $sort = !empty($order['sort']) ? $order['sort'] : 'desc';

      switch($by)
      {
        case 'assegnatario':
          return $this->join('users', 'assegnatario', '=', 'users.id')
                      ->orderBy(DB::raw("CONCAT(first_name, ' ', last_name)"), $sort);
          break;

        case 'tipo_sim':
          $stati = "CASE";
          foreach(config('commerciale.simaziendali.tipi') as $key => $stato)
          {
            $stati .= " WHEN tipo_sim = $key THEN '$stato' ";
          }
          $stati .= "END as tiposim_value, commerciale__simaziendali.*";

          return $this->select(DB::raw($stati))->orderBy('tiposim_value', $sort);
          break;
      }

      return $this->orderBy($by, $sort);
    }
}
