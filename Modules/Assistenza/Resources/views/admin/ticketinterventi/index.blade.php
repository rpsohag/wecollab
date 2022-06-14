@php
    $settori = config('assistenza.ticket_intervento.settori');
    $intervento_tipi = config('commerciale.interventi.tipi');
@endphp

@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('assistenza::ticketinterventi.title.ticketinterventi') }} 
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('assistenza::ticketinterventi.title.ticketinterventi') }}</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="btn-group pull-right" style="margin: 0 15px 15px 0;">
                    @if(auth_user()->hasAccess('assistenza.ticketinterventi.exportexcel'))
                        <a href="{{ route('admin.assistenza.ticketintervento.exportexcel', request()->all()) }}" class="btn bg-olive btn-flat" style="padding: 4px 10px;">
                            <i class="fa fa-table"> </i> Esporta Excel
                        </a>
                    @endif
                    <a href="{{ route('admin.assistenza.ticketintervento.create') }}" class="btn btn-primary btn-flat" style="padding: 4px 10px;">
                        <i class="fa fa-pencil"></i> {{ trans('Crea Rapporto Intervento') }}
                    </a>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header">

                    <section class="bg-gray filters">
                        {!! Form::open(['route' => ['admin.assistenza.ticketintervento.index'], 'method' => 'get']) !!}
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="col-md-2">
                                            {!! Form::weDate('data_intervento', 'Data intervento', $errors) !!}
                                        </div>
                                        <div class="col-md-2">
                                            {!! Form::weSelectSearch('utente','Utente' , $errors , $utenti) !!}
                                        </div>
                                        <div class="col-md-2">
                                            {!! Form::weSelectSearch('cliente','Cliente' , $errors , $clienti) !!}
                                        </div>
                                        <div class="col-md-2">
                                            {!! Form::weSelectSearch('ordinativo','Ordinativo' , $errors , $ordinativi) !!}
                                        </div>
                                        <div class="col-md-2">
                                            {!! Form::weSelectSearch('attivita','Attivita' , $errors , $attivita) !!}
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::weText('nota','Note' , $errors) !!}
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
                                {!! order_th('data_intervento', 'Data Intervento') !!}
                                {!! order_th('utente', 'Utente') !!}
                                {!! order_th('cliente', 'Cliente') !!}
                                {!! order_th('ordinativo', 'Ordinativo') !!}
                                {!! order_th('attivita', 'Attività') !!}
                                {!! order_th('settore', 'Settore') !!}
                                {!! order_th('note', 'Nota') !!}
                                {{-- {!! order_th('num_giornate_ore', 'Numero Giornate/Ore') !!} --}}
                                <th>Numero Giornate/Ore</th>
                                {!! order_th('created_at', trans('core::core.table.created at')) !!}
                                {{-- <th data-sortable="false">{{ trans('core::core.table.actions') }}</th> --}}
                            </tr>
                            </thead>
                            <tbody>
                                @if(!empty($ticketinterventi))
                                    @foreach($ticketinterventi as $ticketintervento)

                                        @php
                                        $gruppo = $ticketintervento->ordinativo->giornate->where('gruppo_id', $ticketintervento->gruppo_id)->first();

                                        $intervento_tipo = !empty($gruppo) ? $intervento_tipi[$gruppo->tipo] : '';

                                        $voce = $ticketintervento->voci->first();
                                        @endphp

                                        <tr>
                                            <td data-sort="{{ (!empty($voce) ? $voce->data_intervento : '') }}">
                                                <a href="{{ route('admin.assistenza.ticketintervento.read', [$ticketintervento->id]) }}">
                                                    {{ (!empty($voce) ? get_date_ita($voce->data_intervento) : '') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.assistenza.ticketintervento.read', [$ticketintervento->id]) }}">
                                                    {{ optional($ticketintervento->created_user)->full_name }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.assistenza.ticketintervento.read', [$ticketintervento->id]) }}">
                                                    {{ optional($ticketintervento->cliente)->ragione_sociale }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.assistenza.ticketintervento.read', [$ticketintervento->id]) }}">
                                                {{ optional($ticketintervento->ordinativo)->oggetto }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.assistenza.ticketintervento.read', [$ticketintervento->id]) }}">
                                                    {{ optional($ticketintervento->gruppo)->nome }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.assistenza.ticketintervento.read', [$ticketintervento->id]) }}">
                                                    {{ $settori[$ticketintervento->settore_id] }}
                                                </a>
                                            </td>
                                            <td> 
                                                {{ $ticketintervento->note }}
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.assistenza.ticketintervento.read', [$ticketintervento->id]) }}">
                                                {{ optional($ticketintervento->voci)->sum('quantita') }}
                                                {{ $intervento_tipo }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.assistenza.ticketintervento.read', [$ticketintervento->id]) }}">
                                                {{ get_date_hour_ita($ticketintervento->created_at) }}
                                                </a>
                                            </td>
                                            <!-- <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.assistenza.ticketintervento.edit', [$ticketintervento->id]) }}" class="btn btn-default btn-flat"><i class="fa fa-pencil"></i></a>
                                                    <button class="btn btn-danger btn-flat" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.assistenza.ticketintervento.destroy', [$ticketintervento->id]) }}"><i class="fa fa-trash"></i></button>
                                                </div>
                                            </td> -->
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        <div class="text-right pagination-container">
                          {{ $ticketinterventi->links() }}
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
        <dd>{{ trans('assistenza::ticketinterventi.title.create ticketintervento') }}</dd>
    </dl>
@stop

@push('js-stack')
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'c', route: "<?= route('admin.assistenza.ticketintervento.create') ?>" }
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
