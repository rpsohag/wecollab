<?php

namespace Modules\Amministrazione\Entities;

//use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use EloquentFilter\Filterable;
use Illuminate\Support\Facades\DB;
use Auth;
class Clienti extends Model
{
    //use Translatable;
    use Filterable;

    protected $table = 'amministrazione__clienti';
    // public $translatedAttributes = [];
    protected $fillable = [
        'azienda',
        'tipo',
        'ragione_sociale',
        'p_iva',
        'cod_fiscale',
        'nazione',
        'indirizzo',
        'citta',
        'provincia',
        'cap',
        'email',
        'pec',
        'codice_univoco',
        'aree',
        'logo',
        'tipologia',
        'pa',
        'hash_link',
        'commerciale_id',
        'default_ordinativo'
    ];

    public static function getRules()
    {
        return [
            'ragione_sociale' => 'required',
            'p_iva' => 'required_if:cod_fiscale,""|nullable|size:11',
            'tipologia' => 'required',
            'tipo' => 'required',
            //'nazione' => 'required',
            //'citta' => 'required',
            //'provincia' => 'required|size:2',
            //'cap' => 'required|size:5',
            'email' =>'email|nullable',
            'pec' => 'email|nullable',
            'logo' => 'image'
        ];
    }

    protected static function boot()
    {
        parent::boot();

        if(Auth::check())
        {
            static::addGlobalScope('clienti', function (Builder $builder) {
            $builder->where('tipo', '!=', 2);
            });
            static::addGlobalScope('ordine', function (Builder $builder) {
            $builder->orderBy('ragione_sociale', 'asc');
            });
        }
    }

    public function getCreatedAtAttribute($value)
    {
        return get_date_ita($value);
    }

    public function scopeCommerciali($query)
    {
        $roles = [];
        $roles[user(setting('admin::direttore_tecnico'))->id] = user(setting('admin::direttore_tecnico'))->id;
        $roles[user(setting('admin::direttore_commerciale'))->id] = user(setting('admin::direttore_commerciale'))->id;
        $roles[user(setting('admin::direttore_pa'))->id] = user(setting('admin::direttore_pa'))->id;
        $roles[user(setting('admin::amministrazione'))->id] = user(setting('admin::amministrazione'))->id;
        $roles[user(setting('admin::segreteria_commerciale'))->id] = user(setting('admin::segreteria_commerciale'))->id;      

        if(Auth::user()->hasAccess('commerciale.filtri.cliente')) {
            if(in_array(Auth::id(), $roles)) {
                return $query;
            } else {
                return $query->where('commerciale_id', Auth::id());
            }
        } else {
            return $query;
        }
    }

    public function setLogoAttribute($logo)
    {
        $this->attributes['logo'] = set_blob($logo);
    }

    public function setAreeAttribute($aree)
    {
        $this->attributes['aree'] = empty($aree) ? NULL : json_encode($aree);
    }

    public function getAreeAttribute($aree)
    {
        return json_decode($aree);
    }

    public function modelFilter()
    {
        return $this->provideFilter(\Modules\Filters\Admin\Amministrazione\ClientiFilter::class);
    }

    public function sede()
    {
        return $this->hasMany('Modules\Amministrazione\Entities\ClienteIndirizzi', 'cliente_id')
        ->select("*", DB::raw("CONCAT(indirizzo, ', ', cap, ' ', citta, ' ', provincia) as indirizzo_completo"))
        ->first();
    }

    public function sedeLegale()
    {
        $sedeLegale =
        $this->hasMany('Modules\Amministrazione\Entities\ClienteIndirizzi', 'cliente_id')
        -> select("*", DB::raw("CONCAT(indirizzo, ', ', cap, ' ', citta, ' ', provincia) as indirizzo_completo"))
        -> whereRaw(DB::raw('replace(lcase(denominazione), " ", "" ) = "sedelegale"' ))
        -> first();


        if(!empty($sedeLegale))
        	return $sedeLegale ;
        else
        	return $this->sede();
    }

   	public function setSedeLegale($new)
    {
     	$sedeLegale = $this->sedeLegale();

	    if(!empty($sedeLegale))
        	$sedeLegale->update($new);
      else
      {
  			$indirizzo_new=  new ClienteIndirizzi();
  			$indirizzo_new-> citta = $new['citta'];
  			$indirizzo_new-> denominazione = $new['denominazione'];
  			$indirizzo_new-> indirizzo = $new['indirizzo'];
  			$indirizzo_new-> provincia = $new['provincia'];
  			$indirizzo_new-> cap = $new['cap'];
  			$indirizzo_new-> nazione = $new['nazione'];
  			$indirizzo_new-> denominazione = $new['denominazione'];

  			$this->indirizzi()->save($indirizzo_new);
  		}
    }

    public function indirizzi()
    {
        return $this->hasMany('Modules\Amministrazione\Entities\ClienteIndirizzi', 'cliente_id')->select("*", DB::raw("CONCAT(indirizzo, ', ', cap, ' ', citta, ' ', provincia) as indirizzo_completo"));
    }

    public function referenti()
    {
        return $this->hasMany('Modules\Amministrazione\Entities\ClienteReferenti', 'cliente_id');
    }

    public function offerte()
    {
        return $this->hasMany('Modules\Commerciale\Entities\Offerta', 'cliente_id');
    }

    public function fatture()
    {
        return $this->hasMany('Modules\Commerciale\Entities\Fatturazione', 'cliente_id');
    }

    public function censimento()
    {
        return $this->hasOne('Modules\Commerciale\Entities\CensimentoCliente', 'cliente_id');
    }

    public function commerciale()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'commerciale_id');
    }

    /**
     * Get all of the metas for the clienti.
     */
    public function metas()
    {
        return $this->morphToMany('Modules\Wecore\Entities\Meta', 'metagable', 'wecore__metagable');
    }

    public function ambiente()
    {
        return $this->hasOne('Modules\Amministrazione\Entities\ClienteAmbienti', 'cliente_id');
    }

    public function notes()
    {
        return $this->morphToMany('Modules\Wecore\Entities\Meta', 'metagable', 'wecore__metagable')->where('name', 'note')->orderBy('created_at', 'desc');
    }
}
