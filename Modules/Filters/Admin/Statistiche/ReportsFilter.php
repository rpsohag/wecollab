<?php

namespace Modules\Filters\Admin\Statistiche;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Facades\DB;

class ReportsFilter extends ModelFilter
{
  public function dataInizio($data_inizio)
  {
      return $this->whereDate('created_at', '<=', set_date_ita($data_inizio));
  }

  public function dataFine($data_fine)
  {
      return $this->whereDate('created_at', '>=', set_date_ita($data_fine));
  }

  public function utente($utente)
  {
    if($utente > 0)
      return $this->where('causer_id', $utente);
  }

}
