@extends('layouts.master')

@section('content-header')

<?php //dd($utenti); ?>

    <h1>Statistiche</h1>
    <h2>Richieste Intervento</h2>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ route('admin.statistiche.statistica.index') }}">Statistiche</a></li>
        <li class="active">Richieste Intervento</li>
    </ol>
@stop

@section('content')
<div class="box box-primary">
  <div class="box-header">
      <section class="bg-gray filters">
          {!! Form::open(['route' => ['admin.statistiche.statistica.richiesteintervento'], 'method' => 'get']) !!}
              <div class="row">
                  <div class="col-md-10">
                      <div class="row">
                          <div class="col-md-2">
                            {!! Form::weSelectSearch('lavorato','Utente' , $errors , $utenti) !!}
                          </div>
                          <div class="col-md-2">
                            {!! Form::weSelectSearch('cliente','Cliente' , $errors , $clienti) !!}
                          </div>
                          <div class="col-md-3">
                            {!! Form::weDate('data_apertura_statistiche', 'Data inizio', $errors) !!}
                          </div>
                          <div class="col-md-3">
                            {!! Form::weDate('data_chiusura_statistiche', 'Data fine', $errors) !!}
                          </div>
                      </div>
                  </div>
                  <div class="col-md-2 text-right">
                      {!! Form::weSubmit('Cerca') !!}
                      {!! Form::weReset('Svuota') !!}
                  </div>
              </div>
              <input type="hidden" name="order[by]" value="{{ (!empty(request('order')['by']) ? request('order')['by'] : null) }}">
              <input type="hidden" name="order[sort]" value="{{ (!empty(request('order')['sort']) ? request('order')['sort'] : null) }}">
          {!! Form::close() !!}
      </section>
    </div>
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Riepilogo</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover table-striped">
              <tbody>
                <tr>
                  <th>RICHIESTE ATTIVE</th>
                  <th>RICHIESTE RISOLTE</th>
                  <th>TEMPO MEDIO RISOLUZIONE LAVORATIVA</th>
                  <th>TEMPO TOTALE RISOLUZIONE LAVORATIVA</th>
                  <th>TEMPO MEDIO RISOLUZIONE LAVORATIVA ( < 3 ORE )</th>
                  <th>RICHIESTE RISOLTE ( < 3 ORE )</th>
                </tr>
                <tr>
                  <td>{{$numero_aperte}}</td>
                  <td>{{$numero_chiuse}}</td>
                  <td>{{secondsToTime($tempo_medio_risoluzione)}}</td>
                  <td>{{secondsToTime($tempo_totale_risoluzione)}}</td>
                  <td>{{secondsToTime($media_chiuse_min3)}}</td>
                  <td>{{$min3}}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
    </div>
    @include('statistiche::admin.statistica.partials.richiesteintervento_blocchi')
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

@endpush
