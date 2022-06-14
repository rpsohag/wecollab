@php
$visibile = 0;
$lavorazione_visibile = 0;
$sospeso = null;

if(!empty($richiesteintervento->id))
{
  //FUNZIONA
  if($richiesteintervento->checkLavoro() > 0)
      $visibile = 1;

  if(!empty($richiesteintervento->checkinLavorazione()))
  {
      $lavorazione_visibile = 1;
      $azione_trovata = $richiesteintervento->checkinLavorazione();
      $sospeso = $richiesteintervento->checkinSospeso();
  }
}
@endphp
<div class="box-body">
    @if(!empty($richiesteintervento->id))
        <div class="row">
            <div class="col-md-2">
                <strong>Codice</strong>: {{ get_if_exist($richiesteintervento->codice) }}
            </div>
            <div class="col-md-10">
                <strong>Data Apertura</strong>: {{ get_date_hour_ita($richiesteintervento->created_at) }}
            </div>
        </div>
        <hr>
        <br>
    @endif
  <div class="row">
      <div class="col-md-3 push-left">
           {!! Form::weSelectSearch('cliente_id', 'Cliente *', $errors, $clienti, get_if_exist($richiesteintervento, 'cliente_id'), ['id'=>'cliente_select']) !!}
      </div>
      <div class="col-md-3">
           {!! Form::weSelectSearch('procedura_id', 'Procedura *', $errors, $procedure, get_if_exist($richiesteintervento, 'procedura_id'), ['id'=>'procedura_select']) !!}
      </div>
      <div class="col-md-3">
           {!! Form::weSelectSearch('area_id', 'Area di Intervento *' , $errors, $aree, get_if_exist($richiesteintervento, 'area_id'), ['id'=>'area_select']) !!}
      </div>
      <div class="col-md-3">
           {!! Form::weSelectSearch('gruppo_id', 'Ambito *' , $errors , $gruppi, get_if_exist($richiesteintervento, 'gruppo_id'), ['id'=>'gruppo_select']) !!}
      </div>
  </div>

  <div class="row">
      <div class="col-md-3">
          {!! Form::weSelectSearch('ordinativo_id', 'Ordinativo *', $errors, $ordinativi, get_if_exist($richiesteintervento, 'ordinativo_id'),['id'=>'ordinativo_select']) !!}
      </div>
      <div id="indirizzo_div" class="col-md-3 hidden">
          @if(!empty($richiesteintervento->indirizzo))
              <?php $full_indirizzo = $richiesteintervento->indirizzo->citta . ' - ' . $richiesteintervento->indirizzo->indirizzo . ' (' . $richiesteintervento->indirizzo->cap . ' ' . $richiesteintervento->indirizzo->provincia . ')'; ?>
          @else
              <?php $full_indirizzo = ''; ?>
          @endif
           {!! Form::weSelectSearch('indirizzo_id', 'Indirizzo', $errors, (!empty($richiesteintervento->indirizzo) ? [$richiesteintervento->indirizzo->id => $full_indirizzo] : [0 => '']), (!empty($indirizzi_sel) ? $indirizzi_sel : ''), ['id'=>'indirizzo_select']) !!}
      </div>
      <div class="col-md-3">
          {!! Form::weText('oggetto', 'Oggetto *', $errors, get_if_exist($richiesteintervento, 'oggetto')) !!}
      </div>
      <div class="col-md-6">
          {!! Form::weSelect('livello_urgenza', 'Livello urgenza', $errors, config('assistenza.richieste_intervento.livelli_urgenza'), get_if_exist($richiesteintervento, 'livello_urgenza'),['id'=>'livello_urgenza_select']) !!}
          <div id="motivo_urgenza" class="{{ (!get_if_exist($richiesteintervento, 'livello_urgenza') && $richiesteintervento->livello_urgenza == 0) ? 'hidden' : '' }}">
              {!! Form::weText('motivo_urgenza', 'Motivo urgenza *', $errors, get_if_exist($richiesteintervento, 'motivo_urgenza')) !!}
          </div>
      </div>
  </div>

  <div class="row">
      <div class="col-md-12">
          {!! Form::weTextarea('descrizione_richiesta', 'Descrizione', $errors, get_if_exist($richiesteintervento, 'descrizione_richiesta')) !!}
      </div>
  </div>
</div>

