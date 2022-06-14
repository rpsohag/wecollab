@php
$azienda = get_azienda();

$stati_filter = ['' => ''];
$stati_filter = $stati_filter + config('commerciale.offerte.stati');

$stati = config('commerciale.offerte.stati');
$stati[101] = 'Accettata senza Determina/ODA';
$stati[102] = 'Accettata senza Ordine e Determina';

$stati = collect($stati)->sort()->toArray();

$stati_colori = json_decode(setting('commerciale::offerte::stati_colori'), true);

$sn = [-1 => ''];
$sn = $sn + config('wecore.sn');

$docs_base = json_decode(setting("commerciale::offerte::doc_base"));
$doc_base = get_if_exist($docs_base, $azienda) ? $docs_base->$azienda : '';

@endphp

@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('commerciale::offerte.title.offerte') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('commerciale::offerte.title.offerte') }}</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="btn-group pull-right" style="margin: 0 15px 15px 0;">
                    @if (!empty($doc_base))
                        <a href="{{ $doc_base }}" class="btn btn-default btn-flat">
                            <i class="fa fa-download"></i> Documento base
                        </a>
                    @endif 
                    <a href="{{ route('admin.commerciale.offerte.export.excel', $_GET) }}" class="btn bg-olive btn-flat">
                        <i class="fa fa-table"> </i> Esporta Excel
                    </a>
                    <a href="{{ route('admin.commerciale.offerte.export.excel.scadenze', $_GET) }}" style="margin-left:4px;" class="btn bg-olive btn-flat">
                        <i class="fa fa-table"> </i> Esporta Scadenze
                    </a>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header">
                    
                  <section class="bg-gray filters">
                      {!! Form::open(['route' => ['admin.commerciale.offerta.index'], 'method' => 'get']) !!}
                          <div class="row">
                              <div class="col-md-10">
                                  <div class="row">
                                    <div class="col-md-3">
                                        {!! Form::weSelectSearch('cliente','Cliente' , $errors , $clienti) !!}
                                    </div>
                                    <div class="col-md-3">
                                        {!! Form::weText('oggetto', 'Oggetto', $errors) !!}
                                    </div>
                                    <div class="col-md-3">
                                        {!! Form::weSelectSearch('commerciale', 'Commerciale' , $errors , $commerciali) !!}
                                    </div>
                                    <div class="col-md-3">
                                        {!! Form::weDateRange('range', 'Intervallo', $errors) !!}
                                    </div>
                                    <div class="col-md-2">
                                        {!! Form::weText('codice', 'Numero Offerta', $errors) !!}
                                    </div>
                                    <div class="col-md-2">
                                        {!! Form::weSelectSearch('stato','Stato' , $errors , $stati_filter) !!}
                                    </div>
                                    <div class="col-md-1">
                                        {!! Form::weSelect('fatturata','Fatturata' , $errors , $sn) !!}
                                    </div>
                                    <div class="col-md-2">
                                        {!! Form::weSelect('allegati','Allegati' , $errors , config('commerciale.offerte.filter_allegati'), '', ['style="width: 180px;"']) !!}
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
                              {!! order_th('codice', 'Numero Offerta') !!}
                              {!! order_th('data_offerta', 'Data') !!}
                              {!! order_th('importo_esente', 'Importo Iva Esclusa') !!}
                              {!! order_th('importo_iva', 'Importo Iva Inclusa') !!}
                              {!! order_th('cliente', 'Cliente') !!}
                              {!! order_th('oggetto', 'Oggetto') !!}
                              {!! order_th('stato', 'Stato') !!}
                              {!! order_th('fatturata', 'Fatturata') !!}
                            </tr>
                            </thead>
                            <tbody>
                                @if(!empty($offerte))
                                    @foreach($offerte as $offerta)
                                        @php
                                        if(is_numeric($offerta->stato))
                                        {
                                            $color = "#3c8dbc";
                                            $color_bg = '';

                                            if($offerta->stato !== 0 && $offerta->stato !== 102 && !empty($stati_colori[$offerta->stato]))
                                            $color = "#FFF";

                                            if($offerta->stato == 1 && $offerta->oda_determina_ids->isEmpty())
                                            $offerta->stato = 101;

                                            if(get_if_exist($offerta->cliente, 'tipologia')){
                                                if(strtolower($offerta->cliente->tipologia) == 'pubblico' && $offerta->stato == 1 && empty($offerta->ordine_mepa_id) && $offerta->oda_determina_ids->isEmpty())
                                                {
                                                    $color = '#000';
                                                    $offerta->stato = 102;
                                                }
                                            }

                                            if(!empty($stati_colori[$offerta->stato]))
                                            $color_bg = $stati_colori[$offerta->stato];
                                        }
                                        @endphp

                                        <tr style="background-color: {{ $color_bg }} ;">
                                            <td>
                                                <a style="color: {{ $color }}" href="{{ ($offerta->stato == -1 ? route('admin.commerciale.offerta.edit', [$offerta->id]) : route('admin.commerciale.offerta.read', [$offerta->id])) }}">
                                                    {{ $offerta->numero_offerta() }} 
                                                </a>
                                            </td>
                                            <td>
                                                <a style="color: {{ $color }}" href="{{ ($offerta->stato == -1 ? route('admin.commerciale.offerta.edit', [$offerta->id]) : route('admin.commerciale.offerta.read', [$offerta->id])) }}">
                                                    {{  $offerta->data_offerta }}
                                                </a>
                                            </td>
                                            <td class="text-right">
                                                <a style="color: {{ $color }}" href="{{ ($offerta->stato == -1 ? route('admin.commerciale.offerta.edit', [$offerta->id]) : route('admin.commerciale.offerta.read', [$offerta->id])) }}">
                                                    {!! get_currency($offerta->importo_esente) !!}
                                                </a>
                                            </td>
                                            <td class="text-right">
                                                <a style="color: {{ $color }}" href="{{ ($offerta->stato == -1 ? route('admin.commerciale.offerta.edit', [$offerta->id]) : route('admin.commerciale.offerta.read', [$offerta->id])) }}">
                                                    {{ get_currency($offerta->importo_iva) }}
                                                </a>
                                            </td>
                                            <td>
                                                <a style="color: {{ $color }}" href="{{ ($offerta->stato == -1 ? route('admin.commerciale.offerta.edit', [$offerta->id]) : route('admin.commerciale.offerta.read', [$offerta->id])) }}">
                                                    {{ get_if_exist($offerta->cliente, 'ragione_sociale') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a style="color: {{ $color }}" href="{{ ($offerta->stato == -1 ? route('admin.commerciale.offerta.edit', [$offerta->id]) : route('admin.commerciale.offerta.read', [$offerta->id])) }}">
                                                    {{ $offerta->oggetto }}
                                                </a>
                                            </td>
                                            <td>
                                                <a style="color: {{ $color }}" href="{{ ($offerta->stato == -1 ? route('admin.commerciale.offerta.edit', [$offerta->id]) : route('admin.commerciale.offerta.read', [$offerta->id])) }}">
                                                    @if(is_numeric($offerta->stato))
                                                    {{ $stati[$offerta->stato] }}
                                                    @endif
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                <a style="color: {{ $color }}" href="{{ ($offerta->stato == -1 ? route('admin.commerciale.offerta.edit', [$offerta->id]) : route('admin.commerciale.offerta.read', [$offerta->id])) }}">
                                                    {!! sn_icon($offerta->fatturata()) !!}
                                                </a>
                                            </td>
                                            {{-- <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('admin.commerciale.offerta.edit', [$offerta->id]) }}" class="btn btn-default btn-flat"><i class="fa fa-pencil"></i></a>
                                                        <button class="btn btn-danger btn-flat" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.commerciale.offerta.destroy', [$offerta->id]) }}"><i class="fa fa-trash"></i></button>
                                                    </div>
                                            </td> --}}
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        <div class="text-right pagination-container">
                          {{ $offerte->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Legenda -->
    <section class="content">
      <h4>Legenda Colori : </h4>
        @foreach ($stati as $key => $stato)
          @if(!empty($stati_colori[$key]))
            <div class="row">
              <div class="col-md-1" style="{{($key === 0) ? 'border: 1px solid black' : ''}};height:15px; background-color:{{ $stati_colori[$key] }};">  </div>
              <div class="col-md-11">{{ $stato }}</div>
            </div>
          @endif
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
        <dd>{{ trans('commerciale::offerte.title.create offerta') }}</dd>
    </dl>
@stop

@push('js-stack')
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'c', route: "<?= route('admin.commerciale.offerta.create') ?>" }
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

