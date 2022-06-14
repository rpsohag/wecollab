@php
$gruppi = (empty($gruppi)) ? [] : $gruppi;
$attivita_list = [''] + $ordinativo->attivita->pluck('oggetto', 'id')->toArray();
@endphp

<div class="">
	@foreach ($procedure as $procedura)
	<div class="row">
		<div class="col-md-12">
			<caption>
				<h3><strong>{!! $procedura->titolo !!}</strong></h3>
			</caption>
		</div>
	</div>
	@foreach ($procedura->aree as $area)
	@php	$chiusa = true; @endphp	
		@foreach ($area->attivita  as $gruppo )
			
		 	@php
			$giornate = $gg_ordinativi[$gruppo->id];
			$interventi_sum = $ordinativo->interventi_sum_by_gruppo($gruppo->id);
			if(get_if_exist($giornate, 'quantita') > 0){
				 $chiusa = false;
			} 
			@endphp
	 	@endforeach

	<div class="box-header with-border  {{ ($chiusa) ? 'collapsed-box' : '' }} ">
		<h4>{!! $area->titolo !!}</h4>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool {{ ($chiusa) ? 'collapsed' : '' }}" data-widget="collapse"  data-toggle="collapse" data-target="#collapse{{$area->id}}" aria-expanded="{{ ($chiusa) ? 'false' : 'true'}}">
				<i class="fa fa-{{ ($chiusa) ? 'plus' : 'minus' }}"></i>
			</button>
		</div>
	</div>
	<div class="box-body" >
		<div class="box box-white box-solid {{ ($chiusa) ? 'collapse' : 'collapse in' }}  " id="collapse{{$area->id}}">

			@foreach ($area->attivita  as $gruppo )
			@php
			$giornate = $gg_ordinativi[$gruppo->id];
			$interventi_sum = $ordinativo->interventi_sum_by_gruppo($gruppo->id)
			@endphp
			<div class="col-md-3">
				<div class="box box-info box-solid {{ (get_if_exist($giornate, 'quantita') > 0) ? '' : 'collapsed-box' }}" >
					<div class="box-header with-border">
						<h3 class="box-title">{{ $gruppo->nome }}</h3>

						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse">
								<i class="fa {{ (get_if_exist($giornate, 'quantita') > 0) ? 'fa-minus' : 'fa-plus' }}"></i>
							</button>
						</div>
						<!-- /.box-tools -->
					</div>
					<!-- /.box-header -->
					<div class="box-body text-center">
						<div class="row">
							<div class="col-md-5">
								{{ Form::weInt('giornate['.$gruppo->id.'][quantita]', 'Quantità', $errors, get_if_exist($giornate, 'quantita'), ['class' => "form-control text-center", 'placeholder' => '']) }}
							</div>
							<div class="col-md-7">
								{{ Form::weSelect('giornate['.$gruppo->id.'][tipo]', 'Tipo', $errors, config('commerciale.interventi.tipi'), get_if_exist($giornate, 'tipo')) }}
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								{{ Form::weInt('giornate['.$gruppo->id.'][quantita_gia_effettuate]', 'Giornate/Ore già effettuate', $errors, get_if_exist($giornate, 'quantita_gia_effettuate'), ['class' => "form-control text-center", 'placeholder' => '']) }}
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6 border-right">
								<div class="description-block">
									<h5 class="description-header text-success">Effettuate</h5>
									<span class="description-text">{{ $interventi_sum }}</span>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="description-block">
									<h5 class="description-header text-warning">Residue</h5>
									<span class="description-text"> {{ get_if_exist($giornate, 'quantita_residue') ? $giornate->quantita_residue : 0 }} </span>
								</div>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-md-12">
								{{ Form::weSelectSearch('giornate['.$gruppo->id.'][attivita]', 'Attività', $errors, $attivita_list, get_if_exist($giornate, 'attivita'), ['style' => 'width: 100%;']) }}
							</div>
						</div>
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->
			</div>
			@endforeach
		</div>
	</div>
	@endforeach
	<hr/>
	@endforeach
	{{-- <div style="margin-left:10px; margin-bottom:12px;"><a type="button" class="btn btn-flat btn-primary" href="{{ route('admin.commerciale.ordinativo.importa.interventi', $ordinativo->offerta->id) }}"><i class="fa fa-upload"> </i> Importa Interventi</a></div> --}}
</div>
