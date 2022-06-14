<div class="row">
  <div class="col-md-12">
    <div class="col-md-12 {{ $attivita->percentuale_completamento == 100 ? 'bg-success' : 'bg-info' }}">
      <div class="col-md-1 text-center">
        <i style="margin-top:24px;" class="icon fa fa-5x fa-file text-primary"></i>
      </div> 
      <div class="col-md-11">
        <h2 class="display-4">{{ get_if_exist($attivita, 'oggetto') }} <small>( Cliente: {{ $attivita->cliente->ragione_sociale }} )</small> </h2>
        <p class="lead">Informazioni riguardo l'attività con data inizio <span class="text-primary text-bold">{{ $attivita->data_inizio }}</span>{!! get_if_exist($attivita, 'data_fine') ? ' e data fine <span class="text-primary text-bold">'.$attivita->data_fine.'</span>' : '' !!}.</p>
      </div>
    </div>
      <div class="col-md-12">
        <br>
        <div class="alert alert-info alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
          <h4><i class="icon fa fa-info"></i> Informazioni</h4>
          L'attività è stata creata in data <strong class="font-bold">{{ get_date_hour_ita(get_if_exist($attivita, 'created_at')) }}</strong> da <strong class="font-bold">{{ get_if_exist($attivita->created_user, 'full_name') }}</strong>.
          <br> Area di Intervento: <strong>{{ optional($attivita->area)->titolo }}</strong>
          <br> Ambito: <strong>{{ optional($attivita->gruppo)->nome }}</strong>
          @if(get_if_exist($attivita,'fatturazione'))
            <br><strong class="font-bold text-primary text-uppercase">La fatturazione è abilitata per questa attività.</strong>
          @endif
        </div>
        @if(!empty($attivita->data_chiusura))
          <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-info"></i> Attività Completata</h4>
            L'attività è stata completata in data <strong class="font-bold">{{ get_if_exist($attivita, 'data_chiusura') }}</strong>.
          </div>
        @endif
        @if(!$attivita->hasPresoVisione() && $attivita->hasRequisiti() && $attivita->percentuale_completamento != 100)
          <div class="row">
              <div class="col-md-12">
                  <div class="callout callout-warning">
                  <h4>Presa In Carico</h4>
                  <div class="row">
                      <div class="col-md-8">
                      <p>Non hai preso in carico l'attività che ti è stata assegnata.</p>
                      </div>
                      <div class="col-md-4">
                      <a href="{{ route('admin.tasklist.attivita.presavisione', $attivita) }}" class="btn btn-primary btn-flat pull-right" data-toggle="tooltip" data-placement="top" title="Conferma la presa in carico dell'attività"><i class="fa fa-check"></i> Ho preso in carico.</a>
                      </div>
                  </div>
                  </div>
              </div>
          </div>
        @endif
        <div class="box box-success">
          <div class="progress {{ $attivita->percentuale_completamento == 100 ? '' : 'active' }}">
              <div class="progress-bar {{ $attivita->percentuale_completamento == 100 ? 'progress-bar-success' : 'progress-bar-warning' }} progress-bar-striped" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                  <span class="text-bold text-uppercase">STATO: {{ $stati[get_if_exist($attivita, 'stato')] }} - {{ $attivita->percentuale_completamento() }}</span>
              </div>
          </div>
          <div class="box-body">
            @if(!empty($attivita->descrizione))
              <div class="row">
                <div class="col-md-12">
                  <div class="box box-info box-solid">
                    <div class="box-header with-border">
                      <h3 class="box-title">Descrizione</h3>
                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                      </div>
                    </div> 
                    <div class="box-body">
                        {{ get_if_exist($attivita, 'descrizione')}}
                    </div>
                  </div>
                </div>
              </div>
            @endif
            <div class="row">
              @if(optional($attivita->ordinativo)->oggetto)
                <div class="col-md-12">
                  <div class="info-box col-md-12">
                    <span class="info-box-icon bg-blue"><i class="fa fa-file-text"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Ordinativo</span>    
                      <span class="info-box-number">{{ get_if_exist($attivita->ordinativo, 'oggetto') }}</span>
                      <a data-toggle="tooltip" title=""  data-original-title="Visualizza ordinativo" 
                          class="btn btn-sm btn-default" href = {{ route('admin.commerciale.ordinativo.read', $attivita->ordinativo) }} >
                          Apri Ordinativo &nbsp;
                          <i class="fa fa-external-link-square" style="font-size:15px"> </i>
                      </a>
                    </div>
                  </div>
                </div>
                <br>
              @endif
              <div class="col-md-12">
                <div class="box box-info">
                  <div class="box-header">
                    <h4>Partecipanti</h4>
                  </div>
                  <div class="box-body">
                    <div class="table-responsive">
                      <table class="table table-striped">
                        <tr>
                          <th style="width:30%">Nome</th>
                          <th class="text-center" style="width:30%">Ruolo</th>
                          <th class="text-center" style="width:30%">Email</th>
                          @if(!empty($attivita->opzioni) && !empty($attivita->opzioni['prese_visioni']) && $attivita->opzioni['prese_visioni'] == 1)
                            <th class="text-center" style="width:10%">Presa In Carico</th>
                          @endif
                        </tr>
                        <tbody>
                          @foreach($partecipanti as $key => $partecipante)
                            <tr>
                              <td>{{ $partecipante['nome'] }}</td>   
                              <td class="text-center">{{ $partecipante['ruolo'] }}</td>   
                              <td class="text-center"><a href="mailto:{{ $partecipante['email'] }}">{{ $partecipante['email'] }}</a></td>       
                              @if(!empty($attivita->opzioni) && !empty($attivita->opzioni['prese_visioni']) && $attivita->opzioni['prese_visioni'] == 1)
                                <td class="text-center">
                                  @if(str_contains($partecipante['ruolo'], 'Assegnatario'))
                                    @if(!empty($attivita->opzioni) && !empty($attivita->opzioni['multi_presa_in_carico']) && $attivita->opzioni['multi_presa_in_carico'] == 1)
                                      @if($attivita->hasPresoVisione($key))
                                        <i class="fa fa-2x fa-check text-success" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Ha preso in carico l'attività."></i>
                                      @else
                                        <i class="fa fa-2x fa-spinner text-warning" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Non ha ancora preso in carico l'attività."></i>
                                      @endif
                                    @else
                                      @if($attivita->hasPresoVisione($key))
                                        <i class="fa fa-2x fa fa-check text-success" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Ha preso in carico l'attività."></i>
                                      @else
                                        @if($attivita->preseVisioni())
                                          <i class="fa fa-2x fa-lock text-info" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Presa in carico multipla non abilitata."></i>
                                        @else
                                          <i class="fa fa-2x fa-spinner text-warning" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Non ha ancora preso in carico l'attività."></i>
                                        @endif
                                      @endif
                                    @endif
                                  @else 
                                    <i class="fa fa-2x fa-user text-info" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Per il ruolo non è richiesta la presa in carico."></i>
                                  @endif
                                </td>       
                              @endif
                            </tr>                
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              @if($attivita->partecipanti()->contains('id', Auth::id()))
                <div class="col-md-12">
                  <div class="box box-info">
                    <div class="box-header">
                      <h4>Lavorazione</h4>
                    </div>
                    <div class="box-body">
                      <div class="table-responsive">
                        <table class="table table-striped">
                          <tr>
                            <th style="width:30%">Nome</th>
                            <th class="text-center" style="width:30%">Timesheets Collegati</th>
                            <th class="text-center" style="width:30%">Ore Lavorate</th>
                            <th style="width:10%"></th>
                          </tr>
                          <tbody>
                            @foreach($lavoratori as $lavoratore)
                              <tr>
                                <td>{{ $lavoratore['nome'] }}</td>   
                                <td class="text-center">{{ $lavoratore['timesheets'] }}</td>     
                                <td class="text-center">{{ $lavoratore['tempo_lavorato'] }}</td> 
                                <td></td>    
                              </tr>                
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              @endif
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="box {{ $attivita->hasRequisiti() ? 'box-success' : 'box-warning' }}">
                  <div class="box-header">
                    <h4>Attività Propedeutiche</h4>
                  </div>
                  <div class="box-body">
                    <div class="row">
                      @if(!empty($attivita->requisiti()) && count($attivita->requisiti()) > 0)
                        @if($attivita->hasRequisiti() && $attivita->percentuale_completamento != 100)
                          <div class="col-md-12">
                            <div class="alert alert-success alert-dismissible">
                              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                              <h4><i class="icon fa fa-check"></i> Ottimo!</h4>
                              L'attività ha tutti i requisiti per poter essere lavorata.
                            </div>
                          </div>
                        @endif
                        @foreach($attivita->requisiti() as $requisito)
                          <div class="col-md-6">
                            <div class="info-box">
                              <span class="info-box-icon {{ $requisito->percentuale_completamento == 100 ? 'bg-green' : 'bg-gray' }}"><i class="fa fa-external-link"></i></span>
                              <div class="info-box-content">
                                <span class="info-box-text">COMPLETAMENTO ATTIVITA' ( <span class="text-bold {{ $requisito->percentuale_completamento == 100 ? 'text-success' : 'text-red' }}"> Progresso: {{ $requisito->percentuale_completamento() }}</span> )</span>
                                <span class="info-box-number"><a target="_blank" href="{{ route('admin.tasklist.attivita.edit', ['attivita' => $requisito->id]) }}">{{ $requisito->oggetto }}</a></span>
                              </div>
                            </div>
                          </div>
                        @endforeach
                      @else 
                        <div class="col-md-12">
                          <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-check"></i> Ottimo!</h4>
                            L'attività per essere lavorata non necessita di alcun requisito.
                          </div>
                        </div>
                      @endif
                    </div> 
                  </div> 
                </div>
              </div>
            </div>
            {{-- Note --}}
            @include('wecore::admin.partials.note', ['model' => $attivita])
            {{-- Allegati --}}
            @if(Auth::user()->inRole('admin') || $attivita->partecipanti()->contains('id', Auth::id()))
              @include('wecore::admin.partials.files_read', ['model' => $attivita])
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>