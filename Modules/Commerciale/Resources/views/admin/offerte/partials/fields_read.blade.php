<div class="row">
  <div class="col-md-12">
    <div class="col-md-12 bg-info">
      <div class="col-md-1 text-center">
        <i style="margin-top:24px;" class="icon fa fa-5x fa-file text-primary"></i>
      </div> 
      <div class="col-md-11">
        <h2 class="display-4">{{ get_if_exist($offerta, 'oggetto') }}</h2>
        <p class="lead">Informazioni riguardo l'offerta con numero <span style="color:rgb(59, 64, 131)" class="text-bold">{{$offerta->numero_offerta()}}</span>.</p>
      </div>
    </div>
    <div class="col-md-12">
      <br>
      <div class="alert alert-info alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-info"></i> Creazione</h4>
        L'offerta è stata creata in data <strong class="font-bold">{{ get_date_hour_ita(get_if_exist($offerta, 'created_at')) }}</strong> da <strong class="font-bold">{{ get_if_exist($offerta->created_user, 'full_name') }}</strong>.
      </div>
      @if(get_if_exist($offerta, 'note'))
        <div class="alert alert-info alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
          <h4><i class="icon fa fa-info"></i> Note</h4>
            {{ get_if_exist($offerta, 'note') }}
        </div>
      @endif
    </div>
    @if(!empty($offerta->approvazioni))
      <div class="col-md-12">
        <div class="box box-success">
          <div class="box-header">
            <h4>Approvazioni</h4>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-md-12">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <tr>
                      <th style="width:70%">Ruolo</th>
                      <th class="text-center" style="width:30%">Approvazione</th>
                    </tr>
                    <tbody>
                      @php $ruoli = ['amministrazione' => 'Amministratore', 'direttore_pa' => 'Direttore PA', 'direttore_tecnico' => 'Direttore Tecnico', 'direttore_commerciale' => 'Direttore Commerciale']; @endphp 
                      @foreach($offerta->approvazioni as $ruolo => $boolean)
                        <tr>
                          <td>{{ $ruoli[$ruolo] }}</td>
                          <td class="text-center">{!! ($boolean) ? '<i class="fa fa-2x fa-check text-success" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="L\'approvazione è stata concessa."></i>' : '<i class="fa fa-2x fa-spinner text-warning" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="L\'approvazione non è stata ancora concessa."></i>' !!}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif
    <div class="col-md-12">
      <div class="box box-success">
          <div class="progress active">
              <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                  <span class="text-bold text-uppercase">STATO: {{ $stati[get_if_exist($offerta, 'stato')] }}</span>
              </div>
          </div>
          <div class="box-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="box box-info">
                    <div class="box-header">
                      <h4>Informazioni {{ $offerta->cliente->ragione_sociale }} <small>( Cliente {{ $offerta->cliente->tipologia }} )</small></h4>
                    </div>
                    <div class="box-body">
                      <div class="table-responsive">
                        <table class="table table-striped">
                          <tr>
                            <th style="width:20%">Sede Legale</th>
                            <th style="width:10%">P.IVA</th>
                            <th style="width:20%">Cod. Fiscale</th>
                            <th style="width:20%">Email</th>
                            <th style="width:20%">PEC</th>
                            <th style="width:10%">Telefono</th>
                          </tr>
                          <tbody>
                            <tr>
                              <td>
                                @if(!empty($offerta->cliente->sedeLegale()))
                                  {{ $offerta->cliente->sedeLegale()->indirizzo_completo }}
                                @endif
                              </td>
                              <td>
                                @if(get_if_exist($offerta->cliente, 'p_iva'))
                                  {{ $offerta->cliente->p_iva }}
                                @endif
                              </td>
                              <td>
                                @if(get_if_exist($offerta->cliente, 'cod_fiscale'))
                                  {{ $offerta->cliente->cod_fiscale }}
                                @endif                            
                              </td>
                              <td>
                                @if(get_if_exist($offerta->cliente->sedeLegale(), 'email'))
                                  <a href="mailto:{{ $offerta->cliente->sedeLegale()->email }}">{{ $offerta->cliente->sedeLegale()->telefono }}</a>
                                @endif                           
                              </td>
                              <td>
                                @if(get_if_exist($offerta->cliente, 'pec'))
                                  <a href="mailto:{{ $offerta->cliente->pec }}">{{ $offerta->cliente->pec }}</a>
                                @endif
                              </td>
                              <td>
                                @if(get_if_exist($offerta->cliente->sedeLegale(), 'telefono'))
                                  <a href="tel:{{ $offerta->cliente->sedeLegale()->telefono }}">{{ $offerta->cliente->sedeLegale()->telefono }}</a>
                                @endif
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
                @if(!empty($offerta->voci) && count($offerta->voci) > 0)
                  <div class="col-md-12">
                    <div class="box box-primary">
                      <div class="box-header">
                        <h4>Voci</h4>
                      </div>
                      <div class="box-body table-responsive">
                        <table class="table table-striped voci">
                          <thead>
                            <tr>
                              <th>Descrizione</th>
                              <th style="width:10%;" class="text-center">Quantità</th>
                              <th class="text-center" style="width:15%;">Importo Singolo</th>
                              <th class="text-center" style="width:10%;">IVA</th>
                              <th class="text-center" style="width:15%;">Importo</th>
                              <th class="text-center" style="width:15%;">Importo con IVA</th>
                              <th class="text-center" style="width:8%;">Esente IVA</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach ($offerta->voci as $key => $voce)
                                <tr>
                                    <td>{{  get_if_exist($voce, 'descrizione')  }}</td>
                                    <td class="text-center">{{  get_if_exist($voce, 'quantita')   }}</td>
                                    <td class="text-center">{{  get_if_exist($voce, 'importo_singolo')  }}</td>
                                    <td class="text-center">{{  get_if_exist($voce, 'iva')  }}</td>
                                    <td class="text-center">{{  get_if_exist($voce, 'importo')  }}</td>
                                    <td class="text-center">{{  get_if_exist($voce, 'importo_iva')  }}</td>
                                    <td class="text-center">{!!   ((get_if_exist($voce, 'esente_iva')==1 ) ? '<i class="fa fa-check fa-2x text-success" aria-hidden="true"></i>' : '<i class="fa fa-ban fa-2x text-danger" aria-hidden="true"></i>')  !!}</td>
                                </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                      <div class="box-footer">
                        <div class="col-md-7"><h4 class="text-bold pull-right" style="padding-top:22px;">TOTALI:</h4>&nbsp;</div>
                        <div class="col-md-1 hidden">
                            {{ Form::weInt('iva', 'IVA', $errors, (get_if_exist($offerta, 'iva') ) ? get_if_exist($offerta, 'iva') : $iva, ['readonly' => 'readonly']) }}
                        </div>
                        <div class="col-md-2">
                            {{ Form::weCurrency('importo_esente', 'Importo', $errors, get_currency(get_if_exist($offerta, 'importo_esente')), ['readonly' => 'readonly']) }}
                        </div>
                        <div class="col-md-2">
                            {{ Form::weCurrency('importo_iva', 'Importo + IVA', $errors, get_currency(get_if_exist($offerta, 'importo_iva')), ['readonly' => 'readonly']) }}
                        </div>
                      </div>
                    </div>
                  </div>
                @endif
              </div>
            </div> 
          </div> 
        </div>
        <div class="col-md-12">
          <div class="box box-success box-shadow">
            <div class="box-header with-border">
              <h4 class="box-title">Allegati</h4>
              <div class="box-tools pull-right">
                <span data-toggle="tooltip" title="" class="badge bg-green" data-original-title="{{ count($offerta->files) }} Files">{{ count($offerta->files) }}</span>
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="box-footer">
              @foreach($offerta->files as $key => $file)
                <div class="row">
                  <div class="col-md-12">
                      <div class="box box-solid box-shadow">
                          <div class="box-body {{ ($file->id == $offerta->offerta_definitiva_id) ? 'bg-success' : (($offerta->oda_determina_ids->contains($file->id)) ? 'bg-info' : '') }}">
                              <div class="row">
                              <div class="col-md-1">
                                  <span class="fa-stack fa-2x">
                                      <i class="fa fa-square-o fa-stack-2x"></i>
                                      <i class="fa {{ file_icons($file->value->extension) }} fa-stack-1x"></i>
                                  </span>
                              </div>
                              <div class="col-md-8">
                                  <div class="row">
                                      <div class="col-md-8">
                                          <a href="{{ download_file($file->value->path, $file->value->client_name) }}" target="_blank"><strong>{{ $file->value->name }}</strong></a>
                                          <button type="button" class="btn btn-xs btn-flat btn-default" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#modal-default" data-size="modal-lg" data-title="Anteprima {{ $file->value->name }}" data-action="{{ ($file->value->extension == 'pdf' ? url($file->value->path) : 'https://docs.google.com/gview?url=' . url($file->value->path) . '&embedded=true') }}" data-type="iframe">
                                              <i class="fa fa-eye"></i>
                                          </button>
                                      </div>
                                      <div class="col-md-4">
                                          <i class="fa fa-calendar"> </i> {{ $file->updated_at }}
                                      </div>
                                      <div class="col-md-8">
                                          <i class="fa fa-save"> </i> {{ mb($file->value->size) }} MB
                                      </div>
                                      <div class="col-md-4">
                                          <i class="fa fa-user"> </i> {{ $file->updatedUser->first_name }} {{ $file->updatedUser->last_name }}
                                      </div>
                                  </div>
                              </div>
                              <div class="col-md-3 text-right">
                                  @if($file->id == $offerta->ordine_mepa_id)
                                      <span class="label bg-yellow">ORDINE MEPA</span>
                                  @endif
      
                                  @if($file->id == $offerta->offerta_definitiva_id)
                                      <span class="label bg-green">OFFERTA DEFINITIVA</span>
                                  @endif
      
                                  @if($offerta->oda_determina_ids->contains($file->id))
                                      <span class="label bg-blue">{{ $od }}</span>
                                  @endif
                              </div>
                              </div>
                          </div>
                      </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div> 
        </div> 
      </div> 
    </div>
  </div>
</div>