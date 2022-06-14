@extends('layouts.master')

@php
	$colori = [0=>'bg-white',1=>'bg-green',2=>'bg-red'];
	$testo = [0=>"In Attesa" ,1=>'Approvata',2=>'Rifiutata'];
	$icona = [0=>"fa fa-clock-o" ,1=>'fa fa-thumbs-up',2=>'fa fa-thumbs-down'];
	$icone_fpm = [1=>'fa fa-plane',2=>'fa fa-user-secret',3=>'fa fa-user-md',4=>'fa fa-eur',5=>'fa fa-car'];
@endphp

@section('content-header')
    <h1>
        Visualizza Richiesta {{$tipologie_richieste[$richiesta->tipologia]}}
    </h1>
@stop
@section('content')
<div class="section">
	<div class="box box-solid">
			@if($richiesta->tipologia == 1 || $richiesta->tipologia== 2 || $richiesta->tipologia == 3 )
			<form action="{{route('admin.account.richieste.update',[$richiesta->id])}}" method="post">
				@csrf
				<div class="box-body contenitore">
						<div class="row">
							<div class="col-md-4">
								<div class="info-box">
									<span class="info-box-icon bg-blue"><i class="{{$icone_fpm[$richiesta->tipologia]}}"></i></span>
									<div class="info-box-content">
										<span class="info-box-text">{{$tipologie_richieste[$richiesta->tipologia]}}</span>
										<span class="info-box-number">{{ucfirst($richiesta->user->full_name)}}</span>
										@if($richiesta->tipologia == 2)
											@foreach ($richiesta->meta as $chiave => $valore )
												<span>Tipo Permesso:</span> <strong>{{$valore}}</strong>
											@endforeach
										@endif
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="info-box">
									<span class="info-box-icon bg-purple"><i class="fa fa-calendar"></i></span>
									<div class="info-box-content">
										<span>Da:</span> <strong>{{ucfirst(read_data_richieste($richiesta->from,$richiesta->tipologia))}}</strong>
										<br>
										<br>
										<span>A:&nbsp;&nbsp;</span> <strong>{{ucfirst(read_data_richieste($richiesta->to,$richiesta->tipologia))}}</strong>
									</div>
								</div>
							</div>
							@if($richiesta->tipologia == 3 && $richiesta->user_id == $current_user)
								<div class="col-md-4">
									<div class="info-box">
									<span class="info-box-icon bg-navy"><i class="fa fa-ticket"></i></span>
										<div class="info-box-content">
												{!! Form::weText('note' , 'Note / Numero Protocollo Malattia', $errors ,$richiesta->note) !!}
										</div>
									</div>
								</div>
							@else
							<div class="col-md-4">
									<div class="info-box">
									<span class="info-box-icon bg-navy"><i class="fa fa-comment-o"></i></span>
										<div class="info-box-content">
											<span>Note:</span> <strong>{{$richiesta->note}}</strong>
										</div>
									</div>
								</div>
							@endif		
						</div>
						<div class="row">
							@foreach(json_decode($richiesta->user->profile->approvatori_fpm) as $k => $approvatore)
								<div class="col-md-4">
									<div class="info-box">
										<span class="info-box-icon {{$colori[$richiesta->approvazioni($approvatore)->stato]}}">
											@if($richiesta->stato == 2 && $richiesta->approvazioni($approvatore)->stato == 0)
												<i class="fa fa-times"></i>
											@else
												<i class="{{$icona[$richiesta->approvazioni($approvatore)->stato]}}"></i>
											@endif
										</span>
										<div class="info-box-content">
											@if($richiesta->tipologia == 3)
												<span class="info-box-text">Notificata</span>
											@elseif($richiesta->stato == 2 && $richiesta->approvazioni($approvatore)->stato == 0)
												<span class="info-box-text">N.D</span>
											@else
												<span class="info-box-text">{{$testo[$richiesta->approvazioni($approvatore)->stato]}}</span>
											@endif
											<span class="info-box-number">{{$utenti[$approvatore]}}</span>
										</div>
									</div>
								</div>
							@endforeach
						</div>
					</div>
				</div>
				<div class="box-footer">
					@if($richiesta->tipologia == 3 && $richiesta->user_id == $current_user)
						<button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Modifica</button>
					@endif
					@if(in_array($current_user ,json_decode($richiesta->user->profile->approvatori_fpm)) && $richiesta->tipologia != 3)
						@if($richiesta->approvazioni($current_user)->stato == 0)
							<a class="btn btn-success btn-flat" href="{{route('admin.account.richieste.approva',$richiesta->id)}}"><i class="fa fa-thumbs-up"></i> Approva</a>
							<a class="btn btn-danger btn-flat" href="{{route('admin.account.richieste.rifiuta',$richiesta->id)}}"><i class="fa fa-thumbs-down"></i> Boccia</a>
						@endif
						@if($richiesta->approvazioni($current_user)->stato == 1)
							<a class="btn btn-danger btn-flat" href="{{route('admin.account.richieste.rifiuta',$richiesta->id)}}"><i class="fa fa-thumbs-down"></i> Boccia</a>
						@endif
						@if($richiesta->approvazioni($current_user)->stato == 2)
							<a class="btn btn-success btn-flat" href="{{route('admin.account.richieste.approva',$richiesta->id)}}"><i class="fa fa-thumbs-up"></i> Approva</a>
						@endif
					@endif
					<a class="btn btn-warning btn-flat" href="{{route('admin.account.richieste.index',['tab'=>$richiesta->tipologia])}}"><i class="fa fa-arrow-left"></i> Indietro</a>
				</div>
			</form>
			@endif
			@if($richiesta->tipologia == 4)
			<div class="box-body contenitore">
				<div class="row">
					<div class="col-md-4">
						<div class="info-box">
							<span class="info-box-icon bg-navy"><i class="fa fa-euro"></i></span>
							<div class="info-box-content">
								<span class="info-box-text">Totale</span>
								<span id="tot_da_pagare" class="info-box-number">{{$richiesta->totale}}</span>
							</div>
						</div>
					</div>
				</div>
					<div class="info-box">
						<span class="info-box-icon bg-green"><i class="fa fa-map-signs"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Percorsi Effettuati</span>
							@foreach ($richiesta->vociTrasferte as $indice => $trasferta)
								<div class="col-md-12">
									<div class="col-md-2">
										<span>Data: <strong>{{$trasferta->data}}</strong></span>
									</div>
									<div class="col-md-3">
										<span>Tipologia: <strong>{{$tipi_trasferte[$trasferta->tipologia]}}</strong></span>
									</div>
									<div class="col-md-2">
										<span>Importo: <strong>{{$trasferta->importo}}</strong></span>
									</div>
									<div class="col-md-3">
										<span>Attività: <strong>{{$attivita[$trasferta->attivita_id]}}</strong></span>
									</div>
									<div class="col-md-2">
										<span>Note: <strong>{{$trasferta->note}}</strong></span>
									</div>
								</div>
							@endforeach
							<div class="row"></div>
						</div>
					</div>
					<div class="row">
						@foreach(json_decode($utente->profile->approvatori_rimborsi) as $k => $approvatore)
						<div class="col-md-4">
							<div class="info-box">
								<span class="info-box-icon {{$colori[$richiesta->approvazioni($approvatore)->stato]}}"><i class="{{$icona[$richiesta->approvazioni($approvatore)->stato]}}"></i></span>
								<div class="info-box-content">
									<span class="info-box-text">{{$testo[$richiesta->approvazioni($approvatore)->stato]}}</span>
									<span class="info-box-number">{{$utenti[$approvatore]}}</span>
								</div>
							</div>
						</div>
						@endforeach
					</div>
			</div>
			<div class="box-footer">
				@if($richiesta->tipologia == 3 && $richiesta->user_id == $current_user)
					<button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Modifica</button>
				@endif
				@if(in_array($current_user ,json_decode($richiesta->user->profile->approvatori_rimborsi)) && $richiesta->tipologia != 3)
					@if($richiesta->approvazioni($current_user)->stato == 0)
						<a class="btn btn-success btn-flat" href="{{route('admin.account.richieste.approva',$richiesta->id)}}"><i class="fa fa-thumbs-up"></i> Approva</a>
						<a class="btn btn-danger btn-flat" href="{{route('admin.account.richieste.rifiuta',$richiesta->id)}}"><i class="fa fa-thumbs-down"></i> Boccia</a>
					@endif
					@if($richiesta->approvazioni($current_user)->stato == 1)
						<a class="btn btn-danger btn-flat" href="{{route('admin.account.richieste.rifiuta',$richiesta->id)}}"><i class="fa fa-thumbs-down"></i> Boccia</a>
					@endif
					@if($richiesta->approvazioni($current_user)->stato == 2)
						<a class="btn btn-success btn-flat" href="{{route('admin.account.richieste.approva',$richiesta->id)}}"><i class="fa fa-thumbs-up"></i> Approva</a>
					@endif
				@endif
				<a class="btn btn-warning btn-flat" href="{{route('admin.account.richieste.index',['tab'=>$richiesta->tipologia])}}"><i class="fa fa-arrow-left"></i> Indietro</a>
			</div>
			@endif
			@if($richiesta->tipologia == 5)
			<div class="box-body contenitore">
				<div class="row">
					<div class="col-md-4">
						<div class="info-box">
							<span class="info-box-icon bg-aqua"><i class="fa fa-eur"></i></span>
							<div class="info-box-content">
							<span class="info-box-text">Totale</span>
							<span id="tot_rimb_spese" class="info-box-number">{{$richiesta->totale}}</span>
							</div>
					
						</div>
					</div>
					<div class="col-md-8">
							<div class="info-box">
								<span class="info-box-icon bg-blue">
									<i class="fa fa-car"></i>
								</span>
								<div class="info-box-content">
									<span class="info-box-text">Autovettura  (<strong>{{$richiesta->modello}}</strong>)</span>
									<span id="autovettura_sel" class="info-box-number">{{$richiesta->targa}}</span>
									<span class="info-box-number">{{$richiesta->costo_km}} (Costo Km)</span>
								</div>
							</div>
						</div>
					</div>
				<div class="info-box">
					<span class="info-box-icon bg-green"><i class="fa fa-map-signs"></i></span>
					<div class="info-box-content">
						<span class="info-box-text"><strong>Percorsi Effettuati</strong></span>
						<br>
						@foreach ($richiesta->vociKm as $indice => $km)
							<div class="col-md-12">
								<div class="col-md-2">
									<span>Data: <strong>{{$km->data}}</strong></span>
								</div>
								<div class="col-md-2">
									<span>Partenza: <strong>{{ucfirst($km->data)}}</strong></span>
								</div>
								<div class="col-md-2">
									<span>Arrivo: <strong>{{ucfirst($km->arrivo)}}</strong></span>
								</div>
								<div class="col-md-1">
									<span>A/R: <strong>{{$km->ar == 1 ? 'Si' :'No'}}</strong></span>
								</div>
								<div class="col-md-1">
									<span>km: <strong>{{$km->km}}</strong></span>
								</div>
								<div class="col-md-2">
									<span>Attività: <strong>{{$attivita[$km->attivita_id]}}</strong></span>
								</div>
								<div class="col-md-2">
									<span>Note: <strong>{{$km->note}}</strong></span>
								</div>
							</div>
						@endforeach
					</div>
					<div class="row"></div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="info-box">
							<span class="info-box-icon {{$colori[$richiesta->stato]}}"><i class="{{$icona[$richiesta->stato]}}"></i></span>
							<div class="info-box-content">
								<span class="info-box-text">Stato</span>
								<span class="info-box-number">{{$testo[$richiesta->stato]}}</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="box-footer">
				@if($richiesta->tipologia == 3 && $richiesta->user_id == $current_user)
					<button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Modifica</button>
				@endif
				@if(in_array($current_user ,json_decode($richiesta->user->profile->approvatori_rimborsi)) && $richiesta->tipologia != 3)
					@if($richiesta->approvazioni($current_user)->stato == 0)
						<a class="btn btn-success btn-flat" href="{{route('admin.account.richieste.approva',$richiesta->id)}}"><i class="fa fa-thumbs-up"></i> Approva</a>
						<a class="btn btn-danger btn-flat" href="{{route('admin.account.richieste.rifiuta',$richiesta->id)}}"><i class="fa fa-thumbs-down"></i> Boccia</a>
					@endif
					@if($richiesta->approvazioni($current_user)->stato == 1)
						<a class="btn btn-danger btn-flat" href="{{route('admin.account.richieste.rifiuta',$richiesta->id)}}"><i class="fa fa-thumbs-down"></i> Boccia</a>
					@endif
					@if($richiesta->approvazioni($current_user)->stato == 2)
						<a class="btn btn-success btn-flat" href="{{route('admin.account.richieste.approva',$richiesta->id)}}"><i class="fa fa-thumbs-up"></i> Approva</a>
					@endif
				@endif
				<a class="btn btn-warning btn-flat" href="{{route('admin.account.richieste.index',['tab'=>$richiesta->tipologia])}}"><i class="fa fa-arrow-left"></i> Indietro</a>
			</div>
			@endif
	</div>
</div>
@endsection
