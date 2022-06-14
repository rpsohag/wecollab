<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VociKm extends Model
{
    protected $table = 'km__voci';

    protected $fillable = [
		'id',
		'data',
		'richiesta_id',
		'partenza',
		'arrivo',
		'km',
		'ar',
		'attivita_id',
		'ordinativo_id',
		'note'
	];

	public function getDataAttribute($date)
    {
        return get_date_ita($date);
    }

	public function setDataAttribute($date)
    {
        $this->attributes['data'] = set_date_ita($date);
    }


}