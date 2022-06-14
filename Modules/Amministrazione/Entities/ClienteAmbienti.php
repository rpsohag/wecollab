<?php

namespace Modules\Amministrazione\Entities;

//use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class ClienteAmbienti extends Model
{
    //use Translatable;

    protected $table = 'amministrazione__clienti_ambienti';
    //public $translatedAttributes = [];
    protected $fillable = [
        'cliente_id',
        'admin',
        'password_admin',
        'adm',
        'password_adm',
        'n_db',
        'ambiente',
        'api_sso'
    ];

    public static function getRules()
    {
        return [
            //'admin' => 'required',
            //'n_db' => 'required',
            //'adm' => 'required',
            //'ambiente' => 'required',
            //'api_sso' => 'required'
        ];
    }

    public function cliente()
    {
        return $this->belongsTo('Modules\Amministrazione\Entities\Clienti', 'cliente_id');
    }
}
