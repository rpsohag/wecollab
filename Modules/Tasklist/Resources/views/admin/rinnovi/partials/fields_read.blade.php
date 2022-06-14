@php

$rinnovo = (empty($rinnovo)) ? '' : $rinnovo;

$user = !empty($rinnovo) ? $rinnovo->utenti()->pluck('users.id')->toArray() : [];

   
if(!empty($rinnovo->notifiche))
{
$numero_notifiche = count($rinnovo->notifiche);
}
else
{
    $numero_notifiche = 0;
}

$clients = [-1 => ''];
$clients = $clients + $clienti;

$tipi_rinnovi = [-1 => ''] + config('tasklist.rinnovi.tipi');

$tipi_avvisi = [-1 => ''] + config('tasklist.notifiche.tipi');

$tipologie = [-1 => ''] + config('tasklist.notifiche.notifica');

@endphp

<div class="box-body">
 <div class="row">
     <div class="col-md-4">   	
        <strong>Titolo</strong>: <span class="pull-right">{{  get_if_exist($rinnovo, 'titolo')  }}</span>
        <br><br> 
        <strong>Cliente</strong>: <span class="pull-right">{{ $clients[get_if_exist($rinnovo, 'cliente_id')] }}</span>
        <br><br> 
        <strong>Data e Ora Rinnovo </strong>: <span class="pull-right">{{   get_if_exist($rinnovo, 'data')  }}</span>
        <br><br> 
        <strong>Tipo Rinnovo</strong>: <span class="pull-right">{{   $tipi_rinnovi[get_if_exist($rinnovo, 'tipo')] }}</span>
        <br><br> 
        @if(get_if_exist($rinnovo, 'ordinativo'))
            <strong>Ordinativo</strong>: <a href="{{ route('admin.commerciale.ordinativo.edit', $rinnovo->ordinativo->id) }}" class="pull-right">{{ $rinnovo->ordinativo->oggetto }}</a>
            <br><br> 
        @endif 
        <strong>Descrizione</strong>: <span class="pull-right">{{ get_if_exist($rinnovo, 'descrizione')  }}</span>
        <br><br> 
        <strong>Utenti da Notificare</strong>:
        <ul>
        @foreach ($user as $key => $value)
            <li  >{{$utenti[$value]}}</li>
        @endforeach
        </ul>
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



