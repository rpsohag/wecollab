<div class="box-body">
  <div class="row">
      <div class="col-md-2">
           <strong>Cliente</strong>: {{ $richiesteintervento->cliente->ragione_sociale }}
      </div>
      @if(!empty($richiesteintervento->indirizzo))
          <?php $full_indirizzo = (!empty($richiesteintervento->indirizzo->denominazione) ? $richiesteintervento->indirizzo->denominazione . ' | ' : '') . $richiesteintervento->indirizzo->citta . ' - ' . $richiesteintervento->indirizzo->indirizzo . ' (' . $richiesteintervento->indirizzo->cap . ' ' . $richiesteintervento->indirizzo->provincia . ')'; ?>
      @else
          <?php $full_indirizzo = 'Non inserito'; ?>
      @endif
      <div class="col-md-3">
           <strong>Indirizzo</strong>: {{ $full_indirizzo }}
      </div>
      <div class="col-md-2">
           <strong>Procedura</strong>: {{ $richiesteintervento->procedura->titolo }}
      </div>
      <div class="col-md-3">
           <strong>Area di intervento</strong>: {{ $richiesteintervento->area->titolo }}
      </div>
      <div class="col-md-2">
           <strong>Ambito</strong>: {{ $richiesteintervento->gruppo->nome }}
      </div>
  </div>
  <br>
  <div class="row">
      <div class="col-md-3">
          <strong>Oggetto</strong>: {{ $richiesteintervento->oggetto }}
      </div>
      <div class="col-md-3">
          <strong>Codice</strong>: {{ $richiesteintervento->codice }}
      </div>
      <div class="col-md-3">
          <strong>Data Apertura</strong>: {{ get_date_hour_ita($richiesteintervento->created_at) }}
      </div>
      @if(!empty(get_if_exist($richiesteintervento, 'ordinativo_id')))
        <div class="col-md-3">
            <strong>Ordinativo</strong>: {{ $richiesteintervento->ordinativo->oggetto }}
        </div>
      @endif
  </div>
    <br>
  <div class="row">
      <div class="col-md-3">
          <strong>Livello urgenza</strong>: {{ config('assistenza.richieste_intervento.livelli_urgenza')[$richiesteintervento->livello_urgenza] }}
      </div>
      <div class="col-md-3">
          @if(!empty(get_if_exist($richiesteintervento, 'motivo_urgenza')))
              <strong>Motivo urgenza</strong>: {{ $richiesteintervento->motivo_urgenza }}
          @endif
      </div>
  </div>

  <br>

  <div class="row">
      <div class="col-md-12">
          {!! Form::weTextarea('descrizione_richiesta', 'Descrizione', $errors, get_if_exist($richiesteintervento, 'descrizione_richiesta'), ['readonly' => 'readonly']) !!}
      </div>
  </div>
</div>

{{-- Allegati --}}
@include('wecore::admin.partials.files_read', ['model' => $richiesteintervento])

{{-- Contatti --}}
<div class="box box-info box-shadow">
  <div class="box-header with-border">
    <h3 class="box-title">Contatti</h3>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
    <div class="row">
        @if(!empty(get_if_exist($richiesteintervento, 'richiedente')))
          <div class="col-md-4">
              <strong>Richiedente</strong>: {{ $richiesteintervento->richiedente }}
          </div>
        @endif
        @if(!empty(get_if_exist($richiesteintervento, 'numero_da_richiamare')))
          <div class="col-md-4">
              <strong>Numero da richiamare</strong>: {{ $richiesteintervento->numero_da_richiamare }}
          </div>
        @endif
        @if(!empty(get_if_exist($richiesteintervento, 'email')))
          <div class="col-md-4">
              <strong>Email</strong>: {{ $richiesteintervento->email }}
          </div>
        @endif
    </div>
  </div>
  <!-- /.box-body -->
</div>

{{-- Destinatario --}}
<div class="box box-warning box-shadow">
  <div class="box-header with-border">
    <h3 class="box-title">Destinatario</h3>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
        @foreach($richiesteintervento->destinatari as $desti)
          {{ $desti->full_name }}
          @if(!$loop->last)
            ,
          @endif
        @endforeach
  </div>
  <!-- /.box-body -->
</div>

@if(!empty($richiesteintervento_azioni->first()) && $richiesteintervento_azioni->first()->tipo != 1)
<div class="row">
 <div class="col-md-12">
     <!-- /.box -->
      <div class="box box-success box-shadow">
         <div class="box-header with-border">
            <h3 class="box-title">
                Azioni
            </h3>
            <!-- tools box -->
            <div class="box-tools pull-right">
                <span data-toggle="tooltip" title="" class="badge bg-green" data-original-title="Intervento"></span>
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
            <!-- /. tools -->
          </div>
          <!-- /.box-header -->
          <div class="box-body">
                      @foreach($richiesteintervento_azioni->sortBy('updated_at') as $azione)
                      @if($azione->tipo != 1)
                        <div class="row statica">
                            <div class="col-md-2">
                              @if($azione->tipologia_intervento == 2)
                                <i data-toggle="tooltip" data-placement="right" title="" data-original-title="{{ config('assistenza.richieste_intervento.richieste_procedure')[$azione->tipologia_intervento] }}" class="text-info fa fa-television"></i>
                              @elseif($azione->tipologia_intervento == 1)
                                <i data-toggle="tooltip" data-placement="right" title="" data-original-title="{{ config('assistenza.richieste_intervento.richieste_procedure')[$azione->tipologia_intervento] }}" class="text-info fa fa-truck"></i>
                              @endif
                              <span class="text-blue">{{ $azione->created_user->full_name }}</span> <br/>
                                {{ get_date_hour_ita($azione->created_at) }} - {{ get_date_hour_ita($azione->updated_at) }}
                            </div>
                            <div class="col-md-2">
                              (<strong class="text-info">{!! config('assistenza.richieste_intervento.azioni.tipi')[$azione->tipo] !!}</strong>)
                            </div>
                            <div class="col-md-8">
                              <br/>
                              {{ $azione->descrizione }}
                            </div>
                            <div class="col-md-12" ><hr style="margin:0px"></div>
                        </div>
                      @endif
                  @endforeach
          </div>
        </div>
    </div>
  </div>
</div>
@endif
