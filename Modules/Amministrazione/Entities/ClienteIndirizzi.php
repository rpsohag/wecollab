<?php

namespace Modules\Amministrazione\Entities;

//use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class ClienteIndirizzi extends Model
{
    //use Translatable;

    protected $table = 'amministrazione__clienti_indirizzi';
    //public $translatedAttributes = [];
    protected $fillable = [
        'denominazione',
        'nazione',
        'indirizzo',
        'cap',
        'citta',
        'provincia',
        'telefono',
        'fax',
        'email',
        'cliente_id'
    ];

    public static function getRules()
    {
        return [
          'denominazione' =>function ($attribute, $value, $fail) {
                                              if (is_numeric($value) && $value * 1 == $value) {
                                                  $fail(':attribute non è valido.');
                                              }
                                            },
          'nazione' => 'required|string',
          'cap' => 'required|numeric',
          'indirizzo' => 'required|string',
          'provincia' => 'required|size:2',
          'citta' => 'required|string|min:2',
          'email' => 'nullable|email'
        ];
    }

    public static function getRulesStore()
    {
      return [
        'indirizzo_base.denominazione' =>function ($attribute, $value, $fail) {
                                            if (is_numeric($value) && $value * 1 == $value) {
                                                $fail(':attribute non è valido.');
                                            }
                                          },
        'indirizzo_base.nazione' => 'required|string',
        'indirizzo_base.cap' => 'required|numeric',
        'indirizzo_base.indirizzo' => 'required|string',
        'indirizzo_base.provincia' => 'required|size:2',
        'indirizzo_base.citta' => 'required|string|min:2',
        'indirizzo_base.email' => 'nullable|email'
        ];
      }

    public function cliente()
    {
        return $this->belongsTo('Modules\Amministrazione\Entities\Clienti', 'cliente_id');
    }
}
