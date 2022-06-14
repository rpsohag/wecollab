<div class="box-body">
	<div class="row">
		<div class="col-md-12">
			{{ Form::weSelectSearch('cliente_id', 'Cliente', $errors, $enti, get_if_exist($censimentocliente, 'cliente_id'), ['id' => 'cliente']) }}
		</div>
	</div>

	<div class="row bg-gray">
		<div class="col-md-12"><br></div>
		<div class="col-md-6">
			<div class="box box-success">
				<div class="box-header with-border">
          <h3 class="box-title">Sede legale</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							{{ Form::weText('indirizzo', 'Indirizzo', $errors, get_if_exist($censimentocliente, 'indirizzo')) }}
						</div>
						<div class="col-md-8">
							{{ Form::weText('citta', 'Città', $errors, get_if_exist($censimentocliente, 'citta')) }}
						</div>
						<div class="col-md-4">
							{{ Form::weText('provincia', 'Provicia', $errors, get_if_exist($censimentocliente, 'provincia'), ['maxlength' => 2]) }}
						</div>
						<div class="col-md-4">
							{{ Form::weText('cap', 'CAP', $errors, get_if_exist($censimentocliente, 'cap'), ['maxlength' => 10]) }}
						</div>
						<div class="col-md-8">
							{{ Form::weText('nazione', 'Nazione', $errors, (!empty(get_if_exist($censimentocliente, 'nazione')) ? get_if_exist($censimentocliente, 'nazione') : 'Italia')) }}
						</div>
					</div>
				</div>
			</div>

			<div class="box box-warning">
				<div class="box-header with-border">
          <h3 class="box-title">Pianta organica</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
				<div class="box-body">
					<table class="table">
						@foreach(config('commerciale.censimenticlienti.pianta_organica') as $key => $po)
						<tr>
							<td>{{ $po }}</td>
							<td>{{ Form::weInt('pianta_organica['.$key.']', '', $errors, (!empty($censimentocliente->pianta_organica) ? get_if_exist($censimentocliente->pianta_organica, $key) : ''), ['class' => 'form-control po', 'onchange' => 'poTotale()']) }}</td>
						</tr>
						@endforeach
						<tfoot>
							<th>TOTALE</th>
							<th id="po-totale"></th>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="box box-info">
				<div class="box-header with-border">
          <h3 class="box-title">Info</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							{{ Form::weText('sindaco', 'Sindaco', $errors, get_if_exist($censimentocliente, 'sindaco')) }}
						</div>
						<div class="col-md-6">
							{{ Form::weText('sindaco_email', 'Email', $errors, get_if_exist($censimentocliente, 'sindaco_email')) }}
						</div>
						<div class="col-md-6">
							{{ Form::weText('sindaco_telefono', 'Telefono', $errors, get_if_exist($censimentocliente, 'sindaco_telefono')) }}
						</div>
						<div class="col-md-12">
							<hr/>
							{{ Form::weText('segretario', 'Segretario', $errors, get_if_exist($censimentocliente, 'segretario')) }}
						</div>
						<div class="col-md-6">
							{{ Form::weText('segretario_email', 'Email', $errors, get_if_exist($censimentocliente, 'segretario_email')) }}
						</div>
						<div class="col-md-6">
							{{ Form::weText('segretario_telefono', 'Telefono', $errors, get_if_exist($censimentocliente, 'segretario_telefono')) }}
						</div>
						<div class="col-md-12">
							<hr/>
							{{ Form::weText('referente', 'Referente Unico del Progetto', $errors, get_if_exist($censimentocliente, 'referente')) }}
						</div>
						<div class="col-md-6">
							{{ Form::weText('referente_email', 'Email', $errors, get_if_exist($censimentocliente, 'referente_email')) }}
						</div>
						<div class="col-md-6">
							{{ Form::weText('referente_telefono', 'Telefono', $errors, get_if_exist($censimentocliente, 'referente_telefono')) }}
						</div>
						<div class="col-md-12">
							<hr/>
						</div>
						<div class="col-md-4">
							{{ Form::weInt('numero_dipendenti', 'Numero dipendenti', $errors, get_if_exist($censimentocliente, 'numero_dipendenti')) }}
						</div>
						<div class="col-md-4">
							{{ Form::weSelect('fascia_abitanti', 'Fascia di abitanti', $errors, config('commerciale.censimenticlienti.fasce_abitanti'),   get_if_exist($censimentocliente, 'fascia_abitanti')) }}
						</div>
						<div class="col-md-4">
							{{ Form::weInt('numero_utilizzatori_urbi', 'Numero Utilizzatori Urbi', $errors, get_if_exist($censimentocliente, 'numero_utilizzatori_urbi')) }}
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-12">
			<div class="box">
				<div class="box-header with-border">
          <h3 class="box-title">Note</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
				<div class="box-body">
					{{ Form::weTextarea('note', ' ', $errors, get_if_exist($censimentocliente, 'note')) }}
				</div>
			</div>
		</div>
	</div>

	@if(!empty($procedure) && count($procedure) > 0)
		<h3><strong>SITUAZIONE SOFTWARE ATTUALI</strong></h3>
		<hr>
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
		<caption><span class="badge bg-green" style="margin-left:12px; margin-top:8px;"><i class="fa fa-arrow-right" aria-hidden="true"></i><strong> Inserisci nuova situazione software</strong></span></caption>
		<br><br>
		@if(!empty($censimentocliente->referenti) && count($censimentocliente->referenti) > 0)
			@php $new_record_key = count($censimentocliente->referenti) + 2; @endphp
		@else 
			@php $new_record_key = 1; @endphp
		@endif
		<div class="nuova-situazione-row">
			<div class="row" data-id="{{ $new_record_key }}" style="margin-left:6px;">
				<div class="col-lg-4">
					{{ Form::weSelectSearch('referenti['.$new_record_key.'][procedura_id]', 'Procedura', $errors, $procedure_list, '',  ['onchange' => 'proceduraSelect(this)', 'id' => 'referente-procedura', 'data-row' => $new_record_key]) }}
				</div>
				<div class="col-lg-4">
					{{ Form::weSelectSearch('referenti['.$new_record_key.'][area_id]', 'Area Intervento', $errors, [0 => ''] + $aree, '',  ['onchange' => 'areaSelect(this)', 'id' => 'referente-area', 'data-row' => $new_record_key]) }}
				</div>
				<div class="col-lg-4">
					{{ Form::weSelectSearch('referenti['.$new_record_key.'][attivita_id]', 'Attività', $errors, [0 => ''] + $attivita, '',  ['id' => 'referente-attivita']) }}
				</div>
				<div class="col-lg-4">
					{{ Form::weText('referenti['.$new_record_key.'][nome]', 'Referente', $errors, '', ['id' => 'referente-nome']) }}
				</div>
				<div class="col-lg-4">
					{{ Form::weText('referenti['.$new_record_key.'][email]', 'Email', $errors, '', ['id' => 'referente-email']) }}
				</div>
				<div class="col-lg-4">
					{{ Form::weText('referenti['.$new_record_key.'][telefono]', 'Telefono', $errors, '', ['id' => 'referente-telefono']) }}
				</div>
				<div class="col-lg-3">
					{{ Form::weSelect('referenti['.$new_record_key.'][sw]', 'Attuale Software', $errors, config('commerciale.censimenticlienti.altri_software'), '', ['id' => 'referente-sw']) }}
				</div>
				<div class="col-lg-3">
					{{ Form::weCurrency('referenti['.$new_record_key.'][spesa]', 'Attuale Spesa', $errors, '', ['id' => 'referente-spesa']) }}
				</div>
				<div class="col-lg-6">
					{{ Form::weText('referenti['.$new_record_key.'][note]', 'Note', $errors, '', ['id' => 'referente-note']) }}
				</div>
				<hr>
			</div>
		</div>
		<div class="text-center">
			<button id="add-situazione" type="button" class="btn btn-md btn-flat btn-primary"><i class="fa fa-plus"> </i> Aggiungi</button>
		</div>
	    @foreach($procedure as $procedura)
			<h3><strong>{!! $procedura->titolo !!}</strong> <small>( Modifica situazioni software esistenti )</small></h3>
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
						@if(count($censimentocliente->referenti) > 0)
					     	<?php $count[$procedura->id] = 0; ?>
							@foreach($censimentocliente->referenti as $key => $referente)
								@if($referente->procedura_id == $procedura->id)
									@if(!empty($referente->email) || !empty($referente->spesa) || !empty($referente->nome) || !empty($referente->telefono) || !empty($referente->area_id)) 
										<?php $count[$procedura->id]++; ?>
										<div class="row" data-id="{{ $key }}-badges">
											@if(!empty($referente->area_id))
												<div class="col-md-10">
													<caption><span class="badge bg-red" style="margin-left:12px;"><i class="fa fa-arrow-right" aria-hidden="true"></i><strong> {{ $censimentocliente->situazione_software_area($referente->area_id) }}</strong></span>
													@if(!empty($referente->attivita_id))
														<span class="badge bg-orange" style="margin-left:12px;"><strong> {{ $censimentocliente->situazione_software_attivita($referente->attivita_id) }}</strong></span>
													@endif
													</caption>
												</div>
											@endif
											<div class="col-md-2 pull-right text-right">
									      		<button class="btn btn-sm btn-flat btn-danger" type="button" onclick="removeSituazione({{$key}})"><i class="fa fa-trash"> </i></button>
											</div>	
										</div>
										<div class="row" data-id="{{ $key }}" style="margin-left:6px;">
											<hr>
											<div class="col-lg-4">
												{{ Form::weSelectSearch('referenti['.$key.'][procedura_id]', 'Procedura', $errors, [0 => ''] + $procedure_list, get_if_exist($referente, 'procedura_id'), ['onchange' => 'proceduraSelect(this)', 'data-row' => $key]) }}
											</div>
											<div class="col-lg-4">
												{{ Form::weSelectSearch('referenti['.$key.'][area_id]', 'Area Intervento', $errors, [0 => ''] + $aree, get_if_exist($referente, 'area_id'), ['onchange' => 'areaSelect(this)', 'data-row' => $key]) }}
											</div>
											<div class="col-lg-4">
												{{ Form::weSelectSearch('referenti['.$key.'][attivita_id]', 'Attività', $errors, [0 => ''] + $attivita, get_if_exist($referente, 'attivita_id'), ['data-row' => $key]) }}
											</div>
											<div class="col-lg-3">
												{{ Form::weText('referenti['.$key.'][nome]', 'Referente', $errors, get_if_exist($referente, 'nome')) }}
											</div>
											<div class="col-lg-3">
												{{ Form::weText('referenti['.$key.'][email]', 'Email', $errors, get_if_exist($referente, 'email')) }}
											</div>
											<div class="col-lg-3">
												{{ Form::weText('referenti['.$key.'][telefono]', 'Telefono', $errors, get_if_exist($referente, 'telefono')) }}
											</div>
											<div class="col-lg-3">
												{{ Form::weSelect('referenti['.$key.'][sw]', 'Attuale Software', $errors, config('commerciale.censimenticlienti.altri_software'), get_if_exist($referente,'sw')) }}
											</div>
											<div class="col-lg-8">
												{{ Form::weText('referenti['.$key.'][note]', 'Note', $errors, get_if_exist($referente, 'note')) }}
											</div>
											<div class="col-lg-4">
												{{ Form::weCurrency('referenti['.$key.'][spesa]', 'Attuale Spesa', $errors, get_if_exist($referente, 'spesa')) }}
											</div>
											<hr>
										</div>
									@endif
								@endif
							@endforeach	
						@endif
						@if(!empty($count[$procedura->id]) && $count[$procedura->id] == 0 || empty($count[$procedura->id]))
							<div style="margin-top:15px;" class="alert alert-info alert-dismissible">
								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
								<h4><i class="icon fa fa-ban"></i> {{ $procedura->titolo }}</h4>
								Non vi è alcuna situazione software presente per questa procedura.
							</div>
						@endif
					</div>
				</div>      
			</div>
		@endforeach
    @endif
