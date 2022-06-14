@php

//dd($richiesta_intervento);
$richiesta_intervento = (empty($richiesta_intervento)) ? '' : $richiesta_intervento;
$ticketintervento = (empty($ticketintervento)) ? '' : $ticketintervento;
//dd($ticketintervento);

//dd($ticketintervento_fields);

$tipologie_intervento = config('assistenza.ticket_intervento.tipologie');
$settori = config('assistenza.ticket_intervento.settori');
$intervento_tipi = config('commerciale.interventi.tipi');

if(!empty($ticketintervento))
{
    $gruppo_id = !empty(request('gruppo_id')) ? request('gruppo_id') : $ticketintervento->gruppo_id;
    $area_intervento = $ticketintervento->ordinativo->giornate->where('gruppo_id', $gruppo_id)->first();
    //dd($area_intervento);

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
    
 @endphp
<div class="box-body">
    <div class="row">
        <div class="col-md-3">
             {!! Form::weSelectSearch('cliente_id','Cliente *' , $errors , $clienti, (!empty($richiesta_intervento) ? get_if_exist($richiesta_intervento, 'cliente_id') : get_if_exist($ticketintervento, 'cliente_id') ),['id'=>'cliente_id']) !!}
        </div>
        <div class="col-md-3">
            {!! Form::weSelectSearch('ordinativo_id','Ordinativo *' , $errors , $ordinativi,  (!empty($richiesta_intervento) ? get_if_exist($richiesta_intervento, 'ordinativo_id') : get_if_exist($ticketintervento, 'ordinativo_id')) ,['id'=>'ordinativo_id']) !!}     
       </div>
       <div class=" col-md-2 ">
        {{ Form::weRadio('procedura_id', 'Procedura *', $errors,$procedure, (!empty($richiesta_intervento) ? get_if_exist($richiesta_intervento, 'procedura_id') : get_if_exist($ticketintervento, 'procedura_id')),['class'=>'procedura_radio']) }}
        </div>
        <div class="col-md-2">
            {!! Form::weSelectSearch('area_di_intervento_id','Area Di Intervento *' , $errors ,$aree_di_intervento, (!empty($richiesta_intervento) ? get_if_exist($richiesta_intervento, 'area_id') : get_if_exist($ticketintervento, 'area_di_intervento_id')) ,['id'=>'area_di_intervento_id']) !!} 
       </div>

       <div class="col-md-2">
            {!! Form::weSelectSearch('gruppo_id','Attivita *' , $errors ,$gruppo_attivita, (!empty($richiesta_intervento) ? get_if_exist($richiesta_intervento, 'gruppo_id') : get_if_exist($ticketintervento, 'gruppo_id')),['id'=>'gruppo_id']) !!} 
       </div>
        <div class="col-md-3">
            {!! Form::weDate('data','Data Documento *' , $errors , (empty(get_if_exist($ticketintervento, 'data'))) ? date('d/m/Y') : get_date_hour_ita($ticketintervento->data)) !!} <!--manca qui -->
        </div>
        <div class="col-md-3">
            {!! Form::weText('codice_ticket','Numero rapporto di intervento' , $errors, get_if_exist($ticketintervento,'codice_ticket') ? $ticketintervento->numero_ticket() : $ticket->get_next_codice_ticket() ,['readonly=true']) !!} <!--manca qui -->
        </div>

    {{--
        <div class="col-md-4">
             {!! Form::weSelectSearch('tipologia_id','Tipologia di Intervento *' , $errors , $tipologie_intervento,  get_if_exist($ticketintervento, 'tipologia_id')) !!}
        </div>
        <div class="col-md-4">
             {!! Form::weSelectSearch('settore_id','Settore *' , $errors , $settori,  get_if_exist($ticketintervento, 'settore_id')) !!}
        </div>
    --}}

        <!-- Default unchecked -->
        
        <div class=" col-md-3 ">
            {{ Form::weCheckbox('formazione', 'formazione', $errors, (!empty($richiesta_intervento) ? get_if_exist($richiesta_intervento, 'formazione') : get_if_exist($ticketintervento, 'formazione'))) }}
        </div>
        <div class=" col-md-3 ">
            {{ Form::weCheckbox('consulenza', 'consulenza', $errors, (!empty($richiesta_intervento) ? get_if_exist($richiesta_intervento, 'consulenza') : get_if_exist($ticketintervento, 'consulenza'))) }}
        </div>
    
   
        {{--
        <div class="col-md-6">
             {!! Form::weText('materiale_consegnato','Materiale Consegnato' , $errors , get_if_exist($ticketintervento, 'materiale_consegnato')) !!}
        </div>
        --}}
         <div class="col-md-12">
             {!! Form::weTextarea('note','Note' , $errors , (!empty($richiesta_intervento) ? get_if_exist($richiesta_intervento, 'descrizione_richiesta') : get_if_exist($ticketintervento, 'note'))) !!}
        </div>
    </div>

    @if (!empty($ticketintervento))
   <div class="row">
    <div class="col-md-12">
        <!-- /.box -->
        <div class="box box-success box-shadow">
            <div class="box-header with-border">
               <h3 class="box-title">
                   Storico Interventi
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
                            <div class="col-md-6">
                                {{ Form::weTextarea('tickets[' . $voce->id . '][descrizione]', 'Descrizione *', $errors, get_if_exist($voce, 'descrizione'), [$disabled]) }}
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        {{ Form::weDate('tickets['. $voce->id. '][data_intervento]', 'Data *', $errors, get_date_hour_ita($voce->data_intervento), [$disabled]) }}
                                    </div>
                                    <div class="col-md-6">
                                    @if(strtolower($intervento_tipo) == 'giornate')
                                        {{ Form::weInt('tickets['. $voce->id. '][quantita]', $intervento_tipo . ' Lavorate *', $errors,  get_if_exist($voce, 'quantita'), ['readonly', $disabled]) }}
                                    @else
                                        {{ Form::weInt('tickets['. $voce->id. '][quantita]', $intervento_tipo . ' Lavorate *', $errors,  get_if_exist($voce, 'quantita'), [$disabled, 'min' => 1, 'max' => (get_if_exist($voce, 'quantita') + $area_intervento->quantita_residue)]) }}
                                    @endif
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        {{ Form::weInt('tickets['. $voce->id. '][ora_inizio_1]', 'Ora di Inizio ', $errors,  get_if_exist($voce, 'ora_inizio_1'), [$disabled]) }}
                                    </div>
                                    <div class="col-md-6">
                                        {{ Form::weInt('tickets['. $voce->id. '][ora_fine_1]', 'Ora di Fine ', $errors,  get_if_exist($voce, 'ora_fine_1'), [$disabled]) }}
                                    </div>
                                </div>
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

          
            {{-- var token = $("input[name='_token']").val();
            var aree = $.parseJSON(atob("{!!$json_aree!!}"));
            var gruppi = $.parseJSON(atob("{!!$json_gruppi!!}"));--}}
           

            $('select[name="cliente_id"], select[name="ordinativo_id"], input[class="procedura_radio"], select[name="area_di_intervento_id"]').change(function(e) {
                var target = $(e.target).attr('name');
                var token = $('input[name="_token"]').val();
                var clienteId = $('select[name="cliente_id"]').val();
                var ordinativoId = $('select[name="ordinativo_id"]').val();
                var proceduraId = $('.procedura_radio:checked').val();
                var areaId = $('select[name="area_di_intervento_id"]').val();
                // var gruppoId = $('select[name="gruppo_id"]').val();
                var ordinativo = $('#ordinativo_id');
                var aree = $('#area_di_intervento_id');
                var gruppi = $('#gruppo_id');
                var procedure = $('.procedura_radio');
               

                $.post("{{ route('admin.assistenza.ticketintervento.ajaxrequest') }}", { _token: token, cliente_id: clienteId, ordinativo_id: ordinativoId, procedura_id: proceduraId, area_id: areaId })
                    .done(function(data) {
                        
                        if("ordinativi" in data && target == 'cliente_id') {
                            ordinativo.empty();
                            aree.empty();
                            gruppi.empty();

                            for (var key in data.ordinativi){ 
                                var newOption = new Option(data.ordinativi[key],key);
                                ordinativo.append(newOption);
                            }
                        }

                        if("procedure" in data && target == 'ordinativo_id') {

                            switch(Object.keys(data.procedure).length){

                                case 0 : 
                                    procedure.each(function( index ) {
                                        $( this ).attr('disabled','disabled');
                                        $( this ).removeAttr('checked');
                                    });
                                    break;

                                case 1 : 
                                
                                    procedure.each(function( index ) {
                                        if(typeof data.procedure[$( this ).val()]   !== 'undefined'   ){
                                            $( this ).removeAttr('disabled');
                                            $( this ).click();
                                        } else {
                                            $( this ).attr('disabled','disabled');
                                            $( this ).removeAttr('checked');
                                        }
                                    });

                                break;
                                default: 
                                    procedure.each(function( index ) {
                                        if(typeof data.procedure[$( this ).val()]   !== 'undefined'   ){
                                            $( this ).removeAttr('disabled' );
                                            $( this ).removeAttr('checked');
                                        }else{
                                            $( this ).attr('disabled','disabled');
                                            $( this ).removeAttr('checked');
                                        }
                                    });
                                
                                 break;
                            };
                        }

                        if("aree" in data && target == 'procedura_id') {
                            var selectOne = 0;
                            aree.empty();
                            gruppi.empty();

                            for(var key in data.aree) {
                                if(key != 0)
                                    selectOne = key;

                                var newOption = new Option(data.aree[key], key);
                                aree.append(newOption);
                            }

                            if(Object.keys(data.aree).length == 2)
                                aree.val(selectOne).trigger('change');
                            else
                                aree.select2('open');
                        }

                        if("gruppi" in data && target == 'area_di_intervento_id') { //i gruppi sono le attivita
                            gruppi.empty();

                            
                            //console.log(data);
                            for (var key in data.gruppi) {
                                var gruppo = data.gruppi[key];
                                var quantita_tipo = (gruppo['quantita_residue'] > 0) ? ' (' + gruppo['quantita_residue'] + ' ' + gruppo['tipo'] + ')' : '';

                                if(key != 0)
                                    selectOne = key;             

                                var newOption = new Option(gruppo['titolo'] + quantita_tipo, key);
                                gruppi.append(newOption);
                            }

                            if(Object.keys(data.gruppi).length == 2)
                                gruppi.val(selectOne).trigger('change');
                            else
                                gruppi.select2('open');
                        }

                       // console.log(data.aree);
                        
                    });
            });
            
/*
            $('input[radio]').change(function(e) {
                var nuove_aree_di_intervento = {} ;
                $("#id_area_di_intervento").empty();

                var procedura_selezionata = $('input[radio]:checked').val(); 
                aree.forEach(element => {
                    //scorro tutto l'array e controllo quelli che hanno il procedura id  =  alla procedura selezionata

                    if(element.procedura_id == procedura_selezionata){
                        var newOption = new Option(element.titolo, element.id, false, false);
                        $('#id_area_di_intervento').append(newOption);
                    }
                     
                 });
                 //assegnare nuove_aree_di_intervento alla select delle aree di intervento
                //  console.log(nuove_aree_di_intervento);
                $("#id_area_di_intervento").trigger('change');
                $("#id_area_di_intervento").select2('open');
                $("#id_gruppo_id").select2('close');  
            });


            $('#id_area_di_intervento').change(function(e) {
                var nuove_attivita = {} ;
                $("#id_gruppo_id").empty();
                var area_di_intevento_selezionata = $(this).val(); 
                gruppi.forEach(element => {
                    //scorro tutto l'array e controllo quelli che hanno il procedura id  =  alla procedura selezionata

                    if(element.area_id ==area_di_intevento_selezionata){
                        
                        var newOption = new Option(element.nome, element.id, false, false);
                        $('#id_gruppo_id').append(newOption);   
                    }    
                 });
                 //assegnare nuove_attivita alla select delle attivita
                 
                 $("#id_gruppo_id").trigger('change');
                    $("#id_gruppo_id").select2('open');
                  
            });
   
          */


           /* $('#id_cliente_id').change(function(e) {
                var cid = $(this).val();
                $.post("{{ route('admin.assistenza.ticketintervento.checkordinativo')}}",  {cliente_id:cid , _token:token} )
                .done(function( data ) {
                    var ordinativi = $.parseJSON(atob(data));
                        ordinativi.forEach(element => {
                            //scorro tutto l'array 
                            var newOption = new Option(element.oggetto, element.id, false, false);
                            $('#id_ordinativo_id').append(newOption);
                        });
                        $("#id_ordinativo_id").trigger('change');
                        $("#id_ordinativo_id").select2('open');
                        
                 });
         
            });

   $('#id_ordinativo_id').change(function(e) {
                var nuove_aree_di_intervento = {} ;
                $("#id_area_di_intervento").empty();
                var procedura_selezionata = $('id_ordinativo_id').val();
                aree.forEach(element => {
                    //scorro tutto l'array e controllo quelli che hanno il procedura id  =  alla procedura selezionata

                    if(element.procedura_id == procedura_selezionata){
                        var newOption = new Option(element.titolo, element.id, false, false);
                        $('#id_area_di_intervento').append(newOption);
                    }

                });

                    $("#id_area_di_intervento").trigger('change');
                    $("#id_area_di_intervento").select2('open');
        });*/


        });
    </script>
@endpush
