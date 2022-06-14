<?php

namespace Modules\User\Entities;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Modules\User\Entities\Sentinel\User;
use Modules\User\Entities\Approvazioni;
use Modules\User\Entities\VociTrasferte;

class Richieste extends Model
{
	use Filterable;

    protected $table = 'users__richieste';

	protected $casts = ['meta' => 'array'];

    protected $fillable = [
		'id', 
		'user_id', 
		'tipo',
		'stato',
		'meta',
		'from',
		'to',
		'note',
		'anno',
		'mese',
		'targa',
		'modello',
		'costo_km',
		'totale',
		'draft'
	];

	public function modelFilter()
    {
        return $this->provideFilter(\Modules\Filters\Admin\Users\RichiesteFilter::class);
    }

	public function user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'user_id');
    }


	public function getCostoKmAttribute($value)
    {
        return get_currency($value);
    }

	public function getTotaleAttribute($value)
    {
        return get_currency($value);
    }

	public function approvazioni($id)
    {
        return $this->hasOne(Approvazioni::class, 'richiesta_id')->whereApprovatoreId($id)->first();
    }

	public function vociTrasferte()
	{
		return $this->hasMany(VociTrasferte::class,'richiesta_id');
	}

	public function vociKm()
	{
		return $this->hasMany(VociKm::class,'richiesta_id');
	}

	

}