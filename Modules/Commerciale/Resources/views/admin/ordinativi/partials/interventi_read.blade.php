@php

    $gruppi = (empty($gruppi)) ? [] : $gruppi;
    $attivita_list = [''] + $ordinativo->attivita->pluck('oggetto', 'id')->toArray();
    	 
@endphp

<div class="box-body">
  <div class="row">
    <div class="col-md-4 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-aqua"><i class="fa fa-exclamation-circle"></i></span>

        <div class="info-box-content">
          <span class="info-box-text">Giornate Totali</span>
          <span class="info-box-number">{{ $gg_totali_giorni }} <small>giornate &</small> {{ $gg_totali_ore }} <small>ore</small></span>
        </div>

      </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-aqua"><i class="fa fa-exclamation-circle"></i></span>

        <div class="info-box-content">
          <span class="info-box-text">Giornate Residue Totali</span>
          <span class="info-box-number">{{ $gg_residui_giorni }} <small>giornate &</small> {{ $gg_residui_ore }} <small>ore</small></span>
        </div>

      </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-aqua"><i class="fa fa-exclamation-circle"></i></span>

        <div class="info-box-content">
          <span class="info-box-text">Giornate Effettuate Totali</span>
          <span class="info-box-number">{{ $gg_effettuati_giorni }} <small>giornate &</small> {{ $gg_effettuati_ore }} <small>ore</small></span>
        </div>

      </div>
    </div>
  </div>

@if(!empty($gg_ordinativi))
  @foreach ($gg_ordinativi as $key => $row)
    @php $interventi_sum = $ordinativo->interventi_sum_by_gruppo($row->gruppo_id); @endphp

      @if( $key ==  0  || $gg_ordinativi[$key -1 ]->procedura  != $row->procedura) 
      <div class="row">
      <div class="col-md-12">
        <caption>
          <h3><strong>{!! $row->procedura  !!} </strong></h3>
        </caption>
      </div>
    </div>
    @endif
    
    
      @if( $key ==  0  || $gg_ordinativi[$key -1 ]->area  != $row->area) 
      <div class="row"> 
      <div class="col-md-12">
        <caption>
          <h4>{!! $row->area  !!}</h4>
        </caption>
      </div>
    </div>
    @endif
    
      @if( $row-> quantita  > 0 )

      <div class="col-md-3">
          <div class="box box-info box-solid">
              <div class="box-header with-border">
                  <h3 class="box-title">{{ $row->gruppo }}</h3>

                  <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                      </button>
                  </div>
              <!-- /.box-tools -->
              </div>
              <!-- /.box-header -->
              <div class="box-body text-center">
                  <div class="row">
                      <div class="col-md-12">
                          <b>Quantità : </b> {{ $row-> quantita  }} {{config('commerciale.interventi.tipi')[$row->tipo ] }}
                      </div>

                  </div>
                  <div class="row">
                    <div class="col-sm-12">
                        {{   config('commerciale.interventi.tipi')[ $row->tipo ] }}
                        già effettuate {{    $row->quantita_gia_effettuate  }}
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6 border-right">
                      <div class="description-block">
                        <h5 class="description-header text-success">Effettuate</h5>
                        <span class="description-text">{{ $interventi_sum }}</span>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="description-block">
                        <h5 class="description-header text-warning">Residue</h5>
                        <span class="description-text">
                            {{  ($row->quantita_residue ) ? $row->quantita_residue : 0 }}
                        </span>
                      </div>
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                      <div class="col-md-12">
                        @if(array_key_exists($row->  attivita , $attivita_list)) 
                          Attività  {{ $attivita_list[$row->  attivita  ] }}
                        @endif
                      </div>
                  </div>
              </div>
              <!-- /.box-body -->
          </div>
          <!-- /.box -->
      </div>
      @endif
  @endforeach
@endif

</div>
