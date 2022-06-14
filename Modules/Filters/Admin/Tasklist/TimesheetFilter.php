<?php

namespace Modules\Filters\Admin\Tasklist;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Facades\DB;

class TimesheetFilter extends ModelFilter
{
  public function nota($nota)
  {
    $this->where('nota', 'LIKE', "%$nota%");
  }

  public function cliente($cliente)
  {
    if($cliente > 0)
      return $this->where('cliente_id', $cliente);
  }

  public function tipologia($tipologia)
  {
    if($tipologia != -1)
        return $this->where('tipologia', $tipologia);
  } 

  public function area($area)
  {
    if($area > 0)
      return $this->whereIn('area_id', $area);
  }

  public function dataChiusura($data_chiusura)
  {
      return $this->whereDate('dataora_inizio', '<=', set_date_ita($data_chiusura));
  }

  public function ordinativo($ordinativo)
  {
    if($ordinativo > 0)
      return $this->where('ordinativo_id', $ordinativo);
  }

  public function dataApertura($data_apertura)
  {
      return $this->whereDate('dataora_inizio', '>=', set_date_ita($data_apertura));
  }

  public function utente($utente)
  {
    if($utente > 0)
      return $this->where('created_user_id', $utente);
  }

  /* public function area($area)
  {
    if($area > 0)
      return $this->where('area_id', $area);
  }

  public function procedura($procedura)
  {
    if($procedura > 0)
      return $this->where('procedura_id', $procedura);
  }

  public function gruppo($gruppo)
  {
    if($gruppo > 0)
      return $this->where('gruppo_id', $gruppo);
  } */

  public function order($order)
  {
    $by = !empty($order['by']) ? $order['by'] : 'id';
    $sort = !empty($order['sort']) ? $order['sort'] : 'desc';

    switch($by){

      case 'utente':
        return $this->select('tasklist__timesheets.*')
                    ->leftJoin('users', 'created_user_id', '=', 'users.id')
                    ->orderBy('first_name', $sort);
        break;

      case 'cliente':
        return $this->select('tasklist__timesheets.*')
                    ->leftJoin('amministrazione__clienti', 'cliente_id', '=', 'amministrazione__clienti.id')
                    ->orderBy('ragione_sociale', $sort);
        break;

      case 'area':
        return $this->select('tasklist__timesheets.*')
                    ->leftJoin('profile__aree', 'area_id', '=', 'profile__aree.id')
                    ->orderBy('titolo', $sort);
        break;

      case 'procedura':
        return $this->select('tasklist__timesheets.*')
                    ->leftJoin('profile__procedure', 'procedura_id', '=', 'profile__procedure.id')
                    ->orderBy('titolo', $sort);
        break;

      case 'gruppo':
        return $this->select('tasklist__timesheets.*')
                    ->leftJoin('profile__gruppi', 'gruppo_id', '=', 'profile__gruppi.id')
                    ->orderBy('nome', $sort);
        break;

      case 'attivita':
        return $this->select('tasklist__timesheets.*')
                    ->leftJoin('tasklist__attivita', 'attivita_id', '=', 'tasklist__attivita.id')
                    ->orderBy('oggetto', $sort);
        break;

      case 'ordinativo':
        return $this->select('tasklist__timesheets.*')
                    ->leftJoin('commerciale__ordinativi', 'ordinativo_id', '=', 'commerciale__ordinativi.id')
                    ->orderBy('oggetto', $sort);
        break;

    }

    return $this->orderBy($by, $sort);
  }
}
