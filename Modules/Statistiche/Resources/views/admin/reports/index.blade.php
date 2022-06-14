@extends('layouts.master')

@section('content-header')
    <h1>Reports</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">Reports</li>
    </ol>
@endsection

@section('content')
<div class="box-body">
  <div class="box box-primary box-shadow">
    <div class="box-header with-border">
      <section class="bg-gray filters">
        {!! Form::open(['route' => ['admin.statistiche.reports.index'], 'method' => 'get']) !!}
          <div class="row">
            <div class="col-md-10">
                <div class="row">
                  <input type="hidden" name="reports" value="{{ request('reports') }}" />
                  <div class="col-md-2">
                    {!! Form::weSelectSearch('cliente_id', 'Cliente', $errors, $clienti) !!}
                  </div>
                  <div class="col-md-3">
                    {!! Form::weSelectSearch('area_id', 'Area Intervento', $errors, $aree) !!}
                  </div>
                  <div class="col-md-2">
                    {!! Form::weDate('data_inizio', 'Data inizio', $errors, get_date_ita(request('data_inizio'))) !!}
                  </div>
                  <div class="col-md-2">
                      {!! Form::weDate('data_fine', 'Data fine', $errors, get_date_ita(request('data_fine'))) !!}
                  </div>
                </div>
            </div>
            <div class="col-md-2 text-right">
                {!! Form::weSubmit('Cerca') !!}
                {!! Form::weReset('Svuota') !!}
            </div>
          </div>
          <input type="hidden" name="order[by]" value="{{ (!empty(request('order')['by']) ? request('order')['by'] : null) }}">
          <input type="hidden" name="order[sort]" value="{{ (!empty(request('order')['sort']) ? request('order')['sort'] : null) }}">
        {!! Form::close() !!}
      </section>
    </div>
    <div class="box-body">
      <div class="nav-tabs-custom">
        @include('partials.form-tab-headers')
        <div class="tab-content">
            <ul class="nav nav-tabs">
                <li class="{{ empty(request('reports')) || request('reports') == 'assistenza' ? 'active' : '' }}"><a href="{{ route('admin.statistiche.reports.index', ['reports' => 'assistenza']) }}">Assistenza</a></li>
                <li class="{{ request('reports') == 'timesheets' ? 'active' : '' }}"><a href="{{ route('admin.statistiche.reports.index', ['reports' => 'timesheets']) }}">Timesheets</a></li>
                <li class="{{ request('reports') == 'rapporti' ? 'active' : '' }}"><a href="{{ route('admin.statistiche.reports.index', ['reports' => 'rapporti']) }}">Rapporti</a></li>
            </ul>
            @if(empty(request('reports')) || request('reports') == 'assistenza')
              <div class="row">
                <div class="col-md-12">
                  <div class="box box-warning">
                    <div class="box-header">
                        <h4><strong>Assistenza</strong></h4>
                    </div>
                    <div class="box-body">
                      <div class="table-responsive">
                        <table class="table table-striped">
                          <tr>
                            <th>Dipendente</th>
                            <th>In Lavorazione</th>
                            <th>Chiusi</th>
                            <th>Totali</th>
                            <th>Tempo Di Lavorazione</th>
                          </tr>
                          <tbody>
                            @foreach ($utenti as $utente)
                              <tr>
                                <td>{!! $utente->full_name !!}</td>
                                <td>{{ $utente->tickets(set_date_ita(request('data_inizio')), set_date_ita(request('data_fine')), "lavorati", request('cliente_id'), request('area_id'))->count() }}</td>
                                <td>{{ $utente->tickets(set_date_ita(request('data_inizio')), set_date_ita(request('data_fine')), "chiusi", request('cliente_id'), request('area_id'))->count() }}</td>
                                <td>{{ $utente->tickets(set_date_ita(request('data_inizio')), set_date_ita(request('data_fine')), "totali", request('cliente_id'), request('area_id'))->count() }}</td>
                                <td>{!! $utente->tempo_lavorazione(set_date_ita(request('data_inizio')), set_date_ita(request('data_fine')), "totale", request('cliente_id'), request('area_id')) !!}</td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @endif
            @if(request('reports') == 'timesheets')
              <div class="row">
                <div class="col-md-12">
                  <div class="box box-primary">
                    <div class="box-header">
                        <h4><strong>Timesheets</strong></h4>
                    </div>
                    <div class="box-body">
                      <div class="table-responsive">
                        <table class="table table-striped">
                          <tr>
                            <th>Dipendente</th>
                            <th>Timesheet Totali</th>
                            <th>Durata Totale</th>
                            <th>Dettaglio</th>
                          </tr>
                          <tbody>
                            @foreach ($utenti as $utente)
                              @php
                                $utente_timesheets = $utente->timesheets(null, request('cliente_id'), request('area_id'), set_date_ita(request('data_inizio')), set_date_ita(request('data_fine')))->get();
                                $timesheets_automatici = collect($utente_timesheets)->filter(function ($item) {
                                    return false !== strpos($item->nota, '( Azione ) :');
                                })->count();
                                $timesheets_manuali = $utente_timesheets->count() - $timesheets_automatici;
                              @endphp
                              @if($utente_timesheets->count() > 0)
                                <tr>
                                  <td>{!! $utente->full_name !!}</td>
                                  <td><strong data-toggle="tooltip" title="Totale timesheets">{!! $utente_timesheets->count() !!}</strong> {!! ' - ( <i data-toggle="tooltip" title="Totale timesheets manuali" class="fa fa-user" style="color:rgb(30, 122, 30);"></i> ' . $timesheets_manuali . ' <i data-toggle="tooltip" title="Totale timesheets automatici" class="fa fa-cogs" style="color:rgb(66, 61, 61);"></i> ' . $timesheets_automatici . ' )' !!}</td>
                                  @php  
                                  $durata_media = 0;
                      
                                  foreach($utente_timesheets as $timesheet)
                                    $durata_media = $timesheet->durata_time() + $durata_media;
                      
                                  $durata_totale = get_seconds_to_hours(round($durata_media));
                                  @endphp
                                  <td>{{ $durata_totale }}</td>
                                  <td><button type="button" class="btn btn-success btn-flat timesheets-button" data-data_inizio="{{ request('data_inizio') }}" data-data_fine="{{ request('data_fine') }}" data-utente_id="{{ $utente->id }}" data-area_id="{{ request('area_id') }}" data-cliente_id="{{ request('cliente_id') }}"><i class="fa fa-eye"></i></button></td>
                                </tr>
                              @else 
                                <tr>
                                  <td>{!! $utente->full_name !!}</td>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                </tr>
                              @endif
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @endif
            @if(request('reports') == 'rapporti')
              <div class="row">
                <div class="col-md-12">
                  <div class="box box-warning">
                    <div class="box-header">
                        <h4><strong>Rapporti</strong></h4>
                    </div>
                    <div class="box-body">
                      <div class="table-responsive">
                        <table class="table table-striped">
                          <tr>
                            <th>Dipendente</th>
                            <th>Rapporti Totali</th>
                            <th>Dettaglio</th>
                          </tr>
                          <tbody>
                            @foreach ($utenti as $utente)
                              @php $utente_rapporti = $utente->rapporti(set_date_ita(request('data_inizio')), request('cliente_id'), request('area_id'))->get(); @endphp
                              <tr>
                                <td>{{ $utente->full_name }}</td>
                                <td>{{ $utente_rapporti->count() }}</td>
                                <td>
                                  @if($utente_rapporti->count() > 0)
                                    <button type="button" class="btn btn-success btn-flat rapporti-button" data-data="{{ request('data_inizio') }}" data-utente_id="{{ $utente->id }}" data-area_id="{{ request('area_id') }}" data-cliente_id="{{ request('cliente_id') }}"><i class="fa fa-eye"></i></button>
                                  @endif
                                </td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modals -->
