<?php

namespace Modules\Filters\Admin\Profile;

use EloquentFilter\ModelFilter;

class GruppoFilter extends ModelFilter
{
    public function gruppo($gruppo)
    {
        return $this->where('nome', 'LIKE', "%$gruppo%");
    }

    public function area($area)
    {
      if($area > 0)
        return $this->where('area_id', $area);
    }
}
