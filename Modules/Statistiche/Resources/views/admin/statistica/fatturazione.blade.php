@php
  $f_repilogo =  $fatturazione->select(
                                DB::raw('
                                  YEAR(data) as anno,
                                  SUM(IF(anticipata = 1, totale_netto, 0)) as anticipato,
                                  SUM(IF(anticipata <> 1 AND pagata <> 1, totale_netto, 0)) as no_anticipato_no_pagato,
                                  SUM(IF(anticipata <> 1 AND pagata = 1, totale_netto, 0)) as no_anticipato_pagato,
                                  SUM(IF(anticipata = 1 AND pagata <> 1, totale_netto, 0)) as anticipato_no_pagato,
                                  SUM(IF(pagata = 1, totale_netto, 0)) as pagato,
                                  SUM(IF(nota_di_credito <> 1, totale_netto, 0)) as totale_fatture,
                                  SUM(IF(nota_di_credito = 1, totale_netto, 0)) as totale_note_di_credito,
                                  SUM(IF(nota_di_credito <> 1, totale_netto, (totale_netto *-1 ) )) as totale
                                ')
                              )
                              ->groupBy('anno')
                              ->orderBy('anno', 'desc')
                              ->get();

  $anno_max = $f_repilogo->max('anno');
  $anno_min = $f_repilogo->min('anno');

  // Chart anno/mese
  $ft_anno_mese = $fatturazione->select(
                                    DB::raw('YEAR(data) as anno'),
                                    DB::raw('MONTH(data) as mese'),
                                    DB::raw('SUM(IF(nota_di_credito <> 1, totale_netto, (totale_netto *-1 ) )) as totale')
                                  )
                                  ->groupBy('anno', 'mese')
                                  ->get();

  $data_anno_mese = [];
  for($m = 1; $m <= 12; $m++)
  {
    foreach($ft_anno_mese as $anno_mese)
    {
      $totale = 0;

      if($anno_mese->mese == $m)
        $totale = (int)$anno_mese->totale;

      if(empty($data_anno_mese[$anno_mese->anno][$m]))
        $data_anno_mese[$anno_mese->anno][$m] = $totale;
    }
  }

  $i = 0;
  $chart_anno_mese = [];
  foreach ($data_anno_mese as $anno => $mesi)
  {
    $chart_anno_mese[$i]['name'] = (string) $anno;
    $chart_anno_mese[$i++]['data'] = array_values($mesi);
  }
  $chart_anno_mese = json_encode($chart_anno_mese);

  // Chart andamento annuale
  $chart_andamento_annuale = [];
  $chart_andamento_annuale_obj = $f_repilogo->sortBy('anno')->pluck('totale');

  foreach($chart_andamento_annuale_obj as $aa)
    $chart_andamento_annuale[] = (float)$aa;

  $chart_andamento_annuale = json_encode($chart_andamento_annuale);

@endphp

@extends('layouts.master')

@section('content-header')
    <h1>Statistiche</h1>
    <h2>Fatturazione</h2>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ route('admin.statistiche.statistica.index') }}">Statistiche</a></li>
        <li class="active">Fatturazione</li>
    </ol>

    <div class="box-header">
      <section class="bg-gray filters">
        {!! Form::open(['route' => ['admin.statistiche.statistica.fatturazione'], 'method' => 'get', 'class' => 'row']) !!}
          <div class="col-md-3">
              {!! Form::weSelectSearch('cliente','Cliente' , $errors , $clienti) !!}
          </div>
          <div class="col-md-1 col-xs-6 text-center">
              {!! Form::weSubmit('Cerca') !!}
          </div>
          <div class="col-md-1 col-xs-6 text-center">
              {!! Form::weReset('Svuota') !!}
          </div>
        {!! Form::close() !!}
      </section>
    </div>
@stop

