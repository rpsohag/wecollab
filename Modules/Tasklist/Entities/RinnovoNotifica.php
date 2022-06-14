<?php

namespace Modules\Tasklist\Entities;

use Illuminate\Database\Eloquent\Model;

class RinnovoNotifica extends Model
{

    protected $table = 'tasklist__rinnovo_notifiche';
    protected $fillable = [
        'notifica',
        'cadenza',
        'tipo',
        'rinnovo_id',
        'created_at',
        'updated_at'
    ];

    public static function getRules()
    {
        return [
            'notifica' => 'required',
            'cadenza' => 'required|integer',
            'tipo' => 'required|integer'
        ];
    }

    public function created_user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'created_user_id');
    }

    public function updated_user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'updated_user_id');
    }

    public function rinnovo()
    {
        return $this->belongsTo('Modules\Tasklist\Entities\Rinnovo', 'rinnovo_id');
    }

}
