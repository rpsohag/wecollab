<?php

namespace Modules\Commerciale\Entities;

use Illuminate\Database\Eloquent\Model;

class AnalisiVenditaTemplate extends Model
{
    protected $table = 'commerciale__analisivendite_templates';

    protected $fillable = [
      'nome',
      'attivita'
    ];

    public static function getRules()
    {
        return [
          'nome' => 'required',
          'attivita' => 'required'
        ];
    }

    public function setAttivitaAttribute($attivita)
    {
        $this->attributes['attivita'] = json_encode($attivita);
    }

  	public function getAttivitaAttribute($attivita)
    {
        return json_decode($attivita);
    }
}
