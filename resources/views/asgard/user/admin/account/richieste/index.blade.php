@extends('layouts.master')

@php
$icona = [0=>"fa fa-clock-o" ,1=>'fa fa-thumbs-up',2=>'fa fa-thumbs-down'];
$icone_fpm = [1=>'fa fa-plane',2=>'fa fa-user-secret',3=>'fa fa-user-md',4=>'fa fa-eur',5=>'fa fa-car'];
$tipologie_richieste2 = $tipologie_richieste;
unset($tipologie_richieste2[-1]);
@endphp

@section('content-header')
    <h1>
        Richieste
    </h1>
@stop

@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="row">
			<div class="btn-group pull-right" style="margin: 0 15px 15px 0;">
				<a data-toggle="modal" data-target="#richiesteModal" class="btn btn-primary btn-flat">
					<i class="fa fa-plus"> </i> Nuova Richiesta
				</a>
			</div>
		</div>
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				@foreach ($tipologie_richieste2 as $index => $tipo )
					<li class="{{($index == request('tab') ? 'active' : '')}}"><a href="{{route('admin.account.richieste.index',['tab'=>$index])}}" aria-expanded="false"><i class="{{$icone_fpm[$index]}}"></i>&nbsp;&nbsp;{{$tipo}}</a></li>
				@endforeach
			</ul>
			<div class="box box-primary">
				<div class="box-header">
					<section class="bg-gray filters">
						{!! Form::open(['route' => ['admin.account.richieste.index'], 'method' => 'get']) !!}
							<div class="row">
								<div class="col-md-10">
									<div class="row">
										<div class="col-md-3">
											{!! Form::weSelectSearch('stato' , 'Stato', $errors , $stato_richiesta , $request->stato) !!}
										</div>
										<div class="col-md-3">
											{!! Form::weText('anno' , 'Anno', $errors,!empty($request->anno) ? $request->anno : date('Y') )!!}
										</div>
									</div>
								</div>
								<div class="col-md-2 text-right">
									{!! Form::weSubmit('Cerca') !!}
									<a href="{{route('admin.account.richieste.index',['tab'=>request('tab')])}}" class="btn btn-default btn-flat btn-reset">Svuota</a>
								</div>
							</div>
							<input type="hidden" name="order[by]" value="{{ request('order')['by'] }}">
							<input type="hidden" name="order[sort]" value="{{ request('order')['sort'] }}">
							<input type="hidden" name="tab" value="{{ request('tab') }}">
						{!! Form::close() !!}
					</section>
				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<div class="table-responsive">
						<table class="data-table table table-bordered table-hover">
							<thead>
								<tr>
								{!! order_th('user_id', 'Utente') !!}
								{!! order_th('stato', 'Stato') !!}
								{!! order_th('tipologia', 'Tipologia') !!} 
								{!! order_th('from', 'Da') !!}
								{!! order_th('to', 'a') !!}
								{!! order_th('note', 'Note') !!}
								{!! order_th('created_at', 'Data Creazione') !!}
								</tr>
							</thead>
							<tbody>
							@if(!empty($richieste))
								@foreach($richieste as $richiesta)
								<tr class="{{$richiesta->draft == 1 ? 'bg-warning' : ($richiesta->stato == 1 ? 'bg-success' : ($richiesta->stato == 2 ? 'bg-danger' : ''))}}">
									@if($richiesta->draft == 1)
										<td><a href="{{ route('admin.account.richieste.bozza', [$richiesta->id]) }}">{{ $richiesta->user->full_name}}</a></td>
										<td><a href="{{ route('admin.account.richieste.bozza', [$richiesta->id]) }}"><i class="fa fa-exclamation"></i> Da Inviare</a></td>
										<td><a href="{{ route('admin.account.richieste.bozza', [$richiesta->id]) }}"><i class="{{ $icone_fpm[$richiesta->tipologia]}}"></i> {{ $tipologie_richieste[$richiesta->tipologia] }}</a></td>
										<td><a href="{{ route('admin.account.richieste.bozza', [$richiesta->id]) }}">{{ read_data_richieste($richiesta->from,$richiesta->tipologia) }}</a></td>
										<td><a href="{{ route('admin.account.richieste.bozza', [$richiesta->id]) }}">{{ read_data_richieste($richiesta->to,$richiesta->tipologia) }}</a></td>
										<td><a href="{{ route('admin.account.richieste.bozza', [$richiesta->id]) }}">@if($richiesta->tipologia == 3)<strong>{{ $richiesta->note }}</strong>@else {{ $richiesta->note }} @endif</a></td>
										<td><a href="{{ route('admin.account.richieste.bozza', [$richiesta->id]) }}">{{ read_data_richieste($richiesta->created_at,2) }}</a></td>
									@else
										<td><a href="{{ route('admin.account.richieste.read', [$richiesta->id]) }}">{{ $richiesta->user->full_name}}</a></td>
										<td><a href="{{ route('admin.account.richieste.read', [$richiesta->id]) }}"><i class="{{ $icona[$richiesta->stato] }}"></i> {{$stato_richiesta[$richiesta->stato]}}</a></td>
										<td><a href="{{ route('admin.account.richieste.read', [$richiesta->id]) }}"><i class="{{ $icone_fpm[$richiesta->tipologia]}}"></i> {{ $tipologie_richieste[$richiesta->tipologia] }}</a></td>
										<td><a href="{{ route('admin.account.richieste.read', [$richiesta->id]) }}">{{ read_data_richieste($richiesta->from,$richiesta->tipologia) }}</a></td>
										<td><a href="{{ route('admin.account.richieste.read', [$richiesta->id]) }}">{{ read_data_richieste($richiesta->to,$richiesta->tipologia) }}</a></td>
										<td><a href="{{ route('admin.account.richieste.read', [$richiesta->id]) }}">@if($richiesta->tipologia == 3)<strong>{{ $richiesta->note }}</strong>@else {{ $richiesta->note }} @endif</a></td>
										<td><a href="{{ route('admin.account.richieste.read', [$richiesta->id]) }}">{{ read_data_richieste($richiesta->created_at,2) }}</a></td>
									@endif
								</tr>
								@endforeach
							@endif
							</tbody>
							<tfoot>
								
							</tfoot>
						</table>
						<!-- /.box-body -->
						<!-- Pagination -->
						<div class="text-right pagination-container">
						{{ $richieste->links() }}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
 @include('core::partials.delete-modal')
