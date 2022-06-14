@php
$attivita_list = [];
$visibile = 0;
$lavorazione_visibile = 0;
$posso_lavorarlo = 0;
$richieste_azioni = $richiesteintervento->azioni()->where('tipo', 1)->where('ticket_id', $richiesteintervento->id)->get();

if($richiesteintervento->checklavoro() > 0)
    $visibile = 1;

if(!empty($richiesteintervento->checkInLavorazione()))
    $lavorazione_visibile = 1;

if($richiesteintervento->possoLavorarlo() > 0)
    $posso_lavorarlo = 1;

if(!empty($richiesteintervento->cliente->ambiente)){
  $cliente_ambiente = $richiesteintervento->cliente->ambiente->first();
}

$gruppi = (empty($gruppi)) ? [] : $gruppi;
if(!empty($ordinativo)){
  $attivita_list = [''] + $ordinativo->attivita->pluck('oggetto', 'id')->toArray();
}

@endphp


@extends('layouts.master')

@section('content-header')
    <h1>
      @if(empty($richieste_azioni->last()))
        {{ trans('assistenza::richiesteinterventi.title.read richiesteintervento') }}
      @else
          @if($richiesteintervento->get_stato_integer() == 1)
            {{ trans('assistenza::richiesteinterventi.title.read richiesteintervento') }} - <small> (In lavorazione da <strong>{{$richieste_azioni->last()->created_user->full_name}}</strong>) </small>
          @else
            {{ trans('assistenza::richiesteinterventi.title.read richiesteintervento') }} - <small> (<strong>{{$richiesteintervento->get_stato_text()}}</strong>) </small>
          @endif
      @endif
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ route('admin.assistenza.richiesteintervento.index') }}">{{ trans('assistenza::richiesteinterventi.title.richiesteinterventi') }}</a></li>
        <li class="active">{{ trans('assistenza::richiesteinterventi.title.edit richiesteintervento') }}</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                @include('partials.form-tab-headers')
                <div class="tab-content">
                    <?php $i = 0; ?>
                    @foreach (LaravelLocalization::getSupportedLocales() as $locale => $language)
                        <?php $i++; ?>
                        <div class="tab-pane {{ locale() == $locale ? 'active' : '' }}" id="tab_{{ $i }}">
                            @if(!empty($richieste_azioni->last() && $richieste_azioni->last()->created_user_id != Auth::id() ))
                            <div class="callout callout-danger">
                                <p>Ticket preso in carico da <strong>{{$richieste_azioni->last()->created_user->full_name}}</strong></p>
                            </div>
                            @endif
                            @include('assistenza::admin.richiesteinterventi.partials.fields_read', ['lang' => $locale])
                        </div>
                    @endforeach


                    <div class="box-footer">
                        @if($visibile == 1 && $richiesteintervento->stato !== 3)
                          <a class="btn btn-info pull-left btn-flat" href="{{ route('admin.assistenza.richiesteintervento.edit', $richiesteintervento->id)}}">
                            <i class="fa fa-pencil"></i>
                            {{ trans('core::core.button.update') }}
                          </a>
                        @endif
                        @if(!empty($cliente_ambiente))
                            <button class="btn btn-flat btn-primary pull-left" type="button" onclick="javascript:loginUrbi({{$richiesteintervento->cliente->id}});"><i class="fa fa-external-link"> </i> Accedi ad URBI</button>
                        @endif
                        @if(!$lavorazione_visibile && $richiesteintervento->stato == 4 || $richiesteintervento->stato == 4 && Auth::user()->inRole('admin'))
                        <form id="form-start-lavorazione" action="{{ route('admin.assistenza.richiesteinterventi.startLavorazione') }}" method="POST">
                          {{ csrf_field() }}
                          <input hidden name="ticketID" value="{{ $richiesteintervento->id }}" />
                          <button type="submit" for="#form-start-lavorazione" class="btn btn-warning pull-left btn-flat" data-toggle="tooltip" data-placement="top" title="Riapri il ticket sospeso">Riprendi <i class="fa fa-play"></i></button>
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

                        @if($richiesteintervento->stato == 3)
                        <a class="btn btn-info pull-left btn-flat" href="{{ route('admin.assistenza.richiesteinterventi.riapri',$richiesteintervento->id)}}">
                            <i class="fa fa-plus-square"></i>
                            Riapri Ticket
                        </a>
                        @endif
                        @if(!empty($ordinativo))
                            <button type="button" class="btn btn-warning btn-flat" data-toggle="modal" data-target="#clienteLogs"><i class="fa fa-users"></i> Cliente</button>
                        <a class="btn btn-default pull-left btn-flat" href="{{ route('admin.assistenza.ticketintervento.createbyid',$richiesteintervento->id)}}">
                            <i class="fa fa-plus-square"></i>
                            Crea Rapportino
                        </a>
                        @endif
                        <a class="btn btn-warning pull-right btn-flat" href="{{ route('admin.assistenza.richiesteintervento.index')}}">
                          <i class="fa fa-arrow-left"></i>
                          Indietro
                        </a>
                    </div>
                </div>
            </div> {{-- end nav-tabs-custom --}}
        </div>
    </div>

    <div class="modal fade" id="clienteLogs" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="box-body">
                        @if(empty($gg_ordinativi))
                          <div class="alert alert-danger" role="alert">Non è presente alcun dato da mostrare.</div>
                        @endif
                        @foreach ($gg_ordinativi as $key => $row)
                            @php $interventi_sum = $ordinativo->interventi_sum_by_gruppo($row->gruppo_id); @endphp
                            @if( $key ==  0  || $gg_ordinativi[$key -1 ]->procedura  != $row->procedura)
                                <div class="row">
                                    <div class="col-md-12">
                                        <caption><h3><strong>{!! $row->procedura  !!}</strong></h3></caption>
                                    </div>
                                </div>
                            @endif
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tr>
                                        <th>Area</th>
                                        <th>Attività</th>
                                        <th>Ore Effettuate</th>
                                        <th>Ore Residue</th>
                                    </tr>
                                    <tbody>
                                        <tr>
                                            <td>@if( $key ==  0  || $gg_ordinativi[$key -1 ]->area  != $row->area){!! $row->area  !!}@else / @endif</td>
                                            <td>@if( $row-> quantita  > 0 ){{ $row->gruppo }}@endif</td>
                                            <td>{{$row->quantita_gia_effettuate}}</td>
                                            <td>{{  ($row->quantita_residue ) ? $row->quantita_residue : 0 }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                </div>
            </div>
        </div>
    </div>
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

        function clienteLogs()
        {
            var token = $('input[name="_token"]').val();
            var ticketID = "{{$richiesteintervento->id}}";

            $.get("", { _token: token, ticketID})
                .done(function(data) {
                    if($.trim(data) != '')
                    {
                        location.href = data;
                    }
                });
        }

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
@endpush
