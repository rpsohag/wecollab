<?php

namespace Modules\Commerciale\Entities;

// use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use EloquentFilter\Filterable;
use Modules\Profile\Entities\Gruppo; 
use Modules\Profile\Entities\Area; 
use DB;
use Auth;

class CensimentoCliente extends Model
{
    // use Translatable;
    use Filterable;

    protected $table = 'commerciale__censimenticlienti';
    
    protected $fillable = [
      'azienda',
      'indirizzo',
      'cap',
      'citta',
      'provincia',
      'nazione',
      'sindaco',
      'segretario',
      'referente',
      'sindaco_telefono',
      'segretario_telefono',
      'referente_telefono',
      'sindaco_email',
      'segretario_email', 
      'referente_email',
      'numero_dipendenti',
      'numero_utilizzatori_urbi',
      'fascia_abitanti',
      'referenti',
      'pianta_organica',
      'note',
      'cliente_id',
      'created_user_id',
      'updated_user_id'
    ];
    protected $appends = [
        'indirizzo_completo'
    ];

    public static function getRules()
    {
        return [
          'cap' => 'nullable|min:5|max:5',
          'provincia' => 'nullable|min:2|max:5',
          'numero_dipendenti' => 'nullable|integer',
          'cliente_id' => 'required|integer',
       	  'created_user_id' => 'integer',
          'updated_user_id' => 'integer'
        ];
    }

    protected static function boot()
    {
        parent::boot();

        if(Auth::check())
        {
          static::addGlobalScope('ordine', function (Builder $builder) {
            $builder->orderBy('cliente', 'asc');
          });
        }
    }

    public function modelFilter()
    {
        return $this->provideFilter(\Modules\Filters\Admin\Commerciale\CensimentoClienteFilter::class);
    }

    public function setReferentiAttribute($referenti)
    {
        $this->attributes['referenti'] = json_encode($referenti);
    }

    public function setPiantaOrganicaAttribute($po)
    {
        $this->attributes['pianta_organica'] = json_encode($po);
    }

    public function getReferentiAttribute($referenti)
    {
        $referenti = json_decode($referenti);

        return collect($referenti);
    }

    public function getPiantaOrganicaAttribute($po)
    {
        return json_decode($po);
    }

    public function getCreatedAtAttribute($date)
    {
        return get_date_ita($date);
    }

    public function getIndirizzoCompletoAttribute()
    {
        return $this->indirizzo . ' - ' . $this->cap . ' ' . $this->citta . ' (' . $this->provincia . '), ' . $this->nazione;
    }

    public function fascia_abitanti()
    {
        if(!empty($this->fascia_abitanti))
          return config('commerciale.censimenticlienti.fasce_abitanti')[$this->fascia_abitanti];
    }

    public function situazione_software_attivita($attivita_id)
    {
      $gruppo = Gruppo::find($attivita_id);
      return $gruppo->nome;
    }

    public function situazione_software_area($area_id)
    {
        $area = Area::find($area_id);
        return $area->titolo;
    }

    public function offerte()
    {
      return $this->hasMany('Modules\Commerciale\Entities\Offerta', 'cliente_id', 'cliente_id');
    }

    public function cliente()
    {
        return $this->belongsTo('Modules\Amministrazione\Entities\Clienti', 'cliente_id');
    }

    public function ordinativi()
    {
      if(!empty($this->offerte()))
      {
        $offerte = $this->offerte()->get();
        $ordinativi_id = array();

        foreach($offerte as $offerta)
        {
          if($offerta->ordinativo)
          {
            array_push($ordinativi_id, $offerta->ordinativo->id);
          }
        }

        return Ordinativo::whereIn('id', $ordinativi_id)->get();

      }
    }

    public function created_user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'created_user_id');
    }

    public function updated_user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'updated_user_id');
    }

    public function analisivendita()
    {
        return $this->hasMany('Modules\Commerciale\Entities\AnalisiVendita', 'censimento_id');
    }

     public function segnalazioni_oppotunita()
     {
       return $this->hasMany('Modules\Commerciale\Entities\SegnalazioneOpportunita', 'censimento_id');
     }
}
