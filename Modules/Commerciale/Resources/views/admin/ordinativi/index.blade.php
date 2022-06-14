@php

$ordinativi = (empty($ordinativi)) ? '' : $ordinativi;

@endphp


@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('Ordinativi') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('Ordinativi') }}</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="float-right text-right">
                <a href="{{ route('admin.commerciale.ordinativi.export.excel', $_GET) }}" class="btn bg-olive btn-flat" style="margin-bottom:5px;">
                    <i class="fa fa-table"> </i> Esporta Excel
                </a>
            </div>            
            <div class="box box-primary"> 
                <div class="box-header">
                    <section class="bg-gray filters">
                        {!! Form::open(['route' => ['admin.commerciale.ordinativo.index'], 'method' => 'get']) !!}
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="col-md-2">
                                            {!! Form::weText('codice', 'Numero Ordinativo', $errors) !!}
                                        </div>
                                        <div class="col-md-2">
                                            {!! Form::weSelectSearch('offerta_id','Offerta', $errors , $offerte) !!}
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::weText('oggetto','Oggetto' , $errors , $ordinativi) !!}
                                        </div>
                                        <div class="col-md-2">
                                            {!! Form::weSelectSearch('cliente_id','Cliente' , $errors , $clienti) !!}
                                        </div>
                                        <div class="col-md-2">
                                            {!! Form::weSelect('rinnovo', 'Rinnovi', $errors , ['', 'Si', 'No']) !!}
                                        </div>
                                    </div> 
                                    <div class="row">
                                        <div class="col-md-3">
                                            {!! Form::weDate('data_inizio_1','Da (Inizio)', $errors , $ordinativi) !!}
                                        </div>
                                        <div class="col-md-3">
                                            {!! Form::weDate('data_inizio_2','A (Inizio)', $errors , $ordinativi) !!}
                                        </div>
                                        <div class="col-md-3">
                                            {!! Form::weDate('data_fine_1','Da (Scadenza)', $errors , $ordinativi) !!}
                                        </div>
                                        <div class="col-md-3">
                                            {!! Form::weDate('data_fine_2','A (Scadenza)', $errors , $ordinativi) !!}
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
                                    {!! order_th('codice', 'Numero Ordinativo') !!}
                                    {!! order_th('oggetto', 'Oggetto') !!}
                                    {!! order_th('importo', 'Importo') !!}
                                    {!! order_th('stato', 'Stato') !!}
                                    {!! order_th('cliente', 'Cliente') !!}
                                    {!! order_th('data_inizio', 'Inizio') !!}
                                    {!! order_th('data_fine', 'Scadenza') !!}
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($ordinativi))
                                    @foreach($ordinativi as $ordinativo)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.commerciale.ordinativo.read', [$ordinativo->id]) }}">
                                                    {{ $ordinativo->numero_ordinativo() }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.commerciale.ordinativo.read', [$ordinativo->id]) }}">
                                                    {{ $ordinativo->oggetto }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.commerciale.ordinativo.read', [$ordinativo->id]) }}">
                                                    {{ get_currency($ordinativo->importo()) }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.commerciale.ordinativo.read', [$ordinativo->id]) }}">
                                                    {{ $ordinativo->stato() }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.commerciale.ordinativo.read', [$ordinativo->id]) }}">
                                                    {{ $ordinativo->cliente()->ragione_sociale }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.commerciale.ordinativo.read', [$ordinativo->id]) }}">
                                                    {{ $ordinativo->data_inizio }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.commerciale.ordinativo.read', [$ordinativo->id]) }}">
                                                    {{ $ordinativo->data_fine }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach 
                                @endif
                            </tbody>
                        </table>
                        <div class="text-right pagination-container">
                          {{ $ordinativi->links() }}
                        </div>
                    </div>
                </div>
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
        <dd>{{ trans('commerciale::ordinativi.title.create ordinativo') }}</dd>
    </dl>
@stop

@push('js-stack')
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
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
                "order": [[ 1, "desc" ]],
                "language": {
                    "url": '<?php echo Module::asset("core:js/vendor/datatables/{$locale}.json") ?>'
                }
            });
        });
    </script>
@endpush