<div class="modal fade" id="richiesteModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Seleziona</h4>
			</div>
			{!! Form::open(['route' => ['admin.account.richieste.seleziona.create'], 'method' => 'post']) !!}
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						{!! Form::weSelectSearch('tipologia' , 'Tipologia', $errors , $tipologie_richieste2,request('tab'),['id'=>'seleziona_tipo']) !!}
					</div>
				</div>
				<div class="row macchina {{request('tab') != 5 ? 'hidden' : ''}}">
					<div class="col-md-12">
						@if(!empty($macchine_possedute))
							{!! Form::weSelectSearch('autovettura' , 'Autovettura', $errors , $macchine_possedute,null) !!}
						@else
							<h3 class="text-red">Devi Prima Inserire un'Autovettura dal tuo profilo </h3>
							<a href="{{route('admin.account.profile.edit')}}" >Inserisci Autovettura</a>
						@endif
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button id="apply" type="submit" class="btn btn-primary">Procedi <i class="fa fa-check"></i></button>
			</div>
			{!! Form::close() !!}
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<section class="content">
	<h4>Legenda Colori : </h4>
	<div class="row">
		<div class="col-md-1 bg-warning" style="height:20px;">  </div>
		<div class="col-md-11">Da Inviare</div>
	</div>
	<div class="row">
		<div class="col-md-1 bg-success" style="height:20px;">  </div>
		<div class="col-md-11">Approvata</div>
	</div>
	<div class="row">
		<div class="col-md-1 bg-danger" style="height:20px;">  </div>
		<div class="col-md-11">Rifiutata</div>
	</div>
</section>
@stop

@push('js-stack')
<script>
	$( document ).ready(function() {
		$("#seleziona_tipo").change(function(){
			var selezionato = $(this).val();
			if(selezionato == 5)
			{
				$(".macchina").removeClass("hidden");
				@if(empty($macchine_possedute))
					$("#apply").addClass("disabled");
				@endif
			}
			else
			{
				$(".macchina").addClass("hidden");
				$("#apply").removeClass("disabled");
			}
		})
	})
</script>
@endpush