<div class="modal fade" id="modal-logs" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header"><h3 class="modal-title">Operazioni Settimanali</h3></div>
      <div class="modal-body">
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button></div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-timesheets" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header"><h3 class="modal-title" id="modal-title-timesheets">Timesheets Settimanali</h3></div>
      <div class="modal-body" id="modal-body-timesheets">
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button></div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-rapporti" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header"><h3 class="modal-title">Rapporti Settimanali</h3></div>
      <div class="modal-body">
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button></div>
    </div>
  </div>
</div>
<!-- End Modals -->
@endsection

@push('js-stack')
<script>
  $(document).ready(function(){
      var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
      $(".logs-button").click(function(){
          var data = $(this).data('data');
          var utente_id = $(this).data('utente_id');
          var modal = $('#modal-logs');

          $.ajax({
              url: "{{ route('admin.statistiche.reports.modal') }}",
              type: 'POST',
              data: {_token: CSRF_TOKEN, type: 'logs', utente_id: utente_id, data: data},
              dataType: 'JSON' 
          }).done(function(data) {
              var res = $.parseJSON(data);
              var html = '<div class="callout callout-info"><h4>Avviso!</h4><p>L\'utente non ha dati settimanali da mostrare.</p></div>';

              if(res.empty != 1) {
                html = '';

                for (var key in res.values) {
                  var data = res.values[key];

                  html += '<h4><strong>' + data.data + '</strong> <small> (' + data.count + ')</small></h4>';
                  html += '<p>Prima Operazione: <strong>' + data.first.data + ' (' + data.first.description + ')</strong></p>';
                  html += '<p style="margin-bottom: 5px;">Ultima Operazione: <strong>' + data.last.data + ' (' + data.first.description + ')</strong></p>';
                }
              }

              modal.find('.modal-body').html(html);
              modal.modal('show');
          }); 
      });

      $(".timesheets-button").click(function(){
          var utente_id = $(this).data('utente_id');
          var area_id = $(this).data('area_id');
          var cliente_id = $(this).data('cliente_id');
          var data_inizio = $(this).data('data_inizio');
          var data_fine = $(this).data('data_fine');
          var modal = $('#modal-timesheets');
          var title = $('#modal-title-timesheets');

          $.ajax({
              url: "{{ route('admin.statistiche.reports.modal') }}",
              type: 'POST',
              data: {_token: CSRF_TOKEN, type: 'timesheets', utente_id: utente_id, data_inizio: data_inizio, data_fine: data_fine, area_id: area_id, cliente_id: cliente_id },
              dataType: 'JSON' 
          }).done(function(data) {
              var res = $.parseJSON(data);
              var html = '<div class="callout callout-info"><h4>Avviso!</h4><p>L\'utente non ha dati settimanali da mostrare.</p></div>';

              if(res.empty != 1) {
                html = '';
                var title_html = 'Timesheets di <strong>' + res.utente + '</strong> <small>( Dal ' + res.data_inizio + ' al ' + res.data_fine + ' )</small>';
                title.html(title_html);
                for (var key in res.values) {
                  var data = res.values[key];

                  if(data.alert){
                    html += '<h4><strong>' + data.data + '</strong> <small> (' + data.durata + ')</small> <i class="fa fa-exclamation-triangle" style="color:red;"></i></h4>';
                  } else {
                    html += '<h4><strong>' + data.data + '</strong> <small> (' + data.durata + ')</small></h4>';
                  }
                  html += '<table class="table table-striped table-responsive"><tbody class="table"><tr><td style="word-break: break-all;" width=40%><strong>Nota</strong></td><td><strong>Durata</strong></td><td><strong>Area</strong></td><td><strong>Attività</strong></td><td><strong>Task</strong></td><td><strong>Tipologia</strong></td></tr>';
                  for (var key2 in data.timesheets){
                    var timesheet = res.values[key].timesheets[key2];
                    html += '<tr><td style="word-break: break-all;">' + timesheet.nota + '</td>';
                    html += '<td>' + timesheet.durata + '</td>';
                    html += '<td><span class="label label-danger">' + timesheet.area + '</span></td>';
                    html += '<td><span class="label label-warning">' + timesheet.gruppo + '</span></td>';
                    html += '<td><span class="label label-info">' + timesheet.attivita + '</span</td>';
                    if(timesheet.tipologia == 'Automatico'){
                      html += '<td><span class="label bg-gray">' + timesheet.tipologia + '</span></td></tr>';
                    } else {
                      html += '<td><span class="label label-success">' + timesheet.tipologia + '</span></td></tr>';
                    }
                  }
                  html += '</tbody></table>';
                }
              } 
              /*if(res.empty != 1) {
                  html += '<div id="timesheets-scroll-update" data-area="' + area_id + '" data-cliente="' + cliente_id + '" data-utente="' + utente_id + '" data-ultimadata="' + res.ultima_data + '"></div>';
                  html +='<div class="text-center timesheets-div-scroll"><button type="button" onclick="manualScroll();" class="btn btn-success">Visualizza più risultati</button></div>';
              } else {
                html += '<div id="timesheets-scroll-update" data-info="no-update"></div>'; }*/
              modal.find('.modal-body').html(html);
              modal.modal('show');
          }); 
      });
      $(".rapporti-button").click(function(){
          var data = $(this).data('data');
          var utente_id = $(this).data('utente_id');
          var area_id = $(this).data('area_id');
          var cliente_id = $(this).data('cliente_id');
          var modal = $('#modal-rapporti');

          $.ajax({
              url: "{{ route('admin.statistiche.reports.modal') }}",
              type: 'POST',
              data: {_token: CSRF_TOKEN, type: 'rapporti', utente_id: utente_id, data: data, area_id: area_id, cliente_id: cliente_id },
              dataType: 'JSON' 
          }).done(function(data) {
              var res = $.parseJSON(data);
              var html = '<div class="callout callout-info"><h4>Avviso!</h4><p>L\'utente non ha dati settimanali da mostrare.</p></div>';

              if(res.empty != 1) {
                html = '';

                for (var key in res.values) {
                  var data = res.values[key];

                  html += '<h4><strong>' + data.data + '</strong> <small> (' + data.count + ')</small></h4>';
                  html += '<table class="table table-striped"><tbody class="table"><tr><td><strong>Attività</strong></td><td><strong>Rapporti</strong></td><td><strong>Giornate Residue</strong></td><td><strong>Giornate Effettuate</strong></td></tr>';
                  for (var key2 in data.gruppi){
                    var gruppo = data.gruppi[key2];
                    html += '<tr><td><span class="label label-warning">' + key2 + '</span></td>';
                    html += '<td>' + gruppo.totali + '</td>';
                    html += '<td>' + gruppo.giornate_residue + '</td>';
                    html += '<td>' + gruppo.giornate_effettuate + '</td></tr>';
                  }
                  html += '</tbody></table>';
                }
              }

              modal.find('.modal-body').html(html);
              modal.modal('show');
          }); 
      });
 });

