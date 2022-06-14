<?php

namespace Modules\Tasklist\Entities;

// use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use EloquentFilter\Filterable;
use Carbon\Carbon;
use Auth;

class Timesheet extends Model
{
    // use Translatable;
    use Filterable;

    protected $table = 'tasklist__timesheets';
    // public $translatedAttributes = [];
    protected $fillable = [
      'azienda',
      'cliente_id',
      'procedura_id',
      'area_id',
      'gruppo_id',
      'ordinativo_id',
      'attivita_id',
      'ticket_azione_id',
      'dataora_inizio',
      'dataora_fine',
      'nota',
      'created_user_id',
      'updated_user_id',
      'tipologia'
    ];

    public static function getRules()
    {
        return [
            'cliente_id' => 'required|integer|min:1',
            'procedura_id' => 'required|integer|min:1',
            'ordinativo_id' => 'required|integer|min:1',
            'area_id' => 'required|integer|min:1', 
            'gruppo_id' => 'required|integer|min:1',
            'ora_inizio' => 'required',
            'ora_fine' => 'required',
            'tipologia' => 'required|integer|min:0'
        ];
    }

    public static function getRulesListEdit()
    {
        return [
            'timesheet.edit.*.cliente_id' => 'required|integer|min:1',
            'timesheet.edit.*.procedura_id' => 'required|integer|min:1',
            'timesheet.edit.*.ordinativo_id' => 'required|integer|min:1',
            'timesheet.edit.*.area_id' => 'required|integer|min:1', 
            'timesheet.edit.*.gruppo_id' => 'required|integer|min:1',
            'timesheet.edit.*.ora_inizio' => 'required',
            'timesheet.edit.*.ora_fine' => 'required',
            'timesheet.edit.*.tipologia' => 'required|integer|min:0'
        ];
    }

	protected static function booted()
    {
		static::addGlobalScope('user', function (Builder $builder) {
			$builder->where('created_user_id', Auth::id());
		});
    }
    
    public function setOrdinativoIdAttribute($ordinativo_id)
    {
        $this->attributes['ordinativo_id'] = !empty($ordinativo_id) ? $ordinativo_id : null;
    }

    public function setAttivitaIdAttribute($attivita_id)
    {
        $this->attributes['attivita_id'] = !empty($attivita_id) ? $attivita_id : null; 
    }

    public function tipologia() {
        $tipologie = config('tasklist.timesheets.tipologie');
        return $tipologie[$this->tipologia];
    }

    public function durata_time()
    {
        $start_time = Carbon::parse($this->dataora_inizio);
        $finish_time = Carbon::parse($this->dataora_fine);

        $total_time = $finish_time->diffInSeconds($start_time);

        return $total_time;
    }

    public function durata_lavorativa()
    {
        return working_time($this->dataora_inizio, $this->dataora_fine, 'seconds');
    }

    public function durata($time = null)
    {
        $time = !empty($time) ? $time : $this->durata_time();

        return gmdate('H\h i\m', $time);
    }

    public function durataRaw($time = null)
    {
        $time = !empty($time) ? $time : $this->durata_time();

        return gmdate('H:i', $time);
    }

    public function ora_inizio()
    {
        return Carbon::parse($this->dataora_inizio)->format('H:i');
    }

    public function ora_fine()
    {
        return Carbon::parse($this->dataora_fine)->format('H:i');
    }

    public function setDataoraInizioAttribute($date)
    {
        $date = (strlen($date) > 18 ? "$date" : "$date:00");
        $this->attributes['dataora_inizio'] = set_datetime_ita($date);
    }

    public function setDataoraFineAttribute($date)
    {
        $date = (strlen($date) > 18 ? "$date" : "$date:00");
        $this->attributes['dataora_fine'] = set_datetime_ita($date);
    }

    public function modelFilter()
    {
        return $this->provideFilter(\Modules\Filters\Admin\Tasklist\TimesheetFilter::class);
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

    public function ordinativo()
    {
        return $this->belongsTo('Modules\Commerciale\Entities\Ordinativo', 'ordinativo_id' );
    }

    public function procedura()
    {
        return $this->belongsTo('Modules\Profile\Entities\Procedura', 'procedura_id' );
    }

    public function area()
    {
        return $this->belongsTo('Modules\Profile\Entities\Area', 'area_id' );
    }

    public function gruppo()
    {
        return $this->belongsTo('Modules\Profile\Entities\Gruppo', 'gruppo_id' );
    }

    public function attivita()
    {
        return $this->belongsTo('Modules\Tasklist\Entities\Attivita', 'attivita_id' );
    }

    public function ticket_azione()
    {
        return $this->belongsTo('Modules\Profile\Entities\RichiesteInterventoAzione', 'ticket_azione_id' );
    }
}
