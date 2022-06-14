@php

$rinnovo = (empty($rinnovo)) ? '' : $rinnovo;
$senders = (!empty($rinnovo)) ? $rinnovo->utenti()->pluck('users.id')->toArray() : [];

if(!empty($rinnovo->notifiche))
{
    $numero_notifiche = count($rinnovo->notifiche);
}
else
{
    $numero_notifiche = 0;
}

$tipi_rinnovi = [-1 => ''] + config('tasklist.rinnovi.tipi');

$tipi_avvisi = [-1 => ''] + config('tasklist.notifiche.tipi');

$tipologie = [-1 => ''] + config('tasklist.notifiche.notifica');

@endphp

<input type="hidden" name="rinnovo[cliente_id]" value="{{ $ordinativo->cliente_id }}"/>
<input type="hidden" name="rinnovo[ordinativo_id]" value="{{ $ordinativo->id }}"/>
<input type="hidden" name="rinnovo[azienda]" value="{{ session('azienda') }}"/>
<div class="box-body">
 <div class="row">
     <div class="col-md-8">
         <div class="row">
             <div class="col-md-12">
          		<b>Oggetto Rinnovo : </b>      {{  get_if_exist($rinnovo, 'titolo')  }}
             </div>
             <div class="col-md-12">
                 <b>Data Rinnovo : </b>  {{  get_if_exist($rinnovo, 'data') }}
             </div>
             <div class="col-md-12">
		         <b>Descrizione Rinnovo: </b> {{   get_if_exist($rinnovo, 'descrizione')  }}
		     </div>
             
             <div class="col-md-12">
                <b>Utenti a cui Notificare : </b>
                <ul>
	            	@foreach($senders as $val)
                		<li>{{ $utenti[$val]}}</li>
                	@endforeach  
             	</ul>
             </div>
         </div>
     </div>
     
 </div>

 <div class="row">
    <div class="col-md-12">
        <!-- /.box -->
        <div class="box box-success box-shadow">
            <div class="box-header with-border">
               <h3 class="box-title">Notifiche</h3>
               <!-- tools box -->
               <div class="box-tools pull-right">
                   <span data-toggle="tooltip" title="" class="badge bg-green" data-original-title="{{ $numero_notifiche }} Attività">{{ $numero_notifiche }}</span>
                   <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
               </div>
               <!-- /. tools -->
             </div>
             <!-- /.box-header -->
             <div class="box-body">
             	
             	 @if (!empty($rinnovo->notifiche) && count($rinnovo->notifiche) > 0)
                 	<ul>
                     @foreach ($rinnovo->notifiche as $key => $notifica)
                         <li >
                       	 	@if ($tipologie[get_if_exist($notifica, 'notifica')] == 'email')
                           		{{ 'Invia E-Mail ' .get_if_exist($notifica, 'cadenza')   .' '.  $tipi_avvisi[get_if_exist($notifica, 'tipo')] }}
                           	 @else
                           		{{ 'Sul Portale  ' .get_if_exist($notifica, 'cadenza')   .' '.  $tipi_avvisi[get_if_exist($notifica, 'tipo')] }}
                         	 @endif
                         </li>
                    @endforeach
                     </ul>
                @else
             	
             	
             	
            
                    <div class="callout callout-danger">
                        <h4>ATTENZIONE!</h4>
                        <p>Non sono state impostate notifiche per questo rinnovo.</p>
                    </div>
                @endif
             </div>
             
           </div>
         </div>
         <!-- /.col-->
       </div>
</div>