{{-- Allegati --}}
@include('wecore::admin.partials.filesdrop', ['model' => $richiesteintervento, 'type' => 'assistenza', 'model_name' => 'RichiesteIntervento', 'model_path' => 'Assistenza'])

{{-- Contatti --}}
<div class="box box-info box-shadow">
  <div class="box-header with-border">
    <h3 class="box-title">Contatti</h3>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
    <div class="row">
        <div class="col-md-12">
            <div id="aggiungi_referente_success" class="alert alert-success hidden" role="alert">Hai aggiunto un nuovo referente al cliente.</div>
            <div id="aggiungi_referente_error" class="alert alert-error hidden" role="alert">Impossibile aggiungere il referente al cliente.</div>
        </div>
        <div class="col-md-4">
            {!! Form::weList('richiedente', 'Richiedente', $errors, [], get_if_exist($richiesteintervento, 'richiedente', ['id'=>'contatto_nome'])) !!}
        </div>
        <div class="col-md-4">
            {!! Form::weList('numero_da_richiamare', 'Numero da richiamare', $errors, [], get_if_exist($richiesteintervento, 'numero_da_richiamare', ['id'=>'contatto_numero'])) !!}
        </div>
        <div class="col-md-4">
            {!! Form::weList('email', 'Email *', $errors, [], get_if_exist($richiesteintervento, 'email', ['id'=>'contatto_email'])) !!}
        </div>
        <div class="col-md-4">
          <a type="button" id="aggiungi_referente" class="btn btn-primary btn-flat invisible"><i class="fa fa-user"></i> Aggiungi Referente</a>
        </div>
    </div>
  </div>
  <!-- /.box-body -->
</div>

@php $last_azione = $richiesteintervento_azioni->last() @endphp

@if(!empty($last_azione->tipo) && $last_azione->tipo !== 4 || empty($last_azione->tipo))
  {{-- Destinatario --}}
  <div class="box box-warning box-shadow">
  <div class="box-header with-border">
    <h3 class="box-title">Destinatario *</h3>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
        {{ Form::weTags('destinatario_id', 'Destinatario *', $errors, $destinatari , (!empty($destinatari_sel) ? $destinatari_sel : ''), ['id'=>'destinatari_id', 'multiple'=>'multiple']) }}
        <input id="destinatari-change" type="hidden" name="destinatari_change" value="0">
  </div>
  <!-- /.box-body -->
  </div>
@else 
<div class="hidden">
  {{ Form::weTags('destinatario_id', 'Destinatario *', $errors, $destinatari , (!empty($destinatari_sel) ? $destinatari_sel : ''), ['id'=>'destinatari_id', 'multiple'=>'multiple']) }}
</div>
@endif