</div>

@push('js-stack')
<script type="text/javascript">
	$(document).ready(function() {
		poTotale();

		$('#ente').change(function() {
			var enteId = $(this).val();
			var id_segnalazione = $("#id_segnalazione").val();

			var add_segn = "";
		  	if(id_segnalazione > 0) {
		  		add_segn = '&segnalazione_opportunita=' + id_segnalazione;
		  	}
 			var loc = window.location;

			window.location = loc.protocol + '//' + loc.host + loc.pathname + '?cliente_id=' + enteId + add_segn ;
		});
	});

	function poTotale() {
		var pos = $('input.po');
		var somma = 0;

		pos.each(function() {
			var val = parseInt($(this).val());

			if (!isNaN(val))
				somma += val;
		});

		$('#po-totale').html(somma);
	}

	// Add situazione
	$('#add-situazione').click(function() {
        var row = $('.nuova-situazione-row .row').last().clone();
        var oldId = parseInt(row.attr('data-id'));
        var newId = oldId + 1;

        row.attr('data-id', newId);
        row.find('select[data-row]').attr('data-row', newId);
        row.find('select, input').val('');
        row.find('span.select2').remove();

        var newRow = row[0].outerHTML.split('referenti['+oldId+']').join('referenti['+newId+']')
                                      .split('removeSituazione('+oldId).join('removeSituazione('+newId);

        $('.nuova-situazione-row').append('<hr>').append(newRow);
        bootJs();
    });

	// Remove situazione
	function removeSituazione(id) {
		$('.row[data-id="'+id+'"]').remove();
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
