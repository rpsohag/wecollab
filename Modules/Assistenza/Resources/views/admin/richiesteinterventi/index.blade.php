@extends('layouts.master')

@section('content-header')
    @php
        $stati_colori = json_decode(setting('commerciale::offerte::stati_colori'), true);

        $stati_filter = ['' => ''];
        $stati_filter = $stati_filter + config('assistenza.richieste_intervento.stati');

        $stati = config('assistenza.richieste_intervento.livelli_urgenza');
    @endphp
 
    <h1>
        {{ trans('assistenza::richiesteinterventi.title.richiesteinterventi') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('assistenza::richiesteinterventi.title.richiesteinterventi') }}</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
              <div class="btn-group pull-right" style="padding: 4px 10px;">
                  @if(auth_user()->hasAccess('assistenza.richiesteinterventi.exportexcel'))
                      <a href="{{ route('admin.assistenza.richiesteintervento.exportexcel', request()->all()) }}" class="btn bg-olive btn-flat">
                          <i class="fa fa-table"> </i> Esporta Excel
                      </a>
                  @endif
                  <a href="{{ route('admin.assistenza.richiesteintervento.create') }}" class="btn btn-primary btn-flat" style="margin: 0 15px 15px 0;">
                      <i class="fa fa-pencil"></i> {{ trans('assistenza::richiesteinterventi.button.create richiesteintervento') }}
                  </a>
              </div>
            </div>
            <div class="box box-primary">
              <div class="box-header">

                  <section class="bg-gray filters">
                      {!! Form::open(['route' => ['admin.assistenza.richiesteintervento.index'], 'method' => 'get']) !!}
                          <div class="row">
                              <div class="col-md-10">
                                  <div class="row">
                                    <div class="col-md-3">
                                        {!! Form::weText('codice', 'Codice', $errors) !!}
                                    </div>
                                    <div class="col-md-3">
                                        {!! Form::weSelect('stato', 'Stato', $errors , [0 => 'Tutte', 1 => 'Aperte & Sospese', 2 => 'Aperte', 3 => 'Sospese', 4 => 'Chiuse']) !!}
                                    </div>
                                    <div class="col-md-3">
                                        {!! Form::weDateRange('range_apertura', 'Intervallo Apertura', $errors) !!}
                                    </div>
                                    <div class="col-md-3">
                                        {!! Form::weDateRange('range_chiusura', 'Intervallo Chiusura', $errors) !!}
                                    </div>
                                    <div class="col-md-3">
                                        {!! Form::weSelectSearch('cliente','Cliente' , $errors , $clienti) !!}
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::weText('oggetto', 'Oggetto', $errors) !!}
                                    </div>
                                    <div class="col-md-5">
                                        {!! Form::weText('descrizione', 'Descrizione', $errors) !!}
                                    </div>
                                    <div class="col-md-3">
                                        {!! Form::weText('richiedente', 'Richiedente', $errors) !!}
                                    </div>
                                    <div class="col-md-3">
                                        {!! Form::weTags('destinatario','Destinatari' , $errors , $destinatari, []) !!}
                                    </div>
                                    <div class="col-md-3">
                                      {!! Form::weSelectSearch('area','Area di intervento' , $errors , $aree) !!}
                                    </div>
                                    <div class="col-md-3">
                                        {!! Form::weSelectSearch('gruppo','Ambito' , $errors , $gruppi) !!}
                                    </div>
                                  </div>
                              </div>
                              <div class="col-md-2 text-right">
                                  {!! Form::weSubmit('Cerca') !!}
                                  {!! Form::weReset('Svuota') !!}
                              </div>
                          </div>
                      {!! Form::close() !!}
                  </section>
                </div>
              <div id="index">@include('assistenza::admin.richiesteinterventi.partials.table_index')</div>
            </div>
        </div>
    </div>

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
          <div class="row">
            <div class="col-md-1" style="height:15px; background-color:gray;">  </div>
            <div class="col-md-11">Sospeso</div>
          </div>
      </section>


    @include('core::partials.delete-modal')
@stop

@section('footer')
    <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp;
@stop
@section('shortcuts')
    <dl class="dl-horizontal">
        <dt><code>c</code></dt>
        <dd>{{ trans('assistenza::richiesteinterventi.title.create richiesteintervento') }}</dd>
    </dl>
@stop

@push('js-stack')
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'c', route: "<?= route('admin.assistenza.richiesteintervento.create') ?>" }
                ]
            });
        });

        $(document).ready(function() {
         setInterval(function() {
           var page = window.location.href;
           $.ajax({
           url: page,
           success:function(data)
           {
            $('#index').html(data);
           }
           });
         }, 60000);
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

        $('input[name="range_apertura"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: 'D/M/Y'
        }
        }).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('D/M/Y') + ' - ' + picker.endDate.format('D/M/Y'));
        });
        $('input[name="range_chiusura"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: 'D/M/Y'
        }
        }).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('D/M/Y') + ' - ' + picker.endDate.format('D/M/Y'));
        });
    </script>
@endpush
