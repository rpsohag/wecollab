<?php

namespace Modules\Tasklist\Entities;

//use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\Sentinel\User;
use Modules\Tasklist\Entities\Attivita;
use Auth;

use EloquentFilter\Filterable;

class Attivita extends Model
{
    use Filterable;

    protected $table = 'tasklist__attivita';
    //public $translatedAttributes = []; 

    protected $fillable = [
        'azienda',
        'richiedente_id',
        'procedura_id',
        'area_id',
        'gruppo_id',
        'supervisori_id',
        'oggetto',
        'data_assegnazione',
        'descrizione',
        'durata_valore',
        'durata_tipo',
        'data_fine',
        'data_inizio',
        'data_chiusura',
        'priorita',
        'stato',
        'fatturazione',
        'cliente_id',
        'ordinativo_id',
        'percentuale_completamento',
        'prese_visioni',
        'pinned_by',
        'requisiti',
        'opzioni',
        'created_user_id',
        'updated_user_id',
        'created_at',
        'updated_at'
    ];

    public static function getRules()
    {
        return [
            'oggetto' => 'required',
            'ordinativo_id' => 'required|integer|min:1',
            'richiedente_id' => 'required',
            'assegnatari_id' => 'required',
            'procedura_id' => 'required|integer|min:1',
            'area_id' => 'required|integer|min:1',
            'gruppo_id' => 'required|integer|min:1',
            'priorita' => 'integer',
            'durata_tipo' => 'required|integer',
            'durata_valore' => 'integer|min:0|max:31|nullable',
            'stato' => 'required',
        ];
    }

    public static function getOrdinativoRules()
    {
        return [
            'attivita.richiedente_id' => 'required',
            'attivita.assegnatari_id' => 'required',
            'attivita.priorita' => 'required|integer',
            'attivita.durata_tipo' => 'required',
            'attivita.durata_valore' => 'required|integer',
            'attivita.stato' => 'required',
        ];
    }

    public function getDataInizioAttribute($date)
    {
        return get_date_ita($date);
    }

    public function getDataFineAttribute($date)
    {
        return get_date_ita($date);
    }

    public function getDataChiusuraAttribute($date)
    {
        return get_date_ita($date);
    }

    public function getDurataValoreAttribute($durata_valore)
    {
        return (int)$durata_valore;
    }

    public function setDataInizioAttribute($date)
    {
        $this->attributes['data_inizio'] = set_date_ita($date);
    }

    public function setDataFineAttribute($date)
    {
        $this->attributes['data_fine'] = set_date_ita($date);
    }

    public function getPreseVisioniAttribute($prese_visioni)
    {
        return json_decode($prese_visioni, 1);
    }

    public function setPreseVisioniAttribute($prese_visioni)
    {
        $this->attributes['prese_visioni'] = json_encode($prese_visioni);
    }

    public function getRequisitiAttribute($requisiti)
    {
        return json_decode($requisiti, 1);
    }

    public function setRequisitiAttribute($requisiti)
    {
        $requisitii = array();
        foreach($requisiti as $key => $requisito){
            $requisitii[$requisito] = ['attivita_id' => $requisito];
          }
        $this->attributes['requisiti'] = json_encode($requisitii);
    }

    public function getPinnedByAttribute($pinned_by)
    {
        return json_decode($pinned_by, 1);
    }

    public function setPinnedByAttribute($pinned_by)
    {
        $this->attributes['pinned_by'] = json_encode($pinned_by);
    }

    public function getSupervisoriIdAttribute($supervisori_id)
    {
        return json_decode($supervisori_id, 1);
    }

    public function setSupervisoriIdAttribute($supervisori_id)
    {
        $supervisori = array();
        if(is_array($supervisori_id)){
            foreach($supervisori_id as $key => $supervisore){
                $supervisori[$supervisore] = ['user_id' => $supervisore];
            }
        } else {
            $supervisori[$supervisori_id] = ['user_id' => $supervisori_id];
        }
        $this->attributes['supervisori_id'] = json_encode($supervisori);
    }

    public function getOpzioniAttribute($opzioni)
    {
        return json_decode($opzioni, 1);
    }

    public function setOpzioniAttribute($opzioni)
    {
        $this->attributes['opzioni'] = json_encode($opzioni);
    }

    public function scopeLavorabile($query)
    {
        return $query->where('lavorabile', 'active');
    }

