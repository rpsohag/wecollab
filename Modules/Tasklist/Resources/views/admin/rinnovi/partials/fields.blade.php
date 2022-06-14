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
     <div class="col-md-8">
         <div class="row">
             <div class="col-md-8">
                {{ Form::weText('titolo', 'Titolo *', $errors, get_if_exist($rinnovo, 'titolo')) }}
             </div>
             <div class="col-md-4">
                 {{ Form::weSelectSearch('cliente_id', 'Cliente *', $errors , $clients , get_if_exist($rinnovo, 'cliente_id')) }}
             </div>
             <div class="col-md-6">
                 {{ Form::weDatetime('data', 'Data e Ora Rinnovo *', $errors , get_if_exist($rinnovo, 'data')) }}
             </div>
             <div class="col-md-6">
                {{ Form::weSelectSearch('tipo', 'Tipo Rinnovo *', $errors,  $tipi_rinnovi , get_if_exist($rinnovo, 'tipo')) }}
             </div>
         </div>
     </div>
     <div class="col-md-4">
         {{ Form::weTextarea('descrizione', 'Descrizione', $errors, get_if_exist($rinnovo, 'descrizione')) }}
     </div>
 </div>

 <div class="row">
     <div class="col-md-12">
        {{ Form::weTags('utenti', 'Utenti da Notificare', $errors,  $utenti , $user  ) }}
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
                     @foreach ($rinnovo->notifiche as $key => $notifica)
                         <div class="row">
                            <div class="col-md-4">
                                {{ Form::weSelect('notifiche[' . $notifica->id . '][notifica]', 'Notifica *', $errors,  $tipologie, get_if_exist($notifica, 'notifica')) }}
                            </div>
                            <div class="col-md-2">
                                {{ Form::weInt('notifiche['. $notifica->id. '][cadenza]', 'Cadenza *', $errors, get_if_exist($notifica, 'cadenza')) }}
                            </div>
                            <div class="col-md-4">
                               {{ Form::weSelect('notifiche['. $notifica->id. '][tipo]', 'Info Avviso *', $errors,  $tipi_avvisi, get_if_exist($notifica, 'tipo')) }}
                            </div>
                            <div class="col-md-2 text-center">
                                <br>
                                <button class="btn btn-md btn-flat btn-danger" type="button" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.tasklist.rinnovonotifica.destroy', [$notifica->id]) }}"><i class="fa fa-trash"></i></button>
                            </div>
                        </div>
                        <hr>
                    @endforeach
                @else
                    <div class="callout callout-danger">
                        <h4>ATTENZIONE!</h4>
                        <p>Non sono state impostate notifiche per questo rinnovo.</p>
                    </div>
                @endif
             </div>
             <div class="box-footer">
                 <h4>Aggiungi Notifica</h4>
                 <div class="row">
                    <div class="col-md-4">
                        {{ Form::weSelect('notifiche[add][notifica]', 'Notifica *', $errors, $tipologie) }}
                    </div>
                    <div class="col-md-2">
                        {{ Form::weInt('notifiche[add][cadenza]', 'Cadenza *', $errors) }}
                    </div>
                    <div class="col-md-4">
                       {{ Form::weSelect('notifiche[add][tipo]', '&nbsp;', $errors, $tipi_avvisi) }}
                    </div>
                    <div class="col-md-2 text-center">
                        <br>
                        {{ Form::weSubmit('<i class="fa fa-plus"> </i> Aggiungi', 'class = "btn btn-success btn-flat"') }}
                    </div>
                </div>
             </div>
           </div>
         </div>
         <!-- /.col-->
       </div>
</div>
