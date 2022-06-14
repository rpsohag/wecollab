@php
    $partner = json_decode(setting('clienti::partner'));
    if(!empty($partner)){
        $partner = array_combine($partner, $partner);
        $partner = array_merge(['' => ''], $partner);
    }else{
        $partner = array();
    }

    $tipi = [''] + config('amministrazione.clienti.tipi');

    $tipologie = [''] + config('amministrazione.clienti.tipologie');
@endphp

@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('amministrazione::clienti.title.clienti') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('amministrazione::clienti.title.clienti') }}</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="btn-group pull-right" style="margin: 0 15px 15px 0;">
                    <a href="{{ route('admin.amministrazione.clienti.create') }}" class="btn btn-primary btn-flat" style="padding: 4px 10px;">
                        <i class="fa fa-pencil"></i> {{ trans('amministrazione::clienti.button.create clienti') }}
                    </a>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header">

                    <section class="bg-gray filters">
                        {!! Form::open(['route' => ['admin.amministrazione.clienti.index'], 'method' => 'get']) !!}
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            {!! Form::weText('ragione_sociale', 'Ragione Sociale', $errors) !!}
                                        </div>
                                        <div class="col-sm-3">
                                            {!! Form::weText('p_iva', 'Partita IVA', $errors) !!}
                                        </div>
                                        <div class="col-sm-3">
                                            {!! Form::weSelect('tipo', 'Tipo', $errors, $tipi) !!}
                                        </div>
                                        <div class="col-sm-3">
                                            {!! Form::weTags('aree', 'Partner', $errors, $partner) !!}
                                        </div>
                                        <div class="col-sm-3">
                                            {!! Form::weSelect('tipologia', 'Tipologia', $errors, $tipologie) !!}
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
                              {!! order_th('ragione_sociale', 'Ragione Sociale') !!}
                              {!! order_th('p_iva', 'Partita IVA') !!}
                              {!! order_th('tipo', 'Tipologia') !!}
                              {!! order_th('provincia', 'Provincia') !!}
                              {!! order_th('citta', 'Città') !!}
                              {!! order_th('pec', 'PEC') !!}
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($clienti)): ?>
                            <?php foreach ($clienti as $cliente): ?>
                            <tr>
                                <td>
                                    <a href="{{ route('admin.amministrazione.clienti.read', [$cliente->id]) }}">
                                        {{ $cliente->ragione_sociale }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.amministrazione.clienti.read', [$cliente->id]) }}">
                                        {{ $cliente->p_iva }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.amministrazione.clienti.read', [$cliente->id]) }}">
                                        {{ $tipi[$cliente->tipo] }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.amministrazione.clienti.read', [$cliente->id]) }}">
                                        {{ optional($cliente->indirizzi->first())->provincia }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.amministrazione.clienti.read', [$cliente->id]) }}">
                                        {{ optional($cliente->indirizzi->first())->citta }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.amministrazione.clienti.read', [$cliente->id]) }}">
                                        {{ $cliente->pec }}
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>Ragione Sociale</th>
                                <th>Partita IVA</th>
                                <th>Tipologia</th>
                                <th>Provincia</th>
                                <th>Città</th>
                                <th>PEC</th>
                            </tr>
                            </tfoot>
                        </table>
                        <!-- /.box-body -->
                        <!-- Pagination -->
                        <div class="text-right pagination-container">
                          {{ $clienti->links() }}
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
        <dd>{{ trans('amministrazione::clienti.title.create clienti') }}</dd>
    </dl>
@stop

@push('js-stack')
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'c', route: "<?= route('admin.amministrazione.clienti.create') ?>" }
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
                "order": [[ 0, "asc" ]],
                "language": {
                    "url": '<?php echo Module::asset("core:js/vendor/datatables/{$locale}.json") ?>'
                }
            });
        });
    </script>
@endpush
