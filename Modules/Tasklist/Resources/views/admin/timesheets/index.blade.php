@extends('layouts.master')

@section('content-header')
<?php //dd($richiesteintervento_azioni) ?>
    <h1>
        {{ trans('tasklist::timesheets.title.timesheets') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('tasklist::timesheets.title.timesheets') }}</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-md-2">
                  {!! Form::weDate('data', '', $errors, get_date_ita($date . ' 00:00:00')) !!}
                </div>
                <div class="col-md-10">
                    @if($daily_timesheets > 0)
                        <div class="btn-group pull-right" style="margin: 0 15px 15px 0;">
                            <a href="{{ route('admin.tasklist.timesheet.edit', ['date' => $date]) }}" class="btn btn-primary btn-flat" style="padding: 4px 10px;">
                                <i class="fa fa-pencil"></i> {{ trans('tasklist::timesheets.button.edit timesheet') }}
                            </a>
                        </div>
                    @endif
                    @if(auth_user()->hasAccess('tasklist.timesheets.manage'))
                        <div class="btn-group pull-right" style="margin: 0 15px 15px 0;">
                            <a href="{{ route('admin.tasklist.timesheet.manage', ['date' => $date]) }}" class="btn btn-warning btn-flat" style="padding: 4px 10px;">
                                <i class="fa fa-cogs"></i> Gestisci timesheet
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header"></div>
                <!-- /.box-header -->
                <div id="table-timesheet" class="box-body">
                  {!! Form::open(['route' => ['admin.tasklist.timesheet.store'], 'method' => 'post']) !!}
                    <input type="hidden" name="date" value="{{ set_date_ita($date) }}">
                    <div class="row">
                        <div class="col-md-3 col-lg-2">
                            {!! Form::weSelectSearch('attivita_id', 'Tasklist Attività', $errors, $attivita, '',['id'=>'attivita_select', 'onchange' => 'tasklistAttivitaSelect(this)']) !!}
                        </div>
                        <div class="col-md-6 col-lg-2">
                              {!! Form::weSelectSearch('cliente_id', 'Cliente *', $errors, $clienti, '' , ['onchange' => 'clienteSelect(this)']) !!}
                        </div>
                        <div class="col-md-6 col-lg-2">
                              {!! Form::weSelectSearch('procedura_id', 'Procedura *', $errors, $procedure, '', ['onchange' => 'proceduraSelect(this)']) !!}
                        </div>
                        <div class="col-md-6 col-lg-2">
                              {!! Form::weSelectSearch('area_id', 'Area di intervento *', $errors, $aree, '', ['onchange' => 'areaSelect(this)']) !!}
                        </div>
                        <div class="col-md-6 col-lg-2">
                              {!! Form::weSelectSearch('gruppo_id', 'Ambito *', $errors, $gruppi, '',['id'=>'gruppo_select'] ) !!}
                        </div>
                        <div class="col-md-3 col-lg-2">
                              {!! Form::weSelectSearch('ordinativo_id', 'Ordinativo *', $errors, $ordinativi, '',['id'=>'ordinativo_select']) !!}
                        </div>
                        <div class="col-md-3 col-lg-6">
                            {!! Form::weText('nota', 'Nota', $errors) !!}
                        </div>
                        <div class="col-md-2">
                            {!! Form::weSelectSearch('tipologia', 'Tipologia *', $errors, $tipologie, '')!!}
                        </div>
                        <div class="col-md-3 col-lg-2">
                            {!! Form::weTime('ora_inizio', "Ora di inizio *", $errors,  ( !empty($timesheets->first()) && $timesheets->last()->ora_fine() == '14:00' ? '15:00' : (!empty($timesheets->first()) ? $timesheets->last()->ora_fine() : '09:00' ) ) ) !!}
                        </div>
                        <div class="col-md-3 col-lg-2">
                            {!! Form::weTime('ora_fine', 'Ora di fine *', $errors) !!}
                        </div>
                    </div>
                    <button class="btn btn-md btn-success text-center" type="submit"><i class="fa fa-floppy-o"> </i> Crea</button>
                  {!! Form::close() !!}
                </div>
                <br>
                @if(count($timesheets) > 0)
                <div class="box box-secondary">
                  <div class="table-responsive">
                      <table class="table table-striped">
                          <tr>
                              {!! order_th('cliente', 'Cliente') !!}
                              {!! order_th('procedura', 'Procedura') !!}
                              {!! order_th('area', 'Area di intervento') !!}
                              {!! order_th('gruppo', 'Ambito') !!}
                              {!! order_th('ordinativo', 'Ordinativo') !!}
                              {!! order_th('attivita', 'Tasklist Attività') !!}
                              {!! order_th('nota', 'Nota') !!}
                              {!! order_th('tipologia', 'Tipologia') !!}
                              {!! order_th('ora_inizio', 'Ora Inizio') !!}
                              {!! order_th('durata', 'Durata') !!}
                          </tr>
                          <tbody>
                          @php $durata_totale = 0; @endphp
                          @foreach ($timesheets->sortBy('dataora_inizio') as $timesheet)
                              <tr>
                                  <td>{{ $timesheet->cliente->ragione_sociale }}</td>
                                  <td>{{ $timesheet->procedura->titolo }}</td>
                                  <td>{{ $timesheet->area->titolo }}</td>
                                  <td>{{ $timesheet->gruppo->nome }}</td>
                                  <td>{{ get_if_exist($timesheet->ordinativo, 'oggetto') }}</td>
                                  <td>{{ get_if_exist($timesheet->attivita, 'oggetto') }}</td>
                                  <td>{{ $timesheet->nota }}</td>
                                  <td>{{ $timesheet->tipologia() }}</td>
                                  <td>{{ date('H:i:s', strtotime($timesheet->dataora_inizio)) }}</td>
                                  <td>{{ $timesheet->durata() }}</td>
                              </tr>
                              @php
                                $durata_totale = $timesheet->durata_time() + $durata_totale;
                              @endphp
                          @endforeach
                          </tbody>
                          @if(!empty($durata_totale))
                          <tfoot>
                            <tr>
                              <td colspan="8" class="text-right">
                                <strong>Durata totale:</strong>
                              </td>
                              <td>
                                <strong>{{ $timesheet->durata($durata_totale) }}</strong>
                              </td>
                            </tr>
                          </tfoot>
                          @endif
                      </table>
                  </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @include('core::partials.delete-modal')
