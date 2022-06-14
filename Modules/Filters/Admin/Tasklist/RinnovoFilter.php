<?php

namespace Modules\Filters\Admin\Tasklist;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RinnovoFilter extends ModelFilter
{
    public function titolo($titolo)
    {

        return $this->where('titolo','LIKE', "%$titolo%");
    }

    public function cliente($cliente)
    {
        if($cliente != -1)
            return $this->where('cliente_id', $cliente);
    }

    public function descrizione($descrizione)
    {

        return $this->where('descrizione','LIKE', "%$descrizione%");
    }

    public function range($date)
    {
      if($date)
      {
        $date = explode(' - ', $date);
        $start_array = explode('/', $date[0]);
        $end_array = explode('/', $date[1]);
        $start = Carbon::parse($start_array[2].'-'.$start_array[1].'-'.$start_array[0])->toDateString();
        $end = Carbon::parse($end_array[2].'-'.$end_array[1].'-'.$end_array[0])->toDateString();
        return $this->whereBetween('created_at', [$start, $end]);
      }
    }

    public function tiporinnovo($tipo_rinnovo)
    {
        if($tipo_rinnovo != -1)
            return $this->where('tipo', $tipo_rinnovo);
    }

    public function datarinnovo($data_rinnovo)
    {
        $data = set_date_ita($data_rinnovo);
        return $this->whereDate('data', $data);
    }

    public function order($order)
    {
      $by = !empty($order['by']) ? $order['by'] : 'id';
      $sort = !empty($order['sort']) ? $order['sort'] : 'desc';

      switch($by)
      {
        case 'cliente':
          return $this->join('amministrazione__clienti', 'cliente_id', '=', 'amministrazione__clienti.id')
                      ->orderBy('ragione_sociale', $sort);
          break;

        case 'tipo':
          $stati = "CASE";
          foreach(config('tasklist.rinnovi.tipi') as $key => $stato)
          {
            $stati .= " WHEN tipo = $key THEN '$stato' ";
          }
          $stati .= "END as tipo_value, tasklist__rinnovi.*";

          return $this->select(DB::raw($stati))->orderBy('tipo_value', $sort);
          break;
      }

      return $this->orderBy($by, $sort);
    }
}
