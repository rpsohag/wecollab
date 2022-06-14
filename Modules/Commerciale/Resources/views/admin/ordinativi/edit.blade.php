@php
    $get_genera_fattura['ordinativo_id'] = $ordinativo->id;
@endphp

@extends('layouts.master')

@section('content-header')
    <h4>
        {{ $ordinativo->oggetto }}
    </h4>

    <div class="alert alert-info alert-dismissible" style="margin-bottom:0px;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-info"></i> Ordinativo <strong>#{{ $ordinativo->numero_ordinativo() }}</strong></h4>
        <br> Cliente: <strong>{{  optional($ordinativo->cliente())->ragione_sociale }}</strong>
        <br> Data Apertura: <strong>{{ get_if_exist($ordinativo, 'data_inizio') }}</strong>
        <br> Data Scadenza: <strong>{{ get_if_exist($ordinativo, 'data_fine') }}</strong>
    </div>
    {{-- <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ route('admin.commerciale.ordinativo.index') }}">{{ trans('Ordinativi') }}</a></li>
        <li class="active">{{ trans('Modifica Ordinativo') }}</li>
    </ol> --}}
@stop

@section('content')
    {!! Form::open(['route' => ['admin.commerciale.ordinativo.update', $ordinativo->id], 'method' => 'put', 'id' => 'form-ordinativo', 'files'=> true]) !!}
    <input type="hidden" name="tab" value="{{ request('tab') }}">
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                {{-- @include('partials.form-tab-headers') --}}
                <div class="tab-content">
                    <ul class="nav nav-tabs">
                        @php $active_tab = (empty(request('tab')) ? null : request('tab')) @endphp
                        @if(auth_user()->hasAccess('commerciale.ordinativi.edit.home'))
                            <li class="{{ ((empty(request('tab')) || request('tab') == 'ordinativo') ? 'active' : '') }}"><a href="{{ route('admin.commerciale.ordinativo.edit', [$ordinativo->id, 'tab' => 'ordinativo']) }}">Ordinativo</a></li>
                            @php $active_tab = (empty($active_tab) ? 'ordinativo' : $active_tab) @endphp
                        @endif
                        @if(auth_user()->hasAccess('commerciale.ordinativi.edit.vocieconomiche'))
                            <li class="{{ ((empty($active_tab) || request('tab') == 'vocieconomiche') ? 'active' : '') }}"><a href="{{ route('admin.commerciale.ordinativo.edit', [$ordinativo->id, 'tab' => 'vocieconomiche']) }}">Voci Economiche</a></li>
                            @php $active_tab = (empty($active_tab) ? 'vocieconomiche' : $active_tab) @endphp
                        @endif
                        @if(auth_user()->hasAccess('commerciale.ordinativi.edit.attivita'))
                            <li class="{{ ((empty($active_tab) || request('tab') == 'attivita') ? 'active' : '') }}"><a href="{{ route('admin.commerciale.ordinativo.edit', [$ordinativo->id, 'tab' => 'attivita']) }}">Attività</a></li>
                            @php $active_tab = (empty($active_tab) ? 'attivita' : $active_tab) @endphp
                        @endif
                        @if(auth_user()->hasAccess('commerciale.ordinativi.edit.assistenza'))
                            <li class="{{ ((empty($active_tab) || request('tab') == 'assistenza') ? 'active' : '') }}"><a href="{{ route('admin.commerciale.ordinativo.edit', [$ordinativo->id, 'tab' => 'assistenza']) }}">Assistenza</a></li>
                            @php $active_tab = (empty($active_tab) ? 'assistenza' : $active_tab) @endphp
                        @endif
                        @if(auth_user()->hasAccess('commerciale.ordinativi.edit.rinnovi'))
                            <li class="{{ ((empty($active_tab) || request('tab') == 'rinnovi') ? 'active' : '') }}"><a href="{{ route('admin.commerciale.ordinativo.edit', [$ordinativo->id, 'tab' => 'rinnovi']) }}">Rinnovo</a></li>
                            @php $active_tab = (empty($active_tab) ? 'rinnovi' : $active_tab) @endphp
                        @endif
                        @if(auth_user()->hasAccess('commerciale.ordinativi.edit.interventi'))
                            <li class="{{ ((empty($active_tab) || request('tab') == 'interventi') ? 'active' : '') }}"><a href="{{ route('admin.commerciale.ordinativo.edit', [$ordinativo->id, 'tab' => 'interventi']) }}">Interventi</a></li>
                            @php $active_tab = (empty($active_tab) ? 'interventi' : $active_tab) @endphp
                        @endif
                        @if(auth_user()->hasAccess('commerciale.ordinativi.edit.scadenzefatturazioni'))
                            <li class="{{ ((empty($active_tab) || request('tab') == 'scadenze_ft') ? 'active' : '') }}"><a href="{{ route('admin.commerciale.ordinativo.edit', [$ordinativo->id, 'tab' => 'scadenze_ft']) }}">Scadenze Fatturazioni</a></li>
                            @php $active_tab = (empty($active_tab) ? 'scadenze_ft' : $active_tab) @endphp
                        @endif
                        @if(auth_user()->hasAccess('commerciale.ordinativi.edit.documenti'))
                            <li class="{{ ((empty($active_tab) || request('tab') == 'documenti') ? 'active' : '') }}"><a href="{{ route('admin.commerciale.ordinativo.edit', [$ordinativo->id, 'tab' => 'documenti']) }}">Documenti</a></li>
                            @php $active_tab = (empty($active_tab) ? 'documenti' : $active_tab) @endphp
                        @endif
                    </ul>

                    @if(empty(request('tab')) || request('tab') == 'ordinativo')
                        @if(auth_user()->hasAccess('commerciale.ordinativi.edit.home'))
                            @include('commerciale::admin.ordinativi.partials.fields')
                        @endif
                    @endif

                    @if(request('tab') == 'vocieconomiche' || $active_tab == 'vocieconomiche')
                        @if(auth_user()->hasAccess('commerciale.ordinativi.edit.vocieconomiche'))
                            @include('commerciale::admin.ordinativi.partials.voci_economiche')
                        @endif
                    @endif
                    
                    @if(request('tab') == 'attivita' || $active_tab == 'attivita')
                        @if(auth_user()->hasAccess('commerciale.ordinativi.edit.attivita'))
                            @include('commerciale::admin.ordinativi.partials.attivita')
                        @endif
                    @endif

                    @if(request('tab') == 'assistenza' || $active_tab == 'assistenza')
                        @if(auth_user()->hasAccess('commerciale.ordinativi.edit.assistenza'))
                            @include('commerciale::admin.ordinativi.partials.assistenza')
                        @endif
                    @endif

                    @if(request('tab') == 'rinnovi' || $active_tab == 'rinnovi')
                        @if(auth_user()->hasAccess('commerciale.ordinativi.edit.rinnovi'))
                            @include('commerciale::admin.ordinativi.partials.rinnovi')
                        @endif
                    @endif

                    @if(request('tab') == 'interventi' || $active_tab == 'interventi')
                        @if(auth_user()->hasAccess('commerciale.ordinativi.edit.interventi'))
                            @include('commerciale::admin.ordinativi.partials.interventi')
                        @endif
                    @endif

                    @if(request('tab') == 'scadenze_ft' || $active_tab == 'scadeze_ft')
                        @if(auth_user()->hasAccess('commerciale.ordinativi.edit.scadenzefatturazioni'))
                            @include('commerciale::admin.ordinativi.partials.scadenze_fatturazioni')
                        @endif
                    @endif

                    @if(request('tab') == 'documenti' || $active_tab == 'documenti')
                        @if(auth_user()->hasAccess('commerciale.ordinativi.edit.documenti'))
                            @include('commerciale::admin.ordinativi.partials.documenti')
                        @endif
                    @endif
 
                    <div class="box-footer">
                        <button type="submit" class="btn btn-success btn-flat"> <i class="fa fa-floppy-o"></i> {{ trans('core::core.button.save') }}</button>
                        <a class="btn btn-info pull-left btn-flat" href="{{ route('admin.commerciale.ordinativo.read', ['ordinativo' => $ordinativo->id, 'tab' => $active_tab])}} "><i class="fa fa-arrow-left"></i> vai a visualizzazione </a>
                        <button  type="button"  class="btn btn-danger pull-right btn-flat" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.commerciale.ordinativo.destroy', [$ordinativo->id]) }}"><i class="fa fa-trash"></i> {{ trans('core::core.button.delete') }}</button>
						<a class="btn btn-warning pull-right btn-flat" href="{{ route('admin.commerciale.ordinativo.index')}}"><i class="fa fa-arrow-left"></i> {{ trans('Indietro') }}</a>
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
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'b', route: "<?= route('admin.commerciale.ordinativo.index') ?>" }
                ]
            });
        });
    </script>
    <script>
        $( document ).ready(function() {
            $('input[type="checkbox"].flat-blue, input[type="radio"].flat-blue').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                radioClass: 'iradio_flat-blue'
            });
        });
    </script>
@endpush
