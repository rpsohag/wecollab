@extends('layouts.master')

@section('content-header')
    <h1>
        Amministrazione {{ trans('tasklist::timesheets.title.timesheets') }}
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
                <div class="btn-group pull-right" style="padding: 4px 10px;">
                    <a href="{{ route('admin.tasklist.timesheet.exportexcel', request()->all()) }}" class="btn bg-olive btn-flat">
                        <i class="fa fa-table"> </i> Esporta Excel
                    </a>
                </div>
              </div>
            <div class="box box-primary">
                <div class="box-header">
                  <section class="bg-gray filters">
                      {!! Form::open(['route' => ['admin.tasklist.timesheet.manage'], 'method' => 'get']) !!}
                          <div class="row">
                              <div class="col-md-10">
                                  <div class="row">
                                    <div class="col-md-3">
                                      {!! Form::weSelectSearch('cliente','Cliente' , $errors , $clienti) !!}
                                    </div>
                                    <div class="col-md-3">
                                      {!! Form::weSelectSearch('utente','Utente' , $errors , $utenti) !!}
                                    </div>
                                    <div class="col-md-3">
                                        {!! Form::weSelectSearch('ordinativo','Ordinativo' , $errors , $ordinativi) !!}
                                    </div>
                                    <div class="col-md-3">
                                        {!! Form::weSelectSearch('tipologia', 'Tipologia', $errors, [ -1 => ''] + $tipologie)!!}
                                    </div>
                                    <div class="col-md-6">
                                        {!! Form::weText('nota', 'Nota', $errors) !!}
                                    </div>
                                    <div class="col-md-3">
                                      {!! Form::weDate('data_apertura', 'Data apertura', $errors) !!}
                                    </div>
                                    <div class="col-md-3">
                                        {!! Form::weDate('data_chiusura', 'Data chiusura', $errors) !!}
                                    </div>
                                  </div>
                              </div>
                              <div class="col-md-2 text-right">
                                  {!! Form::weSubmit('Cerca') !!}
                                  {!! Form::weReset('Svuota') !!}
                              </div>
                          </div>
                      {!! Form::close() !!}
                  </section>
                </div>
                @if($timesheets->count() > 0)
                <div class="box box-secondary">
                  <div class="table-responsive">
                      <table class="table table-striped">
                          <tr>
                                {!! order_th('utente', 'Utente') !!}
                                {!! order_th('cliente', 'Cliente') !!}
                                {!! order_th('procedura', 'Procedura') !!}
                                {!! order_th('area', 'Area di intervento') !!}
                                {!! order_th('gruppo', 'Ambito') !!}
                                {!! order_th('ordinativo', 'Ordinativo') !!}
                                {!! order_th('attivita', 'Tasklist Attività') !!}
                                {!! order_th('nota', 'Nota') !!}
                                {!! order_th('tipologia', 'Tipologia') !!}
                                <th><a>Data Inizio</a></th>
                                <th><a>Data Fine</a></th>
                                {!! order_th('durata', 'Durata') !!}
                          </tr>
                          <tbody>
                          @foreach ($timesheets->sortBy('dataora_inizio') as $timesheet)
                              <tr>
                                  <td>{{ $timesheet->created_user->full_name }}</td>
                                  <td>{{ $timesheet->cliente->ragione_sociale }}</td>
                                  <td>{{ $timesheet->procedura->titolo }}</td>
                                  <td>{{ $timesheet->area->titolo }}</td>
                                  <td>{{ get_if_exist($timesheet->gruppo, 'nome') }}</td>
                                  <td>{{ get_if_exist($timesheet->ordinativo, 'oggetto') }}</td>
                                  <td>{{ get_if_exist($timesheet->attivita, 'oggetto') }}</td>
                                  <td>{{ $timesheet->nota }}</td>
                                  <td>{{ $timesheet->tipologia() }}</td>
                                  <td>{{ get_date_hour_ita(date('Y-m-d H:i:s', strtotime($timesheet->dataora_inizio))) }}</td>
                                  <td>{{ get_date_hour_ita(date('Y-m-d H:i:s', strtotime($timesheet->dataora_fine))) }}</td>
                                  <td>{{ $timesheet->durata() }}</td>
                              </tr>
                          @endforeach
                          </tbody>
                      </table>
                      <div class="text-right pagination-container" style="margin-right:10px;">
                        {{ $timesheets->links() }}
                      </div>
                  </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @include('core::partials.delete-modal')
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
    </script>
@endpush
