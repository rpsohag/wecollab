<div class="box-body">
    <div class="row">
        <div class="col-md-5">
            {!! Form::weText('assistenza_per','Assistenza Per' , $errors , $ordinativo->assistenza_per) !!}
        </div>
        <div class="col-md-4">
            @if(!empty($ordinativo->hash_link))
                <div class="row">
                <div class="col-md-4">
                        <p><strong> Ordine Consip </strong></p>
                        <a class="hash_value" style="margin-top:5px;"></a>
                            <input type="checkbox" id="ConsipCheckBox" name="ConsipCheckBox" value='y' />
                           </div>
                    <div class="col-md-4">
                        <p><strong> Link Assistenza </strong></p>
                        <a class="hash_value" style="margin-top:5px;"></a>
                            &nbsp;&nbsp;<a class="btn bg-teal btn-sm" href="javascript:clipboard_copy('https://www.we-com.it/assistenza/ticket_adm/{{$ordinativo->hash_link}}/')"
                            data-toggle="tooltip" data-placement="right" title="" data-original-title="Copia">
                            <i class="fa fa-tag"></i></a>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <p><strong> Link Apertura Ticket </strong></p>
                        <a class="hash_value" style="margin-top:5px;"></a>
                            &nbsp;&nbsp;<a class="btn bg-teal btn-sm" href="javascript:clipboard_copy('https://www.we-com.it/assistenza/ticket/{{$ordinativo->hash_link}}/')"
                            data-toggle="tooltip" data-placement="right" title="" data-original-title="Copia">
                            <i class="fa fa-tag"></i></a>
                        </a>
                    </div>                                               
                </div>
            @else
                <button style="margin-top:24px;" class="btn btn-primary btn-flat" type="submit">Crea codice univoco</button>
            @endif
        </div>
        <div class="col-md-3">
            {!! Form::weText("api_password", 'Password', $errors, $ordinativo->api_password) !!}
        </div>
     <!-- Urgenza field start -->
                <div>
                        <div class="col-md-3" style="margin-bottom:10px;">
                            {{ Form::weText('critica', 'Critica', $errors) }}
                        </div>
                        <div class="col-md-3" style="margin-bottom:10px;">
                            {{ Form::weText('alta', 'Alta', $errors) }}
                        </div>
                        <div class="col-md-3" style="margin-bottom:10px;">
                            {{ Form::weText('media', 'Media', $errors) }}
                        </div>
                        <div class="col-md-3" style="margin-bottom:10px;">
                            {{ Form::weText('bassa', 'Bassa', $errors) }}
                        </div>
                </div>
     <!-- Urgenza field ends here-->
    </div> 
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header">
                    <h4><strong>Attività</strong></h4>
                </div>
                <div class="box-body">
                    <div id="table-attivita">
                        @if(!empty($ordinativo->assistenza))
                            @foreach($ordinativo->assistenza as $key => $attivita)
                                <div class="row bg-odd" data-id="{{ $key }}">
                                    <br>
                                    <div class="col-md-4">
                                        {!! Form::weSelectSearch("attivita['".$key."'][procedura_id]", 'Procedura *', $errors, $procedure_list, $attivita->procedura_id, ['onchange' => 'proceduraSelect(this)', 'data-row' => $key ]) !!}
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::weSelectSearch("attivita['".$key."'][area_id]", 'Area di intervento *', $errors, $aree_list, $attivita->area_id, ['onchange' => 'areaSelect(this)', 'data-row' => $key]) !!}
                                    </div>
                                    <div class="col-md-3">
                                        {!! Form::weSelectSearch("attivita['".$key."'][gruppo_id]", 'Attività *', $errors, $gruppi_list, $attivita->gruppo_id, ['onchange'=>'getAssegnatari('.$key.')']) !!}
                                    </div>
                                    <div class="col-md-1">
                                        <button style="margin-top:22px; margin-left:18px;" class="btn btn-md btn-flat btn-danger" type="button" onclick="removeAttivita({{$key}})"><i class="fa fa-trash"> </i></button>
                                        <button style="margin-top:22px; margin-left:18px;" class="btn btn-md btn-flat btn-warning" type="button" onclick="duplicateAttivita({{$key}})"><i class="fa fa-copy"> </i></button>
                                    </div>
                                    <div class="col-md-12">
                                        {{ Form::weTextarea("attivita['".$key."'][descrizione]", 'Descrizione', $errors, $attivita->descrizione ) }}
                                    </div>
                                    <div class="col-md-11">
                                        {{ Form::weTags("attivita['".$key."'][assegnatari_id]", 'Assegnata a *', $errors, $utenti_list, $attivita->destinatari_ids, ['multiple'=>'multiple']) }}
                                    </div>
                                    <div class="col-md-1">
                                        {{ Form::weInt("attivita['".$key."'][ordine]", 'N° Ordine', $errors, !empty($attivita->ordine) ? $attivita->ordine : 0) }}
                                    </div>
                                </div>
                            @endforeach
                        @else   
                            <div class="row bg-odd" data-id="1">
                                <br>
                                <div class="col-md-4">
                                    {!! Form::weSelectSearch("attivita['1'][procedura_id]", 'Procedura *', $errors, $procedure_list, '', ['onchange' => 'proceduraSelect(this)', 'data-row' => 1]) !!}
                                </div>
                                <div class="col-md-4">
                                    {!! Form::weSelectSearch("attivita['1'][area_id]", 'Area di intervento *', $errors, $aree_list, '', ['onchange' => 'areaSelect(this)', 'data-row' => 1]) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::weSelectSearch("attivita['1'][gruppo_id]", 'Attività *', $errors, $gruppi_list, '', ['onchange'=>'getAssegnatari(1)']) !!}
                                </div>
                                <div class="col-md-1">
                                    <button style="margin-top:22px; margin-left:18px;" class="btn btn-md btn-flat btn-danger" type="button" onclick="removeAttivita(1)"><i class="fa fa-trash"> </i></button>
                                    <button style="margin-top:22px; margin-left:18px;" class="btn btn-md btn-flat btn-warning" type="button" onclick="duplicateAttivita(1)"><i class="fa fa-copy"> </i></button>
                                </div>
                                <div class="col-md-12">
                                    {{ Form::weTextarea("attivita['1'][descrizione]", 'Descrizione', $errors) }}
                                </div>
                                <div class="col-md-11">
                                    {{ Form::weTags("attivita['1'][assegnatari_id]", 'Assegnata a *', $errors, $utenti_list, [], ['multiple'=>'multiple']) }}
                                </div>
                                <div class="col-md-1">
                                    {{ Form::weInt("attivita['1'][ordine]", 'N° Ordine', $errors) }}
                                </div>
                            </div>
                        @endif
                    </div> 
                    <br>
                    <div class="text-center">
                        <button id="add-attivita" type="button" class="btn btn-md btn-flat btn-primary" onclick="newAttivita(1)"><i class="fa fa-plus"> </i> Aggiungi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js-stack')
