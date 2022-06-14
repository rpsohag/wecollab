<?php

namespace Modules\Filters\Admin\Amministrazione;

use EloquentFilter\ModelFilter;
use DB;
use Modules\Amministrazione\Entities\BeneStrumentale;
use Modules\Amministrazione\Entities\Clienti;

class BeniStrumentaliFilter extends ModelFilter
{
    public function assegnatarioIdFilter($assegnatario_id_filter)
    {
      if($assegnatario_id_filter != 0)
        return $this->where('user_id', $assegnatario_id_filter);
    }

    public function tipologiaFilter($tipologia_filter)
    {
      if($tipologia_filter != 0)
        return $this->where('tipologia', $tipologia_filter);
    }

    public function marcaFilter($marca_filter)
    {
        return $this->where('marca', 'LIKE', "%$marca_filter%");
    }

    public function modelloFilter($modello_filter)
    {
        return $this->where('modello', 'LIKE', "%$modello_filter%");
    }

    public function serialNumberFilter($serial_number_filter)
    {
        return $this->where('serial_number', $serial_number_filter);
    }

    public function imeiFilter($imei_filter)
    {
        return $this->where('imei', $imei_filter);
    }

    public function noteFilter($note_filter)
    {
        return $this->where('note', 'LIKE', "%$note_filter%");
    }

    public function dataAssegnazioneFilter($data_assegnazione_filter)
    {
        return $this->whereDate('data_assegnazione', '=', set_date_ita($data_assegnazione_filter));
    }

    public function order($order)
    {
      $by = !empty($order['by']) ? $order['by'] : 'id';
      $sort = !empty($order['sort']) ? $order['sort'] : 'desc';

      switch($by)
      {
        case 'user_id':
          return $this->join('users', 'user_id', '=', 'users.id')
                      ->orderBy(DB::raw("CONCAT(first_name, ' ', last_name)"), $sort);
          break;
      }

      return $this->orderBy($by, $sort);
    }
}
