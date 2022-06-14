@extends('layouts.master')

@section('content-header')
<h1> Modifica il Censimento Cliente di {{$censimentocliente->cliente()->first()->ragione_sociale}} </h1>
<ol class="breadcrumb">
	<li>
		<a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a>
	</li>
	<li>
		<a href="{{ route('admin.commerciale.censimentocliente.index') }}">{{ trans('commerciale::censimenticlienti.title.censimenticlienti') }}</a>
	</li>
	<li class="active">
		{{ trans('commerciale::censimenticlienti.title.edit censimentocliente') }}
	</li>
</ol>
@stop

@section('content')
{!! Form::open(['route' => ['admin.commerciale.censimentocliente.update', $censimentocliente->id], 'method' => 'put']) !!}

<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			@include('partials.form-tab-headers')
			<div class="tab-content">

				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#tab_1" data-toggle="tab">Censimento</a>
					</li>
					<li>
						<a href="#tab_3" data-toggle="tab">Analisi di Vendita</a>
					</li>
					<li>
						<a href="#tab_4" data-toggle="tab">Report viste</a>
					</li>
				</ul>
				<?php $i = 0; ?>
				@foreach (LaravelLocalization::getSupportedLocales() as $locale => $language)
				<?php $i++; ?>
				<div class="tab-pane {{ locale() == $locale ? 'active' : '' }}" id="tab_{{ $i }}">
					@include('commerciale::admin.censimenticlienti.partials.fields', ['lang' => $locale])

					<div class="box-footer">
						<a class="btn btn-info btn-flat" href="{{ route('admin.commerciale.censimentocliente.read', $censimentocliente->id) }}"><i class="fa fa-arrow-left"></i> Vai alla visualizzazione</a>
						<button type="submit" class="btn btn-success btn-flat"><i class="fa fa-floppy-o" aria-hidden="true"></i>
							Salva
						</button>
						<a class="btn btn-danger pull-right btn-flat" href="{{ route('admin.commerciale.censimentocliente.index')}}"><i class="fa fa-arrow-left"></i> Indietro</a>
					</div>
				</div>
				@endforeach
				<div class="tab-pane" id="tab_3">
					@include('commerciale::admin.censimenticlienti.partials.analisi')
					<div class="box-footer">
						<a class="btn btn-danger pull-right btn-flat" href="{{ route('admin.commerciale.censimentocliente.index')}}"><i class="fa fa-arrow-left"></i> Indietro</a>
					</div>
				</div>
				<div class="tab-pane" id="tab_4">
					@include('commerciale::admin.censimenticlienti.partials.reportviste')
					<div class="box-footer">
						<a class="btn btn-danger pull-right btn-flat" href="{{ route('admin.commerciale.censimentocliente.index')}}"><i class="fa fa-arrow-left"></i> Indietro</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
{!! Form::close() !!}

@stop

@section('footer')
<a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp;
@stop
@section('shortcuts')
<dl class="dl-horizontal">
	<dt>
		<code>
			b
		</code>
	</dt>
	<dd>
		{{ trans('core::core.back to index') }}
	</dd>
</dl>
@stop

@push('js-stack')
<script type="text/javascript">
		$( document ).ready(function() {
	$(document).keypressAction({
	actions: [
	{ key: 'b', route: "<?= route('admin.commerciale.censimentocliente.index') ?>" }
		]
		});
	});
</script>
<script>
	$(document).ready(function() {
		$('input[type="checkbox"].flat-blue, input[type="radio"].flat-blue').iCheck({
			checkboxClass : 'icheckbox_flat-blue',
			radioClass : 'iradio_flat-blue'
		});
	});
</script>
<script>
	$(document).ready(function() {
		hide_link_analisivendita();

		$('.update-id_state').change(function() {
			var id_segnalazione = $(this).attr('name').replace('stato_id[', '').replace(']', '');
			console.log(id_segnalazione + ' set stato ' + $(this).val());
			var token = $("input[name='_token']").val();

			$.post("{{ route('admin.commerciale.segnalazioneopportunita.updStato')}}", {
				_token : token,
				segnalazioneid : id_segnalazione,
				statoid : $(this).val()
			}).done(function(data) {

			});

		});



		$('.update-id_commerciale').on('focusin', function(){
		    console.log("Saving value " + $(this).val());
		    $(this).data('val', $(this).val());
		});


		$('.update-id_commerciale').change(function() {
			var id_segnalazione = $(this).attr('name').replace('commerciale_id[', '').replace(']', '');
			console.log(id_segnalazione + ' set commerciale ' + $(this).val());
			var token = $("input[name='_token']").val();
			var id_commerciale = $(this).val() ;

			$.post("{{route('admin.commerciale.segnalazioneopportunita.updCommerciale')}}", {
				_token : token,
				segnalazioneid : id_segnalazione,
				commercialeid : id_commerciale
			}).done(function(data) {
				hide_link_analisivendita();

				var resp = JSON.parse(data);
 				if (resp.response == 1) {
					//aggiornato il commerciale di riferimento e non vi è attività quindi procedo con la creazione del task 
					console.log('inserisco il task');
					$.post("{{ route('admin.tasklist.attivita.store')}}", {
						_token : token,
						// oggetto : 'Ingaggio Commerciale "' + resp.titolo + '" Cliente : '+ resp.cliente,
						oggetto : 'Ingaggio Commerciale "' + resp.titolo+'" ',
						categoria : 'Segnalazione Commerciale',
						richiedente_id : {{Auth::id()}} ,
					 	assegnatari_id : id_commerciale ,
						priorita : 5,
						durata_tipo : 1 ,
						cliente_id :  resp.cliente_id,
						stato : 0,
						segnalazioneopportunita_id: id_segnalazione
					}).done(function(datas) {
 						//console.log(datas);
					});




				} else if (resp.response == 2) { 
					//se lo stato è uguale a due vuol dire che esiste già un attivita quindi vado ad aggiornare il destinatario di  quella presente
					console.log('task aggiornato');				
				
				} else {
					//aggiornamento non riuscito
				}

			});

		});

	});
	function hide_link_analisivendita(){
		$('.update-id_commerciale').each(function() {
 			var id_segnalazione = $(this).attr('name').replace('commerciale_id[', '').replace(']', '');
 			if($(this).val() > 0){
 				$("#links_"+id_segnalazione).show();
 			}else{
 				$("#links_"+id_segnalazione).hide();
 			}

		});
	}
</script>
@endpush