<script>
    // Select procedura
    function proceduraSelect(el) {
      var aree = $.parseJSON(atob("{{ get_json_aree() }}"));
      var row = $(el).attr('data-row');
      var procedura = $('.row[data-id="'+row+'"] select[name*="[procedura_id]"]');
      var area = $('.row[data-id="'+row+'"] select[name*="[area_id]"]');
      var gruppo = $('.row[data-id="'+row+'"] select[name*="[gruppo_id]"]');
      var procedura_selezionata = procedura.val();

      area.empty();
      gruppo.empty();

      var newOption = new Option(" ", 0, false, false);
        area.append(newOption);

      aree.forEach(element => {
          // scorro tutto l'array e controllo quelli che hanno il procedura id  =  alla procedura selezionata
          if(element.procedura_id == procedura_selezionata){
              var newOption = new Option(element.titolo, element.id, false, false);
              area.append(newOption);
          }
      });

      //assegnare nuove_aree_di_intervento alla select delle aree di intervento
      //area_select.trigger('change');
      area.select2('open');
      gruppo.select2('close');
  }

  // Select area
  function areaSelect(el) {
    var gruppi = $.parseJSON(atob("{{ get_json_gruppi() }}"));
    var row = $(el).attr('data-row');
    var area = $(el);
    var gruppo = $('.row[data-id="'+row+'"] select[name*="[gruppo_id]"]');
    var area_selezionata = $(el).val();

    gruppo.empty();

    var newOption = new Option(" ", 0, false, false);
    gruppo.append(newOption);

    gruppi.forEach(element => {
      //scorro tutto l'array e controllo quelli che hanno il procedura id  =  alla procedura selezionata
      if(element.area_id == area_selezionata){

          var newOption = new Option(element.nome, element.id, false, false);
          gruppo.append(newOption);
      }
    });
    //assegnare nuove_attivita alla select delle attivita

    gruppo.select2('open');
    gruppo.trigger('change');
  }

    // Create new activity
    function newAttivita(id) {
        var oldrow = $('#table-attivita .row[data-id="'+id+'"]');
        var row = oldrow.clone();
        var newId = parseInt($('#table-attivita .row:last').attr('data-id')) + 1;
        row.attr('data-id', newId);
        row.find('select[data-row]').attr('data-row', newId);
        row.find('input[data-row]').attr('data-row', newId);
        row.find('select[name*="[procedura_id]"]').attr('name', "attivita['"+newId+"'][procedura_id]");
        row.find('select[name*="[area_id]"]').attr('name', "attivita['"+newId+"'][area_id]");
        row.find('select[name*="[gruppo_id]"]').attr('name', "attivita['"+newId+"'][gruppo_id]");

        row.find('textarea[name*="[descrizione]"]').attr('name', "attivita['"+newId+"'][descrizione]");
        row.find('textarea[name*="[descrizione]"]').attr('id', "attivita['"+newId+"'][descrizione]");

        row.find('select[name*="[assegnatari_id]"]').attr('name', "attivita['"+newId+"'][assegnatari_id]");

        row.find('input').val('');
        row.find('textarea').val('');

        var assegnatari_id = oldrow.find('select[name*="[assegnatari_id]"]')[0].selectize.options;

        var assegnatari_tags = row.find('select[name*="[assegnatari_id]"]').selectize();
        var assegnatari_tags_selectize = assegnatari_tags[0].selectize;
        assegnatari_tags_selectize.clear();

        row.find('.btn-danger').attr('onclick', 'removeAttivita(' + newId + ')');
        row.find('.btn-warning').attr('onclick', 'duplicateAttivita(' + newId + ')');
        row.find('span.select2').remove();
        row.find('select[multiple!=multiple]').removeClass('select2-hidden-accessible');
        row.find('select[multiple!=multiple]').removeAttr('data-select2-id');

        row.find('select[name*="[gruppo_id]"]').attr('onchange', 'getAssegnatari('+newId+')');

        row.find('div.plugin-remove_button').remove();

        $('#table-attivita').append(row);

        row.find("select[multiple!=multiple]").select2();

        assegnator = row.find('select[name*="[assegnatari_id]"]').selectize();
        $.each(assegnatari_id, function() {
            assegnator[0].selectize.addOption(this);
        });
    }

    // Duplicate activity
    function duplicateAttivita(id) {
        var oldrow = $('#table-attivita .row[data-id="'+id+'"]');
        var row = oldrow.clone();
        var newId = parseInt($('#table-attivita .row:last').attr('data-id')) + 1;
        row.attr('data-id', newId);
        row.find('select[data-row]').attr('data-row', newId);
        row.find('input[data-row]').attr('data-row', newId);
        row.find('select[name*="[procedura_id]"]').attr('name', "attivita['"+newId+"'][procedura_id]");
        row.find('select[name*="[area_id]"]').attr('name', "attivita['"+newId+"'][area_id]");
        row.find('select[name*="[gruppo_id]"]').attr('name', "attivita['"+newId+"'][gruppo_id]");

        row.find('textarea[name*="[descrizione]"]').attr('name', "attivita['"+newId+"'][descrizione]");
        row.find('textarea[name*="[descrizione]"]').attr('id', "attivita['"+newId+"'][descrizione]");

        row.find('select[name*="[assegnatari_id]"]').attr('name', "attivita['"+newId+"'][assegnatari_id]");

        var procedura_id = oldrow.find('select[name*="[procedura_id]"]').val();
        var area_id = oldrow.find('select[name*="[area_id]"]').val();
        var gruppo_id = oldrow.find('select[name*="[gruppo_id]"]').val();

        var assegnatari_id = oldrow.find('select[name*="[assegnatari_id]"]')[0].selectize.options;

        row.find('.btn-danger').attr('onclick', 'removeAttivita(' + newId + ')');
        row.find('.btn-warning').attr('onclick', 'duplicateAttivita(' + newId + ')');
        row.find('span.select2').remove();
        row.find('select[multiple!=multiple]').removeClass('select2-hidden-accessible');
        row.find('select[multiple!=multiple]').removeAttr('data-select2-id');

        row.find('select[name*="[gruppo_id]"]').attr('onchange', 'getAssegnatari('+newId+')');

        row.find('div.plugin-remove_button').remove();
        row.find('div.selectize-input.items.not-full').remove();

        $('#table-attivita').append(row);

        row.find('select[name*="[procedura_id]"]').val(procedura_id);
        row.find('select[name*="[area_id]"]').val(area_id);
        row.find('select[name*="[gruppo_id]"]').val(gruppo_id);

        row.find("select[multiple!=multiple]").select2();

        assegnator = row.find('select[name*="[assegnatari_id]"]').selectize();
        $.each(assegnatari_id, function() {
            assegnator[0].selectize.addOption(this);
        });
    }

    // Remove activity
    function removeAttivita(id) {
        var ctrlRows = $('#table-attivita .row').length;
        var row = $('.row[data-id="'+id+'"]');

        if(ctrlRows > 1) {
            row.remove();
        } else {
            row.find('input').val('');
            row.find('input').attr('checked', false);
            row.find('textarea').val('');
            row.find('select option:selected').removeAttr("selected").trigger('change');
            var $select = row.find("select[multiple='multiple']").selectize();
            var assegnatari = $select[0].selectize;
            assegnatari.clear();
        }
    }

    // Get Assegnatari from group
    function getAssegnatari(id_row) {
      var gruppo_selezionato = $('div.row.bg-odd[data-id="'+id_row+'"] select[name*="[gruppo_id]"]').val();
      var select_assegnatari = $('div.row.bg-odd[data-id="'+id_row+'"] select[name*="[assegnatari_id]"]').selectize();
      var assegnatari = select_assegnatari[0].selectize;
      var token = $('input[name="_token"]').val();
      assegnatari.clear();
      $.get("{{ route('admin.tasklist.attivita.assegnatari') }}", { _token: token, gruppo:gruppo_selezionato })
        .done(function(data) {
          if(data != '') {
            data = JSON.parse(data);
            for (var key in data) {
              assegnatari.addOption({value:key,text:data[key]});
              assegnatari.addItem(key);
            }
          }
            assegnatari.refreshOptions();
        });
        assegnatari.close(); 
    }
</script>




@endpush