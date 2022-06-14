<div class="row">
  <div class="col-md-12">
    @if(empty($segnalazione->id))
      <div class="row">
        <div class="col-md-12 bg-green">
          <div class="row">
            <div class="col-md-1 text-center"><i style="margin-top:24px;" class="icon fa fa-5x fa-plus text-success"></i></div> 
            <div class="col-md-11">
              <h2 class="display-4">Segnalazione Opportunità</h2>
              <p class="lead">Crea una nuova segnalazione opportunità per l'azienda.</p>
            </div>
          </div>
        </div>
        <div class="col-md-12">
          <br>
          <div class="box box-success">
            <div class="box-header">
              <h4><strong>GENERALI</strong></h4>
            </div>
            <div class="box-body form-row">
              <div class="form-group col-md-4">
                {{ Form::weText('oggetto', 'Oggetto *', $errors, get_if_exist($segnalazione, 'oggetto')) }}
                <small>( <span class="text-danger text-bold">Obbligatorio</span>. )</small>
              </div>
              <div class="form-group col-md-3">
                  {{ Form::weSelectSearch('cliente_id', 'Cliente', $errors, $clienti, get_if_exist($segnalazione, 'cliente_id')) }}
                  <small>( <span class="text-danger text-bold">Obbligatorio</span> se l'anagrafica del cliente è presente sulla piattaforma. )</small>
              </div>
              <div class="form-group col-md-3">
                {{ Form::weText('cliente', 'Cliente Proposto', $errors, get_if_exist($segnalazione, 'cliente')) }}
                <small>( <span class="text-danger text-bold">Obbligatorio</span> se non è stato possibile selezionare un cliente. )</small>
              </div>
              <div class="form-group col-md-2">
                {{ Form::weSelectSearch('crea_per', 'Crea per:', $errors, $utenti) }}
                <small>( Crea la segnalazione a nome di un altro utente. )</small>
              </div>
              <div class="form-group col-md-12">
                {{ Form::weTextarea('descrizione', 'Descrizione', $errors) }}
              </div>
            </div>
            <div class="box-footer form-row">
                <div class="col-md-12">
                  @include('wecore::admin.partials.filesdrop', ['model' => $segnalazione, 'type' => 'commerciale', 'model_name' => 'SegnalazioneOpportunita', 'model_path' => 'Commerciale'])
                </div>
            </div>
          </div>  
        </div>

        <div class="col-md-12">
          <br>
          <div class="box box-primary">
              <div class="box-header">
                <h4><strong>CHECKLISTS</strong></h4> <small>( Nessun campo è obbligatorio. E' possibile creare una checklist anche da un solo valore. )</small>
              </div>
              <div class="box-body form-row">
                <div class="nuova-checklist-row">
                  @php $new_record_key = 1; @endphp
                  <div class="row" data-id="{{ $new_record_key }}" style="margin-left:6px;">
                    <div class="col-md-12">
                      <caption><span class="badge bg-green" style="margin-bottom:12px; margin-top:6px;"><i class="fa fa-arrow-right" aria-hidden="true"></i><strong> Nuova checklist</strong></span></caption>
                    </div>
                    <div class="col-lg-3">
                      {{ Form::weSelectSearch('checklist['.$new_record_key.'][procedura_id]', 'Procedura', $errors, [''] + $procedure->pluck('titolo', 'id')->toArray(), '',  ['onchange' => 'proceduraSelect(this)', 'id' => 'referente-procedura', 'data-row' => $new_record_key]) }}
                    </div>
                    <div class="col-lg-3">
                      {{ Form::weSelectSearch('checklist['.$new_record_key.'][area_id]', 'Area Intervento', $errors, [0 => ''] + $aree, '',  ['onchange' => 'areaSelect(this)', 'id' => 'referente-area', 'data-row' => $new_record_key]) }}
                    </div>
                    <div class="col-lg-3">
                      {{ Form::weSelectSearch('checklist['.$new_record_key.'][attivita_id]', 'Attività', $errors, [0 => ''] + $attivita, '',  ['id' => 'referente-attivita']) }}
                    </div>
                    <div class="col-lg-3">
                      {{ Form::weCurrency('checklist['.$new_record_key.'][spesa]', 'Attuale Spesa', $errors, '', ['id' => 'referente-spesa']) }}
                    </div>
                    <div class="col-lg-3">
                      {{ Form::weText('checklist['.$new_record_key.'][nome]', 'Referente', $errors, '', ['id' => 'referente-nome']) }}
                    </div>
                    <div class="col-lg-3">
                      {{ Form::weText('checklist['.$new_record_key.'][email]', 'Email', $errors, '', ['id' => 'referente-email']) }}
                    </div>
                    <div class="col-lg-3">
                      {{ Form::weText('checklist['.$new_record_key.'][telefono]', 'Telefono', $errors, '', ['id' => 'referente-telefono']) }}
                    </div>
                    <div class="col-lg-3">
                      {{ Form::weText('checklist['.$new_record_key.'][note]', 'Note', $errors, '', ['id' => 'referente-note']) }}
                    </div>
                  </div>
                </div>
                <div class="text-center">
                  <button id="add-checklist" type="button" class="btn btn-md btn-flat btn-primary"><i class="fa fa-plus"> </i> Aggiungi</button>
                </div>
              </div>
          </div>  
        </div>

      </div>
    @else
      <div class="row">
        <div class="col-md-12 bg-orange">
          <div class="row">
            <div class="col-md-1 text-center"><i style="margin-top:24px;" class="icon fa fa-5x fa-pencil-square-o text-warning"></i></div> 
            <div class="col-md-11">
              <h2 class="display-4">{{ get_if_exist($segnalazione, 'oggetto') }}</h2>
              <p class="lead">Modifica la segnalazione opportunità in oggetto.</p>
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

        <div class="col-md-12">
          <br>
          <div class="box box-warning">
            <div class="box-header">
              <h4><strong>GENERALI</strong></h4>
            </div>

            @if(empty($segnalazione->cliente_id))
              <div class="col-md-12">
                <div class="alert alert-warning alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <h4><i class="icon fa fa-warning"></i> Anagrafica non presente!</h4>
                  La segnalazione non ha un cliente selezionato.
                  @if(!empty($segnalazione->cliente))
                    <br> Cliente proposto: <strong>{{ get_if_exist($segnalazione, 'cliente') }}</strong>
                    <br><br>
                    <a target="_blank" class="btn btn-primary btn-flat btn-sm" href="{{ route('admin.amministrazione.clienti.create' , ['ragione_sociale' => $segnalazione->cliente, 'segnalazione_opportunita' => $segnalazione->id]) }}" >Crea Anagrafica</a>
                  @endif
                </div>
              </div>
            @endif

            @if(!empty($segnalazione->cliente_id) && !optional($segnalazione->cliente())->censimento)
              <div class="col-md-12">
                <div class="alert alert-warning alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <h4><i class="icon fa fa-warning"></i> Censimento non presente!</h4>
                  La segnalazione non ha un censimento. 
                  <br><br>
                  <a target="_blank" class="btn btn-primary btn-flat btn-sm" href="{{ route('admin.commerciale.censimentocliente.create' , ['segnalazione_opportunita' => $segnalazione->id ,'cliente_id' => $segnalazione->cliente_id]) }}" >Crea Censimento</a>
                </div>
              </div>
            @endif

            <div class="box-body form-row">
              <div class="form-group col-md-9">
                {{ Form::weText('oggetto', 'Oggetto *', $errors, get_if_exist($segnalazione, 'oggetto')) }}
              </div>
              <div class="form-group col-md-3">
                  {{ Form::weSelectSearch('cliente_id', 'Cliente *', $errors, [''] + $clienti, get_if_exist($segnalazione, 'cliente_id')) }}
              </div>
              <div class="form-group col-md-12">
                {{ Form::weTextarea('descrizione', 'Descrizione', $errors, get_if_exist($segnalazione, 'descrizione')) }}
              </div>
            </div>
            <div class="box-footer form-row">
                <div class="col-md-12">
                  @include('wecore::admin.partials.filesdrop', ['model' => $segnalazione, 'type' => 'commerciale', 'model_name' => 'SegnalazioneOpportunita', 'model_path' => 'Commerciale'])
                </div>
            </div>
          </div>
        </div>

        <div class="col-md-12">
          <br> 
          <div class="box box-primary">
            <div class="box-header">
              <h4><strong>CHECKLISTS</strong></h4>
            </div>
            <div class="box-body">
              @if(!empty($spesa_totale['totale']))
                <div class="col-xs-12">
                  <div class="info-box" style="margin-left:15px;">
                    <span class="info-box-icon bg-green"><i class="fa fa-eur"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Spesa Totale</span>
                      <span class="info-box-number">€ {{ str_replace(',', '.', number_format($spesa_totale['totale'])) }},00</span>
                    </div>
                  </div>
                </div>
              @endif
              @if(!empty($segnalazione->checklist) && count($segnalazione->checklist) > 0)
                @php $new_record_key = count($segnalazione->checklist) + 1; @endphp
              @else 
                @php $new_record_key = 1; @endphp
              @endif
              <div class="nuova-checklist-row">
                <div class="row" data-id="{{ $new_record_key }}" style="margin-left:6px;">
                  <div class="col-md-12">
                    <caption><span class="badge bg-green" style="margin-bottom:12px; margin-top:6px;"><i class="fa fa-arrow-right" aria-hidden="true"></i><strong> Nuova checklist</strong></span></caption>
                  </div>
                  <div class="col-lg-3">
                    {{ Form::weSelectSearch('checklist['.$new_record_key.'][procedura_id]', 'Procedura', $errors, [''] + $procedure->pluck('titolo', 'id')->toArray(), '',  ['onchange' => 'proceduraSelect(this)', 'id' => 'referente-procedura', 'data-row' => $new_record_key]) }}
                  </div>
                  <div class="col-lg-3">
                    {{ Form::weSelectSearch('checklist['.$new_record_key.'][area_id]', 'Area Intervento', $errors, [0 => ''] + $aree, '',  ['onchange' => 'areaSelect(this)', 'id' => 'referente-area', 'data-row' => $new_record_key]) }}
                  </div>
                  <div class="col-lg-3">
                    {{ Form::weSelectSearch('checklist['.$new_record_key.'][attivita_id]', 'Attività', $errors, [0 => ''] + $attivita, '',  ['id' => 'referente-attivita']) }}
                  </div>
                  <div class="col-lg-3">
                    {{ Form::weCurrency('checklist['.$new_record_key.'][spesa]', 'Attuale Spesa', $errors, '', ['id' => 'referente-spesa']) }}
                  </div>
                  <div class="col-lg-3">
                    {{ Form::weText('checklist['.$new_record_key.'][nome]', 'Referente', $errors, '', ['id' => 'referente-nome']) }}
                  </div>
                  <div class="col-lg-3">
                    {{ Form::weText('checklist['.$new_record_key.'][email]', 'Email', $errors, '', ['id' => 'referente-email']) }}
                  </div>
                  <div class="col-lg-3">
                    {{ Form::weText('checklist['.$new_record_key.'][telefono]', 'Telefono', $errors, '', ['id' => 'referente-telefono']) }}
                  </div>
                  <div class="col-lg-3">
                    {{ Form::weText('checklist['.$new_record_key.'][note]', 'Note', $errors, '', ['id' => 'referente-note']) }}
                  </div>
                </div>
              </div>
              <div class="text-center">
                <button id="add-checklist" type="button" class="btn btn-md btn-flat btn-primary"><i class="fa fa-plus"> </i> Aggiungi</button>
              </div>
              @if(!empty($segnalazione->id))
                @foreach($procedure as $procedura)
                  <h4>{!! $procedura->titolo !!}</h4>
                  <div class="row">
                    <div class="col-xs-12">
                      <div class="info-box info-box-sm" style="margin-left:15px; margin-top:6px;">
                        <span class="info-box-icon bg-green"><i class="fa fa-eur"></i></span>
                        <div class="info-box-content">
                          <span class="info-box-text">Spesa Totale</span>
                          @if(!empty($spesa_totale[$procedura->id]))
                            <span class="info-box-number">€ {{ str_replace(',', '.', number_format($spesa_totale[$procedura->id])) }},00</span>
                          @else 
                          <span class="info-box-number">€ 0,00</span>
                          @endif
                        </div>
                      </div>
                    </div>
                    </div>
                  <div class="box-body">
                    <div class="box box-primary box-shadow">
                      <div class="box-body">		
                        @if(!empty($segnalazione->checklist) && count($segnalazione->checklist) > 0)
                            <?php $count[$procedura->id] = 0; ?>
                          @foreach($segnalazione->checklist as $key => $checklist)
                            @if($checklist->procedura_id == $procedura->id)
                                <?php $count[$procedura->id]++; ?>
                                <div class="row" data-id="{{ $key }}-badges">
                                  @if(!empty($checklist->area_id))
                                    <div class="col-md-10">
                                      <caption><span class="badge bg-red" style="margin-left:12px;"><i class="fa fa-arrow-right" aria-hidden="true"></i><strong> {{ $segnalazione->checklist_area($checklist->area_id) }}</strong></span>
                                      @if(!empty($checklist->attivita_id))
                                        <span class="badge bg-orange" style="margin-left:12px;"><strong> {{ $segnalazione->checklist_attivita($checklist->attivita_id) }}</strong></span>
                                      @endif
                                      </caption>
                                    </div>
                                  @endif
                                  <div class="col-md-2 pull-right text-right">
                                        <button class="btn btn-sm btn-flat btn-danger" type="button" onclick="removeChecklist({{$key}})"><i class="fa fa-trash"> </i></button>
                                  </div>	
                                </div>
                                <div class="row" data-id="{{ $key }}" style="margin-left:6px;">
                                  <hr>
                                  <div class="col-lg-3">
                                    {{ Form::weSelectSearch('checklist['.$key.'][procedura_id]', 'Procedura', $errors, [''] + $procedure->pluck('titolo', 'id')->toArray(), get_if_exist($checklist, 'procedura_id'), ['onchange' => 'proceduraSelect(this)', 'data-row' => $key]) }}
                                  </div>
                                  <div class="col-lg-3">
                                    {{ Form::weSelectSearch('checklist['.$key.'][area_id]', 'Area Intervento', $errors, [0 => ''] + $aree, get_if_exist($checklist, 'area_id'), ['onchange' => 'areaSelect(this)', 'data-row' => $key]) }}
                                  </div>
                                  <div class="col-lg-3">
                                    {{ Form::weSelectSearch('checklist['.$key.'][attivita_id]', 'Attività', $errors, [0 => ''] + $attivita, get_if_exist($checklist, 'attivita_id'), ['data-row' => $key]) }}
                                  </div>
                                  <div class="col-lg-3">
                                    {{ Form::weCurrency('checklist['.$key.'][spesa]', 'Attuale Spesa', $errors, get_if_exist($checklist, 'spesa')) }}
                                  </div>
                                  <div class="col-lg-3">
                                    {{ Form::weText('checklist['.$key.'][nome]', 'Referente', $errors, get_if_exist($checklist, 'nome')) }}
                                  </div>
                                  <div class="col-lg-3">
                                    {{ Form::weText('checklist['.$key.'][email]', 'Email', $errors, get_if_exist($checklist, 'email')) }}
                                  </div>
                                  <div class="col-lg-3">
                                    {{ Form::weText('checklist['.$key.'][telefono]', 'Telefono', $errors, get_if_exist($checklist, 'telefono')) }}
                                  </div>
                                  <div class="col-lg-3">
                                    {{ Form::weText('checklist['.$key.'][note]', 'Note', $errors, get_if_exist($checklist, 'note')) }}
                                  </div>
                                  <hr>
                                </div>
                              {{-- endif --}}
                            @endif
                          @endforeach	
                        @endif
                        @if(!empty($count[$procedura->id]) && $count[$procedura->id] == 0 || empty($count[$procedura->id]))
                          <div style="margin-top:15px;" class="alert alert-info alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-ban"></i> {{ $procedura->titolo }}</h4>
                            Non vi è alcuna checklist per questa procedura.
                          </div>
                        @endif
                      </div>
                    </div>
                  </div>      
                @endforeach
              @endif
            </div>
          </div> 
        </div>

        @if(Auth::user()->inRole('admin') && !empty($segnalazione->censimento()) && !empty($segnalazione->cliente_id) || user(setting('admin::direttore_commerciale'))->email == Auth::user()->email && !empty($segnalazione->censimento()) && !empty($segnalazione->cliente_id))
          <div class="col-md-12 bg-orange" style="height:55px; margin-bottom:20px;">
            <div class="col-md-12 text-center">
              <button style="margin-top:11px;" type="button" class="btn btn-success btn-flat" data-toggle="modal" data-target="#accettaSegnalazione">Accetta Segnalazione</button>   
              <button style="margin-top:11px;" type="button" class="btn btn-danger btn-flat" data-toggle="modal" data-target="#modal-reject-confirmation" data-action-target="{{ route('admin.commerciale.segnalazioneopportunita.reject', [$segnalazione->id]) }}">Rifiuta Segnalazione</button>  
            </div>
          </div>
        @endif
      </div>
    @endif
  </div> 
</div>

@push('js-stack')
  <script type="text/javascript">
    	// Add checklist
      $('#add-checklist').click(function() {
      var row = $('.nuova-checklist-row .row').last().clone();
      var oldId = parseInt(row.attr('data-id'));
      var newId = oldId + 1;
      row.attr('data-id', newId);
      row.find('select[data-row]').attr('data-row', newId);
      row.find('select, input').val('');
      row.find('span.select2').remove();
      row.find('.btn-danger').remove();

      row.prepend('<div class="col-lg-12"><button class="btn btn-sm btn-flat btn-danger pull-right clearfix" type="button" onclick="removeChecklist('+newId+')"><i class="fa fa-trash"> </i></button></div>');
      var newRow = row[0].outerHTML.split('checklist['+oldId+']')
                                    .join('checklist['+newId+']')
                                    .split('removeChecklist('+oldId)
                                    .join('addChecklist('+newId);
      
      

      $('.nuova-checklist-row').append('<hr class="hr-remove" data-id="'+newId+'-hr">').append(newRow);
      bootJs();
    });

	  // Remove checklist
	  function removeChecklist(id) {
      $('.row[data-id="'+id+'"]').remove();
      $('.hr-remove[data-id="'+id+'-hr"]').remove();
		  $('.row[data-id="'+id+'-badges"]').remove();
    }

	  var aree = $.parseJSON(atob("{{ get_json_aree() }}"));
    var gruppi = $.parseJSON(atob("{{ get_json_gruppi() }}"));

    // Select procedura
    function proceduraSelect(el) {
      var row = $(el).attr('data-row');
      var procedura = $('.row[data-id="'+row+'"] select[name*="[procedura_id]"]');
      var area = $('.row[data-id="'+row+'"] select[name*="[area_id]"]');
      var gruppo = $('.row[data-id="'+row+'"] select[name*="[attivita_id]"]');
      var procedura_selezionata = procedura.val();

      area.empty();

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
      var row = $(el).attr('data-row');
      var area = $(el);
	    var gruppo = $('.row[data-id="'+row+'"] select[name*="[attivita_id]"]');
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
  </script>
@endpush
