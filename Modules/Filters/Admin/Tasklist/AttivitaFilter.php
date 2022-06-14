<?php

namespace Modules\Filters\Admin\Tasklist;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Facades\DB;
use Modules\Tasklist\Entities\Attivita;
use Auth;

class AttivitaFilter extends ModelFilter
{
    public function cerca($cerca)
    {
        return $this->where('oggetto', 'LIKE', "%$cerca%")
                    ->orWhereHas('notes', function ($q) use ($cerca) {
                        $q->where('value', 'LIKE', "%$cerca%");
                    })
                    ->orWhereHas('richiedente', function ($q) use ($cerca) {
                        $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%$cerca%");
                    });;
    }

    public function azienda($azienda)
    {
        if(!empty($azienda))
            return $this->where('azienda', config('wecore.aziende.'.$azienda));
    }

    public function ordinativo($ordinativo)
    {
        return $this->whereIn('ordinativo_id', $ordinativo);
    }
    
    public function oggetto($oggetto)
    {

        return $this->where('oggetto','LIKE', "%$oggetto%");
    }

    public function richiedente($richiedente)
    {
        if($richiedente != -1)
            return $this->where('richiedente_id', $richiedente);
    }

    public function assegnatari($assegnatari)
    {
          return $this->whereHas('users', function ($q) use ($assegnatari) {
              $q->whereIn('users.id', $assegnatari);
          });
    }

    public function priorita($priorita)
    {
        if($priorita != -1)
            return $this->where('priorita', $priorita);
    }

    public function procedura($procedura)
    {
        return $this->whereIn('procedura_id', $procedura);
    }

    public function area($area)
    {
        return $this->whereIn('area_id', $area);
    }

    public function gruppo($gruppo)
    {
        return $this->whereIn('gruppo_id', $gruppo);
    }

    public function datainizio($data_inizio)
    {
        $data = set_date_ita($data_inizio);
        return $this->whereDate('data_inizio', '>=', $data);
    }

    public function datafine($data_fine)
    {
        $data = set_date_ita($data_fine);
        return $this->whereDate('data_fine', '>=', $data);
    }

    public function datachiusura($data_chiusura)
    {
        $data = set_date_ita($data_chiusura);
        return $this->whereDate('data_chiusura', '>=', $data);
    }

    public function stato($stato)
    {
        if(!in_array(-1, $stato))
            return $this->whereIn('stato', $stato);
    }

    public function cliente($cliente)
    {
        return $this->whereIn('cliente_id', $cliente);
    }

    public function ordinativoSn($sn)
    {
        if($sn == 1)
          return $this->has('ordinativo');
        elseif($sn == 0)
          return $this->doesnthave('ordinativo');
    }

    public function order($order)
    {
      $by = !empty($order['by']) ? $order['by'] : 'id';
      $sort = !empty($order['sort']) ? $order['sort'] : 'desc';

      switch($by)
      {
        case 'cliente':
            return $this->select('tasklist__attivita.*')
                        ->leftJoin('amministrazione__clienti', 'cliente_id', '=', 'amministrazione__clienti.id')
                        ->orderBy('ragione_sociale', $sort);
            break;

        case 'richiedente':
          return $this->select(DB::raw("CONCAT(first_name, ' ', last_name) as full_name, tasklist__attivita.*"))
                      ->join('users', 'richiedente_id', '=', 'users.id')
                      ->orderBy('full_name', $sort);
          break;

        case 'stato':
          $stati = "CASE";
          foreach(config('tasklist.attivita.stati') as $key => $stato)
          {
            $stati .= " WHEN stato = $key THEN '$stato' ";
          }
          $stati .= "END as stato_value, tasklist__attivita.*";

          return $this->select(DB::raw($stati))->orderBy('stato_value', $sort);
          break;
      }

      return $this->orderBy($by, $sort);
    }
}