@if(!empty($richiesteintervento_azioni->first()) && $richiesteintervento_azioni->first()->tipo != 1 )
       <div class="row">
      <div class="col-md-12">
          <!-- /.box -->
            <div class="box box-success box-shadow">
              <div class="box-header with-border">
                  <h3 class="box-title">
                      Azioni
                  </h3>
                  <!-- tools box -->
                  <div class="box-tools pull-right">
                      <span data-toggle="tooltip" title="" class="badge bg-green" data-original-title="Intervento"></span>
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                  </div>
                  <!-- /. tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                          @foreach($richiesteintervento_azioni->sortBy('updated_at') as $azione)
                          @if($azione->tipo != 1)
                            <div class="row statica">
                                <div class="col-md-3">
                                  @if($azione->tipologia_intervento == 2)
                                    <i data-toggle="tooltip" data-placement="right" title="" data-original-title="{{ config('assistenza.richieste_intervento.richieste_procedure')[$azione->tipologia_intervento] }}" class="text-info fa fa-television"></i>
                                  @elseif($azione->tipologia_intervento == 1)
                                    <i data-toggle="tooltip" data-placement="right" title="" data-original-title="{{ config('assistenza.richieste_intervento.richieste_procedure')[$azione->tipologia_intervento] }}" class="text-info fa fa-truck"></i>
                                  @endif
                                  <span class="text-blue">{{ $azione->created_user->full_name }}</span> <br/>
                                    {{ get_date_hour_ita($azione->created_at) }} - {{ get_date_hour_ita($azione->updated_at) }}
                                </div>
                                <div class="col-md-2">
                                  <br>
                                  (<strong class="text-info">{!! config('assistenza.richieste_intervento.azioni.tipi')[$azione->tipo] !!}</strong>)
                                </div>
                                <div class="col-md-7">
                                  <br>
                                  {{ $azione->descrizione }}
                                </div>
                                <div class="col-md-12" ><hr style="margin:0px"></div>
                            </div>
                          @endif
                      @endforeach
                </div>
              </div>
            @endif

           @if($lavorazione_visibile == 1)
              <div class="box box-danger box-shadow">
                <div class="box-header with-border">
                  <h3 class="box-title">In Lavorazione</h3>
                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                  </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  <div class="contenitore">
                    <div class="row">
                      <input type="hidden" value="{{$richiesteintervento->id}}" id="id_lavoro">
                      <input type="hidden" value="{{$azione_trovata->id}}" id="id_azione">
                      <div class="col-md-3">
                        {!! Form::weRadio('tipologia_intervento', 'Tipo Di Intervento', $errors,config('assistenza.richieste_intervento.richieste_procedure_icon'), 2 ,['id'=>'id_tipo_intervento' ,'class' => 'class_tipo_intervento']) !!}
                      </div>
                      <div class="col-md-9">
                        <div class="box-body">
                          <textarea class="form-control" id="descrizione_lavoro" name="descrizione" placeholder="Descrizione *" oninput="auto_grow(this)"></textarea>
                        </div>
                      </div>
                    </div>
                      <div class="pull-left">
                          <button type="button" onClick="edit_in_lavorazione(2)" class="btn btn-primary btn-flat btn-azione" data-toggle="tooltip" data-placement="top" title="Salva i progressi effettuati">Salva <i class="fa fa-floppy-o"></i></button>
                          <button type="button" onClick="edit_in_lavorazione(7)" class="btn btn-info btn-flat btn-azione" data-toggle="tooltip" data-placement="top" title="Salva i progressi effettuati e Notifica la mancata risposta del richiedente">Salva & Notifica <i class="fa fa-envelope"></i></button>
                          <button type="button" onClick="edit_in_lavorazione(4)" class="btn btn-warning btn-flat btn-azione" data-toggle="tooltip" data-placement="top" title="Sospendi il ticket">Sospendi <i class="fa fa-pause"></i></button>
                          <button type="button" onClick="edit_in_lavorazione(3)" class="btn btn-success btn-flat btn-azione" data-toggle="tooltip" data-placement="top" title="Risoluzione del ticket">Chiudi <i class="fa fa-check"></i></button>
                      </div>
                  </div>
                </div>
              </div>
            @endif
            <!-- /.box-body -->
          </div>
        </div>

      </div>
       <!-- /.col-->
   </div>

