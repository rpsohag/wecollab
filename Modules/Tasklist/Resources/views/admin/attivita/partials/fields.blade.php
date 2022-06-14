<div class="row">
  <div class="col-md-12">
    @if(empty($attivita->created_at))
      <div id="create_campi">
        <div class="col-md-12 bg-green">
          <div class="col-md-1 text-center">
            <i style="margin-top:24px;" class="icon fa fa-5x fa-pencil-square-o text-success"></i>
          </div> 
          <div class="col-md-11">
            <h2 class="display-4">Informazioni Generali</h2>
            <p class="lead">Compila le informazioni obbligatorie e opzionali per la nuova attività.</p>
          </div>
        </div>
        <div class="col-md-12">
          <br>
          <div class="box box-success">
            <div class="box-body">
              <div class="row">
                <div class="col-md-3">
                  {{ Form::weSelectSearch('cliente_id', 'Cliente', $errors, [''] + $clienti , !empty($attivita->cliente_id) ? $attivita->cliente_id : [], ['id' => 'cliente']) }}
                </div>
                <div class="col-md-9">
                  {{ Form::weText('oggetto', 'Oggetto *', $errors, get_if_exist($attivita, 'oggetto')) }}
                </div>
                <div class="col-md-3">
                  {!! Form::weSelectSearch('procedura_id', 'Procedura *', $errors, [''] + $procedure, get_if_exist($attivita, 'procedura_id'), ['id'=>'procedura_select']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::weSelectSearch('area_id', 'Area di Intervento *' , $errors, [''] + $aree, get_if_exist($attivita, 'area_id'), ['id'=>'area_select']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::weSelectSearch('gruppo_id', 'Ambito *' , $errors , [''] + $gruppi, get_if_exist($attivita, 'gruppo_id'), ['id'=>'gruppo_select']) !!}
                </div>
                <div class="col-md-3">
                  {{ Form::weSelectSearch('ordinativo_id', 'Ordinativo *', $errors, $ordinativi, get_if_exist($attivita,'ordinativo_id')) }}
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-md-3 hidden">
                  {{ Form::weSelect('stato', 'Stato *', $errors, $stati , $stati[0]) }}
                </div>
                <div class="col-md-3">
                  {{ Form::weSelectSearch('richiedente_id', 'Richiedente *', $errors, $utenti, Auth::id()) }}
                </div>
                <div class="col-md-12 hidden">
                  {{ Form::weSlider('percentuale_completamento', 'Percentuale completamento', $errors, 0) }}
                </div>
                <div class="col-md-6">
                  {!! Form::weTags('supervisori_id', 'Supervisori', $errors, $utenti) !!}
                </div>
                <div class="col-md-3 bg-warning">
                  {{ Form::weSelect('priorita', 'Priorità *', $errors, $priorita , (get_if_exist($attivita,'priorita')) ? get_if_exist($attivita,'priorita') : 5) }}
                </div>
                <div class="col-md-12">
                  {{ Form::weTags('assegnatari_id', 'Assegnato a *', $errors, $utenti, array(), ['id'=>'assegnatari_id', 'multiple'=>'multiple'] ) }}
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-md-3">
                  {{ Form::weDate('data_inizio', 'Data di Inizio', $errors , date('d/m/Y')) }}
                </div>
                <div class="col-md-3">
                  {{ Form::weDate('data_fine', 'Data Scadenza', $errors) }}
                </div>
                <div class="col-md-3">
                  {{ Form::weInt('durata_valore', 'Impegno' , $errors, get_if_exist($attivita, 'durata_valore'), ['min' => 0, 'max' => 31]) }}
                </div>
                <div class="col-md-3">
                  {{ Form::weSelect('durata_tipo', '&nbsp;', $errors, $durata_tipo, 1) }}
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-md-12">
                  {{ Form::weTextarea('descrizione', 'Descrizione', $errors) }}
                </div>
              </div>
            </div>
            <div class="box-footer bg-black">
              <h4 class="text-bold">Opzioni</h4>
              @if(auth_user()->hasAccess('commerciale.fatturazioni.create'))
                <div class="col-md-4">
                    {{ Form::weCheckbox('fatturazione', 'Avviso fatturazione al completamento dell\'attività.', $errors, get_if_exist($attivita,'fatturazione')) }}
                </div>
              @endif
              <div class="col-md-4">
                {{ Form::weCheckbox("opzioni[prese_visioni]", 'Richiesta la presa in carico dell\'attività.', $errors, 1) }}
              </div>
              <div class="col-md-4">
                {{ Form::weCheckbox("opzioni[multi_presa_in_carico]", 'Tutti gli assegnatari possono prendere in carico l\'attività.', $errors, 1) }}
              </div>
            </div>
          </div>
          @include('wecore::admin.partials.filesdrop', ['model' => $attivita, 'type' => 'tasklist', 'model_name' => 'Attivita', 'model_path' => 'Tasklist'])
        </div>
        <div class="col-md-12 text-center">
          <a id="create_campi_avanti" style="margin-bottom: 12px;" type="button" class="btn btn-primary">Avanti</a>
        </div>
      </div>
      <div id="create_requisiti" class="hidden">
        <div class="col-md-12 bg-red">
          <div class="col-md-1 text-center">
            <i style="margin-top:24px;" class="icon fa fa-5x fa fa-exclamation-triangle text-danger"></i>
          </div> 
          <div class="col-md-11">
            <h2 class="display-4">Attività Propedeutiche</h2>
            <p class="lead">Seleziona le attività propedeutiche.</p>
          </div>
        </div>
        <div class="col-md-12">
          <br>
          @if(!empty($attivita_padre))
            <div class="info-box">
              <span class="info-box-icon bg-red"><i class="fa fa-external-link"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Attività Propedeutica Collegata</span>
                <span class="info-box-number"><a target="_blank" href="{{ route('admin.tasklist.attivita.edit', ['attivita' => $attivita_padre->id]) }}">{{ $attivita_padre->oggetto }}</a></span>
              </div>
            </div>
          @endif
          <div class="box box-warning {{ !empty($attivita_padre) ? 'hidden' : '' }}">
            <div class="box-body">
              <div class="row">
                <div class="col-md-12">
                  <span data-toggle="tooltip" data-placement="top" title="Seleziona le attività richieste da completare al 100%">{!! Form::weTags('requisiti', 'Attività Propedeutiche', $errors, $requisiti,  !empty($attivita->requisiti) ? $attivita->requisiti()->pluck('id')->toArray() : (!empty($attivita_padre->id) ? $attivita_padre->id : []) ) !!}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-12 text-center">
          <a id="create_requisiti_indietro" style="margin-bottom: 12px;" type="button" class="btn btn-primary">Indietro</a>
          <button id="create_voci_crea" style="margin-bottom: 12px;" type="submit" class="btn btn-success">Conferma & Crea</button>
        </div>
      </div>
    @else 
      @if($attivita->hasRequisiti() || Auth::id() == $attivita->richiedente->id || $attivita->supervisori() && $attivita->supervisori()->contains('id', Auth::id()))
        <div class="col-md-12 {{ $attivita->percentuale_completamento == 100 ? 'bg-success' : 'bg-info' }}">
          <div class="col-md-1 text-center">
            <i style="margin-top:24px;" class="icon fa fa-5x  fa-pencil-square-o text-primary"></i>
          </div> 
          <div class="col-md-11">
            <h2 class="display-4">{{$attivita->oggetto}} <small>( Cliente: {{ $attivita->cliente->ragione_sociale }} )</small> </h2>
            <p class="lead">Modifica e gestisci l'attività.</p>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <br>
            <div class="info-box">
              <span class="info-box-icon bg-aqua"><i class="fa fa-gears"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">COLLEGAMENTO AUTOMATICO</span>
                <span class="info-box-number"><a type="button" class="btn btn-info pull-left btn-flat" href="{{ route('admin.tasklist.attivita.create', ['attivita_padre' => $attivita->id])}} "><i class="fa fa-eye"></i> Crea Attività Propedeutica </a></span>
              </div>
            </div>
            @if(!$attivita->hasPresoVisione() && $attivita->hasRequisiti() && $attivita->percentuale_completamento != 100)
              <div class="row">
                  <div class="col-md-12">
                      <div class="callout callout-warning">
                      <h4>Presa In Carico</h4>
                      <div class="row">
                          <div class="col-md-8">
                          <p>Non hai preso in carico l'attività che ti è stata assegnata.</p>
                          </div>
                          <div class="col-md-4">
                          <a href="{{ route('admin.tasklist.attivita.presavisione', $attivita) }}" class="btn btn-primary btn-flat pull-right" data-toggle="tooltip" data-placement="top" title="Conferma la presa in carico dell'attività"><i class="fa fa-check"></i> Ho preso in carico.</a>
                          </div>
                      </div>
                      </div>
                  </div>
              </div>
            @endif
            @if($attivita->supervisori() && $attivita->supervisori()->contains('id', Auth::id()) && $attivita->preseVisioni() && $attivita->opzioni['multi_presa_in_carico'] != 1 && $attivita->preseVisioni())
              <div class="row">
                  <div class="col-md-12">
                      <div class="callout callout-danger">
                      <h4>Annulla prese in carico</h4>
                      <div class="row">
                          <div class="col-md-8">
                          <p>Annulla tutte le prese in carico effettuate per l'attività.</p>
                          </div>
                          <div class="col-md-4">
                          <a href="{{ route('admin.tasklist.attivita.presavisione.clear', $attivita) }}" class="btn btn-primary btn-flat pull-right" data-toggle="tooltip" data-placement="top" title="Annulla le prese in carico dell'attività"><i class="fa fa-check"></i> Reset</a>
                          </div>
                      </div>
                      </div>
                  </div>
              </div>
            @endif
            <div class="box box-info">
              <div class="box-body">
                <div class="row">
                  <div class="col-md-12">
                    <span data-toggle="tooltip" data-placement="top" title="Seleziona le attività propedeutiche da completare al 100%">{!! Form::weTags('requisiti', 'Attività Propedeutiche', $errors, $requisiti,  !empty($attivita->requisiti) ? $attivita->requisiti()->pluck('id')->toArray() : (!empty($attivita_padre->id) ? $attivita_padre->id : []) ) !!}</span>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 hidden">
                    {{ Form::weInt('durata_valore', 'Impegno' , $errors, get_if_exist($attivita, 'durata_valore'), ['min' => 0, 'max' => 31]) }}
                  </div>
                  <div class="col-md-12 hidden">
                    {{ Form::weSelect('durata_tipo', '&nbsp;', $errors, $durata_tipo, (get_if_exist($attivita, 'durata_tipo') != '' ? $attivita->durata_tipo : 1)) }}
                  </div>
                  <div class="col-md-12 hidden">
                    {{ Form::weDate('data_inizio', 'Data di Inizio', $errors , get_if_exist($attivita,'data_inizio')) }}
                  </div>
                  <div class="col-md-12 hidden">
                    {{ Form::weSelectSearch('cliente_id', 'Cliente', $errors, [''] + $clienti , !empty($attivita->cliente_id) ? $attivita->cliente_id : [], ['id' => 'cliente']) }}
                  </div>
                  <div class="col-md-4 {{ !empty($attivita->ordinativo_id) ? 'hidden' : '' }}">
                    {{ Form::weSelectSearch('ordinativo_id', 'Ordinativo *', $errors, $ordinativi, get_if_exist($attivita,'ordinativo_id')) }}
                  </div>
                  <div class="col-md-9">
                    {{ Form::weSlider('percentuale_completamento', 'Percentuale completamento', $errors, get_if_exist($attivita,'percentuale_completamento')) }}
                  </div>
                  <div class="col-md-3">
                    {{ Form::weSelect('stato', 'Stato *', $errors, $stati , get_if_exist($attivita,'stato')) }}
                  </div>
                  <hr>
                  <div class="col-md-9">
                    {!! Form::weTags('supervisori_id', 'Supervisori', $errors, $utenti, !empty($attivita->supervisori()) ? $attivita->supervisori()->pluck('id')->toArray() : [] ) !!}
                  </div>
                  <div class="col-md-3 bg-warning">
                    {{ Form::weSelect('priorita', 'Priorità *', $errors, $priorita , (get_if_exist($attivita,'priorita')) ? get_if_exist($attivita,'priorita') : 5) }}
                  </div>
                  <div class="col-md-12">
                    {{ Form::weTags('assegnatari_id', 'Assegnato a *', $errors, $utenti, !empty($attivita->users) ? $attivita->users->pluck('id')->toArray() : [], ['id'=>'assegnatari_id', 'multiple'=>'multiple']) }}
                  </div>
                </div>
                <hr>
                <div class="row">
                  <div class="col-md-6">
                    {{ Form::weText('oggetto', 'Oggetto *', $errors, get_if_exist($attivita, 'oggetto')) }}
                  </div>
                  <div class="col-md-3">
                    {{ Form::weDate('data_inizio', 'Data di Inizio', $errors , get_if_exist($attivita, 'data_inizio')) }}
                  </div>
                  <div class="col-md-3">
                    {{ Form::weDate('data_fine', 'Data Scadenza', $errors, get_if_exist($attivita, 'data_fine')) }}
                  </div>
                  <div class="col-md-3">
                    {{ Form::weSelectSearch('richiedente_id', 'Richiedente *', $errors, $utenti, get_if_exist($attivita, 'richiedente_id')) }}
                  </div>
                  <div class="col-md-3">
                    {!! Form::weSelectSearch('procedura_id', 'Procedura *', $errors, [''] + $procedure, get_if_exist($attivita, 'procedura_id'), ['id'=>'procedura_select']) !!}
                  </div>
                  <div class="col-md-3">
                      {!! Form::weSelectSearch('area_id', 'Area di Intervento *' , $errors, [''] + $aree, get_if_exist($attivita, 'area_id'), ['id'=>'area_select']) !!}
                  </div>
                  <div class="col-md-3">
                      {!! Form::weSelectSearch('gruppo_id', 'Ambito *' , $errors , [''] + $gruppi, get_if_exist($attivita, 'gruppo_id'), ['id'=>'gruppo_select']) !!}
                  </div>
                </div>
                <hr>
                <div class="row">
                  <div class="col-md-12">
                    {{ Form::weTextarea('descrizione', 'Descrizione', $errors, get_if_exist($attivita, 'descrizione')) }}
                  </div>
                </div>
              </div>
              <div class="box-footer bg-black {{ ($attivita->supervisori() && $attivita->supervisori()->contains('id', Auth::id()) && !auth_user()->hasAccess('tasklist.attivita.admin') || $attivita->richiedente->id == Auth::id()) ? '' : 'hidden' }}">
                <h4 class="text-bold">Opzioni</h4>
                @if(auth_user()->hasAccess('commerciale.fatturazioni.create'))
                  <div class="col-md-4">
                      {{ Form::weCheckbox('fatturazione', 'Avviso fatturazione al completamento dell\'attività.', $errors, get_if_exist($attivita,'fatturazione')) }}
                  </div>
                @endif
                <div class="col-md-4">
                  {{ Form::weCheckbox("opzioni[prese_visioni]", 'Richiesta la presa in carico dell\'attività.', $errors, !empty($attivita->opzioni)  ? $attivita->opzioni['prese_visioni'] : 0) }}
                </div>
                <div class="col-md-4">
                  {{ Form::weCheckbox("opzioni[multi_presa_in_carico]", 'Tutti gli assegnatari possono prendere in carico l\'attività.', $errors, !empty($attivita->opzioni) ? $attivita->opzioni['multi_presa_in_carico'] : 0) }}
                </div>
              </div>
            </div> 
            @include('wecore::admin.partials.filesdrop', ['model' => $attivita, 'type' => 'tasklist', 'model_name' => 'Attivita', 'model_path' => 'Tasklist'])
            @include('wecore::admin.partials.note', ['model' => $attivita])
            @include('tasklist::admin.attivita.partials.voci')
          </div> 
        </div>
      @else 
        <div class="col-md-12 bg-danger">
          <div class="col-md-1 text-center">
            <i style="margin-top:24px;" class="icon fa fa-5x fa-ban text-red"></i>
          </div> 
          <div class="col-md-11">
            <h2 class="display-4">Attività Propedeutiche</h2>
            <p class="lead">Per essere lavorata le seguenti attività devono essere completate.</p>
          </div>
          <div class="row">
            <div class="col-md-12">
              <br>
              <div class="callout callout-warning">
                <h4>N.B.</h4>

                <p>Verrai notificato via email quando sarà possibile lavorare a questa attività.</p>
              </div>
              <div class="box box-danger">
                <div class="box-body">
                  <div class="row">
                    @foreach($attivita->requisiti() as $requisito)
                      <div class="col-md-6">
                        <div class="info-box">
                          <span class="info-box-icon {{ $requisito->percentuale_completamento == 100 ? 'bg-green' : 'bg-gray' }}"><i class="fa fa-external-link"></i></span>
                          <div class="info-box-content">
                            <span class="info-box-text">COMPLETAMENTO ATTIVITA' ( <span class="text-bold {{ $requisito->percentuale_completamento == 100 ? 'text-success' : 'text-red' }}"> Progresso: {{ $requisito->percentuale_completamento() }}</span> )</span>
                            <span class="info-box-number"><a target="_blank" href="{{ route('admin.tasklist.attivita.edit', ['attivita' => $requisito->id]) }}">{{ $requisito->oggetto }}</a></span>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div> 
              </div>
            </div> 
          </div>
        </div>
      @endif
    @endif
  </div>
</div>

<script>
  $('#create_campi_avanti').click(function() {
      $('#create_campi').addClass('hidden');
      $('#create_requisiti').removeClass('hidden');
      $("html, body").animate({ scrollTop: 0 }, "slow");
  });
  $('#create_requisiti_indietro').click(function() {
      $('#create_campi').removeClass('hidden');
      $('#create_requisiti').addClass('hidden');
      $("html, body").animate({ scrollTop: 0 }, "slow");
  });

  var step = "<?php Print(request()->step); ?>";
  if(step == 'requisiti'){
    $('#create_campi').addClass('hidden');
  }

  $(document).ready(function() {
    var aree = $.parseJSON(atob("{{ get_json_aree() }}"));
    var gruppi = $.parseJSON(atob("{{ get_json_gruppi() }}"));

    var procedura_select = $('#procedura_select');
    var area_select = $('#area_select');
    var gruppo_select = $('#gruppo_select');

    // Selezione procedura
    procedura_select.change(function(e) {
        area_select.empty();
        gruppo_select.empty();
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
      var select_assegnatari = $('#assegnatari_id').selectize();
      var assegnatari = select_assegnatari[0].selectize;
      var token = $('input[name="_token"]').val();
      assegnatari.clear();
      $.get("{{ route('admin.tasklist.attivita.assegnatari') }}", { _token: token, gruppo:gruppo_selezionato })
        .done(function(data) {
          if(data != '') {
            data = JSON.parse(data);
            for (var key in data) {
              assegnatari.addOption({value:key,text:data[key]});
              assegnatari.addItem(key);
            }
          }
            assegnatari.refreshOptions();
        });
        assegnatari.close();
    });

    // Cliente e ordinativi
    $('#cliente').change(function(e) {
      var clienteId = $(this).val();
      window.location.href = '{{ url()->current() }}?cliente_id=' + clienteId;
    });
  });
</script>