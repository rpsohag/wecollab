<?php

namespace Modules\Tasklist\Entities;

use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;

class Rinnovo extends Model
{
    use Filterable;

    protected $table = 'tasklist__rinnovi';
    protected $fillable = [
        'azienda',
        'titolo',
        'descrizione',
        'data',
        // 'tipo',
        'ordinativo_id',
        'cliente_id',
        'created_user_id',
        'updated_user_id',
        'created_at',
        'updated_at'
    ];

    public static function getRules()
    {
        return [
            'titolo' => 'required',
            'data' => 'required',
            'tipo' => 'required|not_in:-1',
            'cliente_id' => 'required|not_in:-1'
        ]; 
    }

    public static function getOrdinativoRules()
    {
        return [
            'rinnovo.titolo' => 'required',
            'rinnovo.data' => 'required',
            //'rinnovo.tipo' => 'required|not_in:-1',
            'rinnovo.cliente_id' => 'required|not_in:-1'
        ];
    }

    public function getDataAttribute($date)
    {
        return get_date_hour_ita($date);
    }

    public function setDataAttribute($date)
    {
        $this->attributes['data'] = set_date_hour_ita($date);
    }

    public function getDataRinnovo()
    {
        switch($this->tipo)
        {
            case 0:
                $d = date('d', strtotime(set_date_hour_ita($this->data)));
                $m = date('m', strtotime(set_date_hour_ita($this->data)));
                $y = date('Y', strtotime(set_date_hour_ita($this->data)));//$y = ($m <= date('m') && ($d < date('d') && $m = date('m'))) ? date('Y', strtotime('+1 years')) : date('Y');
                $hi = date('H:i', strtotime(set_date_hour_ita($this->data)));
                $data = "$d/$m/$y - $hi";
                break;
            case 1:
                $d = date('d', strtotime(set_date_hour_ita($this->data)));
                $m = ($d <= date('d') && ($d < date('d') && $m_o = date('m'))) ? date('m', strtotime('+1 months')) : date('m');
                $y = date('Y', strtotime(set_date_hour_ita($this->data)));
                $hi = date('H:i', strtotime(set_date_hour_ita($this->data)));
                $data = "$d/$m/$y - $hi";
                break;
        }

        return $data;
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

    public function notifiche()
    {
        return $this->hasMany('Modules\Tasklist\Entities\RinnovoNotifica', 'rinnovo_id');
    }

    public function utenti()
    {
        return $this->belongsToMany('Modules\User\Entities\Sentinel\User', 'tasklist__rinnovo_utenti');
    }

    public function ordinativo()
    {
        return $this->belongsTo('Modules\Commerciale\Entities\Ordinativo', 'ordinativo_id');
    }

    public function modelFilter()
    {
        return $this->provideFilter(\Modules\Filters\Admin\Tasklist\RinnovoFilter::class);
    }

}
