<?php

namespace Modules\Commerciale\Entities;

// use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;


class Offerta extends Model
{
    // use Translatable;
    use Filterable;

    protected $table = 'commerciale__offerte';
    // public $translatedAttributes = [];
    protected $fillable = [
        'azienda',
        'anno',
        'numero',
        'stato',
        'oggetto',
        'data_offerta',
        'importo_esente',
        'importo_iva',
        'iva',
        'note',
        'fatturata',
        'offerta_pa',
        'offerta_non_standard',
        'approvazioni',
        'cliente_id',
        'ordinativo_id',
        'offerta_definitiva_id',
        'oda_determina_ids',
        'ordine_mepa_id',
        'created_user_id',
        'updated_user_id',
        'created_at',
        'updated_at'
    ];

    public static function getRules()
    {
        return [
            'data_offerta' => 'required',
            'oggetto' => 'required',
            'cliente_id' => 'required|integer|min:1',
            'importo_esente' => 'required',
            'iva' => 'required'
        ];
    }

    public function getDataOffertaAttribute($date)
    {
        return get_date_ita($date);
    }

    public function getApprovazioniAttribute($approvazioni)
    {
        return json_decode($approvazioni);
    }

    public function setApprovazioniAttribute($approvazioni)
    {
        $this->attributes['approvazioni'] = json_encode($approvazioni);
    }

    public function setDataOffertaAttribute($date)
    {
        $this->attributes['data_offerta'] = set_date_ita($date);
    }

    public function getOdaDeterminaIdsAttribute($od_ids)
    {
        return collect(json_decode($od_ids, true));
    }

    public function setOdaDeterminaIdsAttribute($od_ids)
    {
        $this->attributes['oda_determina_ids'] = json_encode($od_ids);
    }

    public function modelFilter()
    {
        return $this->provideFilter(\Modules\Filters\Admin\Commerciale\OfferteFilter::class);
    }

    public function scopeBozze($query)
    {
        return $query->where('stato', -1);
    }

    public function ruoli()
    {
        $ruoli = config('commerciale.offerte.approvazioni');
        return $ruoli;
    }

    public function bozzaAssegnatari()
    {
        $utenti = null;
        foreach($this->ruoli() as $key => $ruolo){
            if(!empty($this->approvazioni) && isset($this->approvazioni->$ruolo)){
                $utenti[$key] = user(setting('admin::'.$ruolo));
            }
        }

        return collect($utenti);
    }

    public function approvata()
    {
        $approvata = true;

        if($this->approvazioni !== null){
            foreach($this->approvazioni as $ruolo => $boolean){
                if($boolean == 0){
                    $approvata = false;
                }
            }
        }

        return $approvata;
    }

    public function fatturata()
    {
      if(!empty($this->ordinativo) && count($this->ordinativo->fatturazioni_scadenze) > 0)
      {
        $fatturazioni_scadenze = $this->ordinativo->fatturazioni_scadenze->where('fattura_id', '')->count();

        if($fatturazioni_scadenze > 0)
          return 0;
        else
          return 1;
      }
      elseif(!empty($this->ordinativo) && count($this->ordinativo->fatture) > 0)
        return 1;
      else
        return $this->fatturata;
    }

    public function cliente()
    {
        return $this->belongsTo('Modules\Amministrazione\Entities\Clienti', 'cliente_id');
    }

    public function created_user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'created_user_id')->withTrashed();
    }

    public function updated_user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'updated_user_id');
    }

    /**
     * Get all of the metas for the offerta.
     */
    public function metas()
    {
        return $this->morphToMany('Modules\Wecore\Entities\Meta', 'metagable', 'wecore__metagable');
    }
    public function files()
    {
        return $this->morphToMany('Modules\Wecore\Entities\Meta', 'metagable', 'wecore__metagable')->where('name', 'file')->orderBy('created_at', 'desc');
    }

    public function voci()
    {
        return $this->hasMany('Modules\Commerciale\Entities\OffertaVoce', 'offerta_id');
    }

    public function ordinativo()
    {
        return $this->belongsTo('Modules\Commerciale\Entities\Ordinativo', 'ordinativo_id');
    }

    public function analisi_vendita()
    {
        return $this->hasOne('Modules\Commerciale\Entities\AnalisiVendita', 'offerta_id');
    }

    public function numero_offerta()
    {
        return Offerta::numero_offerta_formato($this->anno, $this->numero);
    }

    public static function numero_offerta_new()
    {
        return Offerta::numero_offerta_formato(date('Y'), Offerta::get_numero_offerta_new());
    }

    public static function get_numero_offerta_new()
    {
        $numero_max = Offerta::where('azienda', session('azienda'))
                                    ->where('anno', date('Y'))
                                    ->max('numero');

        if(empty($numero_max))
        {
            $numero = 1;
        }
        else
        {
            $numero = $numero_max + 1;
        }

        return $numero;
    }

    public static function numero_offerta_formato($anno, $numero)
    {
        return $anno . '-' . sprintf('%03d', $numero);
    }
}
