<?php

namespace Modules\Profile\Entities;

// use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

use EloquentFilter\Filterable;
use Modules\User\Entities\Sentinel\User;

class FiguraProfessionale extends Model
{
    use Filterable;

    protected $table = 'profile__figureprofessionali';

    protected $fillable = [
        'descrizione',
        'costo_interno',
        'importo_vendita'
    ];

    public static function getRules()
    {
        return [
            'descrizione' => 'required'
        ];
    }

    public function modelFilter()
    {
        return $this->provideFilter(\Modules\Filters\Admin\Profile\FiguraProfessionaleFilter::class);
    }

    public function getCostoInternoAttribute($value)
    {
        return get_currency($value);
    }

    public function setCostoInternoAttribute($value)
    {
        $this->attributes['costo_interno'] = clean_currency($value);
    }

    public function getImportoVenditaAttribute($value)
    {
        return get_currency($value);
    }

    public function setImportoVenditaAttribute($value)
    {
        $this->attributes['importo_vendita'] = clean_currency($value);
    }
    
}
