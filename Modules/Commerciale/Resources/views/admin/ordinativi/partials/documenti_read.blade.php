<br>
<div class="row">
    <div class="col-xs-12">
        <div class="">
            <div class="">
                <div class="box box-primary">
                    <div class="box-header">
    
                      <div class="box box-default bg-gray filters no-margin no-padding collapsed-box">
                        <div class="box-header with-border cursor-pointer" data-widget="collapse">
                          <h4 class="box-title no-icon">Filtri avanzati</h4>
                        </div>
                        <div class="box-body" style="display: none;">
                          <div class="row">
                              <div class="col-md-12">
                                  <div class="row">
                                    <div class="col-md-8">
                                         {!! Form::weText('nome', 'Nome', $errors, '', ['form' => 'documenti_filters']) !!}
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::weSelectSearch('tipologia', 'Tipologia', $errors, $documenti_tipologie, [], ['form' => 'documenti_filters']) !!}
                                    </div>
                                  </div>
                                  <div class="row">
                                      <div class="col-md-4">
                                          {!! Form::weSelectSearch('procedura', 'Procedura', $errors, $procedure_list, [], ['form' => 'documenti_filters']) !!}
                                      </div>
                                      <div class="col-md-4">
                                          {!! Form::weSelectSearch('area', 'Area di Intervento' , $errors, $aree_list, [], ['form' => 'documenti_filters']) !!}
                                      </div>
                                      <div class="col-md-4">
                                          {!! Form::weSelectSearch('gruppo', 'Attività' , $errors , $gruppi_list, [], ['form' => 'documenti_filters']) !!}
                                      </div>
                                  </div>
                              </div>
                          </div>
                        </div>
                        <div class="box-footer bg-gray">
                            <div class="col-md-2 pull-right">
                                {!! Form::weSubmit('Cerca', 'class="btn btn-primary btn-flat" form="documenti_filters"') !!}
                                {!! Form::weReset('Svuota', 'class="btn btn-default btn-flat btn-reset" form="documenti_filters"') !!}
                            </div>    
                        </div>
                      </div>   
                </div>
                <div class="box box-success box-shadow">
                    <div class="box-header with-border">
                        <h3 class="box-title">Documenti</h3>
                    </div>
                    <div class="box-body" id="allegati">
                        @if(!empty($ordinativo->files) && $ordinativo->files->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tr>
                                        <th style="width: 30%">Nome</th>
                                        <th class="text-center" style="width: 20%">Tipologia</th>
                                        <th class="text-center" style="width: 15%">Caricato da</th>
                                        <th class="text-center" style="width: 15%">Data di Caricamento</th>
                                        <th class="text-center" style="width: 10%">Scarica</th>
                                    </tr>
                                    <tbody>
                                        @foreach ($ordinativo->files as $file)
                                            <tr>
                                                <td><a href="{{ download_file($file->value->path, $file->value->client_name) }}" target="_blank"><strong>{{ $file->value->name }}</strong></a></td>
                                                <td class="text-center">{{ config('commerciale.ordinativi.documenti')[$file->value->tipologia_id] }}</td>
                                                <td class="text-center">{{ $file->createdUser->full_name }}</td>
                                                <td class="text-center">{{ $file->created_at }}</td>
                                                <td class="text-center"><a href="{{ download_file($file->value->path, $file->value->client_name) }}" target="_blank"><i class="fa fa-download fa-2x text-success" aria-hidden="true"></i></a></td>                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="callout callout-info">
                                <p>Non ci sono documenti da mostrare.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



