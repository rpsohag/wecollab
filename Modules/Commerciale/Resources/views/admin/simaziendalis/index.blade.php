@php
    $tipi_sim = config('commerciale.simaziendali.tipi');
@endphp

@extends('layouts.master')

@section('content-header')
    <h1>
        Sim Aziendali
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">Sim Aziendali</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="btn-group pull-right" style="margin: 0 15px 15px 0;">
                    <a href="{{ route('admin.commerciale.simaziendali.create') }}" class="btn btn-primary btn-flat" style="padding: 4px 10px;">
                        <i class="fa fa-pencil"></i> {{ trans('commerciale::simaziendalis.button.create simaziendali') }}
                    </a>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header">
                <section class="bg-gray filters">
                        {!! Form::open(['route' => ['admin.commerciale.simaziendali.index'], 'method' => 'get']) !!}
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="col-md-3">
                                            {!! Form::weSelectSearch('numero_contratto','N. Contratto' , $errors, $contratti) !!}
                                        </div>
                                        <div class="col-md-3">
                                            {!! Form::weText('operatore','Operatore' , $errors) !!}
                                        </div>
                                        <div class="col-md-3">
                                            {!! Form::weText('profilo','Profilo' , $errors) !!}
                                        </div>
                                        <div class="col-md-3">
                                            {!! Form::weText('iccid','ICCID' , $errors) !!}
                                        </div>
                                        <div class="col-md-3">
                                            {!! Form::weText('telefono','Telefono' , $errors) !!}
                                        </div>
                                        <div class="col-md-3">
                                            {!! Form::weSelectSearch('assegnatario','Assegnatario' , $errors , $utenti) !!}
                                        </div>
                                         <div class="col-md-3">
                                            {!! Form::weSelectSearch('tipo_sim','Tipo Sim' , $errors , $tipi_sim) !!}
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
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="data-table table table-bordered table-hover">
                            <thead>
                             <tr>
                               {!! order_th('numero_contratto', 'N. Contratto') !!}
                               {!! order_th('operatore', 'Operatore') !!}
                               {!! order_th('telefono', 'Telefono') !!}
                               {!! order_th('assegnatario', 'Assegnatario') !!}
                               {!! order_th('tipo_sim', 'Tipo Sim') !!}
                               {!! order_th('profilo', 'Profilo') !!}
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($simaziendalis)): ?>
                            <?php foreach ($simaziendalis as $simaziendali): ?>
                             <tr>
                                <td>
                                    <a href="{{ route('admin.commerciale.simaziendali.read', [$simaziendali->id]) }}">
                                        {{ $simaziendali->numero_contratto }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.commerciale.simaziendali.read', [$simaziendali->id]) }}">
                                        {{ $simaziendali->operatore }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.commerciale.simaziendali.read', [$simaziendali->id]) }}">
                                        {{ $simaziendali->telefono }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.commerciale.simaziendali.read', [$simaziendali->id]) }}">
                                       {{ $simaziendali->applicato->first_name . ' ' . $simaziendali->applicato->last_name  }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.commerciale.simaziendali.read', [$simaziendali->id]) }}">
                                          {{ $tipi_sim[$simaziendali->tipo_sim] }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.commerciale.simaziendali.read', [$simaziendali->id]) }}">
                                          {{ $simaziendali->profilo }}
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                        <!-- /.box-body -->
                        <!-- Pagination -->
                        <div class="text-right pagination-container">
                          {{ $simaziendalis->links() }}
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
        <dd>{{ trans('commerciale::simaziendalis.title.create simaziendali') }}</dd>
    </dl>
@stop

@push('js-stack')
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'c', route: "<?= route('admin.commerciale.simaziendali.create') ?>" }
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
    </script>
@endpush
