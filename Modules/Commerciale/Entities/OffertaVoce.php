<?php

namespace Modules\Commerciale\Entities;

use Illuminate\Database\Eloquent\Model;


class OffertaVoce extends Model
{
    protected $table = 'commerciale__offerte_voci';
    protected $fillable = [
        'descrizione' ,
        'quantita',
        'importo_singolo',
        'importo',
        'iva',
        'importo_iva',
        'esente_iva',
        'accettata',
        'offerta_id',
        'created_at',
        'updated_at'
    ];

    public static function getRules()
    {
        return [
            'descrizione' => 'required',
            'quantita' => 'required|integer|min:1',
            'importo_singolo' => 'required',
            'iva' => 'required',
            'importo' =>'required',
            'importo_iva' => 'required'
        ];
    }

    public function getImportoSingoloAttribute($importo_singolo)
    {
        return get_currency($importo_singolo);
    }

    public function getImportoAttribute($importo)
    {
        return get_currency($importo);
    }

    public function getImportoIvaAttribute($importo_iva)
    {
        return get_currency($importo_iva);
    }

    public function offerta()
    {
        return $this->belongsTo('Modules\Commerciale\Entities\Offerta', 'offerta_id');
    }
}
