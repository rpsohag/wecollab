<?php

namespace Modules\Amministrazione\Entities;

use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;
use Auth;
use Modules\User\Entities\Sentinel\User;

class BeneStrumentale extends Model
{

    use Filterable;

    protected $table = 'beni_strumentali';

    protected $fillable = [
      'tipologia',
      'user_id',
      'marca',
      'modello',
      'processore',
      'hdd',
      'memoria',
      'serial_number',
      'imei',
      'note',
      'data_assegnazione'
    ];

    public function modelFilter()
    {
        return $this->provideFilter(\Modules\Filters\Admin\Amministrazione\BeniStrumentaliFilter::class);
    }

    public function setDataAssegnazioneAttribute($data_assegnazione)
    {
        $this->attributes['data_assegnazione'] = set_date_ita($data_assegnazione);
    }

    public function getDataAssegnazioneAttribute($data_assegnazione)
    {
        return get_date_ita($data_assegnazione);
    }


    public function assegnatario()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'user_id');
    }
}
