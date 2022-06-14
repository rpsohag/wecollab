<?php

namespace Modules\Assistenza\Entities;

// use Astrotomic\Translatable\Translatable;
use Modules\Assistenza\Entities\RichiesteInterventoAzione;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use EloquentFilter\Filterable;
use Illuminate\Http\Request;

use Auth;
use Modules\User\Entities\Sentinel\User;

use Notification;
use App\Notifications\newTicketAssegnatari;
use App\Notifications\newTicketClienteButton;

class RichiesteIntervento extends Model
{
    // use Translatable;
    use Filterable;

    protected $table = 'assistenza__richiesteinterventi';
    // public $translatedAttributes = [];
    protected $fillable = [
      'azienda',
      'numero',
      'cliente_id',
      'indirizzo_id',
      'procedura_id',
      'area_id',
      'gruppo_id',
      'ordinativo_id',
      'oggetto',
      'descrizione_richiesta',
      'livello_urgenza',
      'motivo_urgenza',
      'richiedente',
      'numero_da_richiamare',
      'email',
      'stato',
      'message_id',
      'created_user_id',
      'updated_user_id',
      'statistiche'
    ];

    public static function getRules()
    {
        return [
            'cliente_id' => 'required|integer|min:1|not_in:81',
            //'indirizzo_id' => 'min:1',
            'procedura_id' => 'required|integer|min:1',
            'area_id' => 'required|integer|min:1',
            'gruppo_id' => 'required|integer|min:1',
            'ordinativo_id' => 'required|integer|min:1',
            'oggetto' => 'required',
            'motivo_urgenza' => 'required_if:livello_urgenza,1,2',
            //'richiedente' => 'required_without_all:numero_da_richiamare,email',
            //'numero_da_richiamare' => 'required_without_all:richiedente,email',
            //'email' => 'required_without_all:richiedente,numero_da_richiamare|nullable|email',
            'email' => 'required|email',
            'destinatario_id' => 'required'
        ];
    }

    public static function getRulesEmail()
    {
        return [
            'cliente_id' => 'required|integer|min:1',
            'indirizzo_id' => 'min:1',
            'procedura_id' => 'required|integer|min:1',
            'area_id' => 'required|integer|min:1',
            'gruppo_id' => 'required|integer|min:1',
            'oggetto' => 'required',
            'motivo_urgenza' => 'required_if:livello_urgenza,1,2',
            //'richiedente' => 'required_without_all:numero_da_richiamare,email',
            //'numero_da_richiamare' => 'required_without_all:richiedente,email',
            //'email' => 'required_without_all:richiedente,numero_da_richiamare|nullable|email',
            'email' => 'required',
            'destinatario_id' => 'required'
        ];
    }

    public function modelFilter()
    {
        return $this->provideFilter(\Modules\Filters\Admin\Assistenza\RichiesteInterventoFilter::class);
    }

    public function getCodiceAttribute()
    {
        $anno = date('Y', strtotime($this->created_at));

        return sprintf('%05d', $this->numero) . '/' . $anno;
    }

    public function setStatisticheAttribute($statistiche)
    {
        return $this->attributes['statistiche'] = json_encode($statistiche);
    }

    public function getStatisticheAttribute($statistiche)
    {
        return json_decode($statistiche, 1);
    }

    public static function get_next_numero()
    {
        $numero = RichiesteIntervento::max('numero');

        if(empty($numero))
          $numero = 0;

        return ($numero + 1);
    }

    public function metas()
    {
        return $this->morphToMany('Modules\Wecore\Entities\Meta', 'metagable', 'wecore__metagable');
    }

