@php

$clients = [-1 => ''];
$clients = $clients + $clienti;

$tipi_rinnovi = [-1 => ''] + config('tasklist.rinnovi.tipi');

@endphp

@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('tasklist::rinnovi.title.rinnovi') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('tasklist::rinnovi.title.rinnovi') }}</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="btn-group pull-right" style="margin: 0 15px 15px 0;">
                    @if(auth_user()->hasAccess('tasklist.rinnovi.export'))
                        <a href="{{ route('admin.tasklist.rinnovo.exportexcel', request()->all()) }}" class="btn bg-olive btn-flat" style="padding: 4px 10px; margin-right: 8px;">
                            <i class="fa fa-table"> </i> Esporta Excel
                        </a>
                    @endif
                    <a href="{{ route('admin.tasklist.rinnovo.create') }}" class="btn btn-primary btn-flat" style="padding: 4px 10px;">
                        <i class="fa fa-pencil"></i> {{ trans('tasklist::rinnovi.button.create rinnovo') }}
                    </a>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header">

                    <section class="bg-gray filters">
                        {!! Form::open(['route' => ['admin.tasklist.rinnovo.index'], 'method' => 'get']) !!}
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="row">
                                      <div class="col-md-2">
                                          {!! Form::weText('titolo', 'Titolo', $errors) !!}
                                      </div>
                                      <div class="col-md-2">
                                          {!! Form::weSelectSearch('cliente_id', 'Cliente', $errors , $clients) !!}
                                      </div>
                                      <div class="col-md-2">
                                          {!! Form::weText('descrizione', 'Descrizione', $errors) !!}
                                      </div>
                                      <div class="col-md-4">
                                        {!! Form::weDateRange('range', 'Intervallo', $errors) !!}
                                      </div>
                                      <div class="col-md-2">
                                          {!! Form::weSelectSearch('tipo_rinnovo', 'Tipo Rinnovo', $errors, $tipi_rinnovi) !!}
                                      </div>
                                      <div class="col-md-3">
                                          {!! Form::weDate('data_rinnovo', 'Data Rinnovo', $errors) !!}
                                      </div>
                                  </div>
                                </div>
                                 <div class="col-md-2 text-right">
                                    {!! Form::weSubmit('Cerca') !!}
                                    {!! Form::weReset('Svuota') !!}
                                </div>
                            </div>
                            <input type="hidden" name="order[by]" value="{{ request('order')['by'] }}">
                            <input type="hidden" name="order[sort]" value="{{ request('order')['sort'] }}">
                        {!! Form::close() !!}
                    </section>

                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="data-table table table-bordered table-hover">
                            <thead>
                            <tr>
                              {!! order_th('titolo', 'Titolo') !!}
                              {!! order_th('cliente', 'Cliente') !!}
                              {!! order_th('descrizione', 'Descrizione') !!}
                              {!! order_th('tipo', 'Tipo Rinnovo') !!}
                              {!! order_th('data', 'Data Rinnovo') !!}
                              {!! order_th('created_at', trans('core::core.table.created at')) !!}
                              {{-- <th data-sortable="false">{{ trans('core::core.table.actions') }}</th> --}}
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($rinnovi)): ?>
                            <?php foreach ($rinnovi as $rinnovo): ?>
                            <tr>
                                <td>
                                    <a href="{{ route('admin.tasklist.rinnovo.read', [$rinnovo->id]) }}">
                                        {{ $rinnovo->titolo }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.tasklist.rinnovo.read', [$rinnovo->id]) }}">
                                        {{ $clients[$rinnovo->cliente_id] }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.tasklist.rinnovo.read', [$rinnovo->id]) }}">
                                        {{ $rinnovo->descrizione }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.tasklist.rinnovo.read', [$rinnovo->id]) }}">
                                        {{ $tipi_rinnovi[$rinnovo->tipo] }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.tasklist.rinnovo.read', [$rinnovo->id]) }}">
                                        <span class="hidden">{{ set_date_hour_ita($rinnovo->getDataRinnovo()) }}</span>
                                        {{ $rinnovo->getDataRinnovo() }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.tasklist.rinnovo.read', [$rinnovo->id]) }}">
                                        {{ get_date_hour_ita($rinnovo->created_at) }}
                                    </a>
                                </td>
                            <!--    <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.tasklist.rinnovo.edit', [$rinnovo->id]) }}" class="btn btn-default btn-flat"><i class="fa fa-pencil"></i></a>
                                        <button class="btn btn-danger btn-flat" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.tasklist.rinnovo.destroy', [$rinnovo->id]) }}"><i class="fa fa-trash"></i></button>
                                    </div>
                             </td> -->
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>Titolo</th>
                                <th>Cliente</th>
                                <th>Descrizione</th>
                                <th>Tipo Rinnovo</th>
                                <th>Data Rinnovo</th>
                                <th>{{ trans('core::core.table.created at') }}</th>
                               <!-- <th>{{ trans('core::core.table.actions') }}</th>-->
                            </tr>
                            </tfoot>
                        </table>
                        <!-- /.box-body -->
                        <!-- Pagination -->
                        <div class="text-right pagination-container">
                          {{ $rinnovi->links() }}
                        </div>
                    </div>
                </div>
                <!-- /.box -->
            </div>
        </div>
    </div>
    {{-- @include('core::partials.delete-modal') --}}
@stop

@section('footer')
    <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp;
@stop
@section('shortcuts')
    <dl class="dl-horizontal">
        <dt><code>c</code></dt>
        <dd>{{ trans('tasklist::rinnovi.title.create rinnovo') }}</dd>
    </dl>
@stop

@push('js-stack')
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'c', route: "<?= route('admin.tasklist.rinnovo.create') ?>" }
                ]
            });
        });
    </script>
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
                "order": [[ 4, "asc" ]],
                "language": {
                    "url": '<?php echo Module::asset("core:js/vendor/datatables/{$locale}.json") ?>'
                }
            });
        });

        $('input[name="range"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: 'D/M/Y'
        }
        }).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('D/M/Y') + ' - ' + picker.endDate.format('D/M/Y'));
        });
    </script>
@endpush
