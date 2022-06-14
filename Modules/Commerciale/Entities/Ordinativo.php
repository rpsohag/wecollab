<?php

namespace Modules\Commerciale\Entities;

// use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;
use DB;
use Carbon\Carbon;

use Modules\Commerciale\Entities\OrdinativoGiornate;
use Modules\Commerciale\Http\Services\AnalisiVenditaService as AnalisiVenditaService;

class Ordinativo extends Model
{
    use Filterable;

    protected $table = 'commerciale__ordinativi';
    // public $translatedAttributes = [];
    protected $fillable = [
        'azienda',
        'oggetto',
        'note',
        'anno',
        'numero',
        'data_inizio',
        'data_fine',
        'attivita_id',
        'assistenza',
        'voci_economiche',
        'assistenza_per',
        'api_password',
        'hash_link',
        'cliente_id',
        'created_at',
        'updated_at'
    ];

    public static function getRules()
    {
        return [
            'oggetto' => 'required',
            'data_inizio' => 'required',
            'cliente_id' => 'required|integer|not_in:0'
        ]; 
    }

    public function scopeActive($query)
    {
        return $query->whereNull('data_fine')->OrWhereDate('data_fine', ">=", date('Y-m-d'));
    }

    public function getDataInizioAttribute($date)
    {
        return get_date_ita($date);
    }

    public function setDataInizioAttribute($date)
    {
        $this->attributes['data_inizio'] = set_date_ita($date);
    }

    public function getDataFineAttribute($date)
    {
        return get_date_ita($date);
    }

    public function setDataFineAttribute($date)
    {
        $this->attributes['data_fine'] = set_date_ita($date);
    }

    public function getAssistenzaAttribute($assistenza)
    {
        return json_decode($assistenza);
    }

  	public function setAssistenzaAttribute($assistenza)
    {
        $this->attributes['assistenza'] = json_encode($assistenza);
    }

    public function getVociEconomicheAttribute($voci_economiche)
    {
        return json_decode($voci_economiche);
    }

  	public function setVociEconomicheAttribute($voci_economiche)
    {
        $this->attributes['voci_economiche'] = json_encode($voci_economiche);
    }

    public function modelFilter()
    {
        return $this->provideFilter(\Modules\Filters\Admin\Commerciale\OrdinativoFilter::class);
    }