    public function destinatari()
    {
        return $this->belongsToMany('Modules\User\Entities\Sentinel\User', 'assistenza__richiesteinterventi_user');
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

    public function indirizzo()
    {
        return $this->belongsTo('Modules\Amministrazione\Entities\ClienteIndirizzi', 'indirizzo_id' );
    }

    public function area()
    {
        return $this->belongsTo('Modules\Profile\Entities\Area', 'area_id' );
    }

    public function gruppo()
    {
        return $this->belongsTo('Modules\Profile\Entities\Gruppo', 'gruppo_id' );
    }

    public function azioni()
    {
        return $this->hasMany('Modules\Assistenza\Entities\RichiesteInterventoAzione', 'ticket_id');
    }

    public function files()
    {
        return $this->morphToMany('Modules\Wecore\Entities\Meta', 'metagable', 'wecore__metagable')->where('name', 'file')->orderBy('created_at', 'desc');
    }

    public function ultima_azione()
    {
      return $this->hasMany('Modules\Assistenza\Entities\RichiesteInterventoAzione', 'ticket_id')->latest();
    }

    public function checkLavoro()
    {
      if(auth_user()->hasAccess('assistenza.richiesteinterventi.admin'))
        return 1;

      //Fase 1° Verifico se il ticket è mio assegnato o [del mio gruppo] e non è chiuso
      $ticket_corrente = $this;
      $ticket_azioni = $ticket_corrente->azioni()->get();

      //verifico Se è Assegnato almeno a 1 persona che non sono io
      foreach($ticket_azioni as $azione_1)
      {
        if($azione_1->tipo == 1 && $azione_1->created_user_id != Auth::id())
          return 0;
      }

      //verifico Se è CHIUSO
      foreach($ticket_azioni as $azione_2)
      {
        if($azione_2->tipo == 3)
          return 0;
      }

      //area ticket o Assegnatario
      $assegnatari_ticket = $ticket_corrente->destinatari->pluck('id')->toArray();

      if(in_array(Auth::id(),$assegnatari_ticket))
        return 1;
      else
        return 0;
    }

    //LO USIAMO NEL EDIT
    public function checkinLavorazione()
    {
      //Fase 1° Verifico se il ticket ha lo statato a 1
      $ticket_corrente = $this;
      $chiuso = $ticket_corrente->azioni->where('tipo',3)->first();
      $ticket_azioni = null;

      if(empty($chiuso))
        $ticket_azioni = $ticket_corrente->azioni
                                        ->where('tipo',1)
                                        ->where('created_user_id', Auth::id())
                                        ->first();

      return $ticket_azioni;
    }

    // LO USIAMO NEL EDIT
    public function checkinSospeso()
    {
       //Fase 1° Verifico se il ticket ha lo statato a 4
       $ticket_corrente = $this;
       $ticket_azioni = $ticket_corrente->azioni->toArray();

      if(!empty($ticket_azioni) && count($ticket_azioni) > 1)
      {
        $test_mate = count($ticket_azioni)-2;

        $ticket_azioni = RichiesteInterventoAzione::find($ticket_azioni[$test_mate]['id']);

        return $ticket_azioni;
      }
      else
      {
        return null;
      }
    }

    public function get_stato_text()
    {
        $azione = RichiesteInterventoAzione::where('ticket_id', $this->id)->orderBy('id', 'DESC')->first();
        $stato = "Aperto";
        if(!empty($azione)){
            $azione = $azione->tipo;
            if($azione == 2 || $azione == 5 || $azione == 6 || $azione == 7){ $stato = "In Lavorazione";}
            if($azione == 3){ $stato = "Chiuso";}
            if($azione == 4){ $stato = "Sospeso";}
        }

        return $stato;
    }

    public function get_stato_integer()
    {
        $azione = RichiesteInterventoAzione::where('ticket_id', $this->id)->orderBy('id', 'DESC')->first();
        if(!empty($azione)){
            $stato = $azione->tipo;
        } else {
            $stato = null;
        }

        return $stato;
    }

    public function possoLavorarlo()
    {
      $ticket_corrente = $this;
      //Se io sono nei destinatari
      $destinatari_ticket = $ticket_corrente->destinatari->pluck('id')->toArray();

      if(in_array(Auth::id(),$destinatari_ticket))
      {
        //Se è chiuso
        if(!empty($ticket_corrente->azioni->where('tipo',3)->first()))
          return 0;

        //Se non è in lavorazione
        if(!empty($ticket_corrente->azioni->where('tipo',1)->where('created_user_id', '<>', Auth::id())->first()))
          return 0;

        return 1;
      }
      else
          return 0;
    }

    public static function create_ticket_web(Request $request, Bool $getfile=false) {

      $rules = RichiesteIntervento::getRulesEmail();
      $validator = Validator::make($request -> all(), $rules);
      if ($validator -> fails()) {
        return response() -> json($validator -> errors());
      }

      $user_segreteria = User::find(82); // utente di default: segreteria

      $create = $request -> all();
      $create['azienda'] = 'We-COM';
      $create['numero'] = RichiesteIntervento::get_next_numero();
      $create['created_user_id'] = $user_segreteria->id;
      $create['updated_user_id'] = $user_segreteria->id;

      $richiestaintervento = RichiesteIntervento::create($create);

      $create['destinatario_id'] = json_decode($create['destinatario_id']);

      //SALVO I DESTINATARI  da prendere
      $richiestaintervento -> destinatari() -> sync($create['destinatario_id']);

      // Files
      if (!empty($create['files']) )
      {
        foreach ($create['files'] as $key => $file)
        {
          if ($getfile)
          {        
            file_save('assistenza', $richiestaintervento, $request, '', $key);
          }
          else
          {
            file_save('assistenza', $richiestaintervento, $request, '', 'files.' . $key);
          }
        }
      }
		
      if((bool) $richiestaintervento->gruppo->notifiche)
      {

          // Email nuovi destinatari
          foreach($richiestaintervento->destinatari()->get() as $destinatario)
          {
              $richiestaintervento->email_oggetto = 'Ticket assistenza - ' . $richiestaintervento->cliente->ragione_sociale . ' | ' . $richiestaintervento->codice;
              $richiestaintervento->assegnato_da = $user_segreteria->full_name;
              $destinatario->notify(new newTicketAssegnatari($richiestaintervento));
          }
          
      }

      //Email contatto
      $contatto_email = $create['email'];
      $oggetto = 'Ticket assistenza - ' . $richiestaintervento->cliente->ragione_sociale . ' | ' . $richiestaintervento->codice;
      $richiestaintervento->email_oggetto = $oggetto;
      $richiestaintervento->assegnato_da = $user_segreteria->full_name;
      if(!empty($create['tipologia_assistenza']) && $create['tipologia_assistenza'] == 1)
	    {
        $oggetto = 'Ticket assistenza - ' . $richiestaintervento->ordinativo->assistenza_per . ' | ' . $richiestaintervento->codice;
        $richiestaintervento->email_oggetto = $oggetto;
        $data = array(); 
        $data['assistenza_per'] = $richiestaintervento->ordinativo->assistenza_per;
        $data['numero_ticket'] = $richiestaintervento->numero . '/' . date('Y', strtotime($richiestaintervento->created_at));
        $data['nominativo'] = $richiestaintervento->richiedente;
        $data['oggetto'] = $richiestaintervento->oggetto;
		    if(!empty($richiestaintervento->ordinativo->assistenza))
		    {
			    foreach($richiestaintervento->ordinativo->assistenza as $k => $v)
			    {
				    if($v->gruppo_id == $richiestaintervento->gruppo_id)
				    {
					    $data['area'] = ucfirst($v->descrizione);
					    break;
				    }
			    }
	  	  } 
	    	else 
		    {
		      $data['area'] = "";
	  	  }
        $data['motivo_urgenza'] = $richiestaintervento->motivo_urgenza;
        $data['email'] = $richiestaintervento->email;
        $data['descrizione'] = $richiestaintervento->descrizione_richiesta;
        $data['numero'] = $richiestaintervento->numero_da_richiamare;
        $data['link'] = "https://www.we-com.it/assistenza/viewtk/".base64_encode(openssl_encrypt("aa".$richiestaintervento->id."aa","AES-128-ECB","W3collabbe!!2021"))."/";
        $richiestaintervento->url = $data['link'];
        Notification::route('mail', $contatto_email)->notify(new newTicketClienteButton($richiestaintervento));
      	} 
		else 
		{
			$oggetto = 'Ticket assistenza - ' . $richiestaintervento->cliente->ragione_sociale . ' | ' . $richiestaintervento->codice;
      $richiestaintervento->email_oggetto = $oggetto;
      $data = array(); 
      $data['assistenza_per'] = $richiestaintervento->gruppo->nome;
      $data['numero_ticket'] = $richiestaintervento->numero . '/' . date('Y', strtotime($richiestaintervento->created_at));
      $data['nominativo'] = $richiestaintervento->richiedente;
      $data['oggetto'] = $richiestaintervento->oggetto;
      $data['area'] = $richiestaintervento->gruppo->nome;
      $data['motivo_urgenza'] = $richiestaintervento->motivo_urgenza;
      $data['email'] = $richiestaintervento->email;
      $data['descrizione'] = $richiestaintervento->descrizione_richiesta;
      $data['numero'] = $richiestaintervento->numero_da_richiamare;
      $data['link'] = "https://www.we-com.it/assistenza/viewtk/".base64_encode(openssl_encrypt("aa".$richiestaintervento->id."aa","AES-128-ECB","W3collabbe!!2021"))."/";
      $richiestaintervento->url = $data['link'];
      Notification::route('mail', $contatto_email)->notify(new newTicketClienteButton($richiestaintervento));
    }

    // Log
    activity('We-COM-Web') -> performedOn($richiestaintervento) -> withProperties(json_encode($create)) -> log('created');
    $create['ticket_id'] = $richiestaintervento->id;
    $create['ntkt'] = $create['numero'];
    return $create;

  }
}


