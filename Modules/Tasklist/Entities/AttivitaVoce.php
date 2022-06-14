<?php

namespace Modules\Tasklist\Entities;

// use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\Sentinel\User;

class AttivitaVoce extends Model
{
    // use Translatable;

    protected $table = 'tasklist__attivita_voci';
    // public $translatedAttributes = [];

    protected $fillable = [
        'id',
        'descrizione',
        'durata_valore',
        'durata_tipo',
        'data_inizio',
        'data_fine',
        'priorita',
        'stato',
        'users',
        'percentuale_completamento'
    ];

    public static function getRules()
    {
        return [
            'descrizione' => 'required',
            'users' => 'required',
            'priorita' => 'required|integer',
            //'durata_valore' => 'integer',
            'stato' => 'required',
        ];
    }

    public function getDataInizioAttribute($date)
    {
        return get_date_ita($date);
    }

    public function getDataFineAttribute($date)
    {
        return get_date_ita($date);
    }

    public function getUsersAttribute($users)
    {
        $users_id = json_decode($users);

        return User::whereIn('id', $users_id)->get();
    }

    public function setDataInizioAttribute($date)
    {
        $this->attributes['data_inizio'] = set_date_ita($date);
    }

    public function setDataFineAttribute($date)
    {
        $this->attributes['data_fine'] = set_date_ita($date);
    }

    public function setUsersAttribute($users)
    {
        $this->attributes['users'] = json_encode($users);
    }

    /**
     * Attivita
     */
    public function attivita()
    {
        return $this->belongsTo('Modules\Tasklist\Entities\Attivita');
    }

    /**
     * Metas
     */
    public function metas()
    {
        return $this->morphToMany('Modules\Wecore\Entities\Meta', 'metagable', 'wecore__metagable');
    }

    /**
     * Files
     */
    public function files()
    {
        return $this->morphToMany('Modules\Wecore\Entities\Meta', 'metagable', 'wecore__metagable')->where('name', 'file')->orderBy('created_at', 'desc');
    }

    public function notes()
    {
        return $this->morphToMany('Modules\Wecore\Entities\Meta', 'metagable', 'wecore__metagable')->where('name', 'note')->orderBy('created_at', 'desc');
    }
}
