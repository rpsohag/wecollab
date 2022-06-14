<?php

namespace Modules\Filters\Admin\Commerciale;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FatturazioneFilter extends ModelFilter
{
    public function anno($anno)
    {
        return $this->whereRaw('YEAR(data) = ?', $anno);
    }

    public function oggetto($oggetto)
    {
        return $this->where('oggetto', 'LIKE', "%$oggetto%");
    }

    public function cliente($cliente)
    {
      if($cliente > 0)
        return $this->where('cliente_id' , $cliente);
    }

    public function cig($cig)
    {
      if($cig != "")
      {
        return $this->where('cig' ,$cig);
      }
    }

    public function macrocategoria($macrocategoria)
    {
      if($macrocategoria > 0)
      {
        return $this->where('macrocategoria', $macrocategoria);
      }
    }

    public function nfattura($numero)
    {
        return $this->where('n_fattura', $numero);
    }

    public function fepa($fepa)
    {
      if($fepa != -1)
      {
         return $this->where('fepa' , $fepa);
      }
    }

    public function stato($stato)
    {
        switch($stato)
        {
          case 'pagata':
            return $this->where('pagata', 1);
            break;
          case 'scaduta':
            return $this->whereRaw('DATE(?) > DATE(DATE_ADD(data, INTERVAL n_giorni DAY))', Carbon::now()->toDateString())
                        ->where('pagata', '<>', 1);
            break;
          case 'anticipata':
            return $this->where('anticipata', 1)
                        ->where('pagata', '<>', 1);
            break;
          case 'consegnata':
            return $this->where('consegnata', 1)
                        ->where('pagata', '<>', 1);
            break;
          case 'non_pagata':
            return $this->where('pagata', '<>', 1);
            break;
          case 'non_anticipata':
            return $this->where('anticipata', '<>', 1);
            break;
        }
    }

    public function totalenetto($totale_netto)
    {
        return $this->where('totale_netto', clean_currency($totale_netto));
    }

    public function order($order)
    {
      $by = !empty($order['by']) ? $order['by'] : 'id';
      $sort = !empty($order['sort']) ? $order['sort'] : 'desc';

      switch($by)
      {
        case 'n_fattura':
          return $this->orderBy('id', $sort)
                      ->orderBy('n_fattura', $sort);
          break;

        case 'cliente':
          return $this->join('amministrazione__clienti', 'cliente_id', '=', 'amministrazione__clienti.id')
                      ->orderBy('ragione_sociale', $sort);
          break;
      }

      return $this->orderBy($by, $sort);
    }
}
