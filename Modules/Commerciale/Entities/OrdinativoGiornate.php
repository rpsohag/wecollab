<?php

namespace Modules\Commerciale\Entities;

use Illuminate\Database\Eloquent\Model;


class OrdinativoGiornate extends Model
{
    protected $table = 'commerciale__ordinativi_giornate';
    protected $fillable = [
        'ordinativo_id' ,
        'gruppo_id',
        'quantita',
        'quantita_residue',
        'quantita_gia_effettuate',
        'tipo',
        'attivita'
    ];

    public $timestamps = false;

    public function setQuantitaGiaEffettuateAttribute($quantita)
    {
        $this->attributes['quantita_gia_effettuate'] = !empty($quantita) ? $quantita : 0;
    }

    public function setAttivitaAttribute($attivita)
    {
        $this->attributes['attivita'] = !empty($attivita) ? $attivita : 0;
    }

    public function ordinativo()
    {
        return $this->belongsTo('Modules\Commerciale\Entities\Ordinativo', 'ordinativo_id');
    }

    public function attivita()
    {
        return $this->hasOne('Modules\Tasklist\Entities\Attivita');
    }
}