function manualScroll() 
{
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  var info_scroll_timesheets = $('#timesheets-scroll-update').data('info');
          ScrollDebounce = false;
          $(".timesheets-div-scroll").addClass('hidden');
          var utente_id = $('#timesheets-scroll-update').data('utente');
          var area_id = $('.timesheets-button').data('area_id');
          var cliente_id = $('.timesheets-button').data('cliente_id');
          var ultima_data = $('#timesheets-scroll-update').data('ultimadata');
          var modal = $('#modal-timesheets');
          var html = '';
          $.ajax({
              url: "{{ route('admin.statistiche.reports.modal') }}",
              type: 'POST',
              data: {_token: CSRF_TOKEN, type: 'timesheets', utente_id: utente_id, data_inizio: null, data_fine: ultima_data, area_id: area_id, cliente_id: cliente_id },
              dataType: 'JSON' 
          }).done(function(data) {
              modal.find('#timesheets-scroll-update').remove();
              var res = $.parseJSON(data);

              if(res.empty != 1) {
                html = '<hr>';

                for (var key in res.values) {
                  var data = res.values[key];

                  if(data.alert){
                    html += '<h4><strong>' + data.data + '</strong> <small> (' + data.durata + ')</small> <i class="fa fa-exclamation-triangle" style="color:red;"></i></h4>';
                  } else {
                    html += '<h4><strong>' + data.data + '</strong> <small> (' + data.durata + ')</small></h4>';
                  }
                  html += '<table class="table table-striped table-responsive"><tbody class="table"><tr><td style="word-break: break-all;" width=40%><strong>Nota</strong></td><td><strong>Durata</strong></td><td><strong>Area</strong></td><td><strong>Attività</strong></td><td><strong>Task</strong></td><td><strong>Tipologia</strong></td></tr>';
                  for (var key2 in data.timesheets){
                    var timesheet = res.values[key].timesheets[key2];
                    html += '<tr><td style="word-break: break-all;">' + timesheet.nota + '</td>';
                    html += '<td>' + timesheet.durata + '</td>';
                    html += '<td><span class="label label-danger">' + timesheet.area + '</span></td>';
                    html += '<td><span class="label label-warning">' + timesheet.gruppo + '</span></td>';
                    html += '<td><span class="label label-info">' + timesheet.attivita + '</span</td>';
                    if(timesheet.tipologia == 'Automatico'){
                      html += '<td><span class="label bg-gray">' + timesheet.tipologia + '</span></td></tr>';
                    } else {
                      html += '<td><span class="label label-success">' + timesheet.tipologia + '</span></td></tr>';
                    }
                  }
                  html += '</tbody></table>';
                }
                /*if(res.empty != 1) {
                    html += '<div id="timesheets-scroll-update" data-area="' + area_id + '" data-cliente="' + cliente_id + '" data-utente="' + utente_id + '" data-ultimadata="' + res.ultima_data + '"></div>';
                    html +='<div class="text-center timesheets-div-scroll"><button type="button" onclick="manualScroll();" class="btn btn-success">Visualizza più risultati</button></div>';
                }
              } else {
                var html = '<div class="callout callout-info"><h4>Avviso!</h4><p>L\'utente non ha altri timesheets da mostrare.</p></div>';
                html += '<div id="timesheets-scroll-update" data-info="no-update"></div>'; */
              }
              modal.find('.modal-body').append(html);
              html = ''; 
  });   
}
</script>
@endpush
