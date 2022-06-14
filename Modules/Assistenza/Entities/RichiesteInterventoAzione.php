<?php

namespace Modules\Assistenza\Entities;

// use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Auth;

class RichiesteInterventoAzione extends Model
{
    // use Translatable;
    protected $table = 'assistenza__richiesteinterventi_azioni';
    // public $translatedAttributes = [];
    protected $fillable = [
      'descrizione',
      'tipo',
      'tipologia_intervento'
    ];

    public static function getRules()
    {
        return [
            'descrizione' => 'required',
            'tipo' => 'required|integer',
            'tipologia_intervento' => 'required|integer',
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

    public function ticket()
    {
        return $this->belongsTo('Modules\Assistenza\Entities\RichiesteIntervento', 'ticket_id' );
    }

    public static function inLavorazione($ticketID)
    {
        return RichiesteInterventoAzione::where('ticket_id', $ticketID)
                                        ->where('tipo', 1)
                                        ->where('created_user_id', '<>', Auth::id())
                                        ->count();
    }
}
