<br>
<div class="row">
    <div class="col-xs-12">
        <div class="row">
            <div class="col-md-10">
                <div class="btn-group pull-left" style="margin: 0 15px 15px 0;">
                    <button class="btn btn-primary btn-flat" type="button" data-toggle="modal" data-target="#uploadFile" style="padding: 4px 10px;">
                        <i class="fa fa-upload"></i> Carica Documento
                    </button>
                </div>
            </div>
        </div>
        <div class="">
            <div class="">
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
                                        <th class="text-center" style="width: 10%">Elimina</th>
                                    </tr>
                                    <tbody>
                                        @foreach ($ordinativo->files as $file)
                                            <tr>
                                                <td><a href="{{ download_file($file->value->path, $file->value->client_name) }}" target="_blank"><strong>{{ $file->value->name }}</strong></a></td>
                                                <td class="text-center"><span class="label label-success">{{ config('commerciale.ordinativi.documenti')[$file->value->tipologia_id] }}</span></td>
                                                <td class="text-center">{{ $file->createdUser->full_name }}</td>
                                                <td class="text-center">{{ $file->created_at }}</td>
                                                <td class="text-center"><a href="{{ download_file($file->value->path, $file->value->client_name) }}" target="_blank"><i class="fa fa-download fa-2x text-success" aria-hidden="true"></i></a></td>
                                                @if(Auth::user()->full_name == $file->createdUser->full_name)
                                                    <td class="text-center"><button type="button" class="btn btn-danger btn-flat" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.wecore.allegato.destroy', $file->id) }}"><i class="fa fa-trash"></i></button></td>
                                                @else 
                                                    <td></td>
                                                @endif                 
                                            </tr>
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
<div class="modal fade" id="uploadFile" tabindex="2" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Nuovo Documento</h3>
            </div>
            <div class="modal-content">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                          {{ Form::weText('documento_nome', 'Nome *', $errors) }}
                        </div>
                        <div class="col-md-6">
                            {{ Form::weSelectSearch('documento_tipologia_id', 'Tipologia *',  $errors, $documenti_tipologie) }}
                        </div>
                        <div class="col-md-12">
                          {{ Form::weFile('documento_file', 'Documento *', $errors) }}
                        </div>
                        <div class="col-md-4">
                            {{ Form::weSelectSearch('documento_procedura_id', 'Procedura *', $errors, $procedure_list, '', ['id'=>'procedura_select']) }}
                        </div>
                        <div class="col-md-4">
                            {{ Form::weSelectSearch('documento_area_id', 'Area Intervento *', $errors, $aree_list, '', ['id'=>'area_select']) }}
                        </div>
                        <div class="col-md-4">
                            {{ Form::weSelectSearch('documento_gruppo_id', 'Attività *', $errors, $gruppi_list, '', ['id'=>'gruppo_select']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Carica</button>
            </div>
        </div>
    </div>
</div>

@push('js-stack')
<script type="text/javascript">
    var procedura_select = $('#procedura_select');
    var area_select = $('#area_select');
    var gruppo_select = $('#gruppo_select');
    var aree = $.parseJSON(atob("{{ get_json_aree() }}"));
    var gruppi = $.parseJSON(atob("{{ get_json_gruppi() }}"));
    // Selezione procedura
    procedura_select.change(function(e) {
      area_select.empty();
      var procedura_selezionata = procedura_select.val();
      aree.forEach(element => {
          //scorro tutto l'array e controllo quelli che hanno il procedura id  =  alla procedura selezionata
          if(element.procedura_id == procedura_selezionata){
              var newOption = new Option(element.titolo, element.id, false, false);
              area_select.append(newOption);
          }
      });

      //assegnare nuove_aree_di_intervento alla select delle aree di intervento
      area_select.trigger('change');
      area_select.select2('open');
      gruppo_select.select2('close');
    });

    // Selezione area
    area_select.change(function(e) {
      gruppo_select.empty();
      var area_selezionata = area_select.val();
      gruppi.forEach(element => {
          //scorro tutto l'array e controllo quelli che hanno il procedura id  =  alla procedura selezionata
          if(element.area_id == area_selezionata){
              var newOption = new Option(element.nome, element.id, false, false);
              gruppo_select.append(newOption);
          }
      });
      //assegnare nuove_aree_di_intervento alla select delle attivita
      gruppo_select.trigger('change');
      gruppo_select.select2('open');
      area_select.select2('close');
    });
</script>
@endpush



