<div class="box-body">
	<div class="row">
		<div class="col-md-3">
			<div class="info-box bg-gray">
				<span class="info-box-icon bg-aqua"><i class="fa fa-building"></i></span>

				<div class="info-box-content">
					<span class="info-box-text">Censimento Cliente</span>
					<strong class="info-box-text"> <a href="{{ route('admin.commerciale.censimentocliente.read', $analisivendita->censimento_cliente->id) }}">{{ $analisivendita->censimento_cliente()->first()->cliente()->first()->ragione_sociale }}</a>
					<input type="hidden" name="censimento_id" value="{{ $analisivendita->censimento_cliente->id }}">
					</strong>
				</div>
				<!-- /.info-box-content -->
			</div>
		</div>
		<div class="col-md-9">
			<div class="col-md-8">
				{{ Form::weText('titolo', 'Oggetto', $errors, get_if_exist($analisivendita, 'titolo'), ['readonly' => 'readonly']) }}
			</div>
			<div class="col-md-4">
				{{ Form::weDate('data', 'Data', $errors, (!empty($analisivendita) ? get_if_exist($analisivendita, 'data') : date('d/m/Y')), ['readonly' => 'readonly']) }}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-9">
			{{ Form::weText('segnalazione[]', 'Segnalazioni Opportunità di provenienza', $errors, $segnalazioni_selected, ['id' => 'segnalazioni', 'readonly' => 'readonly']) }}
		</div>
		<div class="col-md-3">
			{{ Form::weText('commerciale_id', 'Commerciale assegnato', $errors, get_if_exist($analisivendita, 'commerciale')->full_name, ['readonly' => 'readonly']) }}
		</div>
	</div>

	{{-- Totali --}}
	<div class="row">
		<h3 style="margin-left:10px;"><strong>RIEPILOGO</strong></h3>
		<div class="row">
			<div class="col-md-4 col-sm-6 col-xs-12">
				<div class="info-box">
					<span class="info-box-icon bg-blue"><i class="fa fa-users"></i></span>
			
					<div class="info-box-content">
					<span class="info-box-text">Costo Risorse Interno</span>
					<span class="info-box-number">{{ get_currency($riepilogo['aree']['totali']['costo_interno']) }}</span>
					</div>
				</div>	
			</div>
			<div class="col-md-4 col-sm-6 col-xs-12">
				<div class="info-box">
					<span class="info-box-icon bg-blue"><i class="fa fa-users"></i></span>
			
					<div class="info-box-content">
					<span class="info-box-text">Importo Risorse Uscita</span>
					<span class="info-box-number">{{ get_currency($riepilogo['aree']['totali']['importo_vendita']) }}</span>
					</div>
				</div>	
			</div>
			<div class="col-md-4 col-sm-6 col-xs-12">
				<div class="info-box">
					<span class="info-box-icon {{ ($riepilogo['aree']['totali']['importo_vendita'] - $riepilogo['aree']['totali']['costo_interno']) >= 0 ? 'bg-green' : 'bg-red' }}"><i class="fa fa-eur"></i></span>
					<div class="info-box-content">
						<span class="info-box-text">Profitto Risorse</span>
						<span class="info-box-number totale-costi-totali">{{ get_currency($riepilogo['aree']['totali']['importo_vendita'] - $riepilogo['aree']['totali']['costo_interno']) }}</span>
					</div>
				</div>
		  	</div>
		</div>
		<div class="row">
			<div class="col-md-4 col-sm-6 col-xs-12">
				<div class="info-box">
					<span class="info-box-icon bg-blue"><i class="fa fa-shopping-cart"></i></span>
					<div class="info-box-content">
					<span class="info-box-text">Costi Fissi Interni</span>
					<span id="totale-costi-fissi" class="info-box-number">{{ get_currency($riepilogo['costi_fissi']['totali']['costo_interno']) }}</span>
					</div>
				</div>
			</div>
			<div class="col-md-4 col-sm-6 col-xs-12">
				<div class="info-box">
					<span class="info-box-icon bg-blue"><i class="fa fa-shopping-cart"></i></span>
					<div class="info-box-content">
					<span class="info-box-text">Importo Costi Fissi In Uscita</span>
					<span id="totale-costi-fissi" class="info-box-number">{{ get_currency($riepilogo['costi_fissi']['totali']['costo_uscita']) }}</span>
					</div>
				</div>
			</div>
			<div class="col-md-4 col-sm-6 col-xs-12">
				<div class="info-box">
					<span class="info-box-icon {{ ($riepilogo['costi_fissi']['totali']['costo_uscita'] - $riepilogo['costi_fissi']['totali']['costo_interno']) >= 0 ? 'bg-green' : 'bg-red' }}"><i class="fa fa-eur"></i></span>
					<div class="info-box-content">
						<span class="info-box-text">Profitto Costi Fissi</span>
						<span class="info-box-number totale-costi-totali">{{ get_currency($riepilogo['costi_fissi']['totali']['costo_uscita'] - $riepilogo['costi_fissi']['totali']['costo_interno']) }}</span>
					</div>
				</div>
		  	</div>
		</div>
		<div class="row">
			<div class="col-md-4 col-sm-6 col-xs-12">
				<div class="info-box">
					<span class="info-box-icon bg-blue"><i class="fa fa-eur"></i></span>
					<div class="info-box-content">
						<span class="info-box-text">Costo Interno</span>
						<span class="info-box-number totale-costi-totali">{{ get_currency($riepilogo['aree']['totali']['costo_interno'] + $riepilogo['costi_fissi']['totali']['costo_interno']) }}</span>
					</div>
				</div>
			</div>
			<div class="col-md-4 col-sm-6 col-xs-12">
				<div class="info-box">
					<span class="info-box-icon bg-blue"><i class="fa fa-eur"></i></span>
					<div class="info-box-content">
						<span class="info-box-text">Importo Di Vendita</span>
						<span class="info-box-number">{{ get_currency($riepilogo['aree']['totali']['importo_vendita'] + $riepilogo['costi_fissi']['totali']['costo_uscita']) }}</span>
					</div>
				</div>
			</div>
			<div class="col-md-4 col-sm-6 col-xs-12">
				<div class="info-box">
					<span class="info-box-icon {{ ($riepilogo['aree']['totali']['importo_vendita'] + $riepilogo['costi_fissi']['totali']['costo_uscita']) - ($riepilogo['costi_fissi']['totali']['costo_interno'] + $riepilogo['aree']['totali']['costo_interno']) >= 0 ? 'bg-green' : 'bg-red' }}"><i class="fa fa-eur"></i></span>
					<div class="info-box-content">
						<span class="info-box-text">Profitto</span>
						<span class="info-box-number totale-costi-totali">{{ get_currency(($riepilogo['aree']['totali']['importo_vendita'] + $riepilogo['costi_fissi']['totali']['costo_uscita']) - ($riepilogo['costi_fissi']['totali']['costo_interno'] + $riepilogo['aree']['totali']['costo_interno'])) }}</span>
					</div>
				</div>
		  	</div>
		</div>
	</div>
	<br>

	{{-- Tabella Aree --}}
	@if(!empty($riepilogo['aree']))
		<div class="box box-info box-solid">
			<div class="box-header with-border">
				<div class="row">
					<div class="col-md-12">
						<h3 class="no-margin">
							Riepilogo Aree Intervento
						</h3>
					</div>
				</div>
			</div>
			<div class="box-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered">
						<tr>
							<th style="width:40%">Area Intervento</th>
							<th style="width:20%" class="text-center">Ore</th>
							<th style="width:20%" class="text-center">Costo Interno</th>
							<th style="width:20%" class="text-center">Importo Di Vendita</th>
						</tr>
						@foreach($riepilogo['aree']['aree'] as $key => $item)
							<tr class="table">
								<td>
									{{ $item['nome'] }}
								</td>
								<td class="text-center">
									{{ $item['ore'] }}
								</td>
								<td class="text-center">
									<strong>{{ get_currency($item['costo_interno']) }}</strong>
								</td>
								<td class="text-center">
									<strong>{{ get_currency($item['importo_vendita']) }}</strong>
								</td>
							</tr>
						@endforeach
						<tr class="bg-aqua"> 
							<td colspan="1"><strong>TOTALE</strong></td>
							<td class="text-center"><h4 class="no-margin font-weight-bold">{{ $riepilogo['aree']['totali']['ore'] }}</h4></td>
							<td class="text-center"><h4 class="no-margin font-weight-bold">{{ get_currency($riepilogo['aree']['totali']['costo_interno']) }}</h4></td>
							<td class="text-center"><h4 class="no-margin font-weight-bold">{{ get_currency($riepilogo['aree']['totali']['importo_vendita']) }}</h4></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	@endif

	{{-- Tabella Figure --}}
	@if(!empty($riepilogo['figure']))
		<div class="box box-info box-solid">
			<div class="box-header with-border">
				<div class="row">
					<div class="col-md-12">
						<h3 class="no-margin">
							Riepilogo Risorse
						</h3>
					</div>
				</div>
			</div>
			<div class="box-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered">
						<tr>
							<th style="width:40%">Figura</th>
							<th style="width:20%" class="text-center">Ore</th>
							<th style="width:20%" class="text-center">Costo Interno</th>
							<th style="width:20%" class="text-center">Importo Di Vendita</th>
						</tr>
						@foreach($riepilogo['figure']['figure'] as $key => $item)
							<tr class="table">
								<td>
									{{ $item['nome'] }}
								</td>
								<td class="text-center">
									{{ $item['ore'] }}
								</td>
								<td class="text-center">
									<strong>{{ get_currency($item['costo_interno']) }}</strong>
								</td>
								<td class="text-center">
									<strong>{{ get_currency($item['importo_vendita']) }}</strong>
								</td>
							</tr>
						@endforeach
						<tr class="bg-aqua"> 
							<td colspan="1"><strong>TOTALE</strong></td>
							<td class="text-center"><h4 class="no-margin font-weight-bold">{{ $riepilogo['figure']['totali']['ore'] }}</h4></td>
							<td class="text-center"><h4 class="no-margin font-weight-bold">{{ get_currency($riepilogo['figure']['totali']['costo_interno']) }}</h4></td>
							<td class="text-center"><h4 class="no-margin font-weight-bold">{{ get_currency($riepilogo['figure']['totali']['importo_vendita']) }}</h4></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	@endif

	{{-- Costi fissi --}}
	@if(!empty($riepilogo['costi_fissi']))
		<div class="box box-info box-solid">
			<div class="box-header with-border">
				<div class="row">
					<div class="col-md-12">
						<h3 class="no-margin">Costi Fissi</h3>
					</div>
				</div>
			</div>
			<div class="box-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered">
						<tr>
							<th class="text-center">Descrizione</th>
							<th class="text-center" style="width: 10%;">Quantità</th>
							<th class="text-center">Costo Unitario</th>
							<th class="text-center">Costo Di Uscita</th>
							<th class="text-center">Marketplace</th>
							<th class="text-center" style="width: 10%;">Rincaro</th>
							<th style="width:20%" class="text-center" style="width: 20%;">Totale</th>
						</tr>
						@foreach($riepilogo['costi_fissi']['items'] as $item)
							<tr>
								<td>
									{{ get_if_exist($item, 'nome') }}
								</td>
								<td class="text-center">
									{{ get_if_exist($item, 'quantita') }}
								</td>
								<td class="text-center">
									{{ get_currency(get_if_exist($item, 'costo_unitario') )}}
								</td>
								<td class="text-center">
									{{ get_currency(get_if_exist($item, 'costo_di_uscita')) }}
								</td>
								<td class="text-center">
									@if(!empty(get_if_exist($item, 'link')))
										<a href="{{ get_if_exist($item, 'link') }}" target="_blank"><button class="btn btn-xs btn-flat"><i class="fa fa-external-link"> </i></button></a>
									@endif
								</td>
								<td class="text-center">
									{{ str_replace('.', ',', get_if_exist($item, 'rincaro')) }}
								</td>
								<td class="text-center">
									<strong class="no-margin">{{ get_currency(get_if_exist($item, 'costo_totale')) }}</strong>
								</td>
							</tr>
						@endforeach
						<tr class="bg-aqua">
							<td colspan="2"><strong>TOTALE</strong></td>
							<td class="text-center"><h4 class="no-margin font-weight-bold">{{ get_currency($riepilogo['costi_fissi']['totali']['costo_interno']) }}</h4></td>
							<td class="text-center"><h4 class="no-margin font-weight-bold">{{ get_currency($riepilogo['costi_fissi']['totali']['costo_uscita']) }}</h4></td>
							<td colspan="2"></td>
							<td class="text-center">
								<h4 class="no-margin font-weight-bold">{{ get_currency($riepilogo['costi_fissi']['totali']['costo_uscita']) }}</h4>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	@endif

	{{-- Canoni annuali --}}
	@if(!empty($analisivendita->canoni) && !empty($analisivendita->canoni->anni) && clean_currency(collect($analisivendita->canoni->anni)->first->canone->canone) > 0)
		<div class="box box-success box-solid">
			<div class="box-header with-border">
				<div class="row">
					<div class="col-md-6">
						<h3>Canoni Annuali</h3>
					</div>
					<div class="col-md-6 text-right">
						<strong>Anno inizio</strong>: {{ get_if_exist($analisivendita->canoni, 'anno_inizio') }}
						<br>
						<strong>Durata</strong>: {{ get_if_exist($analisivendita->canoni, 'durata') }} anni
					</div>
				</div>
			</div>
			<div class="box-body">
				<div class="row">
					@foreach($analisivendita->canoni->anni as $anno => $canone)
						<div class="col-md-2 canone">
							<strong>{{ $anno }}</strong>
							<br>
							<h4>{{ get_if_exist($canone, 'canone') }}</h4>
						</div>
					@endforeach
				</div>
			</div>
		</div>
	@endif

	{{-- Attivita --}}
	@if(!empty($riepilogo['attivita']))
		<div class="box box-info box-solid">
			<div class="box-header with-border">
				<div class="row">
					<div class="col-md-12">
						<h3 class="no-margin">Riepilogo Interventi</h3>
					</div>
				</div>
			</div>
			<div class="box-body">
				@foreach($riepilogo['attivita']['aree'] as $id_area => $area)
					<h4><strong>{{ get_if_exist($area, 'nome') }}</strong></h4>
					<hr>
					@foreach($area['gruppi'] as $gruppo)
						<h4 class="text-primary"><strong>{{ get_if_exist($gruppo, 'nome') }}</strong></h4>
						<div class="table-responsive">
							<table class="table table-striped table-bordered">
								<tr>
									<th class="text-center" style="width: 25%;">Figura</th>
									<th class="text-center" style="width: 25%;">Ore</th>
									<th class="text-center" style="width: 25%;">Costo Interno</th>
									<th class="text-center" style="width: 25%;">Importo Di Vendita</th>
								</tr>
								@foreach($gruppo['figure'] as $figura)
									<tr>
										<td>
											{{ get_if_exist($figura, 'nome') }}
										</td>
										<td class="text-center">
											{{ get_if_exist($figura, 'ore') }}
										</td>
										<td class="text-center">
											{{ get_currency((get_if_exist($figura, 'costo_interno')) )}}
										</td>
										<td class="text-center">
											{{ get_currency((get_if_exist($figura, 'importo_vendita'))) }}
										</td>
									</tr>
								@endforeach
								<tr class="bg-aqua">
									<td><strong>TOTALI</strong></td>
									<td class="text-center">
										<h4 class="no-margin font-weight-bold">{{ (get_if_exist($gruppo, 'ore') )}}</h4>
									</td>
									<td class="text-center">
										<h4 class="no-margin font-weight-bold">{{ get_currency((get_if_exist($gruppo, 'costo_interno')) )}}</h4>
									</td>
									<td class="text-center">
										<h4 class="no-margin font-weight-bold">{{ get_currency((get_if_exist($gruppo, 'importo_vendita')) )}}</h4>
									</td>
								</tr>
							</table>
						</div>
					@endforeach
				@endforeach
			</div>
		</div>
	@endif

</div>
	


@push('js-stack')
<script>
$(document).ready(function() {
	//SCRIPT PER IL CHECK AUTO
	$("input[auto-check]").change(function() {
		$(this).parent().parent().parent().find($('input[mixed]')).iCheck('check');
	});
});
</script>
@endpush
