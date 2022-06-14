<?php

namespace Modules\Assistenza\Entities;

use Illuminate\Database\Eloquent\Model;

class TicketInterventoVoci extends Model
{

    protected $table = 'assistenza__ticketinterventi_voci';
    protected $fillable = [
        'data_intervento',
        'descrizione',
        'quantita',
        'ticket_id',
        'ora_inizio_1',
        'ora_fine_1',
        // 'ora_inizio_2',
        // 'ora_fine_2'
    ];

    public static function getRules()
    {
        return [
            'data_intervento' => 'required',
            'descrizione' => 'required',
            'quantita' => 'required'
        ];
    }

    public function setDataInterventoAttribute($date)
    {
        $this->attributes['data_intervento'] = set_date_ita($date);
    }

    public function created_user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'created_user_id');
    }

    public function updated_user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'updated_user_id');
    }

    public function ticketIntervento()
    {
        return $this->belongsTo('Modules\Assistenza\Entities\TicketIntervento', 'ticket_id');
    }

}