    public function created_user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'created_user_id');
    }

    public function updated_user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'updated_user_id');
    }

    public function offerte()
    {
        return $this->hasMany('Modules\Commerciale\Entities\Offerta', 'ordinativo_id', 'id');
    }

    public function cliente()
    {
        return $this->belongsTo('Modules\Amministrazione\Entities\Clienti', 'cliente_id', 'id')->first();
    }

    public function rinnovo()
    {
        return $this->hasOne('Modules\Tasklist\Entities\Rinnovo', 'ordinativo_id');
    }

    public function attivita()
    {
        return $this->hasMany('Modules\Tasklist\Entities\Attivita', 'ordinativo_id');
    }

    public function timesheets()
    {
        return $this->hasMany('Modules\Tasklist\Entities\Timesheet', 'ordinativo_id');
    }

    public function giornate()
    {
        return $this->hasmany('Modules\Commerciale\Entities\OrdinativoGiornate', 'ordinativo_id');
    }

    public function interventi()
    {
        return $this->hasmany('Modules\Assistenza\Entities\TicketIntervento', 'ordinativo_id');
    }

    public function fatture()
    {
        return $this->hasmany('Modules\Commerciale\Entities\Fatturazione', 'ordinativo_id');
    }

    public function fatturazioni_scadenze()
    {
        return $this->hasMany('Modules\Commerciale\Entities\FatturazioneScadenze', 'ordinativo_id');
    }

    public function metas()
    {
        return $this->morphToMany('Modules\Wecore\Entities\Meta', 'metagable', 'wecore__metagable');
    }
    public function files()
    {
        return $this->morphToMany('Modules\Wecore\Entities\Meta', 'metagable', 'wecore__metagable')
                    ->filter(request()->all(), 'Modules\Filters\Admin\Commerciale\DocumentiFilter')
                    ->where('name', 'file')
                    ->orderBy('created_at', 'desc');
    }

    public function stato()
    {
        return !empty($this->data_fine) ? (Carbon::createFromFormat('d/m/Y', $this->data_fine)->isPast() ? 'Chiuso' : 'Aperto') : 'Senza Scadenza';
    }

    public function numero_ordinativo()
    {
        return Ordinativo::numero_ordinativo_formato($this->anno, $this->numero);
    }

    public static function numero_ordinativo_new()
    {
        return Ordinativo::numero_ordinativo_formato(date('Y'), Ordinativo::get_numero_ordinativo_new());
    }

    public static function get_numero_ordinativo_new()
    {
        $numero_max = Ordinativo::where('azienda', session('azienda'))->where('anno', date('Y'))->max('numero');

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

    public static function numero_ordinativo_formato($anno, $numero)
    {
        return $anno . '-' . sprintf('%03d', $numero);
    }

    public function importo_offerte()
    {
        $importo = 0;
        if($this->offerte()->count() > 0)
        {
            foreach($this->offerte()->get() as $offerta)
            {
                $importo += $offerta->importo_esente;
            }
        }
        return $importo;
    }

    public function importo()
    {
        $importo = 0;

        if(!empty($this->voci_economiche))
        {
            foreach($this->voci_economiche as $voce)
            {
                $importo += $voce->importo;
            }
        }

        return $importo;
    }

    public function importo_analisi()
    { 
        $service = new AnalisiVenditaService();
        $importo = 0;
        if($this->offerte()->count() > 0)
        {
            foreach($this->offerte()->get() as $offerta)
            {
                if(!empty($offerta->analisi_vendita()->first()))
                {
                    $costi_fissi = $service->riepilogoCostiFissi($offerta->analisi_vendita()->first());
                    $costi_figure = $service->riepilogoFigure($offerta->analisi_vendita()->first());
                    $importo += ($costi_figure['totali']['costo_interno'] + $costi_fissi['totali']['costo_interno']) ?? 0;                    
                }
            }
        }

        return $importo;
    }

    public function get_giornate_by_gruppo($gruppo_id)
    {
        $giornate = OrdinativoGiornate::where('gruppo_id', $gruppo_id)
                                        ->where('ordinativo_id', $this->id);

        return $giornate->first();
    }

	public static function get_giornate($ordinativo_id ,$full = false )
    {
    	if($full){
    	$sql = "SELECT
	a.procedura_id,
	p.titolo AS procedura,
	g.area_id,
	a.titolo AS area,
	g.id AS gruppo_id,
	g.nome AS gruppo,
	gg.ordinativo_id,
	gg.quantita,
	gg.quantita_residue,
	gg.quantita_gia_effettuate,
	gg.tipo,
	gg.attivita 
FROM
	profile__procedure p
	LEFT JOIN profile__aree a ON a.procedura_id = p.id
	LEFT JOIN profile__gruppi g ON g.area_id = a.id
	LEFT JOIN commerciale__ordinativi_giornate gg ON gg.gruppo_id = g.id 
and  gg.ordinativo_id = $ordinativo_id 
ORDER BY
	p.id,
	a.id,
	g.id"; 
    }else{
    	$sql = "SELECT
	a.procedura_id,
	p.titolo AS procedura,
	g.area_id,
	a.titolo AS area,
	g.id AS gruppo_id,
	g.nome AS gruppo,
	gg.ordinativo_id,
	gg.quantita,
	gg.quantita_residue,
	gg.quantita_gia_effettuate,
	gg.tipo,
	gg.attivita 
FROM
	profile__procedure p
	LEFT JOIN profile__aree a ON a.procedura_id = p.id
	LEFT JOIN profile__gruppi g ON g.area_id = a.id
	LEFT JOIN commerciale__ordinativi_giornate gg ON gg.gruppo_id = g.id 
WHERE gg.ordinativo_id = $ordinativo_id 
ORDER BY
	p.id,
	a.id,
	g.id"; 
    }
    	
	
	
        $giornate = DB::select($sql);

        return $giornate;
    }


    public function interventi_sum_by_gruppo($gruppo_id, $quantita_gia_effettuate = 0)
    {
        $interventi_sum = $quantita_gia_effettuate;
        $intervento = $this->interventi->where('gruppo_id', $gruppo_id)->first();

        // $intervento_tipi = config('commerciale.interventi.tipi');

        // $area_intervento = $this->giornate->where('gruppo_id', $gruppo_id)->first();

        // $intervento_tipo = !empty($area_intervento) ? $intervento_tipi[$area_intervento->tipo] : '';

        // if(!empty($intervento) && strtolower($intervento_tipo) == 'giornate')
        if(!empty($intervento))
          $intervento->voci = $intervento->giornate();

        if(!empty($intervento) && $intervento->count() > 0)
            $interventi_sum += $intervento->voci->sum('quantita');

        return $interventi_sum;
    }

    public function attivitaCompletamentoMedia()
    {
        if(count($this->attivita) > 0)
        {
            $somma = 0;
            $totale_attivita = count($this->attivita);

            foreach ($this->attivita as $key => $attivita)
            {
                $somma += $attivita->percentuale_completamento;
            }

            return round($somma / $totale_attivita, 2);
        }
        else
        {
            return 0;
        }
    }
}
