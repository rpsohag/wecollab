<?php

namespace Modules\Profile\Entities;

//use Astrotomic\Translatable\Translatable;
//
use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;


class Gruppo extends Model
{
    // use Translatable;
    use Filterable;

    protected $table = 'profile__gruppi';
    //public $translatedAttributes = [];
    protected $fillable = [
        'nome',
        'area_id',
        'notifiche',
        'created_at',
        'updated_at',
    ];

    public static function getRules()
    {
        return [
            'nome' => 'required',
        ];
    }

    public function users()
    {
        return $this->belongsToMany('Modules\User\Entities\Sentinel\User', 'profile__gruppo_user');
    }

    public function area()
    {
        return $this->hasOne('Modules\Profile\Entities\Area', 'area_id');
    }

    public function timesheets()
    {
        return $this->hasMany('Modules\Tasklist\Entities\Timesheet');
    }

    public function created_user()
    { 
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'created_user_id');
    }

    public function updated_user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'updated_user_id');
    }

    public function modelFilter()
    {
        return $this->provideFilter(\Modules\Filters\Admin\Profile\GruppoFilter::class);
    }

}
