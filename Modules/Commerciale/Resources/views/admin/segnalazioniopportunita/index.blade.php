@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('commerciale::segnalazioniopportunita.title.segnalazioniopportunita') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('commerciale::segnalazioniopportunita.title.segnalazioniopportunita') }}</li>
    </ol>
@stop

@php 
$stati = config('commerciale.segnalazioneopportunita.stati');
$stati_colori = [0 => '#696969', 1 => '#228B22', 2 => '#FFA500', 3 => '#DC143C', 4 => '#000000', 5 => '#4671C6'];
@endphp

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="btn-group pull-right" style="margin: 0 15px 15px 0;">
                    <a href="{{ route('admin.commerciale.segnalazioniopportunita.export.excel', $_GET) }}" style="padding: 4px 10px;" class="btn bg-olive btn-flat mr-2">
                        <i class="fa fa-table"> </i> Esporta Excel
                    </a>
                    <a href="{{ route('admin.commerciale.segnalazioneopportunita.create') }}" class="btn btn-primary btn-flat" style="padding: 4px 10px;">
                        <i class="fa fa-pencil"></i> {{ trans('commerciale::segnalazioniopportunita.button.create segnalazioneopportunita') }}
                    </a>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header">
                  <section class="bg-gray filters">
                    {!! Form::open(['route' => ['admin.commerciale.segnalazioneopportunita.index'], 'method' => 'get']) !!}
                      <div class="row">
                        <div class="col-md-10">
                          <div class="row">
                            <div class="col-md-5">
                                {!! Form::weText('oggetto', 'Oggetto', $errors) !!}
                            </div>
                            <div class="col-md-3">
                                {!! Form::weSelectSearch('cliente', 'Cliente', $errors, $clienti) !!}
                            </div>
                            <div class="col-md-4">
                                {!! Form::weDateRange('range', 'Intervallo', $errors) !!}
                            </div>
                            <div class="col-md-3">
                                {!! Form::weSelectSearch('utente', 'Creato da', $errors, $utenti) !!}
                            </div>
                              <div class="col-md-3">
                                {!! Form::weSelectSearch('stato', 'Stato Segnalazione', $errors,   array('-1' => 'Tutte') + config('commerciale.segnalazioneopportunita.stati')   ) !!}
                            </div>
                            <div class="col-md-2">
                                {!! Form::weSelect('eliminati', 'Eliminati', $errors, config('wecore.sn')) !!}
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
                                  <!-- Legenda -->
                <div class="row">
                    <div class="col-md-12">
                        <h4>Legenda colori:</h4>
                        @foreach ($stati as $key => $stato)
                            @if(!empty($stati_colori[$key]))
                                <div class="col-md-1" style="height:15px; background-color:{{ $stati_colori[$key] }};">  </div>
                                <div class="col-md-3">{{ $stato }}</div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="data-table table table-bordered table-hover">
                            <thead>
                            <tr>
                              {!! order_th('numero', 'Numero') !!}
                              {!! order_th('cliente', 'Cliente') !!}
                              {!! order_th('oggetto', 'Oggetto') !!}
                              {!! order_th('user', 'Creato da') !!}
                              {!! order_th('created_at', trans('core::core.table.created at')) !!}
                              {!! order_th('stato', 'Stato') !!}
                              {{-- <th data-sortable="false">{{ trans('core::core.table.actions') }}</th> --}}
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($segnalazioniopportunita)): ?>
                            <?php foreach ($segnalazioniopportunita as $segnalazioneopportunita): ?>
                            <tr style="background-color:{{ $stati_colori[$segnalazioneopportunita->stato_id] }};">
                              <td>
                                  <a style="color:white;" href="{{ route('admin.commerciale.segnalazioneopportunita.read', [$segnalazioneopportunita->id]) }}">
                                      {{ $segnalazioneopportunita->numero() }}
                                  </a>
                              </td>
                              <td>
                                  <a style="color:white;" href="{{ route('admin.commerciale.segnalazioneopportunita.read', [$segnalazioneopportunita->id]) }}">
                                      {{ optional($segnalazioneopportunita->cliente())->ragione_sociale }}
                                  </a>
                              </td>
                              <td>
                                  <a style="color:white;" href="{{ route('admin.commerciale.segnalazioneopportunita.read', [$segnalazioneopportunita->id]) }}">
                                      {{ $segnalazioneopportunita->oggetto }}
                                  </a>
                              </td>
                              <td>
                                  <a style="color:white;" href="{{ route('admin.commerciale.segnalazioneopportunita.read', [$segnalazioneopportunita->id]) }}">
                                      {{ get_if_exist($segnalazioneopportunita->created_user, 'full_name') }}
                                  </a>
                              </td>
                              <td>
                                  <a style="color:white;" href="{{ route('admin.commerciale.segnalazioneopportunita.read', [$segnalazioneopportunita->id]) }}">
                                      {{ get_if_exist($segnalazioneopportunita, 'created_at') }}
                                  </a>
                              </td>
                              <td>
                                  <a style="color:white;" href="{{ route('admin.commerciale.segnalazioneopportunita.read', [$segnalazioneopportunita->id]) }}">
                                      {{ $segnalazioneopportunita->stato() }}
                                  </a>
                              </td>
                           <!--   <td>
                                  <div class="btn-group">
                                    @if(!$segnalazioneopportunita->trashed())
                                      <button type="button" class="btn btn-md btn-flat btn-info" data-toggle="modal" data-target="#modal-default" data-size="modal-lg" data-title="Vedi" data-action="{{ route('admin.commerciale.segnalazioneopportunita.edit', $segnalazioneopportunita->id) }}" data-element="#tab_1" data-form-disabled="true">
                                          <i class="fa fa-eye"> </i>
                                      </button>

                                      <a href="{{ route('admin.commerciale.segnalazioneopportunita.edit', [$segnalazioneopportunita->id]) }}" class="btn btn-default btn-flat"><i class="fa fa-pencil"></i></a>

                                      @if($auth_user->inRole('admin') || $auth_user->inRole('direzione-commerciale'))
                                        <button class="btn btn-danger btn-flat" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.commerciale.segnalazioneopportunita.destroy', [$segnalazioneopportunita->id]) }}"><i class="fa fa-trash"></i></button>
                                      @endif
                                    @else
                                      <a href="{{ route('admin.commerciale.segnalazioneopportunita.restore', [$segnalazioneopportunita->id]) }}" class="btn btn-default btn-flat btn-warning"><i class="fa fa-unlock"></i></a>
                                    @endif
                                  </div>
                           </td>-->
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                        <!-- /.box-body -->
                        <!-- Pagination -->
                        <div class="text-right pagination-container">
                          {{ $segnalazioniopportunita->links() }}
                        </div>
                    </div>
                </div>
                <!-- /.box -->
            </div>
        </div>
    </div>
    @include('core::partials.delete-modal')
@stop

@section('footer')
    <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp;
@stop
@section('shortcuts')
    <dl class="dl-horizontal">
        <dt><code>c</code></dt>
        <dd>{{ trans('commerciale::segnalazioniopportunita.title.create segnalazioneopportunita') }}</dd>
    </dl>
@stop

@push('js-stack')
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'c', route: "<?= route('admin.commerciale.segnalazioneopportunita.create') ?>" }
                ]
            });
        });
    </script>
    <?php $locale = locale(); ?>
    <script type="text/javascript">
        $(function () {
            $('.data-table').dataTable({
                "paginate": false,
                "lengthChange": true,
                "filter": false,
                "sort": false,
                "info": false,
                "autoWidth": true,
                "order": [[ 0, "desc" ]],
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
