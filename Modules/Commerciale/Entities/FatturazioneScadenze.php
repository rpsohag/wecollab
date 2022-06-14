<?php

namespace Modules\Commerciale\Entities;

use Illuminate\Database\Eloquent\Model;


class FatturazioneScadenze extends Model
{
    protected $table = 'commerciale__fatturazioni_scadenze';
    protected $fillable = [
        'descrizione',
        'data',
        'data_avviso',
        'importo',
        'fattura_id',
        'ordinativo_id',
        'offerta_id'
    ];

    public static function getRules()
    {
        return [
            'descrizione' => 'required',
            'data' => 'required|date',
            'data_avviso' => 'required|date',
            'importo' => 'required|numeric',
            'ordinativo_id' => 'required|integer',
            'offerta_id' => 'required|integer|not_in:0'
        ];
    }

    public static function getOrdinativoRules()
    {
        return [
            'fatturazioni_scadenze.*.descrizione' => 'required',
            'fatturazioni_scadenze.*.data' => 'required|date',
            'fatturazioni_scadenze.*.data_avviso' => 'required|date',
            'fatturazioni_scadenze.*.importo' => 'required|numeric',
            'fatturazioni_scadenze.*.ordinativo_id' => 'required|integer',
            'fatturazioni_scadenze.*.offerta_id' => 'required|integer|not_in:0'
        ];
    }

    public function getDataAttribute($data)
    {
        return get_date_ita_due($data);
    }

    public function setDataAttribute($data)
    {
        $this->attributes['data'] = set_date_ita($data);
    }

    public function getDataAvvisoAttribute($data)
    {
        return get_date_ita_due($data);
    }

    public function setDataAvvisoAttribute($data)
    {
        $this->attributes['data_avviso'] = set_date_ita($data);
    }

    public function getImportoAttribute($importo)
    {
        return get_currency($importo);
    }

    public function setFatturaIdAttribute($id)
    {
        $id = ($id == 0) ? NULL : $id;
        
        $this->attributes['fattura_id'] = $id;
    }

    public function ordinativo()
    {
        return $this->belongsTo('Modules\Commerciale\Entities\Ordinativo', 'ordinativo_id');
    }

    public function offerta() 
    {
        return $this->belongsTo('Modules\Commerciale\Entities\Offerta', 'offerta_id');
    }

    public function fattura()
    {
        return $this->belongsTo('Modules\Commerciale\Entities\Fatturazione', 'fattura_id');
    }
}
