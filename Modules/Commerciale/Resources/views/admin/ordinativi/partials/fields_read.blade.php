@php

$ordinativo = (empty($ordinativo)) ? '' : $ordinativo;

$offerte = [-1 => ''] + $offerte;

@endphp

<div class="box-body">
    <h3><strong> Totali </strong></h3>
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-euro"></i></span>
    
            <div class="info-box-content">
              <span class="info-box-text">Importo Ordinativo</span>
              <span class="info-box-number">{{ get_currency($ordinativo->importo()) }}</span>
            </div>
    
          </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-blue"><i class="fa fa-euro"></i></span>
    
            <div class="info-box-content">
              <span class="info-box-text">Importo Analisi Vendita</span>
              <span class="info-box-number">{{ get_currency($ordinativo->importo_analisi()) }}</span>
            </div>
    
          </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-euro"></i></span>
    
            <div class="info-box-content">
              <span class="info-box-text">Importo Offerte</span>
              <span class="info-box-number">{{ get_currency($ordinativo->importo_offerte()) }}</span>
            </div>
    
          </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
          <b>Cliente : </b>  {{  optional($ordinativo->cliente())->ragione_sociale }}
        </div>
        <div class="col-md-4">
           <b>Data di Inizio : </b>  {{  get_if_exist($ordinativo, 'data_inizio') }}
        </div>
        <div class="col-md-4">
          <b>Data di Fine : </b>    {{  get_if_exist($ordinativo, 'data_fine') }}
        </div>
        @if(!empty($ordinativo->note))
            <div class="col-md-12">
                <b>Note: </b>    {{  get_if_exist($ordinativo, 'note') }}
            </div>
        @endif
    </div>
    <hr>
    <h3><strong> Offerte Agganciate </strong></h3>
    <div class="row display-flex">
      @foreach($ordinativo->offerte()->get() as $offerta)
        <div class="col-md-4">
          <div class="small-box bg-green h-100 margin-0">
              <div class="inner">
                  <h4>
                      OFFERTA
                      <button type="button" class="btn btn-xs" data-toggle="modal" data-target="#modal-default" data-size="modal-lg" data-title="Dettagli" data-action="{{ route('admin.commerciale.offerta.read', $offerta->id) }}" data-element="#tab_1" data-form-disabled="true">
                          <i class="fa fa-eye text-black"> </i>
                      </button>
                  </h4>
                  <h3>{{ $offerta->numero_offerta() }}</h3>
                  <p>{{ $offerta->oggetto }}</p>
                  @if(!empty($offerta->analisi_vendita()->first()))  
                    <h5>Analisi Vendita:                   
                      <button type="button" class="btn btn-xs" data-toggle="modal" data-target="#modal-default" data-size="modal-lg" data-title="Dettagli" data-action="{{ route('admin.commerciale.analisivendita.read', $offerta->analisi_vendita()->first()->id) }}" data-element="#tab_1" data-form-disabled="true">
                        <i class="fa fa-eye text-black"> </i>
                      </button>
                    </h5> 
                  @endif
                  <hr> 
                  {{ $offerta->cliente()->first()->ragione_sociale }}
              </div>
              <div class="icon">
                  <i class="fa fa-shopping-cart"></i>
              </div>
          </div>
        </div>
      @endforeach
    </div>
    <hr>
    <h3><strong> Riepilogo </strong></h3>
    <div class="row display-flex">
        <div class="col-md-6">
            <div class="small-box bg-yellow h-100 margin-0">
                <div class="inner">
                    <h4>
                        Attività
                    </h4>
                    <h3>{{ $ordinativo->attivitaCompletamentoMedia() }} %</h3>
                </div>
                <div class="icon">
                    <i class="fa fa-bar-chart"></i>
                </div>
                <div class="small-box-footer margin-0 small-box-footer-ordinativo-h-100">
                    <div class="progress progress-sm {{ ($ordinativo->attivitaCompletamentoMedia() != 100) ? 'active' : '' }}">
                        <div class="progress-bar progress-bar-warning progress-bar-striped" role="progressbar" aria-valuenow="{{ $ordinativo->attivitaCompletamentoMedia() }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $ordinativo->attivitaCompletamentoMedia() }}%">
                            <span class="sr-only">{{ $ordinativo->attivitaCompletamentoMedia() }}% completate</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="small-box bg-aqua h-100 margin-0">
                <div class="inner">
                    <h4>
                        Rinnovo
                    </h4>
                    <h3>{!! (!empty($ordinativo->rinnovo)) ? $ordinativo->rinnovo->getDataRinnovo() : '#' !!}</h3>
                    <p><br></p>
                </div>
                <div class="icon">
                    <i class="fa fa fa-bookmark-o"></i>
                </div>
                <p class="small-box-footer margin-0 small-box-footer-ordinativo-h-100">
                    {{ (!empty($ordinativo->rinnovo)) ? $ordinativo->rinnovo->titolo : 'Nessun rinnovo impostato' }}
                    <span data-toggle="tooltip" title="" class="pull-right badge bg-light-blue" data-original-title="{{ (!empty($ordinativo->rinnovo)) ? count($ordinativo->rinnovo->notifiche) : 0 }} notifiche impostate">{{ (!empty($ordinativo->rinnovo)) ? count($ordinativo->rinnovo->notifiche) : 0 }}</span>
                </p>
            </div>
        </div>
    </div>
</div>
