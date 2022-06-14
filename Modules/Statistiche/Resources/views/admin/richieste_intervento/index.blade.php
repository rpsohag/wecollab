@extends('layouts.master')

@section('content-header')
    <h1>Statistiche Tickets</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">Tickets</li>
    </ol>
@endsection

@section('content')
<div class="box-body">
  <div class="row">
    <div class="btn-group pull-right" style="padding: 4px 10px; margin-right: 8px;">
        @if(auth_user()->hasAccess('assistenza.richiesteinterventi.admin'))
          @if(empty(request('stats')) || request('stats') == 'dipendenti')
            <a href="{{ route('admin.assistenza.richiesteintervento.exportexceldipendenti', request()->all()) }}" class="btn bg-olive btn-flat">
                <i class="fa fa-table"> </i> Esporta Statistiche <small>(Dipendenti)</small>
            </a>
          @else
            <a href="{{ route('admin.assistenza.richiesteintervento.exportexcelaree', request()->all()) }}" class="btn bg-olive btn-flat">
              <i class="fa fa-table"> </i> Esporta Statistiche <small>(Aree)</small>
            </a>
          @endif
        @endif
    </div>
  </div>
  <div class="box box-primary box-shadow">
    <div class="box-header with-border">
      <section class="bg-gray filters">
        {!! Form::open(['route' => ['admin.statistiche.statistica.richiesteintervento'], 'method' => 'get']) !!}
          <div class="row">
            <div class="col-md-10">
                <div class="row">
                  <input type="hidden" name="stats" value="{{ request('stats') }}" />
                  <div class="col-md-2">
                    {!! Form::weDate('data_inizio', 'Data Inizio', $errors, date('d-m-Y', strtotime(request('data_inizio')))) !!}
                  </div>
                  <div class="col-md-2">
                      {!! Form::weDate('data_fine', 'Data Fine', $errors, date('d-m-Y', strtotime(request('data_fine')))) !!}
                  </div>
                  <div class="col-md-3">
                    {!! Form::weSelectSearch('cliente','Cliente' , $errors , $clienti, request('cliente')) !!}
                  </div>
                  <div class="col-md-3">
                    {!! Form::weSelectSearch('ordinativo','Ordinativo' , $errors , $ordinativi, request('ordinativo')) !!}
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
    <div class="box-body">
      <div class="nav-tabs-custom">
        @include('partials.form-tab-headers')
        <div class="tab-content">
            <ul class="nav nav-tabs">
                <li class="{{ empty(request('stats')) || request('stats') == 'dipendenti' ? 'active' : '' }}"><a href="{{ route('admin.statistiche.statistica.richiesteintervento', ['stats' => 'dipendenti']) }}">Dipendenti</a></li>
                <li class="{{ request('stats') == 'aree' ? 'active' : '' }}"><a href="{{ route('admin.statistiche.statistica.richiesteintervento', ['stats' => 'aree']) }}">Aree</a></li>
            </ul>
            @if(empty(request('stats')) || request('stats') == 'dipendenti')
              <div class="row">
                <div class="col-md-12">
                  <div class="box box-danger">
                    <div class="box-header">
                        <h4><strong>Statistiche Per Dipendente</strong></h4>
                    </div>
                    <div class="box-body">
                      <div class="table-responsive">
                        <table class="table table-striped">
                          <tr>
                            <th>Dipendente</th>
                            <th>Area</th>
                            <th>Tickets</th>
                            <th>Tempo Di Lavorazione <small>(Totale)</small></th>
                            <th>Tempo Di Lavorazione <small>(Media)</small></th>
                          </tr>
                          <tbody>
                            @foreach ($dettaglio as $key => $record)
                              @foreach($record as $single)
                                <tr>
                                  <td>{!! $single['dipendente'] !!}</td>
                                  <td>{!! $single['titolo'] !!}</td>
                                  <td>{!! $single['tickets'] !!}</td>
                                  <td>{{ get_seconds_to_hours($single['tempo_lavorazione']) }}</td>
                                  <td>{{ get_seconds_to_hours($single['tempo_lavorazione_media']) }}</td>
                                </tr>
                              @endforeach
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @endif
            @if(request('stats') == 'aree')
              <div class="row">
                <div class="col-md-12">
                  <div class="box box-warning">
                    <div class="box-header">
                        <h4><strong>Statistiche Per Area</strong></h4>
                    </div>
                    <div class="box-body">
                      <div class="table-responsive">
                        <table class="table table-striped">
                          <tr>
                            <th>Area</th>
                            <th>Tickets Aperti</th>
                            <th>Tickets Chiusi</th>
                            <th>Tickets Totali</th>
                            <th>Tempo Di Lavorazione <small>(Totale)</small></th>
                            <th>Tempo Di Lavorazione <small>(Media)</small></th>
                            <th>Tempo Di Risoluzione <small>(Media)</small></th>
                            <th>Tempo Di Risoluzione <small>(Media Con Sospensioni)</small></th>
                          </tr>
                          <tbody>
                            @foreach ($dettaglio as $key => $single)
                              @if(!empty($single['titolo']))
                                <tr>
                                  <td>{{ $single['titolo'] }} </td>
                                  <td>{{ $single['aperti'] }} </td>
                                  <td>{{ $single['chiusi'] }} </td>
                                  <td>{{ $single['tickets'] }} </td>
                                  <td>{{ get_seconds_to_hours($single['tempo_lavorazione_media']) }} </td>
                                  <td>{{ get_seconds_to_hours($single['tempo_lavorazione_totale']) }} </td>
                                  <td>{{ get_seconds_to_hours($single['tempo_risoluzione_media']) }} </td>
                                  <td>{{ get_seconds_to_hours($single['tempo_risoluzione_con_sospensioni_media']) }} </td>
                                </tr>
                              @endif
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('js-stack')
@endpush
