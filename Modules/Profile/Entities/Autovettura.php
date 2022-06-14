<?php
namespace Modules\Profile\Entities;

use Illuminate\Database\Eloquent\Model;

class Autovettura extends Model
{
    protected $table = 'profile__autovetture';

    protected $fillable = [
        'id',
        'targa',
		'modello',
		'costo_km',
		'user_id'
	];

	public static function getRules()
	{
		return [
            'targa' => 'required',
            'modello' => 'required',
            'costo_km' => 'required'
        ];
	}

	public function setCostokmAttribute($value)
    {
        $this->attributes['costo_km'] = clean_currency($value);
    }

	public function getCostoKmAttribute($value)
    {
        return get_currency($value);
    }

	public function getFullNameAttribute()
	{
		return $this->targa . ' - ' . $this->modello;
	}

}