<?php

namespace Modules\User\Entities\Sentinel;

use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Users\EloquentUser;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Modules\User\Entities\UserInterface;
use Modules\User\Entities\UserToken;
use Modules\User\Presenters\UserPresenter;
use Modules\User\Entities\Gruppo;
use Modules\Profile\Entities\Autovettura;
use Ohswedd\Presenter\PresentableTrait;
use Modules\Tasklist\Entities\Timesheet;
use Modules\User\Entities\Area;
use Modules\Assistenza\Entities\RichiesteInterventoAzione;
use Modules\Assistenza\Entities\TicketIntervento;
use Modules\Assistenza\Entities\RichiesteIntervento;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Auth;
use DB;

use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cartalyst\Sentinel\Native\Facades\Sentinel;

class User extends EloquentUser implements UserInterface, AuthenticatableContract
{
    use HasApiTokens, Authenticatable, PresentableTrait, SoftDeletes, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'timesheets_report',
        'switch_is_active',
        'ore_lavorative_settimanali',
        'costo_interno',
        'importo_di_vendita',
        'responsabile_id',
        'permissions',
        'first_name',
        'last_name',
    ];

    protected $appends = [
        'full_name'
    ];

    /**
     * {@inheritDoc}
     */
    protected $loginNames = ['email'];

    protected $presenter = UserPresenter::class;

    public function __construct(array $attributes = [])
    {
        $this->loginNames = config('asgard.user.config.login-columns');
        $this->fillable = config('asgard.user.config.fillable');
        if (config()->has('asgard.user.config.presenter')) {
            $this->presenter = config('asgard.user.config.presenter', UserPresenter::class);
        }
        if (config()->has('asgard.user.config.dates')) {
            $this->dates = config('asgard.user.config.dates', []);
        }
        if (config()->has('asgard.user.config.casts')) {
            $this->casts = config('asgard.user.config.casts', []);
        }

        $this->addGlobalScope('ordine', function($query) {
            $query->orderByRaw("CONCAT (first_name, ' ', last_name) asc");
        });
        parent::__construct($attributes);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * @inheritdoc
     */
    public function hasRoleId($roleId)
    {
        return $this->roles()->whereId($roleId)->count() >= 1;
    }

    /**
     * @inheritdoc
     */
    public function hasRoleSlug($slug)
    {
        return $this->roles()->whereSlug($slug)->count() >= 1;
    }

    /**
     * @inheritdoc
     */
    public function hasRoleName($name)
    {
        return $this->roles()->whereName($name)->count() >= 1;
    }

    /**
     * @inheritdoc
     */
    public function isActivated()
    {
        if (Activation::completed($this)) {
            return true;
        }

        return false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function api_keys()
    {
        return $this->hasMany(UserToken::class);
    }

    /**
     * @inheritdoc
     */
    public function getFirstApiKey()
    {
        $userToken = $this->api_keys->first();

        if ($userToken === null) {
            return '';
        }

        return $userToken->access_token;
    }

    public function __call($method, $parameters)
    {
        #i: Convert array to dot notation
        $config = implode('.', ['asgard.user.config.relations', $method]);

        #i: Relation method resolver
        if (config()->has($config)) {
            $function = config()->get($config);
            $bound = $function->bindTo($this);

            return $bound();
        }

        #i: No relation found, return the call to parent (Eloquent) to handle it.
        return parent::__call($method, $parameters);
    }

    /**
     * @inheritdoc
     */
    public function hasAccess($permission)
    {
        $permissions = $this->getPermissionsInstance();

        return $permissions->hasAccess($permission);
    }

    // Get profile
    public function profile()
    {
        return $this->hasOne(\Modules\Profile\Entities\Profile::class, 'user_id', 'id');
    }

    public function gruppi()
    {
        return $this->belongsToMany('Modules\Profile\Entities\Gruppo', 'profile__gruppo_user');
    }

    public function aree()
    {
        return $this->belongsToMany('Modules\Profile\Entities\Area', 'profile__area_user');
    }

    public function attivita() 
    {
        return $this->belongsToMany('Modules\Tasklist\Entities\Attivita', 'tasklist__attivita_user');
    }

  	public  static function elencoCommerciali()
    {
        $role = Sentinel::findRoleBySlug('commerciale');

        return $role->users()->with('roles')->get();
    }
    
    public function logs($data_inizio = null, $data_fine = null)
    {
            return $this->hasMany('Spatie\Activitylog\Models\Activity', 'causer_id')->where([['description', '<>', 'scheduled']])->when(!is_null($data_inizio), function ($query) use ($data_inizio) {
                $query->whereDate('updated_at', '>=', $data_inizio);
            })->when(!is_null($data_fine), function ($query) use ($data_fine) {
                $query->whereDate('updated_at', '<=', $data_fine);
            });
    }

    public function logs_weekly_reports($user_id = 1)
    {
        // Week's Array
        $dates = collect();
        foreach( range( -6, 0 ) AS $i ) {
            $date = Carbon::now()->addDays( $i )->format('Y-m-d');
            $dates->put( $date, 0);
        }

        // Count daily operations based on a week.
        $logs = Activity::where( 'updated_at', '>=', $dates->keys()->first() )
            ->where([['description', '<>', 'scheduled']])
            ->where( 'causer_id', $user_id)
            ->groupBy( 'date' )
            ->get( [
                 // DB::raw( "upper(DATE_FORMAT(updated_at,'%W %e %M')) as date" ),
                 DB::raw( "DATE(updated_at) as date" ),
                 DB::raw( 'COUNT( * ) as "count"' )
             ] )
             ->pluck( 'count', 'date' );

        if(!empty($logs)){
            return $logs;
        }
    }

    public function logs_weekly_reports_collection($collection_logs)
    {
        // Week's Array
        $dates = collect();
        foreach( range( -6, 0 ) AS $i ) {
            $date = Carbon::now()->addDays( $i )->format('Y-m-d');
            $dates->put( $date, 0);
        }

        // Count daily operations based on a week.
        $logs = $collection_logs->where('created_at', '>=', $dates->keys()->first())
                                ->groupBy(function($item){
                                    return Carbon::createFromFormat('Y-m-d H:i:s', $item->created_at)->format('Y-m-d');
                                });

        if(!empty($logs)){
            return $logs;
        }
    }

    public function supervisionati() 
    {
        $utenti = User::all();
        $primo_livello = $utenti->where('responsabile_id', Auth::id());
        /* if($primo_livello->count() <= 0){
            $ciclo = false;
            while($ciclo == false){
                foreach($primo_livello as $utente){
                    if($utenti->where('responsabile_id', $utente->id)->count() > 0){
                        $primo_livello = $primo_livello->merge($utenti->where('responsabile_id', $utente->id));
                    } else {
                        $ciclo = true;
                    }
                }
            }
        } */
        return $primo_livello->push(Auth::user());
    }

    public function tickets($data_inizio = null, $data_fine = null, $type = "chiusi", $cliente_id = null, $area_id = null)
    {

        if($type == "chiusi") {
            $tickets = RichiesteIntervento::whereHas('ultima_azione', function ($query) {
                                            $query->where('created_user_id', $this->id)->where('tipo', 3);
                                        });
        }

        if($type == "lavorati") {
            $tickets = RichiesteIntervento::whereHas('ultima_azione', function ($query) {
                $query->where('created_user_id', $this->id)->where('tipo', '<>' , 3);
            });            
        }

        if($type == "totali") {
            $tickets = RichiesteIntervento::whereHas('ultima_azione', function ($query) {
                $query->where('created_user_id', $this->id);
            });            
        }

        return $tickets->when(!empty((int)$cliente_id), function ($query) use ($cliente_id) {
                                $query->where('cliente_id', (int)$cliente_id);
                            })->when(!empty((int)$area_id), function ($query) use ($area_id) {
                                $query->where('area_id', (int)$area_id);
                            })->when(!is_null($data_inizio), function ($query) use ($data_inizio) {
                                $query->whereDate('updated_at', '>=', $data_inizio);
                            })->when(!is_null($data_fine), function ($query) use ($data_fine) {
                                $query->whereDate('updated_at', '<=', $data_fine);
                            });
    }

    public function tempo_lavorazione($data_inizio = null, $data_fine = null, $type = "totale", $cliente_id = null, $area_id = null)
    {
        $tempo = 0;
         foreach($this->tickets($data_inizio, $data_fine, "chiusi", $cliente_id, $area_id)->get() as $ticket){
            $azioni = RichiesteInterventoAzione::where('created_user_id', $this->id)->where('tipo', '<>', 6)->where('tipo', '<>', 4)->select(\DB::raw('SUM(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as diff'))->where('ticket_id', $ticket->id)->get();

            if(!empty($azioni)){
                $tempo = $tempo + $azioni->first()->diff;
            }
        } 

        $hours = $tempo / 3600;
        $days = floor($hours / 8);
        $halfday = ($hours - $days * 8) / 4.5;
        $days += $halfday < 1 ? 0.5: 1;
        
        $days = (float)$days;
        
        if($days < 1 ){
          $result = 'Mezza giornata lavorativa.';
        } else {
            $days_splitted = explode(',', $days);
            if(!empty($days_splitted[0]) && $days_splitted[0] == 1){
                $result = $days_splitted[0] . ' Giorno Lavorativo';
            } else {
                $result = $days_splitted[0] . ' Giorni Lavorativi';
            }
            if(!empty($days_splitted[1]) && $days_splitted[1]){
                $result .= ' e mezzo';
            }
        }

        if($tempo == 0)
            $result = "";
  
        return $result;
    }

    public function setCostoInternoAttribute($costo_interno)
    {
        $this->attributes['costo_interno'] = clean_currency($costo_interno);
    }

    public function getCostoInternoAttribute($costo_interno)
    {
        return get_currency($costo_interno);
    }

    public function setImportoDiVenditaAttribute($importo_di_vendita)
    {
         $this->attributes['importo_di_vendita'] = clean_currency($importo_di_vendita);
    }

    public function getImportoDiVenditaAttribute($importo_di_vendita)
    {
        return get_currency($importo_di_vendita);
    }

    public function timesheets($data = null, $cliente_id = null, $area_id = null, $data_inizio = null, $data_fine = null)
    {

        if(empty($data_inizio) || empty($data_fine)){

            if(!empty($data)){
                if(!empty($cliente_id) && !empty($area_id)){
                    return $this->hasMany('Modules\Tasklist\Entities\Timesheet', 'created_user_id')->where('cliente_id', $cliente_id)->where('area_id', $area_id)->whereDate('dataora_fine', '=', $data)->withoutGlobalScope('user');
                } elseif(!empty($cliente_id) && empty($area_id))  {
                    return $this->hasMany('Modules\Tasklist\Entities\Timesheet', 'created_user_id')->where('cliente_id', $cliente_id)->whereDate('dataora_fine', '=', $data)->withoutGlobalScope('user');
                } elseif(empty($cliente_id) && !empty($area_id))  {
                    return $this->hasMany('Modules\Tasklist\Entities\Timesheet', 'created_user_id')->where('area_id', $area_id)->whereDate('dataora_fine', '=', $data)->withoutGlobalScope('user');
                } else {
                    return $this->hasMany('Modules\Tasklist\Entities\Timesheet', 'created_user_id')->whereDate('dataora_fine', '=', $data)->withoutGlobalScope('user');
                }
            }
            
            if(!empty($cliente_id) && !empty($area_id)){
                return $this->hasMany('Modules\Tasklist\Entities\Timesheet', 'created_user_id')->where('cliente_id', $cliente_id)->where('area_id', $area_id)->withoutGlobalScope('user');
            } elseif(!empty($cliente_id) && empty($area_id))  {
                return $this->hasMany('Modules\Tasklist\Entities\Timesheet', 'created_user_id')->where('cliente_id', $cliente_id)->withoutGlobalScope('user');
            } elseif(empty($cliente_id) && !empty($area_id))  {
                return $this->hasMany('Modules\Tasklist\Entities\Timesheet', 'created_user_id')->where('area_id', $area_id)->withoutGlobalScope('user');
            } else {
                return $this->hasMany('Modules\Tasklist\Entities\Timesheet', 'created_user_id')->withoutGlobalScope('user');
            }
        } else {

            $tmp_data_fine = new \Carbon\Carbon($data_fine);
            $data_fine = $tmp_data_fine->addDay(1)->format('Y-m-d'); 
            
            if(!empty($cliente_id) && !empty($area_id)){
                return $this->hasMany('Modules\Tasklist\Entities\Timesheet', 'created_user_id')->where('cliente_id', $cliente_id)->where('area_id', $area_id)->whereBetween('dataora_fine', [$data_inizio, $data_fine])->withoutGlobalScope('user');
            } elseif(!empty($cliente_id) && empty($area_id))  {
                return $this->hasMany('Modules\Tasklist\Entities\Timesheet', 'created_user_id')->where('cliente_id', $cliente_id)->whereBetween('dataora_fine', [$data_inizio, $data_fine])->withoutGlobalScope('user');
            } elseif(empty($cliente_id) && !empty($area_id))  {
                return $this->hasMany('Modules\Tasklist\Entities\Timesheet', 'created_user_id')->where('area_id', $area_id)->whereBetween('dataora_fine', [$data_inizio, $data_fine])->withoutGlobalScope('user');
            } else {
                return $this->hasMany('Modules\Tasklist\Entities\Timesheet', 'created_user_id')->whereBetween('dataora_fine', [$data_inizio, $data_fine])->withoutGlobalScope('user');
            }     
        }
    }

    public function tempo_timesheets($user_id = null, $cliente_id = null, $area_id = null, $data_inizio = null, $data_fine = null)
    {

        $tmp_data_fine = new \Carbon\Carbon($data_fine);
        $data_fine = $tmp_data_fine->addDay(1)->format('Y-m-d'); 

        $timesheets = Timesheet::whereBetween('dataora_fine', [$data_inizio, $data_fine])
                    ->withoutGlobalScope('user')
                    ->where( 'created_user_id', $user_id)
                    ->groupBy( 'date' );

        if($cliente_id)
            $timesheets->where('cliente_id', $cliente_id);

        if($area_id)
            $timesheets->where('area_id', $area_id);

        $timesheets = $timesheets->get( ['id',
                    DB::raw( "DATE(dataora_fine) as date" ),
                    DB::raw( 'SUM(TIMESTAMPDIFF(SECOND, dataora_inizio, dataora_fine)) as diff' )
                ] )
                ->sortByDesc('date');
        return $timesheets->take(31);
    }


    public function rapporti_weekly_reports($data = null, $user_id = null, $cliente_id = null, $area_id = null)
    {
        // Week's Array
        $dates = collect();
        foreach( range( -6, 0 ) AS $i ) {
            $date = Carbon::now()->addDays( $i )->format('Y-m-d');
            $dates->put( $date, 0);
        }

        // Count daily timesheets worked time based on a week.
        if(!empty($cliente_id) && !empty($area_id)) {
            $rapporti = TicketIntervento::where( 'created_at', '>=', $dates->keys()->first() )
                ->withoutGlobalScope('user')
                ->where( 'created_user_id', $user_id)
                ->where( 'cliente_id', $cliente_id)
                ->where( 'area_di_intervento_id', $area_id)
                ->groupBy( 'date' )
                ->get( [
                    DB::raw( "DATE(updated_at) as date" ),
                    DB::raw( 'COUNT( * ) as "count"' )
                ] )
                ->pluck( 'count', 'date' );
        } elseif(!empty($cliente_id) && empty($area_id)) {
            $rapporti = TicketIntervento::where( 'created_at', '>=', $dates->keys()->first() )
                ->withoutGlobalScope('user')
                ->where( 'created_user_id', $user_id)
                ->where( 'cliente_id', $cliente_id)
                ->groupBy( 'date' )
                ->get( [
                    DB::raw( "DATE(updated_at) as date" ),
                    DB::raw( 'COUNT( * ) as "count"' )
                ] )
                ->pluck( 'count', 'date' );
        } elseif(empty($cliente_id) && !empty($area_id)) {
            $rapporti = TicketIntervento::where( 'created_at', '>=', $dates->keys()->first() )
                ->withoutGlobalScope('user')
                ->where( 'created_user_id', $user_id)
                ->where( 'area_di_intervento_id', $area_id)
                ->groupBy( 'date' )
                ->get( [
                    DB::raw( "DATE(updated_at) as date" ),
                    DB::raw( 'COUNT( * ) as "count"' )
                ] )
                ->pluck( 'count', 'date' );
        } else {
            $rapporti = TicketIntervento::where( 'created_at', '>=', $dates->keys()->first() )
                ->withoutGlobalScope('user')
                ->where( 'created_user_id', $user_id)
                ->groupBy( 'date' )
                ->get( [
                    DB::raw( "DATE(updated_at) as date" ),
                    DB::raw( 'COUNT( * ) as "count"' )
                ] )
                ->pluck( 'count', 'date' );            
        }

        return $rapporti;
    }

    public function rapporti($data = null, $gruppo_id = null, $cliente_id = null, $area_id = null)
    {
        if(!empty($data)){
            if(!empty($gruppo_id) && !empty($cliente_id) && !empty($area_id)){
              return $this->hasMany('Modules\Assistenza\Entities\TicketIntervento', 'created_user_id')->whereDate('updated_at', '>=', $data)->where('gruppo_id', $gruppo_id)->where('cliente_id', $cliente_id)->where('area_di_intervento_id', $area_id);
            } elseif(!empty($gruppo_id) && empty($cliente_id) && empty($area_id)) {
                return $this->hasMany('Modules\Assistenza\Entities\TicketIntervento', 'created_user_id')->whereDate('updated_at', '>=', $data)->where('gruppo_id', $gruppo_id);
            } elseif(empty($gruppo_id) && empty($area_id) && !empty($cliente_id)) {
                return $this->hasMany('Modules\Assistenza\Entities\TicketIntervento', 'created_user_id')->whereDate('updated_at', '>=', $data)->where('cliente_id', $cliente_id);
            } elseif(empty($gruppo_id) && !empty($area_id) && empty($cliente_id)) {
                return $this->hasMany('Modules\Assistenza\Entities\TicketIntervento', 'created_user_id')->whereDate('updated_at', '>=', $data)->where('area_di_intervento_id', $area_id);
            } else {
                return $this->hasMany('Modules\Assistenza\Entities\TicketIntervento', 'created_user_id')->whereDate('updated_at', '>=', $data);
            }
        } else {
            if(!empty($gruppo_id) && !empty($cliente_id) && !empty($area_id)){
                return $this->hasMany('Modules\Assistenza\Entities\TicketIntervento', 'created_user_id')->where('gruppo_id', $gruppo_id)->where('cliente_id', $cliente_id)->where('area_di_intervento_id', $area_id);
              } elseif(!empty($gruppo_id) && empty($cliente_id) && empty($area_id)) {
                  return $this->hasMany('Modules\Assistenza\Entities\TicketIntervento', 'created_user_id')->where('gruppo_id', $gruppo_id);
              } elseif(empty($gruppo_id) && empty($area_id) && !empty($cliente_id)) {
                  return $this->hasMany('Modules\Assistenza\Entities\TicketIntervento', 'created_user_id')->where('cliente_id', $cliente_id);
              } elseif(empty($gruppo_id) && !empty($area_id) && empty($cliente_id)) {
                  return $this->hasMany('Modules\Assistenza\Entities\TicketIntervento', 'created_user_id')->where('area_di_intervento_id', $area_id);
              } else {
                  return $this->hasMany('Modules\Assistenza\Entities\TicketIntervento', 'created_user_id');
              }
        }
    }

	public function autovetture() 
    {
		return $this->hasMany(Autovettura::class, 'user_id','id');
    }

}