@section('content')
    {{-- {!! Form::open(['route' => ['admin.statistiche.statistica.fatturazione'], 'method' => 'get']) !!} --}}
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
                  <th>ANNO</th>
                  <th>ANTICIPATO</th>
                  <th>NON ANTICIPATO<br>E NON PAGATO</th>
                  <th>ANTICIPATO<br>E NON PAGATO</th>
                  <th>NON ANTICIPATO<br>E PAGATO</th>
                  <th>SCADUTO</th> 
                  <th>SCADUTO<br>E ANTICIPATO</th>
                  <th>PAGATO</th>
                  <th>TOTALE FATTURE</th>
                  <th>TOTALE<br>NOTE DI CREDITO</th>
                  <th>TOTALE FATTURATO</th>
                </tr>
                @foreach ($f_repilogo as $key => $fr)
                  <tr>
                    <td><strong>{{ $fr->anno }}</strong></td>
                    <td>{{ get_currency($fr->anticipato) }}</td>
                    <td>{{ get_currency($fr->no_anticipato_no_pagato) }}</td>
                    <td>{{ get_currency($fr->anticipato_no_pagato) }}</td>
                    <td>{{ get_currency($fr->no_anticipato_pagato) }}</td>
                    <td>{{ get_currency($fatturazione_riepilogo_scaduto) }}</td>
                    <td>{{ get_currency($fatturazione_riepilogo_scaduto_anticipato) }}</td>
                    <td>{{ get_currency($fr->pagato) }}</td>
                    <td>{{ get_currency($fr->totale_fatture) }}</td>
                    <td>{{ get_currency($fr->totale_note_di_credito) }}</td>
                    <td>{{ get_currency($fr->totale) }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-body no-padding">
            <div id="chart-anno-mese"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-body no-padding">
            <div id="chart-andamento-annuale"></div>
          </div>
        </div>
      </div>
    </div>
    {{-- {!! Form::close() !!} --}}
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
                    { key: 'b', route: "<?= route('admin.statistiche.statistica.index') ?>" }
                ]
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('input[type="checkbox"].flat-blue, input[type="radio"].flat-blue').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                radioClass: 'iradio_flat-blue'
            });
        });

        // Chart annuale/mensile
        Highcharts.chart('chart-anno-mese', {
          chart: {
              type: 'areaspline'
          },
          accessibility: {
              description: 'Descrizione dell\'immagine: il grafico rappresenta il fatturato diviso per anni e mesi.'
          },
          title: {
              text: 'Confronto Fatturato Anno/Mese'
          },
          subtitle: {
              text: 'Risorse: modulo Commerciale/Fatturazione.'
          },
          legend: {
              layout: 'vertical',
              align: 'left',
              verticalAlign: 'top',
              x: 150,
              y: 100,
              floating: true,
              borderWidth: 1,
              backgroundColor:
                  Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF'
          },
          xAxis: {
            categories: {!! json_encode(config('wecore.mesi')) !!}
          },
          yAxis: {
              title: {
                  text: 'Totale'
              },
              labels: {
                  formatter: function () {
                      return this.value / 1000 + 'k';
                  }
              }
          },
          tooltip: {
              shared: true,
              valuePrefix: '€ ',
              valueDecimals: 2
          },
          credits: {
              enabled: false
          },
          plotOptions: {
              areaspline: {
                  fillOpacity: 0.5
              }
          },
          series : {!! $chart_anno_mese !!}
      });

      // Chart andamento annuale
      Highcharts.chart('chart-andamento-annuale', {
          title: {
              text: 'Andamento Annuale'
          },
          subtitle: {
              text: 'Risorse: modulo Commerciale/Fatturazione'
          },
          yAxis: {
              title: {
                  text: 'Totale'
              },
              labels: {
                  formatter: function () {
                      return Math.round(this.value / 1000) + 'k';
                  }
              }
          },
          legend: {
              layout: 'vertical',
              align: 'right',
              verticalAlign: 'middle'
          },
          tooltip: {
              shared: true,
              valuePrefix: '€ ',
              valueDecimals: 2
          },
          credits: {
              enabled: false
          },
          plotOptions: {
              series: {
                  label: {
                      connectorAllowed: false
                  },
                  pointStart: {{ $anno_min }}
              }
          },
          series: [{
              name: 'Fatturato',
              data: {!! $chart_andamento_annuale !!}
          }],
          responsive: {
              rules: [{
                  condition: {
                      maxWidth: 500
                  },
                  chartOptions: {
                      legend: {
                          layout: 'horizontal',
                          align: 'center',
                          verticalAlign: 'bottom'
                      }
                  }
              }]
          }
      });
    </script>
@endpush
