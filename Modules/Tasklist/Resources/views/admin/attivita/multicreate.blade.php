@extends('layouts.master')

@section('content-header')
    <h1>
        Creazione attività multiple
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('Attività') }}</li>
    </ol>
@stop

 {{-- Oggetto, ordinativo_id, richiedente_id, assegnatari_id, procedura_id, area_id, gruppo_id, priorità, durata_tipo, stato --}}

@section('content')
    {!! Form::open(['route' => ['admin.tasklist.attivita.multistore'], 'method' => 'post', 'files'=> true]) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                @include('partials.form-tab-headers')
                <div class="tab-content">
                    <input type="hidden" name="ordinativo_id" value="{{ $request->ordinativo_id }}" />
                    <input type="hidden" name="cliente_id" value="{{ $request->cliente_id }}" />
                    <div class="col-md-12 bg-green" style="margin-bottom:14px;">
                        <div class="col-md-1 text-center">
                            <i style="margin-top:24px;" class="icon fa fa-5x  fa-pencil-square-o text-success"></i>
                        </div> 
                        <div class="col-md-11">
                            <h2 class="display-4">Creazione attività per {{ $ordinativo->oggetto }} </h2>
                            <p class="lead">{{ $cliente->ragione_sociale }}</p>
                        </div>
                    </div>
                    <div class="tab-pane active">
                        <div class="box-body">
                            <div id="table-attivita">
                                <div class="row bg-odd" data-id="1">
                                    <br>
                                    <div class="col-md-7">
                                        {!! Form::weText("attivita['1'][oggetto]", 'Oggetto *', $errors, '') !!}
                                    </div>
                                    <div class="col-md-3">
                                        {{ Form::weSelectSearch("attivita['1'][richiedente_id]", 'Richiedente *', $errors, $utenti, Auth::id()) }}
                                    </div>
                                    <div class="col-md-2">
                                        {!! Form::weDate("attivita['1'][data_inizio]", 'Data Inizio *', $errors, date('d/m/Y')) !!}
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::weSelectSearch("attivita['1'][procedura_id]", 'Procedura *', $errors, $procedure, '', ['onchange' => 'proceduraSelect(this)', 'data-row' => 1]) !!}
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::weSelectSearch("attivita['1'][area_id]", 'Area di intervento *', $errors, $aree, '', ['onchange' => 'areaSelect(this)', 'data-row' => 1]) !!}
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::weSelectSearch("attivita['1'][gruppo_id]", 'Ambito *', $errors, $gruppi, '', ['onchange'=>'getAssegnatari(1)']) !!}
                                    </div>
                                    <div class="col-md-6">
                                        {{ Form::weTags("attivita['1'][assegnatari_id]", 'Assegnata a *', $errors, $utenti, [], ['multiple'=>'multiple']) }}
                                    </div>
                                    <div class="col-md-6">
                                        {{ Form::weTags("attivita['1'][supervisori_id]", 'Supervisori', $errors, $utenti, [], ['multiple'=>'multiple']) }}
                                    </div>
                                    <div class="col-md-12">
                                        {{ Form::weTextarea("attivita['1'][descrizione]", 'Descrizione', $errors) }}
                                    </div>
                                    <div class="col-12 text-center">
                                        <h4 class="text-bold" style="margin-left:8px;">Opzioni</h4>
                                        @if(auth_user()->hasAccess('commerciale.fatturazioni.create'))
                                          <div class="col-md-3">
                                            <label><input name="attivita['1'][fatturazione]" type="checkbox" style="margin-right:3px;"> Avviso fatturazione al completamento dell'attività.</label>
                                          </div>
                                        @endif
                                        <div class="col-md-3">
                                            <label><input name="attivita['1'][prese_visioni]" type="checkbox" style="margin-right:3px;" checked> Richiesta la presa in carico dell'attività.</label>
                                        </div>
                                        <div class="col-md-3">
                                            <label><input name="attivita['1'][multi_presa_in_carico]" type="checkbox" style="margin-right:3px;" checked> Tutti gli assegnatari possono prendere in carico l'attività.</label>
                                        </div>
                                        <div class="col-md-3">
                                            <button style="margin-bottom:14px;" class="btn btn-md btn-flat btn-danger" type="button" onclick="removeAttivita(1)"><i class="fa fa-trash"> </i></button>
                                            <button style="margin-bottom:14px;" class="btn btn-md btn-flat btn-warning" type="button" onclick="duplicateAttivita(1)"><i class="fa fa-copy"> </i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="text-center">
                                <button id="add-timesheet" type="button" class="btn btn-md btn-flat btn-primary" onclick="newAttivita(1)"><i class="fa fa-plus"> </i> Aggiungi</button>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-success btn-flat"><i class="fa fa-floppy-o"></i> Salva</button>
                        <a class="btn btn-warning pull-right btn-flat" href="{{ route('admin.tasklist.timesheet.index', ['date' => request('date')])}}"><i class="fa fa-arrow-left"></i> Indietro</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@stop
