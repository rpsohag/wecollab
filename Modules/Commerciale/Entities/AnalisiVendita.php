<?php

namespace Modules\Commerciale\Entities;

// use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;
use Auth;
use DB;

class AnalisiVendita extends Model
{
    // use Translatable;
    use Filterable;

    protected $table = 'commerciale__analisivendite';
    // public $translatedAttributes = [];
    protected $fillable = [
      'azienda',
      'titolo',
      'data',
      'attivita',
      'canoni',
      'costi_fissi',
      'censimento_id',
      'offerta_id',
      'commerciale_id'
    ];

    public static function getRules()
    {
        return [
          'titolo' => 'required',
          'data' => 'required',
          'censimento_id' => 'required|integer|min:1',
          'commerciale_id' => 'required|integer|min:1'
        ];
    }

    public function modelFilter()
    {
        return $this->provideFilter(\Modules\Filters\Admin\Commerciale\AnalisiVenditaFilter::class);
    }

    public function setAttivitaAttribute($attivita)
    {
        $this->attributes['attivita'] = json_encode($attivita);
    }

  	public function getAttivitaAttribute($attivita)
    {
        return json_decode($attivita);
    }
    // public function getAttivitaAttribute($attivita)
    // {
    //     $attivita = json_decode($attivita);
    //
    //     if(!empty($attivita))
    //     {
    //       foreach($attivita as $key => $check)
    //       {
    //         if(!empty($check))
    //         {
    //           foreach($check as $k => $c)
    //           {
    //             if(!empty($c))
    //             {
    //               $attivita->$key->checked = true;
    //             }
    //           }
    //         }
    //       }
    //     }
    //
    //     return $attivita;
    // }

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

    public function setCanoniAttribute($canoni)
    {
        $this->attributes['canoni'] = json_encode($canoni);
    }

  	public function getCanoniAttribute($canoni)
    {
        return json_decode($canoni);
    }

    public function setCostiFissiAttribute($costi_fissi)
    {
        $this->attributes['costi_fissi'] = json_encode($costi_fissi);
    }

  	public function getCostiFissiAttribute($costi_fissi)
    {
        return json_decode($costi_fissi);
    }

    public function getSegnalazioneAttribute($segnalazione)
    {
        return json_decode($segnalazione);
    }

  	public function setSegnalazioneAttribute($segnalazione)
    {
        $this->attributes['segnalazioni'] = json_encode($segnalazione);
    }


    public function setDataAttribute($data)
    {
        $this->attributes['data'] = set_date_ita($data);
    }

    public function getDataAttribute($data)
    {
        return get_date_ita($data);
    }

    public function censimento_cliente($censimentocliente_id = false)
    {
      if($censimentocliente_id)
        return CensimentoCliente::findOrFail($censimentocliente_id);
      else
        return $this->belongsTo('Modules\Commerciale\Entities\CensimentoCliente', 'censimento_id');
    }

	public function elencoSegnalazioni()
    {
    	return SegnalazioneOpportunita::where('azienda', session('azienda'))->where('analisivendita_id', $this->id)->pluck('oggetto', 'id') ->toArray();
    }

	public function segnalazioni()
    {
      return $this->hasMany('Modules\Commerciale\Entities\SegnalazioneOpportunita', 'analisivendita_id');
    }

    public function offerta()
    {
        return $this->belongsTo('Modules\Commerciale\Entities\Offerta', 'offerta_id');
    }

    public function commerciale()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'commerciale_id');
    }

	  public function elencoSegnalazioniVista()
    {
      $preventivs = $this;

			return SegnalazioneOpportunita::select(
   							 DB::raw("CONCAT(numero,' - ',oggetto,' di ',first_name ,' ',last_name ) AS names"),'commerciale__segnalazioniopportunita.id')
							->leftJoin('users', 'users.id', '=', 'created_user_id')
							->where('azienda', session('azienda'))
                          	->where('stato_id','!=', 3)
						   	->where( function($query) use ($preventivs  )
							{
							    $query ->where('analisivendita_id', 0)
							       		->orWhere('analisivendita_id', $preventivs->id)
							       		->orWhereNull('analisivendita_id');
							})->pluck('names', 'commerciale__segnalazioniopportunita.id')
                          	->toArray();
	    }
}
