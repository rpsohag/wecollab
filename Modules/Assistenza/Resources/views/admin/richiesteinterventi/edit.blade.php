@php
$visibile = 0;
$lavorazione_visibile = 0;
$posso_lavorarlo = 0;

$richieste_azioni = $richiesteintervento->azioni()->where('tipo', 1)->where('ticket_id', $richiesteintervento->id)->get();

if($richiesteintervento->checkLavoro() > 0)
    $visibile = 1;

if(!empty($richiesteintervento->checkInLavorazione()))
    $lavorazione_visibile = 1;

if($richiesteintervento->possoLavorarlo() > 0)
    $posso_lavorarlo = 1;

if(!empty($richiesteintervento->cliente->ambiente))
  $ambiente = $richiesteintervento->cliente->ambiente()->first();
@endphp

@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('assistenza::richiesteinterventi.title.edit richiesteintervento') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ route('admin.assistenza.richiesteintervento.index') }}">{{ trans('assistenza::richiesteinterventi.title.richiesteinterventi') }}</a></li>
        <li class="active">{{ trans('assistenza::richiesteinterventi.title.edit richiesteintervento') }}</li>
    </ol>
@stop

@section('content')
    {!! Form::open(['route' => ['admin.assistenza.richiesteintervento.update', $richiesteintervento->id], 'method' => 'put', 'files'=> true]) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                @include('partials.form-tab-headers')
                <div class="tab-content">
                    <?php $i = 0; ?>
                    @foreach (LaravelLocalization::getSupportedLocales() as $locale => $language)
                        <?php $i++; ?>
                        <div class="tab-pane {{ locale() == $locale ? 'active' : '' }}" id="tab_{{ $i }}">
                            @include('assistenza::admin.richiesteinterventi.partials.fields', ['lang' => $locale])
                        </div>
                    @endforeach

                    <div class="box-footer">
                        <a class="btn btn-info pull-left btn-flat" href="{{ route('admin.assistenza.richiesteintervento.read', $richiesteintervento->id) }}"><i class="fa fa-arrow-left"></i> Vai alla visualizzazione</a>
                        @if(!empty($ambiente) && !empty($ambiente->n_db) && !empty($ambiente->api_sso))
                            <button class="btn btn-flat btn-primary pull-left" type="button" onclick="javascript:loginUrbi({{$richiesteintervento->cliente->id}});"><i class="fa fa-external-link"> </i> Accedi ad URBI</button>
                        @elseif(!empty($ambiente) && !empty($ambiente->n_db) && empty($ambiente->api_sso))
                            <a class="btn btn-flat btn-primary pull-left" data-toggle="modal" data-target="#ambienteInfo"><i class="fa fa-info"> </i> Ambiente</a>
                        @endif
                        @if($visibile == 1)
                            <button type="submit" class="btn btn-success btn-flat pull-left"><i class="fa fa-floppy-o"></i> {{ trans('core::core.button.update') }}</button>
                        @endif
                        {!! Form::close() !!}
                        @if(!$lavorazione_visibile && $richiesteintervento->stato == 4 || $richiesteintervento->stato == 4 && Auth::user()->inRole('admin'))
                        <form id="form-start-lavorazione" action="{{ route('admin.assistenza.richiesteinterventi.startLavorazione') }}" method="POST">
                          {{ csrf_field() }}
                          <input hidden name="ticketID" value="{{ $richiesteintervento->id }}" />
                          <button type="submit" for="#form-start-lavorazione" class="btn btn-warning btn-flat" data-toggle="tooltip" data-placement="top" title="Riapri il ticket sospeso">Riprendi <i class="fa fa-play"></i></button>
                        </form>
                        @else
                            @if($posso_lavorarlo == 1 && !$lavorazione_visibile)
                                <form id="form-start-lavorazione" action="{{ route('admin.assistenza.richiesteinterventi.startLavorazione') }}" method="POST">
                                  {{ csrf_field() }}
                                  <input hidden name="ticketID" value="{{ $richiesteintervento->id }}" />
                                  <button type="submit" for="#form-start-lavorazione" class="btn btn-warning pull-left btn-flat">
                                    <i class="fa fa-code-fork"></i> Inizia Lavorazione
                                  </button>
                                </form>
                            @endif
                        @endif
                        @if(auth_user()->hasAccess('assistenza.richiesteinterventi.admin'))
                            <button type="button" class="btn btn-danger pull-right btn-flat" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.assistenza.richiesteintervento.destroy', $richiesteintervento->id) }}"><i class="fa fa-trash"></i> {{ trans('core::core.button.cancel') }}</button>
                        @endif
                        <a class="btn btn-warning pull-right btn-flat" href="{{ route('admin.assistenza.richiesteintervento.index')}}"><i class="fa fa-arrow-left"></i> Indietro</a>
                    </div>
                </div>
            </div> {{-- end nav-tabs-custom --}}
        </div>
    </div>
    {!! Form::close() !!}

@if(!empty($ambiente) && !empty($ambiente->n_db) && empty($ambiente->api_sso))
  <div class="modal fade" id="ambienteInfo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="ambienteInfo"><strong>Ambiente</strong></h3>
        </div>
        <div class="modal-body">
          <div class="box box-default box-solid">
            <div class="box-body">
                <ul class="nav nav-stacked">
                    <li class="padding"><strong>Ambiente</strong>: <span class="pull-right">
                        <a target="_blank" id="ambiente_link" href="{{optional($ambiente)->ambiente}}">{{optional($ambiente)->ambiente}}</a>
                            &nbsp;&nbsp;<a class="btn bg-teal btn-sm" href="javascript:copia('ambiente_link')"
                            data-toggle="tooltip" data-placement="right" title="" data-original-title="Copia">
                            <i class="fa fa-tag"></i>
                        </a>
                    </li>
                    <li class="padding"><strong>Admin</strong>:
                        <span class="pull-right" id="ambiente_admin"> {{optional($ambiente)->admin}}
                            <a class="btn bg-teal btn-sm" href="javascript:copia('ambiente_admin')"
                            data-toggle="tooltip" data-placement="right" title="" data-original-title="Copia">
                                <i class="fa fa-tag"></i>
                            </a>
                        </span>
                    </li>
                    <li class="padding"><strong>Admin Password</strong>:
                        <span class="pull-right" id="ambiente_password_admin"> {{optional($ambiente)->password_admin}}
                            <a class="btn bg-teal btn-sm" href="javascript:copia('ambiente_password_admin')"
                            data-toggle="tooltip" data-placement="right" title="" data-original-title="Copia">
                                <i class="fa fa-tag"></i>
                            </a>
                        </span>
                    </li>
                    <li class="padding"><strong>ADM</strong>:
                        <span class="pull-right" id="ambiente_adm"> {{optional($ambiente)->adm}}
                            <a class="btn bg-teal btn-sm" href="javascript:copia('ambiente_adm')"
                            data-toggle="tooltip" data-placement="right" title="" data-original-title="Copia">
                                <i class="fa fa-tag"></i>
                            </a>
                        </span>
                    </li>
                    <li class="padding"><strong>ADM Password</strong>:
                        <span class="pull-right" id="ambiente_password_adm"> {{optional($ambiente)->password_adm}}
                            <a class="btn bg-teal btn-sm" href="javascript:copia('ambiente_password_adm')"
                            data-toggle="tooltip" data-placement="right" title="" data-original-title="Copia">
                                <i class="fa fa-tag"></i>
                            </a>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
        </div>
      </div>
    </div>
  </div>
@endif

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
                    { key: 'b', route: "<?= route('admin.assistenza.richiesteintervento.index') ?>" }
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
    <script>
        function startLavorazione()
        {
            var token = $('input[name="_token"]').val();
            var ticketID = "{{$richiesteintervento->id}}";

            $.post("{{ route('admin.assistenza.richiesteinterventi.startLavorazione') }}", { _token: token, ticketID})
                .done(function(data) {
                    if($.trim(data) != '')
                    {
                        location.href = data;
                    }
                });
        }
    </script>
@endpush
