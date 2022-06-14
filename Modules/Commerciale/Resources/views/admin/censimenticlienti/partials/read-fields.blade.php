@php
  $po_totale = 0;
 //dd($censimentocliente);
 //dd($procedure);
@endphp

<div class="box-body bg-gray">
	<div class="row">
    <div class="col-md-12"><br></div>
    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-body box-profile">
          @if(!empty($censimentocliente->cliente()->first()))
            <img class="profile-user-img img-responsive img-circle" src="{{ (empty(get_if_exist($censimentocliente->cliente()->first(), 'logo'))) ? set_via_placeholder(100) : get_if_exist($censimentocliente->cliente()->first(), 'logo') }}" alt="Logo">

            <h3 class="profile-username text-center">{{ get_if_exist($censimentocliente->cliente()->first(), 'ragione_sociale') }}</h3>

            <p class="text-muted text-center">{{ ucfirst($censimentocliente->cliente()->first()->tipologia) }}</p>

            <ul class="list-group list-group-unbordered">
              <li class="list-group-item">
                <b>Offerte totali</b>
                <a class="btn btn-xs btn-flat btn-default" href="{{ route('admin.commerciale.offerta.create') }}"><i class="fa fa-plus"> </i></a>
                <a class="pull-right" href="{{ route('admin.commerciale.offerta.index', ['cliente' => $censimentocliente->cliente()->first()->id]) }}">{{ $censimentocliente->cliente()->first()->offerte()->where('azienda', session('azienda'))->count() }}</a>
              </li>
              <li class="list-group-item">
                <b>Numero di dipendenti</b>
                <span class="pull-right">{{ $censimentocliente->numero_dipendenti }}</span>
              </li>
              <li class="list-group-item">
                <b>Fascia di abitanti</b>
                <span class="pull-right">{{ $censimentocliente->fascia_abitanti() }}</span>
              </li>
              <li class="list-group-item">
                <b>Numero Utilizzatori Urbi</b>
                <span class="pull-right">{{ $censimentocliente->numero_utilizzatori_urbi }}</span>
              </li>
            </ul>

            <div class="btn-group">
              <a href="{{ route('admin.amministrazione.clienti.edit', $censimentocliente->cliente()->first()->id) }}" class="btn btn-primary btn-flat"><b>Vai alla rubrica</b></a>
              <a href="{{ route('admin.commerciale.censimentocliente.edit', $censimentocliente->id) }}" class="btn btn-warning btn-flat"><b>Modifica</b></a>
            </div>
          @endif
        </div>
      </div>

      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Informazioni</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <h4><i class="fa fa-info-circle margin-r-5"> </i> Info</h4>

          @if(!empty($censimentocliente->sindaco))
            <h5><strong>Sindaco</strong></h5>
            <p class="text-muted">
              {{ $censimentocliente->sindaco }}

              @if(!empty($censimentocliente->sindaco_email))
                <br>
                <i class="fa fa-envelope margin-r-5"> </i> <a href="mailto:{{ $censimentocliente->sindaco_email }}">{{ $censimentocliente->sindaco_email }}</a>
              @endif

              @if(!empty($censimentocliente->sindaco_telefono))
                <br>
                <i class="fa fa-phone-square margin-r-5"> </i> <a href="tel:{{ $censimentocliente->sindaco_telefono }}">{{ $censimentocliente->sindaco_telefono }}</a>
              @endif
            </p>
          @endif

          @if(!empty($censimentocliente->segretario))
            <h5><strong>Segretario</strong></h5>
            <p class="text-muted">
              {{ $censimentocliente->segretario }}

              @if(!empty($censimentocliente->segretario_email))
                <br>
                <i class="fa fa-envelope margin-r-5"> </i> <a href="mailto:{{ $censimentocliente->segretario_email }}">{{ $censimentocliente->segretario_email }}</a>
              @endif

              @if(!empty($censimentocliente->segretario_telefono))
                <br>
                <i class="fa fa-phone-square margin-r-5"> </i> <a href="tel:{{ $censimentocliente->segretario_telefono }}">{{ $censimentocliente->segretario_telefono }}</a>
              @endif
            </p>
          @endif

          @if(!empty($censimentocliente->referente))
            <h5><strong>Referente Unico del Progetto</strong></h5>
            <p class="text-muted">
              {{ $censimentocliente->referente }}

              @if(!empty($censimentocliente->referente_email))
                <br>
                <i class="fa fa-envelope margin-r-5"> </i> <a href="mailto:{{ $censimentocliente->referente_email }}">{{ $censimentocliente->referente_email }}</a>
              @endif

              @if(!empty($censimentocliente->referente_telefono))
                <br>
                <i class="fa fa-phone-square margin-r-5"> </i> <a href="tel:{{ $censimentocliente->referente_telefono }}">{{ $censimentocliente->referente_telefono }}</a>
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
            <tfoot>
              <th>TOTALE</th>
              <th class="text-center">{{ $po_totale }}</th>
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
            <div class="table-responsive ">
              <table class="table table-striped table-bordered">
                <thead class="bg-primary">
                  <tr>
                    <th class="text-center">Area</th>
                    <th class="text-center">Attività</th>
                    <th class="text-center">Referente</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Telefono</th>
                    <th class="text-center">Software Attuale</th>
                    <th class="text-center">Spesa Attuale</th>
                    <th class="text-center">Note</th>
                  </tr>
                </thead>
                @if(count($censimentocliente->referenti) > 0)
                  @foreach($censimentocliente->referenti as $key => $referente)
                    @if($referente->procedura_id == $procedura->id)
                      @if(!empty($referente->email) || !empty($referente->spesa) || !empty($referente->nome) || !empty($referente->telefono) || !empty($referente->area_id) || !empty($referente->attivita_id)) 
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
                          <td class="valign-middle"><a href="mailto:{!! get_if_exist($referente, 'email') !!}">{!! get_if_exist($referente, 'email') !!}</a></td>
                          <td class="valign-middle">
                            <a href="tel:{!! get_if_exist($referente, 'telefono') !!}">{!! get_if_exist($referente, 'telefono') !!}</a>
                          </td>
                          <td class="valign-middle">{!! (!empty($referente) ? config('commerciale.censimenticlienti.altri_software')[(get_if_exist($referente, 'sw'))] : '') !!}</td>
                          <td class="valign-middle">{!! get_if_exist($referente, 'spesa') !!}</td>
                          <td class="valign-middle">
                            @if(get_if_exist($referente, 'note'))
                              <button type="button" class="btn btn-primary btn-flat btn-sm" data-toggle="modal" data-target="#modal-note-{{ $key }}">
                                <i class="fa fa-file-text"> </i>
                              </button>

                              <!-- Modal -->
                              <div class="modal fade" id="modal-note-{{ $key }}" tabindex="-1" role="dialog" aria-labelledby="Note" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                  <div class="modal-content">
                                    <div class="modal-header"> 
                                      <h5 class="modal-title" id="exampleModalLabel">Note {{ $censimentocliente->situazione_software_area($referente->area_id) }}</h5>
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Chiudi">
                                        <span aria-hidden="true">&times;</span>
                                      </button>
                                    </div>
                                    <div class="modal-body">
                                      {{ get_if_exist($referente, 'note') }}
                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            @endif
                          </td>
                        </tr>
                      @endif
                    @endif
                  @endforeach
                @endif
                <tfoot class="bg-info">
                  <tr>
                    <th colspan="5"></th>
                    <th>TOTALE</th>
                    @if(!empty($spesa_totale[$procedura->id]))
                      <th>{{ get_currency($spesa_totale[$procedura->id]) }}</th>
                    @else 
                      <th>{{ get_currency(0) }}</th>
                    @endif
                    <th></th>
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

@push('js-stack')
  <script>
    $(document).ready(function() {
      $('input, select, textarea')
        .attr('disabled', 'disabled')
        .addClass('disabled');
    });
  </script>
@endpush
