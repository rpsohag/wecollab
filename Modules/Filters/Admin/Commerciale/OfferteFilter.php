<?php

namespace Modules\Filters\Admin\Commerciale;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OfferteFilter extends ModelFilter
{
    public function allegati($allegato)
    {
        switch($allegato)
        {
            case 1: // Senza Determina
                return $this->whereHas('cliente', function($q) use ($allegato)
                            {
                                return $q->where('tipologia', "pubblico");
                            })
                            ->whereNull('oda_determina_ids');
                break;

            case 2: // Senza ODA
                $this->whereHas('cliente', function($q) use ($allegato)
                        {
                            return $q->where('tipologia', "privato");
                        })
                        ->whereNull('oda_determina_ids');
                break;

            case 3: // Senza Offerta definitiva
                return $this->whereNull('offerta_definitiva_id');
                break;

            case 4: // Con Determina
                return $this->whereHas('cliente', function($q) use ($allegato)
                            {
                                return $q->where('tipologia', "pubblico");
                            })
                            ->whereNotNull('oda_determina_ids');
                break;

            case 5: // Con ODA
                $this->whereHas('cliente', function($q) use ($allegato)
                        {
                            return $q->where('tipologia', "privato");
                        })
                        ->whereNotNull('oda_determina_ids');
                break;

            case 6: //Con  Offerta definitiva
                return $this->whereNotNull('offerta_definitiva_id');
                break;
        }
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

    public function commerciale($commerciale)
    {
      if($commerciale > 0)
        return $this->whereRelation('analisi_vendita', 'commerciale_id', $commerciale);
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

    public function codice($codice)
    {
      if($codice != "")
      {
        return $this->where(DB::raw("CONCAT(anno, '-', numero)"), 'LIKE', "%$codice%");
      }
    }

    public function stato($stato)
    {
      if($stato >= 0)
         return $this->where('stato' , $stato);
      elseif($stato == '-1')
        return $this->bozze();
    }

    public function fatturata($fatturata)
    {
      if($fatturata != -1)
      {
         return $this->where('fatturata' , $fatturata);
      }
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

        case 'cliente':
          return $this->join('amministrazione__clienti', 'cliente_id', '=', 'amministrazione__clienti.id')
                      ->orderBy('ragione_sociale', $sort);
          break;

        case 'stato':
          $stati = "CASE";
          foreach(config('commerciale.offerte.stati') as $key => $stato)
          {
            $stati .= " WHEN stato = $key THEN '$stato' ";
          }
          $stati .= "END as stato_value, commerciale__offerte.*";

          return $this->select(DB::raw($stati))->orderBy('stato_value', $sort);
          break;
      }

      return $this->orderBy($by, $sort);
    }
}
