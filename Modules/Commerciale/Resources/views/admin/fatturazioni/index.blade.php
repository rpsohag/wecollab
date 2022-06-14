@php
    $scelta = [-1=>'',0=>'NO',1=>'SI'];
    $stati_colori = json_decode(setting('commerciale::fatturazione::colori'));
    $colore_testo = '';

    $stati = [''] + config('commerciale.fatturazioni.stati');
    $stati['non_pagata'] = 'Non Pagata';
    $stati['non_anticipata'] = 'Non Anticipata';
    unset($stati['default']);
@endphp
@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('commerciale::fatturazioni.title.fatturazioni') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('commerciale::fatturazioni.title.fatturazioni') }}</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="btn-group pull-right" style="margin: 0 15px 15px 0;">
                    <a href="{{ route('admin.commerciale.fatturazione.export.excel.voci', $_GET) }}" class="btn bg-orange btn-flat">
                        <i class="fa fa-table"> </i> Esporta Voci Excel
                    </a>
                  <a href="{{ route('admin.commerciale.fatturazione.export.excel', $_GET) }}" class="btn bg-olive btn-flat">
                      <i class="fa fa-table"> </i> Esporta Fatture Excel
                  </a>
                  <a href="{{ route('admin.commerciale.fatturazione.create') }}" class="btn btn-primary btn-flat">
                      <i class="fa fa-pencil"></i> {{ trans('commerciale::fatturazioni.button.create fatturazione') }}
                  </a>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header">
                    <section class="bg-gray filters">
                        {!! Form::open(['route' => ['admin.commerciale.fatturazione.index'], 'method' => 'get']) !!}
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="col-md-2">
                                            {!! Form::weInt('anno', 'Anno', $errors) !!}
                                        </div>
                                        <div class="col-md-2">
                                            {!! Form::weText('n_fattura', 'Numero', $errors) !!}
                                        </div>
                                        <div class="col-md-3">
                                            {!! Form::weText('oggetto', 'Oggetto', $errors) !!}
                                        </div>
                                        <div class="col-md-3">
                                            {!! Form::weSelectSearch('cliente','Cliente' , $errors , $clienti) !!}
                                        </div>
                                        <div class="col-md-2">
                                            {!! Form::weText('cig', 'Cig', $errors) !!}
                                        </div>
                                        <div class="col-md-2">
                                            {!! Form::weSelect('fepa','FEPA' , $errors , $scelta) !!}
                                        </div>
                                        <div class="col-md-2">
                                            {!! Form::weSelect('stato','Stato' , $errors , $stati) !!}
                                        </div>
                                        <div class="col-md-3">
                                            {!! Form::weSelect('macrocategoria', 'Macrocategoria', $errors, $macrocategorie) !!}
                                        </div>
                                        <div class="col-md-2">
                                            {!! Form::weCurrency('totale_netto', 'Totale netto', $errors) !!}
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
                                  {!! order_th('n_fattura', 'N. Fattura') !!} 
                                  {!! order_th('oggetto', 'Oggetto') !!}
                                  {!! order_th('cliente', 'Cliente') !!}
                                  {!! order_th('data', 'Data') !!}
                                  {!! order_th('cig', 'Cig') !!}
                                  {!! order_th('acconto', 'Acconto') !!}
                                  {!! order_th('totale_netto', 'Netto') !!}
                                  {!! order_th('iva', 'IVA') !!}
                                  {!! order_th('totale_fattura', 'TOT Fattura') !!}
                                  {!! order_th('totale_importo_dovuto', 'Dovuto') !!}
                                  {{-- <th data-sortable="false">{{ trans('core::core.table.actions') }}</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($fatturazioni)): ?>
                            <?php
                            foreach ($fatturazioni as $fatturazione):
                                $colore_testo = '';

                                if($fatturazione->pagata == 1)
                                {
                                    $colore = $stati_colori->pagata;
                                    $colore_testo = 'text-white';
                                }
                                elseif($fatturazione->anticipata == 1)
                                {
                                    $colore = $stati_colori->anticipata;
                                }
                                elseif($fatturazione->scaduta())
                                {
                                    $colore = $stati_colori->scaduta;
                                    $colore_testo = 'text-white';
                                }
                                elseif($fatturazione->consegnata == 1) {
                                    $colore = $stati_colori->consegnata;
                                }
                                else
                                {
                                    $colore = $stati_colori->default;
                                    $colore_testo = 'text-white';
                                }
                            ?>
                            <tr class="{{$colore_testo}}" style="background-color: {{ (!empty($colore)) ? $colore : "" }} ;">
                                <td data-sort="{{ $fatturazione->id }}">
                                    <a href="{{ route('admin.commerciale.fatturazione.read', [$fatturazione->id]) }}">
                                        {{ $fatturazione->get_numero_fattura() }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.commerciale.fatturazione.read', [$fatturazione->id]) }}">
                                        {{ $fatturazione->oggetto }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.commerciale.fatturazione.read', [$fatturazione->id]) }}">
                                        {{ $fatturazione->cliente->ragione_sociale }}
                                    </a>
                                </td>
                                <td data-sort="{{ set_date_ita($fatturazione->data) }}">
                                    <a href="{{ route('admin.commerciale.fatturazione.read', [$fatturazione->id]) }}">
                                        {{ $fatturazione->data  }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.commerciale.fatturazione.read', [$fatturazione->id]) }}">
                                        {{ $fatturazione->cig }}
                                    </a>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.commerciale.fatturazione.read', [$fatturazione->id]) }}">
                                        {{ $fatturazione->acconto }}
                                    </a>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.commerciale.fatturazione.read', [$fatturazione->id]) }}">
                                        {{ $fatturazione->totale_netto }}
                                    </a>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.commerciale.fatturazione.read', [$fatturazione->id]) }}">
                                        {{ $fatturazione->iva.' %' }}
                                    </a>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.commerciale.fatturazione.read', [$fatturazione->id]) }}">
                                        {{ $fatturazione->totale_fattura }}
                                    </a>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.commerciale.fatturazione.read', [$fatturazione->id]) }}">
                                        {{ $fatturazione->totale_importo_dovuto }}
                                    </a>
                                </td>
                                {{-- <td>
                                      <div class="btn-group">
                                          <a href="{{ route('admin.commerciale.fatturazione.edit', [$fatturazione->id]) }}" class="btn btn-default btn-flat"><i class="fa fa-pencil"></i></a>
                                          <button class="btn btn-danger btn-flat" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.commerciale.fatturazione.destroy', [$fatturazione->id]) }}"><i class="fa fa-trash"></i></button>
                                      </div>
                                </td> --}}
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>N. Fattura </th>
                                    <th>Oggetto</th>
                                    <th>Cliente</th>
                                    <th>Data</th>
                                    <th>Cig</th>
                                    <th>Acconto</th>
                                    <th>Netto</th>
                                    <th>IVA</th>
                                    <th>TOT Fattura</th>
                                    <th>Dovuto</th>
                                    {{-- <th data-sortable="false">{{ trans('core::core.table.actions') }}</th> --}}
                                </tr>
                            </tfoot>
                        </table>
                        <!-- /.box-body -->
                        <!-- Pagination -->
                        <div class="text-right pagination-container">
                          {{ $fatturazioni->links() }}
                        </div>
                    </div>
                </div>
                <!-- /.box -->
            </div>
        </div>
    </div>
    <!-- Legenda -->
    <section class="content">
      <h4>Legenda Colori : </h4>
        @foreach ($stati_colori as $key => $colore)
          <div class="row">
            <div class="col-md-1" style="{{($key === 'consegnata') ? 'border: 1px solid black' : ''}};height:15px; background-color:{{$colore}};"></div>
            <div class="col-md-11">{{ config('commerciale.fatturazioni.stati')[$key] }}</div>
          </div>
        @endforeach
  </section>

    @include('core::partials.delete-modal')
@stop

@section('footer')
    <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp;
@stop
@section('shortcuts')
    <dl class="dl-horizontal">
        <dt><code>c</code></dt>
        <dd>{{ trans('commerciale::fatturazioni.title.create fatturazione') }}</dd>
    </dl>
@stop

@push('js-stack')
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'c', route: "<?= route('admin.commerciale.fatturazione.create') ?>" }
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
                "order": [[ 3, "desc" ], [ 0, "desc" ]],
                "language": {
                    "url": '<?php echo Module::asset("core:js/vendor/datatables/{$locale}.json") ?>'
                }
            });
        });
    </script>
@endpush
