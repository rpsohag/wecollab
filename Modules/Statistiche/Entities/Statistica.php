<?php

namespace Modules\Statistiche\Entities;

use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;

class Statistica extends Model
{
    use Filterable;

    public function modelFilter()
    {
        return $this->provideFilter(\Modules\Filters\Admin\Statistiche\ReportsFilter::class);
    }

}
