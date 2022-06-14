<?php

namespace Modules\Assistenza\Entities;

// use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

use EloquentFilter\Filterable;
use Modules\Assistenza\Entities\TicketInterventoVoci;

class TicketIntervento extends Model
{
    use Filterable;

    protected $table = 'assistenza__ticketinterventi';
    // public $translatedAttributes = [];
    protected $fillable = [
        'azienda',
        'data',
        'codice_ticket',
        'n_di_intervento',
        'descrizione_ticket',
        'materiale_consegnato',
        'cliente_id',
        'ordinativo_id',
        'gruppo_id',
        'tipologia_id',
        'settore_id',
        'note',
        'created_user_id',
        'updated_user_id',
        'created_at',
        'updated_at',
        'formazione',
        'consulenza',
        'area_di_intervento_id',
        'attivita_id',
        'procedura_id'
    ];

    public static function getRules()
    {
        return [
            'cliente_id' => 'required',
            'ordinativo_id' => 'required|integer|min:1',
            'gruppo_id' => 'required',
            //'tipologia_id' => 'required',
            //'settore_id' => 'required',
            'data' => 'required'
           
        ];
    }

    public function setDataAttribute($date)
    {
        $this->attributes['data'] = set_date_ita($date);
    }

/*    
    public function setDataConsulenza($consulenza)
    {
        $this->attributes['consulenza'] = $consulenza;
    }

    public function setDataFormazione($formazione)
    {
        $this->attributes['formazione'] = $formazione;
    }
*/

    public function modelFilter()
    {
        return $this->provideFilter(\Modules\Filters\Admin\Assistenza\TicketInterventoFilter::class);
    }

    public function created_user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'created_user_id');
    }

    public function updated_user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'updated_user_id');
    }

    public function cliente()
    {
        return $this->belongsTo('Modules\Amministrazione\Entities\Clienti', 'cliente_id' );                                 
    }

    public function gruppo()
    {
        return $this->belongsTo('Modules\Profile\Entities\Gruppo', 'gruppo_id' );
    }

    public function procedura()
    {
        return $this->belongsTo('Modules\Profile\Entities\Procedura', 'procedura_id' );
    }

    public function ordinativo()
    {
        return $this->belongsTo('Modules\Commerciale\Entities\Ordinativo', 'ordinativo_id' );
    }

    public function voci()
    {
        return $this->hasMany('Modules\Assistenza\Entities\TicketInterventoVoci', 'ticket_id');
    }
  

    //Antonio 
    public function area()
    {
        return $this->belongsTo('Modules\Profile\Entities\Area', 'area_di_intervento_id');
    }
   /* 
    public function attivita()
    {
        return $this->belongsTo('Modules\Profile\Entities\Gruppo', 'attivita_id');
    }
    */

    public function giornate()
    {
        $ticketintervento = $this;

        return TicketInterventoVoci::whereHas('ticketintervento', function($q) use($ticketintervento)
        {
            $q->where('ordinativo_id', $ticketintervento->ordinativo_id)
              ->where('gruppo_id', $ticketintervento->gruppo_id);
        })->get();
    }

    public static function numero_ticket_formato($data, $numero)
    {
        $anno = date('Y',strtotime($data));
        
        return $anno . '-' . sprintf('%05d', $numero);
    }

    public function setCodiceTicketAttribute($valore)
    {
        if(strpos($valore,'-')!== false)
        {
            $numeretto = explode('-',$valore)[1];
            $numeretto = ltrim($numeretto,0);
        }else{
            $numeretto = $valore;
        }
       
        $this->attributes['codice_ticket'] = $numeretto;
    }

    public function numero_ticket()
    {
        return TicketIntervento::numero_ticket_formato($this->data, $this->codice_ticket);
    }

    public static function get_next_codice_ticket()
    {
        $codice_ticket = TicketIntervento::max('codice_ticket');

         return ($codice_ticket + 1);
    }
    public static function get_next_n_ticket()
    {
        $n_ticket = TicketIntervento::latest()->first();

        if(empty($n_ticket))
            $numero = 1;
        else
        {
            $split = explode('_',$n_ticket['n_di_intervento']);
            $numero = trim($split[0]) + 1;
        }

         return $numero.'_'.date("Y");
    }


}
