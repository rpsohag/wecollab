@extends('layouts.master')

@php
	$tipologie_richieste['-1'] = "Seleziona Una Tipologia";
	$imp_indennita_gg = $currentUser->profile->indennita_giornaliera;
	$imp_indennita_pernotto = $currentUser->profile->indennita_pernottamento;


	if($tipologia_sel == 5)
	{
		$costo_auto = clean_currency($macchina_sel->costo_km);
	}
	else
	{
		$costo_auto = 0;
	}

@endphp

@section('content-header')
    <h1>
        Richiesta {{config('wecore.richieste.tipologie_richieste')[$tipologia_sel]}} [<span class="text-red">Da Inviare</span>]
    </h1>
@stop
@section('content')
<div class="section">
	@if($tipologia_sel == 5)
		<div class="row">
			<div class="col-md-4 col-sm-6 col-xs-12">
				<div class="info-box">
					<span class="info-box-icon bg-blue">
						<i class="fa fa-car"></i>
					</span>
					<div class="info-box-content">
						<span class="info-box-text">Autovettura  (<strong>{{$macchina_sel->modello}}</strong>)</span>
						<span id="autovettura_sel" class="info-box-number">{{$macchina_sel->targa}}</span>
						<span class="info-box-number">{{$macchina_sel->costo_km}} (Costo Km)</span>
					</div>
				</div>
			</div>
		</div>
	@endif
	<div class="box box-solid">
	{!! Form::open(['route' => 'admin.account.richieste.create', 'method' => 'post' , 'id' => "invio_richiesta"]) !!}
		<div class="box-body contenitore">
			<div class="row">
				<input type="hidden" value="{{$tipologia_sel}}" name="tipologia_sel"/>
				<input type="hidden" value="{{$richiesta->id}}" name="id_richiesta"/>
				@if($tipologia_sel == 5)
					<input type="hidden" value="{{$macchina_sel->id}}" name="macchina_selezionata"/>
						<div class="col-md-3">
							{!! Form::weSelectSearch('mese' , 'Mese', $errors , $mesi,$richiesta->mese) !!}
						</div>
						<div class="col-md-2">
							{!! Form::weSelectSearch('anno' , 'Anno', $errors , $anni ,$richiesta->anno) !!}
						</div>
						<div class="col-md-4">
							<br>
							<button id="add_perc" type="button" class="btn btn-info btn-flat"><i class="fa fa-plus"></i> Nuovo Percorso</button>
							<button id="add_perc_interm" type="button" class="btn btn-default btn-flat"><i class="fa fa-plus"></i> Nuovo Percorso Intermedio</button>
						</div>
						<div class="col-md-12 copiatoree hidden calcolokm">
							<div class="col-md-2">
								{!! Form::weDate('km[0][data]' , 'Data *', $errors ,null) !!}
							</div>
							<div class="col-md-2">
								{!! Form::weSelect('km[0][partenza]' , 'Da *', $errors , $valori_sedi,$currentUser->profile->sede_partenza) !!}
							</div>
							<div class="col-md-2">
								{!! Form::weText('km[0][arrivo]' ,'A *',$errors, null) !!}
							</div>
							<div class="col-md-1">
								<br><br>
								<input type="checkbox" value="1" name="km[0][ar]"> A/R (km <strong name="km[0][testo]">0</strong>)</input>
								<input type="hidden" value="0" name="km[0][km]"/>
							</div>
							<div class="col-md-2">
								{!! Form::weSelectSearch('km[0][attivita]' , 'Attivita', $errors , $attivita,null) !!}
							</div>
							<div class="col-md-2">
								{!! Form::weText('km[0][motivazione]' ,'Note / Descrizione *',$errors, null) !!}
							</div>
							<div class="col-md-1">
								<br>
								<button type="button" onclick="EliminaVoceKm(this)" class="btn btn-danger btn-flat"><i class="fa fa-trash"></i></button>
							</div>
						</div>
						<div class="col-md-12">
							{!! Form::weText('note' , 'Note', $errors ,$richiesta->note) !!}
						</div>
						@if(!empty($richiesta->vociKm))
							@foreach($richiesta->vociKm as $indice => $km)
								<div class="col-md-12 calcolokm">
									<div class="col-md-2">
										{!! Form::weDate('km['.($indice + 1).'][data]' , 'Data *', $errors ,$km->data) !!}
									</div>
									<div class="col-md-2">
										@if(isset($valori_sedi[$km->partenza]))
											{!! Form::weSelect('km['.($indice + 1).'][partenza]' , 'Da *', $errors , $valori_sedi,$km->partenza) !!}
										@else
											{!! Form::weText('km['.($indice + 1).'][partenza]' ,'Da *',$errors, $km->partenza) !!}
										@endif
									</div>
									<div class="col-md-2">
										{!! Form::weText('km['.($indice + 1).'][arrivo]' ,'A *',$errors, $km->arrivo) !!}
									</div>
									<div class="col-md-1">
										<br><br>
										<input type="checkbox" {{($km->ar == 1 ? 'checked' : '')}} value="1" name="km[{{$indice + 1}}][ar]"> A/R (km <strong name="km[{{$indice + 1}}][testo]">{{$km->km}}</strong>)</input>
										<input type="hidden" value="{{$km->km}}" name="km[{{$indice + 1}}][km]"/>
									</div>
									<div class="col-md-2">
										{!! Form::weSelectSearch('km['.($indice + 1).'][attivita]' , 'Attivita', $errors , $attivita,$km->attivita) !!}
									</div>
									<div class="col-md-2">
										{!! Form::weText('km['.($indice + 1).'][motivazione]' ,'Note / Descrizione *',$errors, $km->note) !!}
									</div>
									<div class="col-md-1">
										<br>
										<button type="button" onclick="EliminaVoceKm(this)" class="btn btn-danger btn-flat"><i class="fa fa-trash"></i></button>
									</div>
								</div>
							@endforeach
						@endif
					</div>
				@endif
				@if($tipologia_sel == 4)
						<div class="col-md-3">
							{!! Form::weSelectSearch('mese' , 'Mese *', $errors , $mesi,$richiesta->mese) !!}
						</div>
						<div class="col-md-3">
							{!! Form::weSelectSearch('anno' , 'Anno *', $errors , $anni,$richiesta->anno) !!}
						</div>
						<div class="col-md-3">
							<br>
							<button id="add_trasferta" type="button" class="btn btn-info btn-flat"><i class="fa fa-plus"></i> Aggiungi trasferta</button>
						</div>
						<div class="col-md-12">
							{!! Form::weText('note' , 'Note', $errors ,$richiesta->note) !!}
						</div>
						<div class="col-md-12 copiatore hidden calcoloTrasferte">
							<div class="col-md-2">
								{!! Form::weDate('trasferte[0][data]' , 'Data *', $errors ,null) !!}
							</div>
							<div class="col-md-2">
								{!! Form::weSelectSearch('trasferte[0][id_tipo]' , 'Tipologia', $errors , $tipi_trasferte,1) !!}
							</div>
							<div class="col-md-2">
								{!! Form::weCurrency('trasferte[0][importo]' ,'Importo',$errors, $currentUser->profile->indennita_giornaliera,['readonly'=>'readonly','nascosto'=>'nascosto']) !!}
							</div>
							<div class="col-md-2">
								{!! Form::weSelectSearch('trasferte[0][attivita]' , 'Attivita', $errors , $attivita,null) !!}
							</div>
							<div class="col-md-2">
								{!! Form::weText('trasferte[0][motivazione]' ,'Note / Descrizione *',$errors, null) !!}
							</div>
							<div class="col-md-2">
								<br>
								<button type="button" onclick="EliminaVoceTrasferta(this)" class="btn btn-danger btn-flat"><i class="fa fa-trash"></i></button>
							</div>
						</div>
						@if(!empty($richiesta->vociTrasferte))
							@foreach($richiesta->vociTrasferte as $indice => $trasferta)
								<div class="col-md-12 calcoloTrasferte">
									<div class="col-md-2">
										{!! Form::weDate('trasferte['.$indice.'][data]' , 'Data *', $errors ,$trasferta->data) !!}
									</div>
									<div class="col-md-2">
										{!! Form::weSelectSearch('trasferte['.$indice.'][id_tipo]' , 'Tipologia', $errors , $tipi_trasferte,$trasferta->tipologia) !!}
									</div>
									<div class="col-md-2">
										{!! Form::weCurrency('trasferte['.$indice.'][importo]' ,'Importo',$errors, $trasferta->importo,($trasferta->tipologia == 1 || $trasferta->tipologia == 2 ? ['readonly'=>'readonly'] : [])) !!}
									</div>
									<div class="col-md-2">
										{!! Form::weSelectSearch('trasferte['.$indice.'][attivita]' , 'Attivita', $errors , $attivita,$trasferta->attivita_id) !!}
									</div>
									<div class="col-md-2">
										{!! Form::weText('trasferte['.$indice.'][motivazione]' ,'Note / Descrizione *',$errors, $trasferta->note) !!}
									</div>
									<div class="col-md-2">
										<br>
										<button type="button" onclick="EliminaVoceTrasferta(this)" class="btn btn-danger btn-flat"><i class="fa fa-trash"></i></button>
									</div>
								</div>
							@endforeach
						@endif
					</div>
				@endif
			</div>
			<div class="box-footer">
				<button id="btn_invioo" type="button" class="btn btn-success btn-flat" data-toggle="modal" data-target="#saveModal"><i class="fa fa-floppy-o"></i> Invia</button>
				<a class="btn btn-warning pull-right btn-flat" href="{{route('admin.account.richieste.index',['tab'=>$tipologia_sel ])}}"><i class="fa fa-arrow-left"></i> Indietro</a>
				@if($tipologia_sel == 5 || $tipologia_sel == 4)
					<button id="btn_bozza" type="button" class="btn btn-default btn-flat"><i class="fa fa-bolt"></i> Modifica</button>
					@if($tipologia_sel == 4)
						<button type="button" class="btn btn-danger pull-right btn-flat" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.account.richieste.bozza.destroytrasferte', [$richiesta->id]) }}"><i class="fa fa-trash"></i> Elimina</button>
					@endif
					@if($tipologia_sel == 5)
						<button type="button" class="btn btn-danger pull-right btn-flat" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.account.richieste.bozza.destroykm', [$richiesta->id]) }}"><i class="fa fa-trash"></i> Elimina</button>
					@endif
				@endif
			</div>
	{!! Form::close() !!}
	</div>
