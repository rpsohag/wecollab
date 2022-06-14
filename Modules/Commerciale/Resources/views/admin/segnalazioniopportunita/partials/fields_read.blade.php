
<div class="row">
  <div class="col-md-12">
    <div class="row">
      <div class="col-md-12 bg-info">
        <div class="row">
          <div class="col-md-1 text-center"><i style="margin-top:24px;" class="icon fa fa-5x fa-file text-primary"></i></div> 
          <div class="col-md-11">
            <h2 class="display-4">{{ $segnalazione->oggetto }}</h2>
            <p class="lead">Informazioni riguardo la segnalazione commerciale in oggetto.</p>
          </div>
        </div>
      </div>   
      <div class="col-md-6" style="margin-top:38px;">
        <div class="info-box">
          <span class="info-box-icon {{ $segnalazione->stato_id == 1 ? 'bg-green' : ($segnalazione->stato_id == 3 ? 'bg-red' : 'bg-blue') }}"><i class="fa fa-hashtag"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Segnalazione - {{ $segnalazione->stato() }}</span>
            <span class="info-box-number">{{ $segnalazione->numero() }}</span>
          </div>
        </div>
      </div>

      <div class="col-md-6" style="margin-top:16px;">
        <div class="alert alert-info alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
          <h4><i class="icon fa fa-info"></i> Informazioni</h4>
          La segnalazione è stata creata in data <strong class="font-bold">{{ get_date_hour_ita(get_if_exist($segnalazione, 'created_at')) }}</strong> da <strong class="font-bold">{{ get_if_exist($segnalazione->created_user, 'full_name') }}</strong>.
          @if(empty($segnalazione->cliente()))
            <br> Cliente Proposto: <strong>{{ $segnalazione->cliente }}</strong>
          @else 
            <br> Cliente: <strong>{{ optional($segnalazione->cliente())->ragione_sociale }}</strong>
          @endif
          <br> Stato: <strong>{{ $segnalazione->stato() }}</strong>
          @if(!empty($segnalazione->analisi_vendita))
            <br> Analisi vendita: <a target="_blank" href="{{ route('admin.commerciale.analisivendita.edit' , [$segnalazione->analisi_vendita->id]) }}"> {{ $segnalazione->analisi_vendita->titolo }} &nbsp;&nbsp;<i class="fa fa-external-link"> </i> </a>
          @endif 
          @if(!empty($segnalazione->censimento) && optional($segnalazione->cliente())->ragione_sociale)
            <br> Censimento: <a target="_blank" href="{{ route('admin.commerciale.censimentocliente.edit' , [$segnalazione->censimento->id]) }}"> {{ optional($segnalazione->cliente())->ragione_sociale }} &nbsp;&nbsp;<i class="fa fa-external-link"> </i> </a>
          @endif 
        </div>
      </div>

      @if(!empty($segnalazione->descrizione))
      <div class="col-md-12" style="margin-top:16px;">
        <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
          <h4><i class="icon fa fa-info"></i> Descrizione</h4>
          {{ $segnalazione->descrizione }}
        </div>
      </div>
      @endif

      <div class="col-md-12">
        <br> 
        <div class="box box-primary">
          <div class="box-header">
            <h4><strong>CHECKLISTS</strong></h4>
          </div>
          <div class="box-body">
            <section>
              @foreach($procedure as $procedura)
                <div class="box box-primary">
                  <div class="box-header with-border">
                    <h3 class="box-title">{!! $procedura->titolo !!}</h3>
                  </div>
                  <div class="box-body">
                    <div class="table-responsive ">
                      <table class="table table-striped table-bordered">
                        <thead class="bg-primary">
                          <tr>
                            <th style="width:20%" text-center">Area</th>
                            <th style="width:15%" class="text-center">Attività</th>
                            <th style="width:15%" class="text-center">Referente</th>
                            <th style="width:15%" class="text-center">Email</th>
                            <th style="width:15%" class="text-center">Telefono</th>
                            <th style="width:15%" class="text-center">Spesa Attuale</th>
                            <th style="width:5%" class="text-center">Note</th>
                          </tr>
                        </thead>
                        @if(count($segnalazione->checklist) > 0)
                          @foreach($segnalazione->checklist as $key => $checklist)
                            @if($checklist->procedura_id == $procedura->id)
                                <tr>
                                  @if(!empty($checklist->area_id) || (int)$checklist->area_id != 0)
                                    <td class="valign-middle">{{ $segnalazione->checklist_area($checklist->area_id) }}</td>
                                  @else 
                                    <td></td>
                                  @endif
                                  @if(!empty($checklist->attivita_id) || (int)$checklist->attivita_id != 0)
                                    <td class="valign-middle">{{ $segnalazione->checklist_attivita($checklist->attivita_id) }}</td>
                                  @else 
                                    <td></td>
                                  @endif
                                  <td class="valign-middle">{!! get_if_exist($checklist, 'nome') !!}</td>
                                  <td class="valign-middle"><a href="mailto:{!! get_if_exist($checklist, 'email') !!}">{!! get_if_exist($checklist, 'email') !!}</a></td>
                                  <td class="valign-middle">
                                    <a href="tel:{!! get_if_exist($checklist, 'telefono') !!}">{!! get_if_exist($checklist, 'telefono') !!}</a>
                                  </td>
                                  <td class="valign-middle">{!! get_if_exist($checklist, 'spesa') !!}</td>
                                  <td class="valign-middle">
                                    @if(get_if_exist($checklist, 'note'))
                                      <div class="text-center">
                                        <button type="button" class="btn btn-primary btn-flat btn-sm" data-toggle="modal" data-target="#modal-note-{{ $key }}">
                                          <i class="fa fa-file-text"> </i>
                                        </button>
                                      </div>
                                      <div class="modal fade" id="modal-note-{{ $key }}" tabindex="-1" role="dialog" aria-labelledby="Note" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                          <div class="modal-content">
                                            <div class="modal-header"> 
                                              <h5 class="modal-title" id="exampleModalLabel">Note {{ $segnalazione->checklist_area($checklist->area_id) }}</h5>
                                              <button type="button" class="close" data-dismiss="modal" aria-label="Chiudi">
                                                <span aria-hidden="true">&times;</span>
                                              </button>
                                            </div>
                                            <div class="modal-body">
                                              {{ get_if_exist($checklist, 'note') }}
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
                          @endforeach
                        @endif
                        <tfoot class="bg-info">
                          <tr>
                            <th class="text-right" colspan="5">TOTALE</th>
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
            </section>
          </div> 
        </div> 
      </div> 
      {{-- Allegati --}}
      <div class="col-md-12">
        @include('wecore::admin.partials.files_read', ['model' => $segnalazione])
      </div>
    </div> 
  </div> 
</div>