@push('js-stack')
<script>

  $(document).ready(function() {

    // Aggiungi referente sezione contatti
    var contatto_email = $('#email');
    var contatto_numero = $('#numero_da_richiamare');
    var contatto_nome = $('#richiedente');

    contatto_email.change(function() {
      var contatto_numero = $('#numero_da_richiamare').val();
      var contatto_nome = $('#richiedente').val();
      if(contatto_numero !== null && contatto_nome !== null){
        var button_referente = $('#aggiungi_referente');
        button_referente.removeClass('invisible');
      } else {
        var button_referente = $('#aggiungi_referente');
        button_referente.addClass('invisible');
      }
    });

    contatto_numero.change(function() {
      var contatto_numero = $('#numero_da_richiamare').val();
      var contatto_nome = $('#richiedente').val();
      var contatto_email = $('#email').val();
      if(contatto_email !== null && contatto_nome !== null){
        var button_referente = $('#aggiungi_referente');
        button_referente.removeClass('invisible');
      } else {
        var button_referente = $('#aggiungi_referente');
        button_referente.addClass('invisible');
      }
    });

    // Sezione Contatti auto-select with filter
    contatto_nome.change(function() {
      var val = $('#richiedente').val()
      var numero = $('#richiedentes option').filter(function() {
          return this.value == val;
      }).data('numero');
      var email = $('#richiedentes option').filter(function() {
          return this.value == val;
      }).data('email');
      if(contatto_numero.value == undefined){
        if(numero !== null && numero !== undefined){
            contatto_numero.val(numero);
        }
      }
      if(contatto_email.value == undefined){
        if(email !== null && email !== undefined){
            contatto_email.val(email);
        }
      }
      if(email !== null && numero !== null){
        $('#aggiungi_referente').removeClass('invisible');
      }
    });

    $("#aggiungi_referente").click(function(e) {
        e.preventDefault();
        var token = $('input[name="_token"]').val();
        var cliente_id = $('select[name="cliente_id"]').val();
        var contatto_numero = $('#numero_da_richiamare').val();
        var contatto_nome = $('#richiedente').val();
        var contatto_email = $('#email').val();
        $.ajax({
            url: "{{ route('admin.assistenza.richiesteinterventi.aggiungireferente') }}",
            type:"POST",
            data: { '_token': token, 'cliente_id': cliente_id, 'contatto_email': contatto_email, 'contatto_numero': contatto_numero, 'contatto_nome': contatto_nome },
            dataType: 'JSON',
            success:function(data){
                $('#aggiungi_referente_error').addClass('hidden');
                $('#aggiungi_referente_success').removeClass('hidden');
            },error:function(){
                $('#aggiungi_referente_success').addClass('hidden');
                $('#aggiungi_referente_error').removeClass('hidden');
            }
        });
    });

    var token = $('input[name="_token"]').val();
    var aree = $.parseJSON(atob("{{ get_json_aree() }}"));
    var gruppi = $.parseJSON(atob("{{ get_json_gruppi() }}"));

    var procedura_select = $('#procedura_select');
    var area_select = $('#area_select');
    var gruppo_select = $('#gruppo_select');
    var clienti_select = $('#cliente_select');

    var select_destinatari = $('#destinatari_id').selectize();
    var destinatari = select_destinatari[0].selectize;

    var clienteId = $('select[name="cliente_id"]').val();


    ottieniAjaxRichiedenti(token,clienteId);

    // Check cambio destinatari
    destinatari.on('change', function() {
      $('#destinatari-change').val(1);
    });

    @if(!empty($richiesteinervento->id))
    //Mostrare il campo indirizzo nell'edit se presente.
    var campo_indirizzo = <?= ($richiesteintervento->indirizzo_id == 0 ? $richiesteintervento->indirizzo_id : 'undefined') ?>;
    if(campo_indirizzo !== 'undefined'){
      var indirizzo_div = $('#indirizzo_div');
      indirizzo_div.removeClass('hidden');
    }
    @endif

    // Selezione cilente
    clienti_select.change(function(e) {
      var route = "<?php echo Route::currentRouteName(); ?>";
      if(route !== 'admin.assistenza.richiesteintervento.edit'){
        procedura_select.select2('open');
        $('#ordinativo_select').val("34").trigger('change');
      }
    });

    // Selezione procedura
    procedura_select.change(function(e) {
      area_select.empty();
      var procedura_selezionata = procedura_select.val();
      aree.forEach(element => {
          //scorro tutto l'array e controllo quelli che hanno il procedura id  =  alla procedura selezionata
          if(element.procedura_id == procedura_selezionata){
              var newOption = new Option(element.titolo, element.id, false, false);
              area_select.append(newOption);
          }
      });

      //assegnare nuove_aree_di_intervento alla select delle aree di intervento
      area_select.trigger('change');
      area_select.select2('open');
      gruppo_select.select2('close');
    });

    // Selezione area
    area_select.change(function(e) {
      gruppo_select.empty();
      var area_selezionata = area_select.val();
      gruppi.forEach(element => {
          //scorro tutto l'array e controllo quelli che hanno il procedura id  =  alla procedura selezionata
          if(element.area_id == area_selezionata){
              var newOption = new Option(element.nome, element.id, false, false);
              gruppo_select.append(newOption);
          }
      });
      //assegnare nuove_aree_di_intervento alla select delle attivita
      gruppo_select.trigger('change');
      gruppo_select.select2('open');
      area_select.select2('close');
    });

    // Selezione attività
    gruppo_select.change(function(e) {
      var gruppo_selezionato = $(this).val();
      destinatari.clear();
      //destinatari.clearOptions();
      $.post("{{ route('admin.assistenza.richiesteinterventi.ajaxrequestdestinatari') }}", { _token: token, gruppo:gruppo_selezionato })
        .done(function(data) {
          if(data != '') {
            data = JSON.parse(data);
            for (var key in data) {
              destinatari.addOption({value:key,text:data[key]});
              destinatari.addItem(key);
            }
          }
            destinatari.refreshOptions();
        });
        destinatari.close();
    });

    var livello_urgenza_select = $('#livello_urgenza_select');

    // Selezione livello urgenza
    livello_urgenza_select.change(function(e)
    {
      var livelloUrgenzaSelezionato = $(this).val();
      var motivoUrgenza = $("#motivo_urgenza");

      if(livelloUrgenzaSelezionato > 0)
        motivoUrgenza.removeClass('hidden');
      else if (livelloUrgenzaSelezionato == 0)
        motivoUrgenza.addClass('hidden');
    });

    $('select[name="cliente_id"]').change(function(e) {

    var token = $('input[name="_token"]').val();
    var target = $(e.target).attr('name');
    var clienteId = $('select[name="cliente_id"]').val();
    var ordinativo = $('#ordinativo_select');
    var indirizzo = $('#indirizzo_select');

    $.post("{{ route('admin.assistenza.richiesteinterventi.ajaxrequestrichiesta') }}", { _token: token, cliente_id: clienteId })
      .done(function(data) {

        if("ordinativi" in data && target == 'cliente_id') {
            ordinativo.empty();
            for (var key in data.ordinativi){
                var newOption = new Option(data.ordinativi[key],key);
                ordinativo.append(newOption);
            }
        }
        if("indirizzi" in data && target == 'cliente_id') {
              //console.log(data.indirizzi);
              indirizzo.empty();
              var indirizzo_div = $('#indirizzo_div');
              indirizzo_div.removeClass('hidden');
              for (var key in data.indirizzi){
                  var newOption = new Option(data.indirizzi[key][0],data.indirizzi[key][1]);
                  indirizzo.append(newOption);
              }
        }

                if("default_ordinativo" in data)
                {
                  $('#ordinativo_select').val(data.default_ordinativo).trigger('change');
                }

            });


          ottieniAjaxRichiedenti(token,clienteId);


       });
  });

  function auto_grow(element) {
    element.style.height = "5px";
    element.style.height = (element.scrollHeight)+"px";
  }

  function edit_in_lavorazione(tipo_id)
  {

      $('.btn-azione').addClass('disabled');
      var token = $('input[name="_token"]').val();
      var descr = $('#descrizione_lavoro').val();
      var id_tipo_intervento = $('.class_tipo_intervento:checked').val();
      var id_azione = $('#id_azione').val();

      if($('#descrizione_lavoro').val() == '')
      {
        $('#descrizione_lavoro').parent().addClass('has-error');
        $('.btn-azione').removeClass('disabled');
      }
      else
      {
        if(tipo_id == 3){

          if(confirm('Sei sicuro di voler chiudere questo ticket? L\'azione sarà irreversibile, continuare?'))
          {
              $.post("{{ route('admin.assistenza.richiesteinterventi.iniziolavoro') }}", { _token: token, id_azione:id_azione , id_tip:id_tipo_intervento, descr:descr,tipo:tipo_id})
                .done(function(data) {
                    location.reload();
                });
          }
          else {
            $('.btn-azione').removeClass('disabled');
            return false;
          }
        } else {

      $.post("{{ route('admin.assistenza.richiesteinterventi.iniziolavoro') }}", { _token: token, id_azione:id_azione , id_tip:id_tipo_intervento, descr:descr,tipo:tipo_id})
          .done(function(data) {
              location.reload();
          }); 
        }
    }
  }

   function ottieniAjaxRichiedenti(token ,clienteId)
  {
    $.post("{{ route('admin.assistenza.richiesteinterventi.ajaxrequestcontatti') }}", { _token: token, cliente_id: clienteId })
      .done(function(datax) {
        if(datax[4]=='ok')
        {
            //1° arr numero_da_richiamare
            var html_num='';
            for(var i = 0 ; i < datax[0].length; i++)
            {
              html_num += '<option>'+datax[0][i]+'</option>';
            }

            var html_nomi='';
            for(var i = 0 ; i < datax[1].length; i++)
            {
              html_nomi += '<option id="' +datax[3][i]+ '" data-numero="' +datax[0][i]+ '" data-email="' +datax[2][i]+ '">'+datax[1][i]+'</option>';
            }

            var html_email='';
            for(var i = 0 ; i < datax[2].length; i++)
            {
              html_email += '<option>'+datax[2][i]+'</option>';
            }

            $('#numero_da_richiamares').html(html_num);
            $('#richiedentes').html(html_nomi);
            $('#emails').html(html_email);

          }

      });
  }

</script>
@endpush