@stop

@section('footer')
    <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp;
@stop

@push('js-stack')
    <?php $locale = locale(); ?>
    <script type="text/javascript">
        $(function () {
            $('.data-table').dataTable({
                "paginate": false,
                "lengthChange": false,
                "filter": false,
                "sort": false,
                "info": false,
                "autoWidth": true,
                "order": [[ 0, "desc" ]],
                "language": {
                    "url": '<?php echo Module::asset("core:js/vendor/datatables/{$locale}.json") ?>'
                }
            });

            // Date change
            $('input[name="data"]').on("dp.change", function() {
              var date = $(this).val();

              window.location.href = window.location.pathname+"?"+$.param({'date':date});
            });
        });

        // Select cliente
        function clienteSelect(el) {
          var clienteId = $(el).val();
          var token = $('input[name="_token"]').val();
          var ordinativo = $('#table-timesheet select[name="ordinativo_id"]');
          var tasklist = $('#table-timesheet select[name="attivita_id"]');
          var procedura = $('#table-timesheet select[name="procedura_id"]');
          var area = $('#table-timesheet select[name="area_id"]');
          var gruppo = $('#table-timesheet select[name="gruppo_id"]');

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
            var token = $('input[name="_token"]').val();
            var cliente = $('#table-timesheet select[name="cliente_id"]');
            var ordinativo = $('#table-timesheet select[name="ordinativo_id"]');
            var procedura = $('#table-timesheet select[name="procedura_id"]');
            var area = $('#table-timesheet select[name="area_id"]');
            var gruppo = $('#table-timesheet select[name="gruppo_id"]');
            var nota = $('#table-timesheet input[name="nota"]');
            var tasklist = $('#table-timesheet select[name="attivita_id"]');

            $.post("{{ route('admin.tasklist.timesheet.timesheetsAjaxRequest') }}", { _token: token, tasklist_attivita: attivita })
                .done(function(data) {
                    if($.trim(data) != ''){
                        //if(cliente.val() == 0){
                        if("ordinativo" in data) {
                            var chiave = Object.keys(data.ordinativo)[0];
                            ordinativo.empty();
                            ordinativo.append(new Option(data.ordinativo[chiave], chiave));
                        }
                        if("procedura" in data) {
                            var chiave = Object.keys(data.procedura)[0]
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

        var aree = $.parseJSON(atob("{{ get_json_aree() }}"));
        var gruppi = $.parseJSON(atob("{{ get_json_gruppi() }}"));

        // Select procedura
        function proceduraSelect(el) {
          var row = $(el).attr('data-row');
          var procedura = $('#table-timesheet select[name="procedura_id"]');
          var area = $('#table-timesheet select[name="area_id"]');
          var gruppo = $('#table-timesheet select[name="gruppo_id"]');
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
        var row = $(el).attr('data-row');
        var area = $(el);
        var gruppo = $('#table-timesheet select[name="gruppo_id"]');
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
    </script>
@endpush