    public function created_user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'created_user_id')->withTrashed();
    }

    public function updated_user()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'updated_user_id')->withTrashed();
    }

    public function users() // Assegnatari
    {
        return $this->belongsToMany('Modules\User\Entities\Sentinel\User', 'tasklist__attivita_user')->withTrashed();
    }

    public function supervisori() // Supervisori
    {
        $ids = $this->supervisori_id;
        if(!empty($ids)){
          return User::withTrashed()->whereIn('id', $ids)->get();
        } else {
            return false;
        }
    }

    public function pinnedBy() // Chi ha pinnato l'attività
    {
        $ids = $this->pinned_by;
        if(!empty($ids)){
			foreach($ids as $key => $valore){
				unset($ids[$key]['data']);
			}
          return User::withTrashed()->whereIn('id', $ids)->get();
        } else {
            return false;
        }
    }

    public function preseVisioni() // Prese Visioni
    {
        $ids = collect($this->prese_visioni)->pluck('user_id')->toArray();
        if(!empty($ids)){
          return User::withTrashed()->whereIn('id', $ids)->get();
        } else {
            return false;
        }
    }

    public function partecipanti() // Partecipanti (User)
    {
        $partecipanti = array();
        $partecipanti[$this->richiedente->id] = $this->richiedente->id;
  
        if($this->supervisori()){
          foreach($this->supervisori() as $supervisore){
            if(empty($partecipanti[$supervisore->id])){       
              $partecipanti[$supervisore->id] = $supervisore->id;    
            }
          }
        }
  
        foreach($this->users as $partecipante){
          if(empty($partecipanti[$partecipante->id])){
            $partecipanti[$partecipante->id] = $partecipante->id;
          }
        }

        return User::withTrashed()->whereIn('id', $partecipanti)->get();
    }

    public function hasPresoVisione($user = null) // L'Auth user ha preso visione dell'attività
    {
        if(!empty($this->opzioni) && $this->opzioni['multi_presa_in_carico'] != 1){
            if($this->preseVisioni()){
                return true;
            }
        }

        if(is_null($user)){
            if(!empty($this->opzioni) && $this->opzioni['prese_visioni'] == 1){
                $assegnatari = $this->users;
                $prese_visioni = $this->preseVisioni();
                if($assegnatari->contains('id', Auth::user()->id)){
                    if($this->richiedente->id == Auth::id()){
                        return true;
                    }
                    if(!empty($prese_visioni)){
                        if($prese_visioni->contains('id', Auth::user()->id)){
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                } else {
                    return true;
                }
            } else {
                return true;
            }
        } else {
            $user = User::find($user);
            if(!empty($this->opzioni) && $this->opzioni['prese_visioni'] == 1){
                $assegnatari = $this->users;
                $prese_visioni = $this->preseVisioni();
                if($assegnatari->contains('id', $user->id)){
                    if($this->richiedente->id == $user->id){
                        return true;
                    }
                    if(!empty($prese_visioni)){
                        if($prese_visioni->contains('id', $user->id)){
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                } else {
                    return true;
                }
            } else {
                return true;
            }           
        }

        return true;
    }

    public function requisiti() // Requisiti Attività per i completamento
    {
        $ids = $this->requisiti;
        if(!empty($ids)){
            return Attivita::whereIn('id', $ids)->get();
        }
    }

    public function hasRequisiti() // L'attività ha tutti i requisiti
    {
        $requisiti = $this->requisiti();

        if(!empty($requisiti) && $requisiti->count() > 0){
            foreach($requisiti as $requisito){
                if($requisito->percentuale_completamento == 100){ 
                    continue; 
                } else {
                    return false;
                }
            }
        }
        
        return true;
    } 

    public function richiedente()
    {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'richiedente_id' );
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

    public function timesheet()
    {
        return $this->belongsToMany('Modules\Tasklist\Entities\Timesheet');
    }

    public function metas()
    {
        return $this->morphToMany('Modules\Wecore\Entities\Meta', 'metagable', 'wecore__metagable');
    }

    public function notes()
    {
        return $this->morphToMany('Modules\Wecore\Entities\Meta', 'metagable', 'wecore__metagable')->where('name', 'note')->orderBy('created_at', 'desc');
    }

    public function files()
    {
        return $this->morphToMany('Modules\Wecore\Entities\Meta', 'metagable', 'wecore__metagable')->where('name', 'file')->orderBy('created_at', 'desc');
    }

    public function voci()
    {
        return $this->hasMany('Modules\Tasklist\Entities\AttivitaVoce');
    }

    /*
     * Get all of the owning attivitable models.
     */
    public function attivitable()
    {
        return $this->morphTo();
    }

    public function percentuale_completamento()
    {
        return round($this->percentuale_completamento) . '%';
    }

    public function data_completamento()
    {
        if($this->percentuale_completamento == 100){
            if($this->notes()->count() > 0){
                $note = $this->notes()->where('value', 'LIKE', '%Ho completato l\'attività in data%')->first();
                return (!empty($note) ? $note->created_at : false);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function modelFilter()
    {
        return $this->provideFilter(\Modules\Filters\Admin\Tasklist\AttivitaFilter::class);
    }



}
