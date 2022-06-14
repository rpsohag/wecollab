@php
 //dd($errors);   
@endphp

<div class="box-body">

  {{-- <input type="hidden" name="date" value="{{ request('date') }}"> --}}
  {{-- <div class="table-responsive"> --}}
    <div>
      <div id="table-timesheet">
            @if(!empty($timesheets))
              @foreach ($timesheets->sortBy('dataora_inizio') as $timesheet)
                <div class="row bg-odd" data-id="{{ $timesheet->id }}" data-type="edit">
                    <div class="col-md-3">
                      {!! Form::weSelectSearch('timesheet[edit]['.$timesheet->id.'][attivita_id]', 'Tasklist Attività', $errors, $attivita, get_if_exist($timesheet, 'attivita_id'), ['onchange' => 'tasklistAttivitaSelect(this)', 'data-row' => $timesheet->id] ) !!}
                    </div>
                    <div class="col-md-3">
                      {!! Form::weSelectSearch('timesheet[edit]['.$timesheet->id.'][cliente_id]', 'Cliente *', $errors, $clienti, get_if_exist($timesheet, 'cliente_id'), ['onchange' => 'clienteSelect(this)', 'data-row' => $timesheet->id]) !!}
                    </div>
                    <div class="col-md-2">
                      {!! Form::weSelectSearch('timesheet[edit]['.$timesheet->id.'][procedura_id]', 'Procedura *', $errors, $procedure, get_if_exist($timesheet, 'procedura_id'), ['onchange' => 'proceduraSelect(this)', 'data-row' => $timesheet->id]) !!}
                    </div>
                    <div class="col-md-2">
                      {!! Form::weSelectSearch('timesheet[edit]['.$timesheet->id.'][area_id]', 'Area di intervento *', $errors, $aree, get_if_exist($timesheet, 'area_id'), ['onchange' => 'areaSelect(this)', 'data-row' => $timesheet->id]) !!}
                    </div>
                    <div class="col-md-2">
                      {!! Form::weSelectSearch('timesheet[edit]['.$timesheet->id.'][gruppo_id]', 'Ambito *', $errors, $gruppi, get_if_exist($timesheet, 'gruppo_id'), ['data-row' => $timesheet->id]) !!}
                    </div>
                    <div class="col-md-3">
                      {!! Form::weSelectSearch('timesheet[edit]['.$timesheet->id.'][ordinativo_id]', 'Ordinativo *', $errors, $ordinativi, get_if_exist($timesheet, 'ordinativo_id'), ['data-row' => $timesheet->id]) !!}
                    </div>
                    <div class="col-md-4">
                      {!! Form::weText('timesheet[edit]['.$timesheet->id.'][nota]', 'Nota', $errors, get_if_exist($timesheet, 'nota'), ['data-row' => $timesheet->id]) !!}
                    </div>
                    <div class="col-md-2">
                      {!! Form::weSelectSearch('timesheet[edit]['.$timesheet->id.'][tipologia]', 'Tipologia *', $errors, $tipologie, get_if_exist($timesheet, 'tipologia'), ['data-row' => $timesheet->id]) !!}
                    </div>
                    <div class="col-md-1">
                      {!! Form::weTime('timesheet[edit]['.$timesheet->id.'][ora_inizio]', 'Ora Inizio *', $errors, $timesheet->ora_inizio(), ['data-row' => $timesheet->id]) !!}
                    </div>
                    <div class="col-md-1">
                      {!! Form::weTime('timesheet[edit]['.$timesheet->id.'][ora_fine]', 'Ora Fine *', $errors, $timesheet->ora_fine(), ['data-row' => $timesheet->id]) !!}
                    </div>
                    <div class="col-md-1 hidden">
                      {!! Form::weText('timesheet[edit]['.$timesheet->id.'][id]', 'ID', $errors, $timesheet->id, ['data-row' => $timesheet->id]) !!}
                    </div>
                    <div class="col-md-1">
                      <button style="margin-top: 24px;" class="btn btn-md btn-flat btn-danger" type="button" onclick="removeTimesheet({{ $timesheet->id }})"><i class="fa fa-trash"> </i></button>
                      <button style="margin-top: 24px;" class="btn btn-md btn-flat btn-warning" type="button" onclick="duplicateTimesheet({{ $timesheet->id }})"><i class="fa fa-copy"> </i></button>
                    </div>
                </div>
              @endforeach
            @endif
      </div>
      <br>
      <div class="text-center">
          <button id="add-timesheet" type="button" class="btn btn-md btn-flat btn-primary"><i class="fa fa-plus"> </i> Aggiungi</button>
      </div>
      <!-- /.box-body -->
  </div>
  {{-- </div> --}}
