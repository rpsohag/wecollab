@php

$ticketintervento = (empty($ticketintervento)) ? '' : $ticketintervento;

$tipologie_intervento = config('assistenza.ticket_intervento.tipologie');
$settori = config('assistenza.ticket_intervento.settori');
$intervento_tipi = config('commerciale.interventi.tipi');


if(!empty($ticketintervento))
{
    
    $gruppo_id = !empty(request('gruppo_id')) ? request('gruppo_id') : $ticketintervento->gruppo_id;
    $area_intervento = $ticketintervento->ordinativo->giornate->where('gruppo_id', $gruppo_id)->first();

    $intervento_tipo = !empty($area_intervento) ? $intervento_tipi[$area_intervento->tipo] : '';

    if(empty($area_intervento))
    {
        $area_intervento = new stdClass();
        $area_intervento->quantita_residue = 0;
    }

    $ticketintervento->voci_all = $ticketintervento->giornate();
}

if(!empty($ticketintervento->voci_all))
    $numero_voci = count($ticketintervento->voci_all);
else
    $numero_voci = 0;


//dd($ticketintervento);
//dd($aree_di_intervento);

@endphp
<div class="box-body">
    <div class="row"> 
    	
        <div class="box-body no-padding col-md-5">
            <ul class="nav nav-stacked">
                <li class="padding"><strong>Cliente</strong>: <span class="pull-right">{{ (!empty(get_if_exist($ticketintervento, 'cliente_id')) ? $ticketintervento->cliente->ragione_sociale :'' ) }}</span></li><!--Cliente è il nome della relazione(sarebbe il metodo) nel model ticketintervento che mi permette di prendere la ragione sociale -->
                <li class="padding"><strong>Procedura</strong>: <span class="pull-right">{{ (!empty(get_if_exist($ticketintervento, 'procedura_id')) ? $ticketintervento->procedura->titolo : '' ) }}</span></li>
                <li class="padding"><strong>Area Di Intervento</strong>: <span class="pull-right">{{ (!empty(get_if_exist($ticketintervento, 'area_di_intervento_id')) ? $ticketintervento->area->titolo : '') }}</span></li>
                <li class="padding"><strong>Attivit&agrave;</strong>: <span class="pull-right">{{ (!empty(get_if_exist($ticketintervento, 'gruppo_id')) ? $ticketintervento->gruppo->nome: '') }}</span></li>
                <li class="padding"><strong>Ordinativo</strong>: <span class="pull-right">{{ (!empty(get_if_exist($ticketintervento, 'ordinativo_id')) ? $ticketintervento->ordinativo->oggetto : '') }}</span></li>
                <li class="padding"><strong>Data Documento</strong>: <span class="pull-right">{{ (empty(get_if_exist($ticketintervento, 'data')) ? '' : get_date_hour_ita($ticketintervento->data)) }}</span></li>
                <li class="padding"><strong>Numero rapporto intervento</strong>:<span class="pull-right">{{ $ticketintervento->numero_ticket() }}</span></li>
            {{-- <li class="padding"><strong>Tipologia di Intervento</strong>: <span class="pull-right">{{ $tipologie_intervento[get_if_exist($ticketintervento, 'tipologia_id')] }}</span></li>
                <li class="padding"><strong>Settore</strong>: <span class="pull-right">{{ $settori[get_if_exist($ticketintervento, 'settore_id')] }}</span></li> --}}
                <li class="padding"><strong>Note</strong>: <span class="pull-right">{{  get_if_exist($ticketintervento, 'note') }}</span></li>
            </ul>
        </div>
                <div class="col-md-12">
                    <br>
                    
                    <strong>Tipo di intervento</strong>:

                    @if(get_if_exist($ticketintervento, 'formazione') == 1 || get_if_exist($ticketintervento, 'consulenza') == 1)
                      <ul>
                        @if(get_if_exist($ticketintervento, 'formazione') == 1)
                      		<li>Formazione</li>
                      	@endif

                        @if(get_if_exist($ticketintervento, 'consulenza') == 1)
                      		<li>Consulenza</li>
                      	@endif
                      </ul>
                    @endif
                
                        {{--
                            <li class="padding"><strong>Formazione</strong>: <span class="pull-right">{{ $settori[get_if_exist($ticketintervento, 'formazione')] }}</span></li>
                            <li class="padding"><strong>Consulenza</strong>: <span class="pull-right">{{ get_if_exist($ticketintervento, 'consulenza') }}</span></li>
                            --}}
             </ul>
        </div>
    	
    	{{-- 
    	
        <div class="col-md-3">
            <b>Cliente</b> {{ $clienti[get_if_exist($ticketintervento, 'cliente_id')] }}
        </div>
        <div class="col-md-3">
            <b>Ordinativo</b> {{ $ordinativi[get_if_exist($ticketintervento, 'ordinativo_id')] }}
        </div>
        <div class="col-md-3">
            <b> Area Di Intervento</b>   {{$gruppi[get_if_exist($ticketintervento, 'gruppo_id')] }}
        </div>
        <div class="col-md-3">
          <b>Data Documento</b> {{ (empty(get_if_exist($ticketintervento, 'data')) ? '' : get_date_hour_ita($ticketintervento->data)) }}
        </div>
        <div class="col-md-4">
           <b>Codice Ticket</b>   {{ get_if_exist($ticketintervento,'codice_ticket') }}
        </div>
        <div class="col-md-4">
            <b>Tipologia di Intervento</b>  {{ $tipologie_intervento[get_if_exist($ticketintervento, 'tipologia_id')] }}
        </div>
        <div class="col-md-4">
           <b>Settore</b>   {{ $settori[get_if_exist($ticketintervento, 'settore_id')] }}
        </div>
        <div class="col-md-6">
             <b>Materiale Consegnato</b> {{ get_if_exist($ticketintervento, 'materiale_consegnato') }}
        </div>
         <div class="col-md-12">
          <b>Note</b> {{  get_if_exist($ticketintervento, 'note') }}
        </div>--}}
    </div>

    @if (!empty($ticketintervento))
   <div class="row">
    <div class="col-md-12">
        <!-- /.box -->
        <div class="box box-success box-shadow">
            <div class="box-header with-border">
               <h3 class="box-title">
                   Intervento
                   &nbsp;&nbsp;&nbsp;
                   <small data-toggle="tooltip" title="" class="label bg-blue" data-original-title="{{ $intervento_tipo }} residue">{{ $intervento_tipo }} residue {{ $area_intervento->quantita_residue }}</small>
               </h3>
               <!-- tools box -->
               <div class="box-tools pull-right">
                   <span data-toggle="tooltip" title="" class="badge bg-green" data-original-title="{{ $numero_voci }} Intervento">{{ $numero_voci }}</span>
                   <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
               </div>
               <!-- /. tools -->
             </div>
             <!-- /.box-header -->
             <div class="box-body">
                 @if(!empty($ticketintervento->voci) && count($ticketintervento->voci) > 0)
                     @foreach ($ticketintervento->voci as $key => $voce)
                        @php
                          $disabled = ($voce->ticket_id != $ticketintervento->id ? 'disabled' : '');
                        @endphp
                         <div class="row">
                            <div class="col-md-4">
                              <b>Descrizione</b>  {{   get_if_exist($voce, 'descrizione')   }}
                            </div>
                            <div class="col-md-2">
                              <b>Data</b>  {{  get_date_ita($voce->data_intervento)  }}
                            </div>
                            <div class="col-md-2">
                            	 <b>{{$intervento_tipo}} Lavorate</b> 
                              @if(strtolower($intervento_tipo) == 'giornate')
                                {{   get_if_exist($voce, 'quantita') }}
                              @else
                                {{    get_if_exist($voce, 'quantita')  }}
                              @endif
                            </div>
                            <div class="col-md-2">
                              <b>Ora di Inizio</b>  {{  get_if_exist($voce, 'ora_inizio_1') }}
                            </div>
                            <div class="col-md-2">
                              <b>Ora di Fine</b> {{    get_if_exist($voce, 'ora_fine_1')  }}
                            </div>
                            {{-- @if(strtolower($intervento_tipo) != 'giornate')
                              <div class="col-md-1 text-center">
                                  <br>
                                  <button class="btn btn-md btn-flat btn-danger" type="button" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.assistenza.ticketinterventovoci.destroy', [$voce->id]) }}"><i class="fa fa-trash"></i></button>
                              </div>
                            @endif --}}
                        </div>
                        <hr>
                    @endforeach
                @else
                    <div class="callout callout-warning">
                        <h4>ATTENZIONE!</h4>
                        <p>Nessuna Voce presente per questo Ticket.</p>
                    </div>
                @endif
             </div>
             {{-- @if(count($ticketintervento->voci_ticket) == 0 || strtolower($intervento_tipo) != 'giornate') --}}
             @if(count($ticketintervento->voci) == 0)
               <div class="box-footer">
                   <h4>Aggiungi Voce</h4>
                   <div class="row">
                      <div class="col-md-3">
                          {{ Form::weText('tickets[add][descrizione]', 'Descrizione *', $errors) }}
                      </div>
                      <div class="col-md-2">
                          {{ Form::weDate('tickets[add][data_intervento]', 'Data *', $errors) }}
                      </div>
                      <div class="col-md-2">
                          @if(strtolower($intervento_tipo) == 'giornate')
                              {{ Form::weInt('tickets[add][quantita]', $intervento_tipo . ' Lavorate', $errors, 1, ['readonly']) }}
                          @else
                              {{ Form::weInt('tickets[add][quantita]', $intervento_tipo . ' Lavorate *', $errors, 1, ['min' => 1, 'max' => $area_intervento->quantita_residue]) }}
                          @endif
                      </div>
                      <div class="col-md-2">
                          {{ Form::weInt('tickets[add][ora_inizio1]', 'Ora di Inizio ', $errors) }}
                      </div>
                      <div class="col-md-2">
                          {{ Form::weInt('tickets[add][ora_fine1]', 'Ora di Fine ', $errors) }}
                      </div>
                      @if(strtolower($intervento_tipo) != 'giornate')
                        <div class="col-md-1 text-center">
                            <br>
                            {{ Form::weSubmit('<i class="fa fa-plus"> </i>', 'class = "btn btn-default btn-flat"') }}
                        </div>
                      @endif
                  </div>
               </div>
             @endif
           </div>
         </div>
         <!-- /.col-->
     </div>
   @endif
</div>

@push('js-stack')
    <script>
        $(document).ready(function() {
            $('select[name="cliente_id"], select[name="ordinativo_id"], select[name="gruppo_id"]').change(function(e) {
                var clienteId = $('select[name="cliente_id"]').val();
                var ordinativoId = $('select[name="ordinativo_id"]').val();
                var gruppoId = $('select[name="gruppo_id"]').val();

                window.location.href = '{{ url()->current() }}?cliente_id=' + clienteId + '&ordinativo_id=' + ordinativoId + '&gruppo_id=' + gruppoId;
            });
        });
    </script>
@endpush
