@extends('layouts.master_pdf')
@section('content')
<style>
  hr.border-bottom {
    margin: 0;
    padding: 0;
    border: none;
    border-bottom: 1px dotted;
  }
</style>

<main class="container">
  @php
  $po_totale = 0;
  @endphp

<div class="box-body bg-gray">
	<div class="row">
    <div class="col-md-12"><br></div>
    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-body box-profile">
          <h3 class="profile-username text-center">{{ $censimentocliente->cliente()->first()->ragione_sociale }}</h3>
          @if(!empty($censimentocliente->cliente()->first()))
            <p class="text-muted text-center">{{ ucfirst($censimentocliente->cliente()->first()->tipologia) }}</p>
            <ul class="list-group list-group-unbordered">
              <li class="list-group-item">
                <b>Offerte totali</b>
                <span class="pull-right">{{ $censimentocliente->cliente()->first()->offerte->where('azienda', session('azienda'))->count() }}</span>
              </li>
              <li class="list-group-item">
                <b>Numero di dipendenti</b>
                <span class="pull-right">{{ $censimentocliente->numero_dipendenti }}</span>
              </li>
              <li class="list-group-item">
                <b>Fascia di abitanti</b>
                <span class="pull-right">{{ $censimentocliente->fascia_abitanti() }}</span>
              </li>
            </ul>
          @endif
        </div>
      </div>

      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Informazioni</h3>
        </div>
        <div class="box-body">
          @if(!empty($censimentocliente->sindaco))
            <h5><strong>Sindaco</strong></h5>
            <p class="text-muted">
              {{ $censimentocliente->sindaco }}
              @if(!empty($censimentocliente->sindaco_email))
                <br>
                <i class="fa fa-envelope margin-r-5"> </i> <span>{{ $censimentocliente->sindaco_email }}</span>
              @endif
              @if(!empty($censimentocliente->sindaco_telefono))
                <br>
                <i class="fa fa-phone-square margin-r-5"> </i> <span>{{ $censimentocliente->sindaco_telefono }}</span>
              @endif
            </p>
          @endif

          @if(!empty($censimentocliente->segretario))
            <h5><strong>Segretario</strong></h5>
            <p class="text-muted">
              {{ $censimentocliente->segretario }}
              @if(!empty($censimentocliente->segretario_email))
                <br>
                <i class="fa fa-envelope margin-r-5"> </i> <span>{{ $censimentocliente->segretario_email }}</span>
              @endif
              @if(!empty($censimentocliente->segretario_telefono))
                <br>
                <i class="fa fa-phone-square margin-r-5"> </i> <span>{{ $censimentocliente->segretario_telefono }}</span>
              @endif
            </p>
          @endif

          @if(!empty($censimentocliente->referente))
            <h5><strong>Referente Unico del Progetto</strong></h5>
            <p class="text-muted">
              {{ $censimentocliente->referente }}
              @if(!empty($censimentocliente->referente_email))
                <br>
                <i class="fa fa-envelope margin-r-5"> </i> <span>{{ $censimentocliente->referente_email }}</span>
              @endif
              @if(!empty($censimentocliente->referente_telefono))
                <br>
                <i class="fa fa-phone-square margin-r-5"> </i> <span>{{ $censimentocliente->referente_telefono }}</span>
              @endif
            </p>
          @endif

          <hr>

          @if(!empty($censimentocliente->indirizzo_completo))
            <h4>
              <i class="fa fa-map-marker margin-r-5"></i> Sede legale
              <a class="btn btn-xs btn-flat btn-default pull-right" href="https://maps.google.com/?q={{ $censimentocliente->indirizzo_completo }}" target="_blank"><i class="fa fa-location-arrow"> </i></a>
            </h4>
            <p class="text-muted">
              {{ $censimentocliente->indirizzo_completo }}
            </p>
            <hr>
          @endif

          <h4><i class="fa fa-sitemap margin-r-5"></i> Pianta Organica</h4>

          <table class="table table-bordered table-striped">
            @foreach(config('commerciale.censimenticlienti.pianta_organica') as $key => $po)
              @php
                $po_totale += get_if_exist($censimentocliente->pianta_organica, $key);
              @endphp
              <tr>
                <td>{{ $po }}</td>
                <td class="text-center">{{ (!empty(get_if_exist($censimentocliente->pianta_organica, $key)) ? $censimentocliente->pianta_organica->$key : 0 ) }}</td>
              </tr>
            @endforeach
            <tfoot class="bg-info">
              <tr>
                <th>TOTALE</th>
                <th class="text-center">{{ $po_totale }}</th>
              </tr>
            </tfoot>
          </table>

          @if(!empty($censimentocliente->note))
            <hr>
            <strong><i class="fa fa-file-text-o margin-r-5"></i> Note</strong>
            <p>{{ $censimentocliente->note }}</p>
          @endif
        </div>
      </div>
    </div>
    <div class="col-md-9">
      @foreach($procedure as $procedura)
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">SITUAZIONE SOFTWARE ATTUALI <strong>{!! $procedura->titolo !!}</strong></h3>
          </div>
          <div class="box-body">
            <div class="table-responsive">
              <table class="table table-striped table-bordered">
                <thead>
                  <tr>
                    <th class="text-center">Area</th>
                    <th class="text-center">Attività</th>
                    <th class="text-center">Referente</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Telefono</th>
                    <th class="text-center">Software Attuale</th>
                    <th class="text-center">Spesa Attuale</th>
                  </tr>
                </thead>
                @if(count($censimentocliente->referenti) > 0)
                  @foreach($censimentocliente->referenti as $key => $referente)
                    @if($referente->procedura_id == $procedura->id)
                      @if(!empty($referente->email) || !empty($referente->spesa) || !empty($referente->nome) || !empty($referente->telefono)) 
                        <tr>
                          @if(!empty($referente->area_id))
                            <td class="valign-middle">{{ $censimentocliente->situazione_software_area($referente->area_id) }}</td>
                          @else 
                            <td></td>
                          @endif
                          @if(!empty($referente->attivita_id))
                            <td class="valign-middle">{{ $censimentocliente->situazione_software_attivita($referente->attivita_id) }}</td>
                          @else 
                            <td></td>
                          @endif
                          <td class="valign-middle">{!! get_if_exist($referente, 'nome') !!}</td>
                          <td class="valign-middle">{!! get_if_exist($referente, 'email') !!}</td>
                          <td class="valign-middle">{!! get_if_exist($referente, 'telefono') !!}</td>
                          <td class="valign-middle">{!! (!empty($referente) ? config('commerciale.censimenticlienti.altri_software')[(get_if_exist($referente, 'sw'))] : '') !!}</td>
                          <td class="text-center">&euro;{!! get_if_exist($referente, 'spesa') !!}</td>
                        </tr>
                      @endif
                    @endif
                  @endforeach
                @endif
                <tfoot class="bg-info">
                  <tr>
                    <th colspan="5"></th>
                    <th class="text-right">TOTALE</th>
                    @if(!empty($spesa_totale[$procedura->id]))
                      <th class="text-center">{{ get_currency($spesa_totale[$procedura->id]) }}</th>
                    @else 
                      <th class="text-center">{{ get_currency(0) }}</th>
                    @endif
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
			@endforeach
    </div>
  </div>
</div>
</main>
@stop
