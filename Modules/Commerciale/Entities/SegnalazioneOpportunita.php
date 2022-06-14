<?php

namespace Modules\Commerciale\Entities; 

// use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Profile\Entities\Gruppo; 
use Modules\Profile\Entities\Area; 
use Auth;
class SegnalazioneOpportunita extends Model
{
    // use Translatable;
    use Filterable, SoftDeletes;

    protected $table = 'commerciale__segnalazioniopportunita';
    // public $translatedAttributes = [];
    protected $fillable = [
      'azienda',
      'numero',
      'cliente',
      'cliente_id',
      'oggetto',
      'descrizione',
      'checklist',
      'stato_id',
      'analisivendita_id',
      'created_user_id',
      'updated_user_id',
      'commerciale_id'
    ];

    protected $appends = [
        'numeroSegnalazione',
    ];

    public static function getRules()
    {
        return [
            'cliente' => 'nullable|required_without:cliente_id',
            'cliente_id' => 'nullable|integer|required_without:cliente',
            'oggetto' => 'required'
        ];
    }

    protected static function boot()
    {
        parent::boot();

        if(Auth::check())
        {
            static::addGlobalScope('azienda', function (Builder $builder) {
                $builder->where('commerciale__segnalazioniopportunita.azienda', session('azienda'));
            });

            static::addGlobalScope('access', function (Builder $builder) {
                $auth = auth_user();

                if(!$auth->hasAccess('commerciale.censimenticlienti.index'))
                    return $builder->where('created_user_id', $auth->id);
            });
        }
    }

    public function getNumeroSegnalazioneAttribute()
    {
        return $this->numero();
    }

    public function modelFilter()
    {
        return $this->provideFilter(\Modules\Filters\Admin\Commerciale\SegnalazioneOpportunitaFilter::class);
    }

    public function setChecklistAttribute($checklist)
    {
        $this->attributes['checklist'] = json_encode($checklist);
    }
    public function setAnalisiVendita($id)
    {
        $this->attributes['analisivendita_id'] =$id;
    }
    public function getClienteAttribute($cliente)
    {
      if(!empty($this->cliente))
      {
        if($this->cliente_id > 0)
          if(!empty($this->cliente()->ragione_sociale))
            return $this->cliente()->ragione_sociale;
        else
          return $this->cliente;
      }
      else
        return $cliente;
    }

    public function getChecklistAttribute($checklist)
    {
        $checklist = json_decode($checklist);
        return collect($checklist);
    } 

    public function getCreatedAtAttribute($date)
    {
        return get_date_ita($date);
    }

    public function files()
    {
        return $this->morphToMany('Modules\Wecore\Entities\Meta', 'metagable', 'wecore__metagable')->where('name', 'file')->orderBy('created_at', 'desc');
    }

    public function checklist_attivita($attivita_id)
    {
      $gruppo = Gruppo::find($attivita_id);
      return optional($gruppo)->nome;
    }

    public function checklist_area($area_id)
    {
        $area = Area::find($area_id);
        return optional($area)->titolo;
    }

    public function created_user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'created_user_id');
    }

    public function updated_user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'updated_user_id');
    }

  	public function commerciale()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'commerciale_id');
    }

    public function cliente()
    {
        return $this->belongsTo('Modules\Amministrazione\Entities\Clienti', 'cliente_id', 'id')->first();
    }

    public function metas()
    {
        return $this->morphToMany('Modules\Wecore\Entities\Meta', 'metagable', 'wecore__metagable');
    }

    public function censimento()
    {
        return $this->belongsTo('Modules\Commerciale\Entities\CensimentoCliente', 'cliente_id', 'cliente_id');
    }

    public function analisi_vendita()
    {
        return $this->belongsTo('Modules\Commerciale\Entities\AnalisiVendita', 'analisivendita_id');
    }

    public function attivita()
    {
        return $this->morphMany('Modules\Tasklist\Entities\Attivita', 'attivitable');
    }

    public function numero()
    {
        return SegnalazioneOpportunita::numero_formato(date('Y', strtotime(set_date_ita($this->created_at))), $this->numero);
    }

    public static function numero_new()
    {
        return SegnalazioneOpportunita::numero_formato(date('Y'), SegnalazioneOpportunita::get_numero_new());
    }

    public static function get_numero_new()
    {
        $numero_max = SegnalazioneOpportunita::withoutGlobalScope('access')
                                            ->whereYear('created_at', date('Y'))
                                            ->max('numero');

        if(empty($numero_max))
            $numero = 1;
        else
            $numero = $numero_max + 1;

        return $numero;
    }

    public static function numero_formato($anno, $numero)
    {
        return $anno . '-' . sprintf('%03d', $numero);
    }

    public function stato()
    {
        return config('commerciale.segnalazioneopportunita.stati')[$this->stato_id];
    }
}
