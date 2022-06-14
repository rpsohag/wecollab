<?php

namespace Modules\Filters\Admin\Commerciale;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Facades\DB;

class OrdinativoFilter extends ModelFilter
{
    public function rinnovo($sn)
    {
        switch($sn)
        {
            case 1: // Si
                return $this->has('rinnovo');
                break;

            case 2: // No
                return $this->doesntHave('rinnovo');
                break;

            default:
                break;
        }
    }

    public function cliente($cliente_id)
    {
        if($cliente_id > 0)
        {
            return $this->where('commerciale__ordinativi.cliente_id', $cliente_id);
        }
    }

    public function offerta($codice)
    {
        if($codice != '-1')
        {
            return $this->whereHas('offerte', function($q) use ($codice) {
                $q->whereId($codice);
            });
        }
    }

    public function codice($codice)
    {
      if($codice != "")
      {
        return $this->where(DB::raw("CONCAT(commerciale__ordinativi.anno, '-', commerciale__ordinativi.numero)"), 'LIKE', "%$codice%");
      }
    }

    public function oggetto($oggetto)
    {
            return $this->where('commerciale__ordinativi.oggetto','LIKE',"%$oggetto%");
    }

    public function datainizio1($data_inizio_1)
    {
        $data = set_date_ita($data_inizio_1);
        return $this->whereDate('data_inizio', '>=', $data);
    }

    public function datainizio2($data_inizio_2)
    {
        $data = set_date_ita($data_inizio_2);
        return $this->whereDate('data_inizio', '<=', $data);
    }

    public function datafine1($data_fine_1)
    {
        $data = set_date_ita($data_fine_1);
        return $this->whereDate('data_fine', '>=', $data);
    }

    public function datafine2($data_fine_2)
    {
        $data = set_date_ita($data_fine_2);
        return $this->whereDate('data_fine', '<=', $data);
    }

    public function order($order)
    {
      $by = !empty($order['by']) ? $order['by'] : 'id';
      $sort = !empty($order['sort']) ? $order['sort'] : 'desc';

      switch($by)
      {
        case 'codice':
            return $this->orderBy('anno', $sort)
                        ->orderBy('numero', $sort);
            break;

        case 'codice_offerta':
          return $this;
          break;

        case 'cliente':
          return $this->join('amministrazione__clienti', 'cliente_id', '=', 'amministrazione__clienti.id')
                      ->orderBy('ragione_sociale', $sort);
          break;
      }

      return $this->orderBy($by, $sort);
    }
}
