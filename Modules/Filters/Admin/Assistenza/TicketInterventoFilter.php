<?php

namespace Modules\Filters\Admin\Assistenza;

use EloquentFilter\ModelFilter;
use DB;

class TicketInterventoFilter extends ModelFilter
{

      public function dataIntervento($data)
        {
            return $this->where('data_intervento', set_date_ita($data));
        }

      public function utente($utente_id)
      {

        if($utente_id > 0)
          return $this->where('assistenza__ticketinterventi.created_user_id', $utente_id);
      }

      public function cliente($cliente_id)
      {
        if($cliente_id > 0)
          return $this->where('cliente_id', $cliente_id);
      }

      public function ordinativo($ordinativo_id)
      {
        if($ordinativo_id > 0)
          return $this->where('ordinativo_id', $ordinativo_id);
      }

      public function area($area)
      {
          return $this->whereIn('area_di_intervento_id', $area);
      }

      public function attivita($attivita_id)
      {
        if($attivita_id > 0)
          return $this->where('gruppo_id', $attivita_id);
      }

      public function nota($nota)
      {
          return $this->where('materiale_consegnato', 'LIKE', "%$nota%")
                      ->orWhere('descrizione_ticket', 'LIKE', "%$nota%")
                      ->orWhereHas('voci', function ($query) use($nota) {
                        $query->where('descrizione', 'LIKE', "%$nota%");
                     });
      }

    public function order($order)
    {
      $by = !empty($order['by']) ? $order['by'] : 'id';
      $sort = !empty($order['sort']) ? $order['sort'] : 'desc';

      switch($by)
      {
        case 'data_intervento':
          return $this->select('assistenza__ticketinterventi.*')
                      ->leftJoin('assistenza__ticketinterventi_voci', 'assistenza__ticketinterventi.id', '=', 'assistenza__ticketinterventi_voci.ticket_id')
                      ->orderBy('assistenza__ticketinterventi_voci.data_intervento', $sort);
          break;

        case 'utente': //manca una select?
          return $this->select('assistenza__ticketinterventi.*')
                      ->leftJoin('users', 'updated_user_id', '=', 'users.id')
                      ->orderBy(DB::raw("CONCAT(first_name, ' ', last_name)"), $sort);
          break;

        case 'cliente':
          return $this->select('assistenza__ticketinterventi.*')
                      ->leftJoin('amministrazione__clienti', 'cliente_id', '=', 'amministrazione__clienti.id')
                      ->orderBy('ragione_sociale', $sort);
          break;

        case 'ordinativo':
          return $this->select('assistenza__ticketinterventi.*')
                      ->leftJoin('commerciale__ordinativi', 'ordinativo_id', '=', 'commerciale__ordinativi.id')
                      ->orderBy('oggetto', $sort);
          break;

        case 'area':
          return $this->select('assistenza__ticketinterventi.*')
                      ->leftJoin('profile__gruppi', 'gruppo_id', '=', 'profile__gruppi.id')
                      ->orderBy('nome', $sort);
          break;

        case 'settore':
          $stati = "CASE";
          foreach(config('assistenza.ticket_intervento.settori') as $key => $stato)
          {
            $stati .= " WHEN settore_id = $key THEN '$stato' ";
          }
          $stati .= "END as settore_value, assistenza__ticketinterventi.*";

          return $this->select(DB::raw($stati))->orderBy('settore_value', $sort);
          break;
      }

      return $this->orderBy($by, $sort);
    }
}