</div>
<br>
@if($tipologia_sel == 5 || $tipologia_sel == 4)
<br>
	<div class="row">
		<div class="col-md-4 col-sm-6 col-xs-12">
			<div class="info-box">
				<span class="info-box-icon bg-green"><i class="fa fa-euro"></i></span>
				<div class="info-box-content">
				<span class="info-box-text">Totale</span>
				<span id="tot_rimborso" class="info-box-number">{{$richiesta->totale}}</span>
				</div>
			</div>
		</div>
	</div>
@endif
@include('core::partials.delete-modal')
<!-- Modal -->
<div class="modal fade" id="saveModal" tabindex="-1" role="dialog" aria-labelledby="saveModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="exampleModalLongTitle">Confermi di voler inviare la richiesta ?</h3>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning pull-right" data-dismiss="modal"><i class="fa fa-arrow-left"></i> Indietro</button>
        <button type="button" data-dismiss="modal" id="btn_invio" class="btn btn-success pull-left"><i class="fa fa-check"></i> Conferma</button>
      </div>
    </div>
  </div>
</div>
@endsection
@push('js-stack')
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCKSLtAPDDZ9CM33kvvV_oXHPNj7h5ppvU"></script>
     <script>

		var directionsDisplay;
		var directionsService = new google.maps.DirectionsService();
		var dist = 0;
		var tot_rimborso = 0;
		var formatter = new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' });

        $( document ).ready(function() {
			@if($tipologia_sel == 5)
				StartKm();
				reloadJs();
			@endif

			$("#btn_bozza").click(function(){
				var elem ="<input type='hidden' value='bozzaupdate' name='bozzaupdate'/><input type='hidden' value='{{$tipologia_sel}}' name='tipologia_sel' ";
				$("#invio_richiesta").append(elem)
				$("#invio_richiesta").submit();
			})

			$("#btn_invio").click(function(){
				$("#invio_richiesta").find("input[name='bozza']").remove();

				@if($tipologia_sel == 4)
					if(validaTrasferte())
					{
						$("#invio_richiesta").submit();
					}
				@endif

				@if($tipologia_sel == 5)
					if(validaRimborsi())
					{
						$("#invio_richiesta").submit();
					}
				@endif

				@if($tipologia_sel == 1 || $tipologia_sel == 2 || $tipologia_sel == 3)
				{
					$("#invio_richiesta").submit();
				}
				@endif
			})
			
			$('#add_trasferta').click(function() {
				var adder = $(".copiatore").clone().removeClass('hidden').removeClass('copiatore');
				var counter = $(".calcoloTrasferte").length;

				adder.find("input,select").each(function(){
					var name_extra = $(this).attr("name");
					var splitter = name_extra.split("[");
					splitter[1] = counter +"]";
					var rename = splitter.join("[");
					$(this).attr("name",rename);
					$(this).attr("id",rename);
					$(this).removeAttr("nascosto");
				})

				$(".contenitore").append(adder);
				reloadJs();
				CalcoloTrasferte();
			})

			$('#add_perc').click(function() {
				var adder = $(".copiatoree").clone().removeClass('hidden').removeClass('copiatoree');
				var counter = $(".calcolokm").length;
				
				adder.find("input,select,strong").each(function(){
					var name_extra = $(this).attr("name");
					var splitter = name_extra.split("[");
					splitter[1] = counter +"]";
					var rename = splitter.join("[");
					$(this).attr("name",rename);
					$(this).attr("id",rename);
				})

				$(".contenitore").append(adder);
				reloadJs();
			})

			$('#add_perc_interm').click(function() {
				var adder = $(".copiatoree").clone().removeClass('hidden').removeClass('copiatoree');
				var counter = $(".calcolokm").length;
	
				adder.find("input,select,strong").each(function(){
					var name_extra = $(this).attr("name");
					var splitter = name_extra.split("[");
					splitter[1] = counter +"]";
					var rename = splitter.join("[");
					$(this).attr("name",rename);
					$(this).attr("id",rename);
				})

				var precedentePercorso = $("input[name='km["+parseInt(counter-1)+"][arrivo]'").val();

				transformSelectIntoInput(adder.find("select:first"),precedentePercorso);

				$(".contenitore").append(adder);
				reloadJs();
			})

			$("select[name*='id_tipo']").change(function(){
				var info_sel = $(this).val();
				var index = $(this).attr("name").split("]")[0].split("[")[1];
				//Se indennita gg o pernotto disabilito l' imporot mettendogli il default
				if(info_sel == 1 || info_sel == 2)
				{
					//ottengo l'id 
					if(info_sel == 1)
					{
						$("input[name='trasferte["+index+"][importo]']").val("{{$imp_indennita_gg}}")
					}
					if(info_sel == 2)
					{
						$("input[name='trasferte["+index+"][importo]']").val("{{$imp_indennita_pernotto}}")
					}
					$("input[name='trasferte["+index+"][importo]']").attr('readonly','readonly');
				}
				else
				{
					$("input[name='trasferte["+index+"][importo]']").removeAttr("readonly");
					$("input[name='trasferte["+index+"][importo]']").val("€ 0,00");
				}
				
				CalcoloTrasferte();
			})

		});

		function reloadJs()
		{
			tot_rimborso = 0;

			var date = new Date();
			date.setMonth($("[name='mese']").val() -1, 1);

			// Datepicker
			$('.datepicker').datetimepicker({
				format: 'DD/MM/YYYY',
				locale: moment.locale('it'),
				defaultDate: date
			});

			//Currency
			$('.currency').focusout(function() {
				currency(this);
			});

			$('.currency').focusin(function() {
				currency(this);
			});

			$(".currency").change(function() {
				CalcoloTrasferte();
			});

			//change Partenza o Arrivo
			$("[name*='partenza'],[name*='arrivo']").change(function() {
				CalcoloKm($(this));
			});

			$("input[type='checkbox']").change(function() 
			{
				CalcoloKm($(this));
			});

			$("select[name*='attivita'] , select[name*='id_tipo']").each(function (i, obj) {

				if (!$(obj).data('select2'))
				{
					$(obj).select2();
				}

				$(this).next().next().remove();
			});

			$("select[name*='id_tipo']").change(function(){
				var info_sel = $(this).val();
				var index = $(this).attr("name").split("]")[0].split("[")[1];
				//Se indennita gg o pernotto disabilito l' imporot mettendogli il default
				if(info_sel == 1 || info_sel == 2)
				{
					//ottengo l'id 
					if(info_sel == 1)
					{
						$("input[name='trasferte["+index+"][importo]']").val("{{$imp_indennita_gg}}")
					}
					if(info_sel == 2)
					{
						$("input[name='trasferte["+index+"][importo]']").val("{{$imp_indennita_pernotto}}")
					}
					$("input[name='trasferte["+index+"][importo]']").attr('readonly','readonly');
				}
				else
				{
					$("input[name='trasferte["+index+"][importo]']").removeAttr("readonly");
					$("input[name='trasferte["+index+"][importo]']").val("€ 0,00");
				}
				
				CalcoloTrasferte();
			})

			$("strong[name*='[testo]']").on('DOMSubtreeModified',function()
			{
				RicalcolaKm();
			})
		}

		function StartKm()
		{
			directionsDisplay = new google.maps.DirectionsRenderer();
		}


		function EliminaVoceTrasferta(e)
		{
			$(e).parent().parent().remove();
			CalcoloTrasferte();
		}

		function EliminaVoceKm(e)
		{
			$(e).parent().parent().remove();
			RicalcolaKm();
		}

		function RicalcolaKm()
		{
			tot_rimborso = 0;

			$("strong[name*='[testo]']").each(function(){
				var km_span = parseInt($(this).text());
				if(!isNaN(km_span))
				{
					tot_rimborso += km_span;
					$("#tot_rimborso").text(formatter.format(tot_rimborso * {{$costo_auto}}));
				}
			})
		}

		function CalcoloKm(info)
		{
			var index = $(info).attr("name").split("]")[0].split("[")[1];
			var partenza = $("*[name='km["+index+"][partenza]']").val();
			var arrivo =$("input[name='km["+index+"][arrivo]']").val();
			var ar = $("input[name='km["+index+"][ar]']");
			var km_hidden = $("input[name='km["+index+"][km]']");
			var testo_info = $("strong[name='km["+index+"][testo]']");

			if(partenza != "" && arrivo !="")
			{
				var request = {
				origin : partenza,
				destination : arrivo,
				travelMode : google.maps.TravelMode.DRIVING
				};
				
				directionsService.route(request, function(result, status) 
				{
					if (status == google.maps.DirectionsStatus.OK) 
					{
						directionsDisplay.setDirections(result);
						dist = result.routes[0].legs[0].distance.value;
						var fin = parseInt((dist) / (1000));
						if(ar.is(":checked"))//Andata e ritorno
						{
							km_hidden.val(fin * 2);
							testo_info.text(fin * 2);
						}
						else
						{
							km_hidden.val(fin);
							testo_info.text(fin);
						}
					}
				});
			}
			else
			{
				testo_info.text("0");
			}
		}

		function CalcoloTrasferte()
		{
			var rimb_spese = 0;

			$("input[name*='[importo]']").not("[nascosto='nascosto']").each(function() {
				rimb_spese +=  pulisciDenaro($(this).val());
			});

			$("#tot_rimborso").text(formatter.format(rimb_spese));
		}

		function pulisciDenaro(importo)
		{
			if(isNaN(parseFloat(importo.replace("€","").replace(",",".").trim())))
			{
				return 0.00;
			}
			else
			{
				return parseFloat(importo.replace("€","").replace(",",".").trim());
			}
			return 
		}


		function transformSelectIntoInput(elementSelector,valore){
			var el = $(elementSelector);

				el.replaceWith($('<input />').attr({ 
				type: 'text',
				id: el.attr('id'),
				name: el.attr('name'),
				class: el.attr('class'),
				value: valore
				}));
		}

		function validaTrasferte()
		{
			var corretto = true;
			$('.calcoloTrasferte').not('.hidden').find(":input,select").each(function(){

				var name = $(this).attr("name");

				if(typeof name !== typeof undefined)
				{
					if(name.includes("data"))
					{
						var data = $(this).val();
						if(data == "")
						{
							$(this).parent().parent().removeClass('has-success');
							$(this).parent().parent().addClass('has-error');
							corretto = false;
						}

						if(data !== "")
						{
							$(this).parent().parent().removeClass('has-error');
							$(this).parent().parent().addClass('has-success');
						}

					}

					if(name.includes("id_tipo"))
					{
						var id_tipo = $(this).val();

						if(id_tipo == "0")
						{
							$(this).parent().removeClass('has-success');
							$(this).parent().addClass('has-error');
							corretto = false;
						}

						if(id_tipo !== "0")
						{
							$(this).parent().removeClass('has-error');
							$(this).parent().addClass('has-success');
						}
					}

					if(name.includes("importo"))
					{
						var importo = $(this).val()

						if(importo == "")
						{
							$(this).parent().removeClass('has-success');
							$(this).parent().addClass('has-error');
							corretto = false;
						}

						if(importo !== "")
						{
							$(this).parent().removeClass('has-error');
							$(this).parent().addClass('has-success');
						}

					}

					if(name.includes("attivita"))
					{
						var attivita = $(this).val();

						if(attivita == "0")
						{
							$(this).parent().removeClass('has-success');
							$(this).parent().addClass('has-error');
							corretto = false;
						}

						if(attivita !== "0")
						{
							$(this).parent().removeClass('has-error');
							$(this).parent().addClass('has-success');
						}
					}

					if(name.includes("motivazione"))
					{
						var motivazione = $(this).val();

						if(motivazione == "")
						{
							$(this).parent().removeClass('has-success');
							$(this).parent().addClass('has-error');
							corretto = false;
						}

						if(motivazione !== "")
						{
							$(this).parent().removeClass('has-error');
							$(this).parent().addClass('has-success');
						}
					}
				}
			});
			return corretto;
		}

		function validaRimborsi()
		{
			var corretto = true;
			$('.calcolokm').not('.hidden').find(":input").each(function(){

				var name = $(this).attr("name");

				if(typeof name !== typeof undefined)
				{
					if(name.includes("data"))
					{
						var data = $(this).val();
						if(data == "")
						{
							$(this).parent().parent().removeClass('has-success');
							$(this).parent().parent().addClass('has-error');
							corretto = false;
						}

						if(data !== "")
						{
							$(this).parent().parent().removeClass('has-error');
							$(this).parent().parent().addClass('has-success');
						}

					}

					if(name.includes("partenza"))
					{
						var partenza = $(this).val()

						if(partenza == "")
						{
							$(this).parent().removeClass('has-success');
							$(this).parent().addClass('has-error');
							corretto = false;
						}

						if(partenza !== "")
						{
							$(this).parent().removeClass('has-error');
							$(this).parent().addClass('has-success');
						}

					}

					if(name.includes("arrivo"))
					{
						var arrivo = $(this).val();

						if(arrivo == "")
						{
							$(this).parent().removeClass('has-success');
							$(this).parent().addClass('has-error');
							corretto = false;
						}

						if(arrivo !== "")
						{
							$(this).parent().removeClass('has-error');
							$(this).parent().addClass('has-success');
						}
					}

					if(name.includes("attivita"))
					{
						var attivita = $(this).val();

						if(attivita == "0")
						{
							$(this).parent().removeClass('has-success');
							$(this).parent().addClass('has-error');
							corretto = false;
						}

						if(attivita !== "0")
						{
							$(this).parent().removeClass('has-error');
							$(this).parent().addClass('has-success');
						}
					}

					if(name.includes("motivazione"))
					{
						var motivazione = $(this).val();

						if(motivazione == "")
						{
							$(this).parent().removeClass('has-success');
							$(this).parent().addClass('has-error');
							corretto = false;
						}

						if(motivazione !== "")
						{
							$(this).parent().removeClass('has-error');
							$(this).parent().addClass('has-success');
						}
					}
				}
			});
			return corretto;
		}

		function time_diff(t1, t2) {
			var parts = t2.split(':');
			var partss = t1.split(':');
			var ritorno = parts[0] - partss[0];
			return ritorno;
			}
    </script>
@endpush