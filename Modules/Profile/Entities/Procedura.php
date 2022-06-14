<?php

namespace Modules\Profile\Entities;

//use Astrotomic\Translatable\Translatable;
//
use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;


class Procedura extends Model
{
    // use Translatable;
    use Filterable;

    protected $table = 'profile__procedure';
    //public $translatedAttributes = [];
    protected $fillable = [
        'titolo',
        'created_at',
        'updated_at'
    ];

    public static function getRules()
    {
        return [
            'titolo' => 'required'
        ];
    }

    public function aree()
    {
        return $this->hasMany('Modules\Profile\Entities\Area');
    }

}



























