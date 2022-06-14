<?php

namespace Modules\Commerciale\Entities;

use Illuminate\Database\Eloquent\Model;


class FatturazioneVoce extends Model
{
    protected $table = 'commerciale__fatturazioni_voci';
    protected $fillable = [
        'descrizione' ,
        'quantita',
        'importo_singolo',
        'importo',
        'iva', 
        'iva_tipo',
        'importo_iva',
        'esente_iva',
        'attivita_svolta',
        'fatturazione_id',
        'created_at',
        'updated_at'
    ];

    public static function getRules()
    {
        return [
            'descrizione' => 'required',
            'quantita' => 'required|integer|min:1',
            'importo_singolo' => 'required|numeric',
            'iva' => 'required|numeric',
            'iva_tipo' => 'required',
            'importo' =>'required|numeric',
            'importo_iva' => 'required|numeric'
        ];
    }

    // public function setImportoSingoloAttribute($importo_singolo)
    // {
    //     $this->attributes['importo_singolo'] = clean_currency($importo_singolo);
    // }

    public function getImportoSingoloAttribute($importo_singolo)
    {
        return get_currency($importo_singolo);
    }

    // public function setImportoAttribute($importo)
    // {
    //     $this->attributes['importo'] = clean_currency($importo);
    // }

    public function getImportoAttribute($importo)
    {
        return get_currency($importo);
    }

    // public function setImportoIvaAttribute($importo_iva)
    // {
    //     $this->attributes['importo_iva'] = clean_currency($importo_iva);
    // }

    public function getImportoIvaAttribute($importo_iva)
    {
        return get_currency($importo_iva);
    }

    public function fattura()
    {
        return $this->belongsTo('Modules\Commerciale\Entities\Fatturazione', 'fatturazione_id');
    }
}
