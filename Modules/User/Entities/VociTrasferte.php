<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VociTrasferte extends Model
{
    protected $table = 'trasferte__voci';

    protected $fillable = [
		'id',
		'data',
		'richiesta_id',
		'tipologia',
		'importo',
		'attivita_id',
		'ordinativo_id',
		'note'
	];

	public function getImportoAttribute($value)
    {
        return get_currency($value);
    }

	public function getDataAttribute($date)
    {
        return get_date_ita($date);
    }

	public function setDataAttribute($date)
    {
        $this->attributes['data'] = set_date_ita($date);
    }


}