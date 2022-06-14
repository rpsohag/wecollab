@extends('layouts.master')

@section('content-header')
    <h1>Quadratura Timesheets</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">Quadratura Timesheets</li>
    </ol>
@endsection

@section('content')
<div class="box-body">
  <div class="box box-primary box-shadow">
    <div class="box-header with-border">
      <section class="bg-gray filters">
        {!! Form::open(['route' => ['admin.statistiche.quadraturatimesheets'], 'method' => 'get']) !!}
          <div class="row">
            <div class="col-md-10">
                <div class="row">
                  <div class="col-md-3">
                    {!! Form::weSelectSearch('utente', 'Utente', $errors, $utenti_list, !empty(request('utente')) ? request('utente') : []) !!}
                  </div>
                  <div class="col-md-2">
                    {!! Form::weSelectSearch('mese', 'Mese', $errors, $mesi, $mese) !!}
                  </div>
                  <div class="col-md-2">
                    {!! Form::weSelectSearch('anno', 'Anno', $errors, $anni, $anno) !!}
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
      <div class="box box-primary box-shadow">
        <div class="box-header with-border">
          <h3 class="box-title">Lista Utenti</h3>
        </div>
        <div class="box-body">
          <div class="table-responsive">
            <table class="table table-striped">
              <tr>
                <th>Utente</th>
                <th class="text-center">N° di Timesheet</th>
                <th class="text-center">Ore Lavorative</th> 
                <th class="text-center">Ore Rendicontate da Timesheet</th> 
                <th class="text-center">Scostamento</th>
                <th class="text-center">Storico Timesheets</th>
              </tr>
              <tbody>
              @if($utenti->count() > 0)
                @foreach ($utenti as $utente)
                <tr>
                  
                  <td>{{ $utente->full_name }}</td>
                  <td class="text-center">{{ count($utente->timesheets) }}</td>
                  <td class="text-center">{{ ( $utente->ore_lavorative_settimanali / 5 ) * $working_days}} Ore</td> 
                  <td class="text-center">
                    @php 
                      $ore_lavorate = 0;
                      foreach($utente->timesheets as $timesheet){
                        $ore_lavorate += strtotime($timesheet->dataora_fine) - strtotime($timesheet->dataora_inizio);
                      }
                      $ore_lavorate_scostamento = ($ore_lavorate / 60) / 60;
                      $ore_lavorate_print = get_seconds_to_hours($ore_lavorate);
                      print_r($ore_lavorate_print);
                    @endphp
                  </td>
                  <td class="text-center font-weight-bold">{{ number_format(  ( $ore_lavorate_scostamento / ( ( $utente->ore_lavorative_settimanali / 5 ) * $working_days ) ) * 100 , 0) }}%</td>
                  {{-- <td class="text-center font-weight-bold">{{ number_format(100 - ( ($ore_lavorate_scostamento * 100) / ($utente->ore_lavorative_settimanali * 4) ), 0) }}%</td> --}}
                  <td class="text-center"><button type="button" class="btn btn-success btn-flat timesheets-button" data-data_inizio="{{ $firstDay }}" data-data_fine="{{ $lastDay }}" data-utente_id="{{ $utente->id }}"><i class="fa fa-eye"></i></button></td>
                </tr>
                @endforeach
              @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
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

@endsection

@push('js-stack')
<script>
  $(document).ready(function(){
      var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
      $('#modal-alert').modal('show');
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
              modal.find('.modal-body').html(html);
              modal.modal('show');
          }); 
      });
 });
</script>
@endpush

