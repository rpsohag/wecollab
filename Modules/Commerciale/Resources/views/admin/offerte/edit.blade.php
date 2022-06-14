@php
    $od = strtolower($offerta->cliente->tipologia) == 'privato' ? 'ODA' : 'DETERMINA';
@endphp

@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('commerciale::offerte.title.edit offerta') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
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
                            @include('commerciale::admin.offerte.partials.fields', ['lang' => $locale])
                        </div>
                    @endforeach

                    <div class="box-footer">
                        <button type="submit" class="btn btn-success btn-flat"><i class="fa fa-floppy-o"></i> {{ trans('core::core.button.save') }}</button>
                        @if($offerta->stato == 1 && empty($offerta->ordinativo_id))
                            <a id="genera_ordinativo" class="btn btn-primary btn-flat" href="{{ route('admin.commerciale.offerta.generaordinativo', $offerta->id)}}"><i class="fa fa-adjust"></i> Genera ordinativo</a>
                        @endif
                        @if(!empty($offerta->ordinativo_id)) 
                            <a class="btn btn-info pull-left btn-flat" href="{{ route('admin.commerciale.ordinativo.read', [$offerta->ordinativo_id])}} "><i class="fa fa-eye"></i> Ordinativo </a>
                        @endif 
                        <button  type="button" class="btn btn-danger pull-right btn-flat" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.commerciale.offerta.destroy', [$offerta->id]) }}"><i class="fa fa-trash"></i> {{ trans('core::core.button.delete') }}</button>

                        <a class="btn btn-warning pull-right btn-flat" href="{{ route('admin.commerciale.offerta.index')}}"><i class="fa fa-arrow-left"></i> Indietro</a>
                        <a class="btn btn-info pull-left btn-flat" href="{{ route('admin.commerciale.offerta.read', [$offerta->id])}} "><i class="fa fa-eye"></i> Vedi </a>
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
    <script>
        $( document ).ready(function() {
            $('input[type="checkbox"].flat-blue, input[type="radio"].flat-blue').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                radioClass: 'iradio_flat-blue'
            });
        });
    </script>
    <script type="text/javascript">
        $("#dropzone").fileinput({
            theme: 'fa',
            showUpload: true,
            uploadUrl: "{{ route('admin.wecore.caricafiles') }}",
            uploadExtraData: function() {
                return {
                    _token: $("input[name='_token']").val(),
                    model_id: "{{ $offerta->id }}",
                    model_name: "Offerta",
                    model_path: "Commerciale",
                    type: "commerciale",
                };
            },
            overwriteInitial: false,
            browseOnZoneClick: true, 
            maxFilesNum: 10,
        });
    </script>
    <script>
        $('#dropzone').on('fileuploaded', function(event) {
            $("#allegati").load(location.href + " #allegati");
        }); 
    </script>
@endpush
