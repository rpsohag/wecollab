@php
    $od = strtolower($offerta->cliente->tipologia) == 'privato' ? 'ODA' : 'DETERMINA';
@endphp

@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('commerciale::offerte.title.read offerta') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }} </a></li>
        <li><a href="{{ route('admin.commerciale.offerta.index') }}">{{ trans('commerciale::offerte.title.offerte') }}</a></li>
        <li class="active">{{ trans('commerciale::offerte.title.edit offerta') }}</li>
    </ol>
@stop

@section('content')
    {!! Form::open(['route' => ['admin.commerciale.offerta.update', $offerta->id], 'method' => 'put', 'files'=> true]) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                @include('partials.form-tab-headers')
                <div class="tab-content">
                    <?php $i = 0; ?>
                    @foreach (LaravelLocalization::getSupportedLocales() as $locale => $language)
                        <?php $i++; ?>
                        <div class="tab-pane {{ locale() == $locale ? 'active' : '' }}" id="tab_{{ $i }}">
                            @include('commerciale::admin.offerte.partials.fields_read', ['lang' => $locale])
                        </div>
                    @endforeach
                    <div class="box-footer">
                        <a class="btn btn-primary btn-flat" href="{{ route('admin.commerciale.offerta.edit', [$offerta->id])}}"><i class="fa fa-pencil"></i> Modifica</a>
                        @if($offerta->analisi_vendita)
                            <a class="btn btn-info btn-flat" href="{{ route('admin.commerciale.analisivendita.read', $offerta->analisi_vendita->id)}}"> Vai a Analisi Vendita</a>
                        @endif
                        @if(empty($offerta->ordinativo_id))
                            @if($offerta->stato == 1)
                                <a id="genera_ordinativo" class="btn btn-primary btn-flat" href="{{ route('admin.commerciale.offerta.generaordinativo', $offerta->id)}}"><i class="fa fa-adjust"></i> Genera ordinativo</a>
                            @endif
                        @else 
                            <a data-toggle="tooltip" title=""  data-original-title="Apri ordinativo"
                            class="btn btn-md btn-default" href = {{ route('admin.commerciale.ordinativo.read', $offerta->ordinativo_id) }} >
                            Apri Ordinativo &nbsp;
                            <i class="fa fa-external-link-square" style="font-size:15px" > </i>
                            </a>
                        @endif
                        {{-- <a class="btn btn-warning btn-flat" href="{{ route('admin.commerciale.offerta.create', ['duplicate_id' => $offerta->id])}}"><i class="fa fa-clone"></i> Duplica Offerta</a> --}}
                        <a class="btn btn-warning pull-right btn-flat" href="{{ route('admin.commerciale.offerta.index')}}"><i class="fa fa-arrow-left"></i> Indietro</a>
                    </div>
                </div>
            </div> {{-- end nav-tabs-custom --}}
        </div>
    </div>
    {!! Form::close() !!}
    @include('core::partials.delete-modal')
@stop

@section('footer')
    <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp;
@stop
@section('shortcuts')
    <dl class="dl-horizontal">
        <dt><code>b</code></dt>
        <dd>{{ trans('core::core.back to index') }}</dd>
    </dl>
@stop

@push('js-stack')
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'b', route: "<?= route('admin.commerciale.offerta.index') ?>" }
                ]
            });

            $('input[type="checkbox"].flat-blue, input[type="radio"].flat-blue').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                radioClass: 'iradio_flat-blue'
            });

            $('#add-file').click(function() {
                var rowFile = $('#upload-files > .row:first-child').clone();
                var id = rowFile.data('id');
                var newId = id + 1;

                rowFile.find('input').val('');
                rowFile.find('input[id="meta[file][' + id + '][name]"]')
                    .attr('name', 'meta[file][' + newId + '][name]')
                    .attr('id', 'meta[file][' + newId + '][name]');
                rowFile.find('label[for="meta[file][' + id + '][name]"]')
                    .attr('for', 'meta[file][' + newId + '][name]');
                rowFile.find('input[id="meta[file][' + id + '][file]"]')
                    .attr('name', 'meta[file][' + newId + '][file]')
                    .attr('id', 'meta[file][' + newId + '][file]');
                rowFile.find('label[for="meta[file][' + id + '][file]"]')
                    .attr('for', 'meta[file][' + newId + '][file]');
                rowFile.find('input[id="elimina-meta[file][' + id + '][file]"]')
                    .attr('name', 'elimina[meta[file][' + newId + '][name]]')
                    .attr('id', 'elimina-meta[file][' + newId + '][file]')
                    .val(0);

                $('#upload-files').prepend('<div class="row" data-id="' + newId + '">' + rowFile.html() + '</div>');
            });
        });
    </script>
@endpush
