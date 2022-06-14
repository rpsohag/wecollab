<?php

namespace Modules\Assistenza\Entities;

// use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;
use Auth;
use Modules\User\Entities\Sentinel\User;
use Modules\Amministrazione\Entities\Clienti;

class AmbienteConversioni extends Model
{

    use Filterable;

    protected $table = 'assistenza__ambienti_conversioni';

    protected $fillable = [
      'nome',
      'cliente_id',
      'user_admin',
      'password_admin',
      'azienda',
      'user_adm',
      'password_adm',
      'dettaglio_conversioni',
      'chiuso',
      'created_user_id',
      'updated_user_id',
    ];

    public static function getRules()
    {
        return [
            'nome' => 'required',
            'cliente_id' => 'integer',
            'created_user_id' => 'integer',
            'updated_user_id' => 'integer',
        ];
    }

    public function modelFilter()
    {
        return $this->provideFilter(\Modules\Filters\Admin\Assistenza\AmbientiConversioniFilter::class);
    }

    public function created_user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'created_user_id');
    }

    public function updated_user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'updated_user_id');
    }

    public function cliente()
    {
        return $this->belongsTo('Modules\Amministrazione\Entities\Clienti', 'cliente_id' );
    }
}
