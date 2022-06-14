<?php

namespace Modules\Filters\Admin\Commerciale;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Facades\DB;

class ContrattiFilter extends ModelFilter
{
    public function titolodescrittivo($titolo)
    {
        return $this->where('titolo_descrittivo', 'LIKE', "%$titolo%");
    }

    public function cliente($cliente)
    {
        if($cliente > 0)
        return $this->where('cliente_id' , $cliente);
    }

    public function chiuso($chiuso)
    {
      if($chiuso != -1)
      {
         return $this->where('chiuso' , $chiuso);
      }
    }

}
