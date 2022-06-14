<?php

namespace Modules\Filters\Admin\Assistenza;

use EloquentFilter\ModelFilter;
use DB;
use Modules\Assistenza\Entities\RichiesteIntervento;
use Modules\Assistenza\Entities\RichiesteInterventoAzione;
use Modules\Amministrazione\Entities\Clienti;
use Carbon\Carbon;

class RichiesteInterventoFilter extends ModelFilter
{
    public function stato($stato)
    {
      $stato = (int)$stato;
      // 0 TUTTI 1 SOSPESI & APERTI 2 APERTI 3 SOSPESI 4 CHIUSE
      return $this->when($stato == 0, function ($q) {
                return $q;
              })
            ->when($stato == 1, function ($q) {
              $q->where(function($q) {
                $q->whereHas('azioni', function ($azioni) {
                  $azioni->where('tipo', '<>', 3)
                      ->whereIn('id', function ($q) {
                          $q->selectRaw('max(id)')
                              ->from('assistenza__richiesteinterventi_azioni')
                              ->whereColumn('ticket_id', 'assistenza__richiesteinterventi.id');
                      });
                });
                $q->orWhereDoesntHave('azioni');
              });
            })
            ->when($stato == 2, function ($q) {
              $q->where(function($q) {
                $q->whereHas('azioni', function ($azioni) {
                  $azioni->whereNotIn('tipo', [3, 4])
                      ->whereIn('id', function ($q) {
                          $q->selectRaw('max(id)')
                              ->from('assistenza__richiesteinterventi_azioni')
                              ->whereColumn('ticket_id', 'assistenza__richiesteinterventi.id');
                      });
                });
                $q->orWhereDoesntHave('azioni');
              });
            })
            ->when($stato == 3, function ($q) {
              $q->whereHas('azioni', function ($azioni) {
                $azioni->where('tipo', 4)
                    ->whereIn('id', function ($q) {
                        $q->selectRaw('max(id)')
                            ->from('assistenza__richiesteinterventi_azioni')
                            ->whereColumn('ticket_id', 'assistenza__richiesteinterventi.id');
                    });
              });
            })
            ->when($stato == 4, function ($q) {
              $q->whereHas('azioni', function ($azioni) {
                $azioni->where('tipo', 3)
                    ->whereIn('id', function ($q) {
                        $q->selectRaw('max(id)')
                            ->from('assistenza__richiesteinterventi_azioni')
                            ->whereColumn('ticket_id', 'assistenza__richiesteinterventi.id');
                    });
              });
            });
    }

    public function dataAperturaStatistiche($data)
    {
        //dd(set_date_ita($data));
        return $this->whereDate('created_at','>=', set_date_ita($data));
    }

    public function dataChiusuraStatistiche($data1)
    {
        //dd(set_date_ita($data1));
        return $this->whereDate('updated_at','<=', set_date_ita($data1));
    }

    public function dataApertura($data_apertura)
    {
        //dd(set_date_ita($data));
        return $this->whereDate('created_at', '>=', set_date_ita($data_apertura));
    }

    public function dataChiusura($data_chiusura)
    {
        $ids_richieste_chiuse = RichiesteInterventoAzione::where('tipo', '3')->whereDate('created_at', '<=', set_date_ita($data_chiusura))->pluck('ticket_id')->toArray();

        return $this->whereIn('id', $ids_richieste_chiuse);
    }

    public function dataInizio($data_inizio)
    {
        return $this->whereDate('created_at', '>=', set_date_ita($data_inizio));
    }

    public function dataFine($data_fine)
    {
        return $this->whereDate('created_at', '<=', set_date_ita($data_fine));
    }

    public function rangeApertura($range_apertura)
    {
      if($range_apertura)
      {
        $date = explode(' - ', $range_apertura);
        $start_array = explode('/', $date[0]);
        $end_array = explode('/', $date[1]);
        $start = Carbon::parse($start_array[2].'-'.$start_array[1].'-'.$start_array[0])->toDateString();
        $end = Carbon::parse($end_array[2].'-'.$end_array[1].'-'.$end_array[0])->toDateString();
        return $this->whereBetween('created_at', [$start, $end]);
      }
    }

    public function rangeChiusura($range_chiusura)
    {
      if($range_chiusura)
      {
        $date = explode(' - ', $range_chiusura);
        $start_array = explode('/', $date[0]);
        $end_array = explode('/', $date[1]);
        $start = Carbon::parse($start_array[2].'-'.$start_array[1].'-'.$start_array[0])->toDateString();
        $end = Carbon::parse($end_array[2].'-'.$end_array[1].'-'.$end_array[0])->toDateString();

        $ids_richieste_chiuse = RichiesteInterventoAzione::where('tipo', '3')->whereBetween('created_at', [$start, $end])->pluck('ticket_id')->toArray();
        return $this->whereIn('id', $ids_richieste_chiuse);
      }
    }

    public function cliente($cliente_id)
    {
      if($cliente_id > 0)
        return $this->where('cliente_id', $cliente_id);
    }

    public function area($area_id)
    {
      if($area_id > 0)
        $this->where('area_id', $area_id);
    }

    public function gruppo($gruppo)
    {
        if($gruppo != 0)
            return $this->where('gruppo_id', $gruppo);
    }

    public function ordinativo($ordinativo)
    {
        if($ordinativo != 0)
            return $this->where('ordinativo_id', $ordinativo);
    }

    public function lavorato($value)
    {
      if($value != 0)
      {
        return $this->whereHas('azioni', function ($q) use ($value) {
            $q->where('created_user_id', $value);
        });
      }
    }

    public function destinatario($destinatari)
    {
      if($destinatari > 0)
      {
        return $this->whereHas('destinatari', function ($q) use ($destinatari) {
            $q->whereIn('user_id', [$destinatari]);
        });
      }
    }

    public function richiedente($richiedente)
    {
        return $this->where('richiedente', 'LIKE', "%$richiedente%");
    }

    public function oggetto($oggetto)
    {
        return $this->where('oggetto', 'LIKE', "%$oggetto%");
    }

    public function descrizione($descrizione)
    {
        return $this->where('descrizione_richiesta', 'LIKE', "%$descrizione%");
    }

    public function codice($codice)
    {
      if($codice != "")
      {
      return $this->where(DB::raw("CONCAT(YEAR(created_at), '/', numero)"), 'LIKE', "%$codice%");
      }
    }

    public function order($order)
    {
      $by = !empty($order['by']) ? $order['by'] : 'id';
      $sort = !empty($order['sort']) ? $order['sort'] : 'desc';

      switch($by){

        case 'cliente':
          return $this->select('assistenza__richiesteinterventi.*')
                      ->leftJoin('amministrazione__clienti', 'cliente_id', '=', 'amministrazione__clienti.id')
                      ->orderBy('ragione_sociale', $sort);
          break;

        case 'area':
          return $this->select('assistenza__richiesteinterventi.*')
                      ->leftJoin('profile__aree', 'area_id', '=', 'profile__aree.id')
                      ->orderBy('titolo', $sort);
          break;

        case 'data_chiusura':
          return $this->select('assistenza__richiesteinterventi.*')
                  		->leftJoin('assistenza__richiesteinterventi_azioni', function($join) {
                  			$join->on('assistenza__richiesteinterventi_azioni.ticket_id', '=', 'assistenza__richiesteinterventi.id')
                          ->where('assistenza__richiesteinterventi_azioni.tipo', 3);
                  			})
                  		->orderBy('assistenza__richiesteinterventi_azioni.created_at', $sort);
          break;
      }

      return $this->orderBy($by, $sort);
    }
}