</div>

{{-- @include('wecore::admin.partials.js_procedure_aree_attivita') --}}
@push('js-stack')
  <script>
    var new_timesheets_counter = 0;

document.addEventListener("DOMContentLoaded", function(event) { 
    // Add timesheet
    $('#add-timesheet').click(function() {
      var row = $('#table-timesheet .row').last().clone();
      var oldId = parseInt(row.attr('data-id'));
      var newId = {{ $latest_timesheet_id }} + 1 + new_timesheets_counter;
      new_timesheets_counter++;
      row.attr('data-id', newId);
      row.find('select[data-row]').attr('data-row', newId);
      row.find('select[name*="[cliente_id]"] option:selected').removeAttr("selected");
      row.find('select[name*="[attivita_id]"] option:selected').removeAttr("selected");
      row.find('select[name*="[ordinativo_id]"]').empty();
      row.find('select[name*="[gruppo_id]"]').empty();
      row.find('select[name*="[area_id]"]').empty();
      row.find('select[name*="[procedura_id]"] option:selected').removeAttr("selected");
      row.find('input').attr('value', '');
      row.find('input[name*="id"]').attr('value', newId);
      row.find('input[data-row]').attr('data-row', newId);
      row.find('span.select2').remove();
      row.find('.btn-danger').attr('onclick', 'removeTimesheet(' + newId + ')');
      row.find('.btn-warning').attr('onclick', 'duplicateTimesheet(' + newId + ')');

      var newRow = row[0].outerHTML.split('timesheet[edit]['+oldId+']').join('timesheet[edit]['+newId+']')
                                    .split('removeTimesheet('+oldId).join('removeTimesheet('+newId);

      $('#table-timesheet').append(newRow);

      bootJs();
    });
  });

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

      // Duplicate timesheet
      function duplicateTimesheet(id) {
        var row = $('#table-timesheet .row[data-id="'+id+'"]').clone();
        var newId = {{ $latest_timesheet_id }} + 1 + new_timesheets_counter;
        var last_date = row.find('input[name*="ora_fine"]').val();
        new_timesheets_counter++;
        row.attr('data-id', newId);
        row.find('select[data-row]').attr('data-row', newId);
        row.attr('data-type', 'edit');
        row.find('input[data-row]').attr('data-row', newId);
        row.find('input[name*="id"]').attr('value', newId);
        row.find('span.select2').remove();
        if(last_date == '14:00'){
          row.find('input[name*="ora_inizio"]').attr('value', '15:00');
          row.find('input[name*="ora_fine"]').attr('value', '');
        }
        row.find('.btn-danger').attr('onclick', 'removeTimesheet(' + newId + ')');
        row.find('.btn-warning').attr('onclick', 'duplicateTimesheet(' + newId + ')');

        var newRow = row[0].outerHTML.split('timesheet[edit]['+id+']').join('timesheet[edit]['+newId+']')
                                      .split('removeTimesheet('+id).join('removeTimesheet('+newId);

        $('#table-timesheet').append(newRow);

        bootJs();
      }

      // Remove timesheet
      function removeTimesheet(id) {
        var ctrlRows = $('#table-timesheet .row').length;
        var row = $('.row[data-id="'+id+'"][data-type="edit"]');

        if(ctrlRows > 1)
          row.remove();
        else {
          row.find('select, input').val('');
          row.find('select2, input').val('');
          bootJs();
        }
      }

      // Select cliente
      function clienteSelect(el) {
        var clienteId = $(el).val();
        var row = $(el).attr('data-row');
        var token = $('input[name="_token"]').val();
        var ordinativo = $('#table-timesheet .row[data-id="'+row+'"] select[name*="[ordinativo_id]"]');
        var tasklist = $('#table-timesheet .row[data-id="'+row+'"] select[name*="[attivita_id]"]');
        var procedura = $('#table-timesheet .row[data-id="'+row+'"] select[name*="[procedura_id]"]');
        var area = $('#table-timesheet .row[data-id="'+row+'"] select[name*="[area_id]"]');
        var gruppo = $('#table-timesheet .row[data-id="'+row+'"] select[name*="[gruppo_id]"]');

        // area.empty();
        // gruppo.empty();

        // procedura.select2('open');

        $.post("{{ route('admin.tasklist.timesheet.timesheetsAjaxRequest') }}", { _token: token, cliente: clienteId })
          .done(function(data) {
              if("ordinativi" in data) {
                  ordinativo.empty();

                  for (var key in data.ordinativi) {
                      var newOption = new Option(data.ordinativi[key],key);
                      ordinativo.append(newOption);
                  }
              }

              if("tasklistAttivita" in data) {
                tasklist.empty();

                for (var key in data.tasklistAttivita){
                    var newOption = new Option(data.tasklistAttivita[key],key);
                    tasklist.append(newOption);
                }
              }
            });
      }

        // Select TasklistAttivita
        function tasklistAttivitaSelect(el) {
            var attivita = $(el).val();
            var row = $(el).attr('data-row');
            var token = $('input[name="_token"]').val();

            var ordinativo = $('#table-timesheet .row[data-id="'+row+'"] select[name*="[ordinativo_id]"]');
            var tasklist = $('#table-timesheet .row[data-id="'+row+'"] select[name*="[attivita_id]"]');
            var procedura = $('#table-timesheet .row[data-id="'+row+'"] select[name*="[procedura_id]"]');
            var area = $('#table-timesheet .row[data-id="'+row+'"] select[name*="[area_id]"]');
            var gruppo = $('#table-timesheet .row[data-id="'+row+'"] select[name*="[gruppo_id]"]');
            var cliente = $('#table-timesheet .row[data-id="'+row+'"] select[name*="[cliente_id]"]');
            var nota = $('#table-timesheet .row[data-id="'+row+'"] input[name*="nota"]');

            $.post("{{ route('admin.tasklist.timesheet.timesheetsAjaxRequest') }}", { _token: token, tasklist_attivita: attivita })
                .done(function(data) {
                    if($.trim(data) != ''){
                        if("ordinativo" in data) {
                            var chiave = Object.keys(data.ordinativo)[0];
                            ordinativo.empty();
                            ordinativo.append(new Option(data.ordinativo[chiave], chiave));
                        }
                        if("procedura" in data) {
                            var chiave = Object.keys(data.procedura)[0];
                            procedura.empty();
                            procedura.append(new Option(data.procedura[chiave], chiave));
                        }
                        if("area" in data) {
                            var chiave = Object.keys(data.area)[0];
                            area.empty();
                            area.append(new Option(data.area[chiave], chiave));
                        }
                        if("gruppo" in data) {
                            var chiave = Object.keys(data.gruppo)[0];
                            gruppo.empty();
                            gruppo.append(new Option(data.gruppo[chiave], chiave));
                        }
                        if("cliente" in data) {
                            var chiave = Object.keys(data.cliente)[0];
                            cliente.empty();
                            cliente.append(new Option(data.cliente[chiave], chiave));
                        }
                        if("nota" in data) {
                            nota.empty();
                            nota.val(data.nota);
                        }
                    } else {
                        $.post("{{ route('admin.tasklist.timesheet.timesheetsAjaxRequest') }}", { _token: token, reset: 'reset' })
                            .done(function(index) {
                                if($.trim(index) != ''){
                                    if("clienti" in index) {
                                        cliente.empty();
                                        for (var key in index.clienti) {
                                            var newOption = new Option(index.clienti[key],key);
                                            cliente.append(newOption);
                                        }
                                    }
                                    if("procedure" in index) {
                                        procedura.empty();
                                        for (var key in index.procedure) {
                                            var newOption = new Option(index.procedure[key],key);
                                            procedura.append(newOption);
                                        }
                                    }
                                    if("attivita" in index) {
                                        tasklist.empty();
                                        for (var key in index.attivita) {
                                            var newOption = new Option(index.attivita[key],key);
                                            tasklist.append(newOption);
                                        }
                                    }
                                    nota.val('');
                                    gruppo.empty();
                                    area.empty();
                                    ordinativo.empty();
                                }
                        });
                    }
            });
        }
  </script>
@endpush