@push('js-stack')
<script>
    // Select procedura
    function proceduraSelect(el) {
      var aree = $.parseJSON(atob("{{ get_json_aree() }}"));
      var row = $(el).attr('data-row');
      var procedura = $('.row[data-id="'+row+'"] select[name*="[procedura_id]"]');
      var area = $('.row[data-id="'+row+'"] select[name*="[area_id]"]');
      var gruppo = $('.row[data-id="'+row+'"] select[name*="[gruppo_id]"]');
      var procedura_selezionata = procedura.val();

      area.empty();
      gruppo.empty();

      var newOption = new Option(" ", 0, false, false);
        area.append(newOption);

      aree.forEach(element => {
          // scorro tutto l'array e controllo quelli che hanno il procedura id  =  alla procedura selezionata
          if(element.procedura_id == procedura_selezionata){
              var newOption = new Option(element.titolo, element.id, false, false);
              area.append(newOption);
          }
      });

      //assegnare nuove_aree_di_intervento alla select delle aree di intervento
      //area_select.trigger('change');
      area.select2('open');
      gruppo.select2('close');
  }

  // Select area
  function areaSelect(el) {
    var gruppi = $.parseJSON(atob("{{ get_json_gruppi() }}"));
    var row = $(el).attr('data-row');
    var area = $(el);
    var gruppo = $('.row[data-id="'+row+'"] select[name*="[gruppo_id]"]');
    var area_selezionata = $(el).val();

    gruppo.empty();

    var newOption = new Option(" ", 0, false, false);
    gruppo.append(newOption);

    gruppi.forEach(element => {
      //scorro tutto l'array e controllo quelli che hanno il procedura id  =  alla procedura selezionata
      if(element.area_id == area_selezionata){

          var newOption = new Option(element.nome, element.id, false, false);
          gruppo.append(newOption);
      }
    });
    //assegnare nuove_attivita alla select delle attivita

    gruppo.select2('open');
    gruppo.trigger('change');
  }

    // Duplicate activity
    function duplicateAttivita(id) {
        var oldrow = $('#table-attivita .row[data-id="'+id+'"]');
        var row = oldrow.clone();
        var newId = parseInt($('#table-attivita .row:last').attr('data-id')) + 1;
        row.attr('data-id', newId);
        row.find('select[data-row]').attr('data-row', newId);
        row.find('input[data-row]').attr('data-row', newId);
        row.find('select[name*="[richiedente_id]"]').attr('name', "attivita['"+newId+"'][richiedente_id]");
        row.find('select[name*="[procedura_id]"]').attr('name', "attivita['"+newId+"'][procedura_id]");
        row.find('select[name*="[area_id]"]').attr('name', "attivita['"+newId+"'][area_id]");
        row.find('select[name*="[gruppo_id]"]').attr('name', "attivita['"+newId+"'][gruppo_id]");

        row.find('textarea[name*="[descrizione]"]').attr('name', "attivita['"+newId+"'][descrizione]");
        row.find('textarea[name*="[descrizione]"]').attr('id', "attivita['"+newId+"'][descrizione]");

        row.find('input[name*="[fatturazione]"]').attr('name', "attivita['"+newId+"'][fatturazione]");
        row.find('input[name*="[prese_visioni]"]').attr('name', "attivita['"+newId+"'][prese_visioni]");
        row.find('input[name*="[multi_presa_in_carico]"]').attr('name', "attivita['"+newId+"'][multi_presa_in_carico]");

        row.find('select[name*="[supervisori_id]"]').attr('name', "attivita['"+newId+"'][supervisori_id][]");
        row.find('select[name*="[assegnatari_id]"]').attr('name', "attivita['"+newId+"'][assegnatari_id][]");

        var richiedente_id = oldrow.find('select[name*="[richiedente_id]"]').val();
        var procedura_id = oldrow.find('select[name*="[procedura_id]"]').val();
        var area_id = oldrow.find('select[name*="[area_id]"]').val();
        var gruppo_id = oldrow.find('select[name*="[gruppo_id]"]').val();

        var supervisori_id = oldrow.find('select[name*="[supervisori_id]"]')[0].selectize.options;
        var assegnatari_id = oldrow.find('select[name*="[assegnatari_id]"]')[0].selectize.options;

        row.find('input[name*="[oggetto]"]').attr('name', "attivita['"+newId+"'][oggetto]").attr('id', "attivita['"+newId+"'][oggetto]");
        row.find('input[name*="[data_inizio]"]').attr('name', "attivita['"+newId+"'][data_inizio]").attr('id', "attivita['"+newId+"'][data_inizio]");
        row.find('.btn-danger').attr('onclick', 'removeAttivita(' + newId + ')');
        row.find('.btn-warning').attr('onclick', 'duplicateAttivita(' + newId + ')');
        row.find('span.select2').remove();
        row.find('select[multiple!=multiple]').removeClass('select2-hidden-accessible');
        row.find('select[multiple!=multiple]').removeAttr('data-select2-id');

        row.find('select[name*="[gruppo_id]"]').attr('onchange', 'getAssegnatari('+newId+')');

        row.find('div.plugin-remove_button').remove();
        row.find('div.selectize-input.items.not-full').remove();

        $('#table-attivita').append(row);

        row.find('select[name*="[richiedente_id]"]').val(richiedente_id);
        row.find('select[name*="[procedura_id]"]').val(procedura_id);
        row.find('select[name*="[area_id]"]').val(area_id);
        row.find('select[name*="[gruppo_id]"]').val(gruppo_id);

        row.find("select[multiple!=multiple]").select2();

        supervisor = row.find('select[name*="[supervisori_id]"]').selectize({plugins: ["remove_button"]});
        $.each(supervisori_id, function() {
            supervisor[0].selectize.addOption(this);
        });

        assegnator = row.find('select[name*="[assegnatari_id]"]').selectize({plugins: ["remove_button"]});
        $.each(assegnatari_id, function() {
            assegnator[0].selectize.addOption(this);
        });
        assegnator[0].selectize.refreshItems();
    }

    // Create new activity
    function newAttivita(id) {
        var oldrow = $('#table-attivita .row[data-id="'+id+'"]');
        var row = oldrow.clone();
        var newId = parseInt($('#table-attivita .row:last').attr('data-id')) + 1;
        row.attr('data-id', newId);
        row.find('select[data-row]').attr('data-row', newId);
        row.find('input[data-row]').attr('data-row', newId);
        row.find('select[name*="[richiedente_id]"]').attr('name', "attivita['"+newId+"'][richiedente_id]");
        row.find('select[name*="[procedura_id]"]').attr('name', "attivita['"+newId+"'][procedura_id]");
        row.find('select[name*="[area_id]"]').attr('name', "attivita['"+newId+"'][area_id]");
        row.find('select[name*="[gruppo_id]"]').attr('name', "attivita['"+newId+"'][gruppo_id]");

        row.find('textarea[name*="[descrizione]"]').attr('name', "attivita['"+newId+"'][descrizione]");
        row.find('textarea[name*="[descrizione]"]').attr('id', "attivita['"+newId+"'][descrizione]");

        row.find('input[name*="[prese_visioni]"]').attr('name', "attivita['"+newId+"'][prese_visioni]");
        row.find('input[name*="[multi_presa_in_carico]"]').attr('name', "attivita['"+newId+"'][multi_presa_in_carico]");

        row.find('select[name*="[supervisori_id]"]').attr('name', "attivita['"+newId+"'][supervisori_id][]");
        row.find('select[name*="[assegnatari_id]"]').attr('name', "attivita['"+newId+"'][assegnatari_id][]");

        //row.find('select option:selected').removeAttr("selected").trigger('change');
        row.find('input').val('');

        var supervisori_id = oldrow.find('select[name*="[supervisori_id]"]')[0].selectize.options;
        var assegnatari_id = oldrow.find('select[name*="[assegnatari_id]"]')[0].selectize.options;

        var assegnatari_tags = row.find('select[name*="[assegnatari_id]"]').selectize({plugins: ["remove_button"]});
        var assegnatari_tags_selectize = assegnatari_tags[0].selectize;
        assegnatari_tags_selectize.clear();

        var supervisori_tags = row.find('select[name*="[supervisori_id]"]').selectize({plugins: ["remove_button"]});
        var supervisori_tags_selectize = supervisori_tags[0].selectize;
        supervisori_tags_selectize.clear();

        row.find('input[name*="[oggetto]"]').attr('name', "attivita['"+newId+"'][oggetto]").attr('id', "attivita['"+newId+"'][oggetto]");
        row.find('input[name*="[data_inizio]"]').attr('name', "attivita['"+newId+"'][data_inizio]").attr('id', "attivita['"+newId+"'][data_inizio]");
        row.find('.btn-danger').attr('onclick', 'removeAttivita(' + newId + ')');
        row.find('.btn-warning').attr('onclick', 'duplicateAttivita(' + newId + ')');
        row.find('span.select2').remove();
        row.find('select[multiple!=multiple]').removeClass('select2-hidden-accessible');
        row.find('select[multiple!=multiple]').removeAttr('data-select2-id');

        row.find('select[name*="[gruppo_id]"]').attr('onchange', 'getAssegnatari('+newId+')');

        row.find('div.plugin-remove_button').remove();
        //row.find('div.selectize-input.items.not-full').remove();

        $('#table-attivita').append(row);

        row.find("select[multiple!=multiple]").select2();

        supervisor = row.find('select[name*="[supervisori_id]"]').selectize();
        $.each(supervisori_id, function() {
            supervisor[0].selectize.addOption(this);
        });

        assegnator = row.find('select[name*="[assegnatari_id]"]').selectize();
        $.each(assegnatari_id, function() {
            assegnator[0].selectize.addOption(this);
        });
    }

      // Remove activity
    function removeAttivita(id) {
        var ctrlRows = $('#table-attivita .row').length;
        var row = $('.row[data-id="'+id+'"]');

        if(ctrlRows > 1) {
            row.remove();
        } else {
            row.find('input').val('');
            row.find('input').attr('checked', false);
            row.find('textarea').val('');
            row.find('select option:selected').removeAttr("selected").trigger('change');
            var $select = row.find("select[multiple='multiple']").selectize();
            var assegnatari = $select[0].selectize;
            var supervisori = $select[1].selectize;
            assegnatari.clear();
            supervisori.clear();
        }
    }

    // Get Assegnatari from group
    function getAssegnatari(id_row) {
      var gruppo_selezionato = $('div.row.bg-odd[data-id="'+id_row+'"] select[name*="[gruppo_id]"]').val();
      var select_assegnatari = $('div.row.bg-odd[data-id="'+id_row+'"] select[name*="[assegnatari_id]"]').selectize();
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
    }
</script>
@endpush
