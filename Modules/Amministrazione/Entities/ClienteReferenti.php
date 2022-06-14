<?php

namespace Modules\Amministrazione\Entities;

// use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class ClienteReferenti extends Model
{
    // use Translatable;

    protected $table = 'amministrazione__clienti_referenti';
    // public $translatedAttributes = [];
    protected $fillable = [
        'nome',
        'cognome',
        'telefono',
        'email',
        'mansione',
        'cliente_id'
    ];

    public static function getRules()
    {
        return [
            'nome' => 'required',
            'cognome' => 'required',
            'email' => 'nullable|email',
            'telefono' => 'nullable|unique:amministrazione__clienti_referenti|numeric|digits_between:6,15',
        ];
    }

    public function cliente()
    {
        return $this->belongsTo('Modules\Amministrazione\Entities\Clienti', 'cliente_id');
    }

    public function full_name()
    {
        return $this->first_nome . ' ' . $this->cognome;
    }
}
