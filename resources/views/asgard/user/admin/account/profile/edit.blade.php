@php
    $user = $currentUser;
	$rimb = json_decode($user->profile->approvatori_rimborsi);
	$fpm = json_decode($user->profile->approvatori_fpm);

	$num_app_rimborsi = 0;
	$num_app_fpm = 0;

	if(!empty($rimb))
	{
		$num_app_rimborsi = count($rimb);
	}

	if(!empty($fpm))
	{
		$num_app_fpm = count($fpm);
	}

	if(strtolower(session('azienda')) == "we-com")
	{
		$email_user = $user->profile->username."@we-com.it";
	}
	else
	{
		$email_user = $user->profile->username."@digitconsulting.it";
	}

@endphp

@extends('layouts.master')

@section('content-header')
<h1>
    {{ trans('user::users.title.edit-profile') }}
</h1>
<ol class="breadcrumb">
    <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
    <li class="active">{{ trans('user::users.breadcrumb.edit-profile') }}</li>
</ol>
@stop

@section('content')
{!! Form::open(['route' => ['admin.account.profile.update'], 'method' => 'put']) !!}
<div class="row">
    <div class="col-md-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
				<li class="active"><a href="#profile_tab" data-toggle="tab">Dashboard</a></li>
                <li class=""><a href="#password_tab" data-toggle="tab">{{ trans('user::users.tabs.new password') }}</a></li>
				<li class=""><a href="#autovetture_tab" data-toggle="tab">Le tue Autovetture</a></li>
				<li class=""><a href="#notifiche_tab" data-toggle="tab">Notifiche</a></li>
            </ul>
            <div class="tab-content">
				<div class="tab-pane active" id="profile_tab">
					<div class="row">
						<div class="col-md-12">
							<div class="box box-widget widget-user">
								<div class="widget-user-header bg-aqua-active">
									<h3 class="widget-user-username">{{ucwords($user->full_name)}}</h3>
									<h5 class="widget-user-desc">{{ucfirst($user->profile->incarico)}}</h5>
									<div class="row">
										<div class="col-md-4" style="color:black">
											<div class="info-box" style="box-shadow:1px 1px 3px #aaa;">
												<span class="info-box-icon bg-navy"><i class="fa fa-file-text"></i></span>
												<div class="info-box-content">
													<div style="display:flex;justify-content:space-between;">
														<span class="info-box-text">Contratto</span>
														@if(!empty($user->profile->matricola))
															<span class="info-box-text">Matricola: <b>{{$user->profile->matricola}}</b></span>
														@endif
													</div>
													<span class="info-box-number">{{ucfirst($user->profile->tipologia_di_contratto)}}</span>
													<span>Data Assunzione: <b>{{$user->profile->data_assunzione}}</b></span>
													<br>
													<span>Data Fine Contratto: <b>{{!empty($user->profile->fine_contratto) ? $user->profile->fine_contratto : "-"}}</b></span>
												</div>
											</div>
										</div>
										<div class="col-md-4" style="color:black">
											<div class="info-box" style="box-shadow:1px 1px 3px #aaa;">
												<span class="info-box-icon bg-navy"><i class="fa fa-envelope-o"></i></span>
												<div class="info-box-content">
													<span class="info-box-text">Contatti</span>
													<span class="info-box-number">{{$user->email}}</span>
													<span>Interno: <b>{{$user->profile->interno}}</b></span>
													@if(!empty($user->profile->num_telefono_aziendale))
														<br>
														<span>Numero Aziendale: <b>{{$user->profile->num_telefono_aziendale}}</b></span>
													@endif
												</div>
											</div>
										</div>
										<div class="col-md-4" style="color:black;margin-top:5px;">
											<div class="info-box" style="box-shadow:1px 1px 3px #aaa;">
												<span class="info-box-icon bg-navy"><i class="fa fa-building"></i></span>
												<div class="info-box-content">
													<span class="info-box-text">Generali</span>
													<span class="info-box-number">{{$user->profile->azienda}}</span>
													<span>Badge: <b>{{$user->profile->badge}}</b></span>
													@if(!empty($user->profile->num_telefono_aziendale))
														<br>
														<span>Sede: <b>{{ucfirst($user->profile->sede_partenza)}}</b></span>
													@endif
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4" style="color:black">
											<div class="info-box" style="box-shadow:1px 1px 3px #aaa;">
												<span class="info-box-icon bg-maroon"><i class="fa fa-user-circle-o"></i></span>
												<div class="info-box-content">
													<span class="info-box-text text-info">Responsabile</span>
													@if(!empty($user->responsabile_id))
														<span class="info-box-number">{{ucfirst($utenti->find($user->responsabile_id)->full_name)}}</span>
													@endif
												</div>
											</div>
										</div>
										<div class="col-md-4" style="color:black">
											<div class="info-box" style="box-shadow:1px 1px 3px #aaa;">
												<span class="info-box-icon bg-maroon">
													@if($num_app_fpm > 1)
														<i class="fa fa-users"></i>
													@else
														<i class="fa fa-user"></i>
													@endif
												</span>
												<div class="info-box-content">
													<span class="info-box-text text-info">
													@if($num_app_fpm == 1)
														Approvatore
													@else
														Approvatori
													@endif Ferie & Permessi</span>
													@if(!empty($num_app_fpm))
														@foreach($fpm as $key => $approvatore)
															<span class="info-box-number">
															@if($num_app_fpm > 1)
																{{$loop->index + 1}}° 
															@endif
																{{ucfirst($utenti->find($approvatore)->full_name)}}</span>
														@endforeach
													@endif
												</div>
											</div>
										</div>
										<div class="col-md-4" style="color:black">
											<div class="info-box" style="box-shadow:1px 1px 3px #aaa;">
												<span class="info-box-icon bg-maroon">
													@if($num_app_rimborsi > 1)
														<i class="fa fa-users"></i>
													@else
														<i class="fa fa-user"></i>
													@endif
												</span>
												<div class="info-box-content">
													<span class="info-box-text text-info">
													@if($num_app_rimborsi == 1)
														Approvatore
													@else
														Approvatori
													@endif Rimborsi</span>
													@if(!empty($num_app_rimborsi))
														@foreach($rimb as $key => $approvatore)
															<span class="info-box-number">
															@if($num_app_rimborsi > 1)
																{{$loop->index + 1}}° 
															@endif
																{{ucfirst($utenti->find($approvatore)->full_name)}}</span>
														@endforeach
													@endif
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12" >
											<div class="info-box" style="box-shadow:1px 1px 3px #aaa;">
												<span class="info-box-icon bg-aqua-active"><i class="fa fa-magic"></i></span>
												<div class="info-box-content" style="overflow-x: auto;">
													@if(!empty($user->profile->num_telefono_aziendale))
														<h3 style="color:black;margin:0;padding:0;">Inserire il Telefono Aziendale nella firma ?</h3>
														<br>
														<span><input id="toggle" checked=checked type="checkbox" data-toggle="toggle"></span>
													@endif
													<span>
														<button id="btn_copy_firma" style="margin-left:30px;" class="btn btn-danger" type="button"><i class="fa fa-copy"></i> Copia Firma</button>
														&nbsp;&nbsp;<span class="text-success hidden" id="msg_firma_copiata"><b>Copiata</b></span>
													</span>
													<hr>
														<div id="email_copy">
															<table style="width: 480px; font-size: 10pt; font-family: Arial, sans-serif; line-height:normal;" class="">
																<tbody class="">
																<tr class="">	
																<td style="padding-top:10px; padding-bottom:12px; width:160px; vertical-align:top" class="" valign="top">
																<a href="https://www.we-com.it" target="_blank" class=""><img alt="Logo" style="width:141px; height:auto; border:0;" src="https://www.we-com.it/firme/v4/we-com_logo.png" class="" width="141" border="0"></a>
																</td>
																<td style="padding-top:6px; padding-bottom:6px;  width:320px; " class="">
																<table class="">
																<tbody class="">
																	<tr class="">
																	<td style="font-size: 12pt; font-family: Arial, sans-serif; font-weight: bold; color: #3d3c3f;" class=""><span style="font-family: Arial, sans-serif; color:#3d3c3f" class="">{{ucfirst($user->full_name)}}</span>
																	</td>
																	</tr>
																	@if(!empty($user->profile->num_telefono_aziendale))
																		<tr id="num_tel_aziendale" class="">
																		<td style="font-size: 10pt; font-family: Arial, sans-serif; color: #000000;" class=""><span style="font-family: Arial, sans-serif; color:#000000" class="">mobile:<a href="tel:{{$user->profile->num_telefono_aziendale}}" style="font-family: Arial, sans-serif; color:#000000;text-decoration:none" class="">+39 {{$user->profile->num_telefono_aziendale}}</a></span></td>
																		</tr>
																	@endif
																	<tr class="">
																	<td style="font-size: 10pt; font-family: Arial, sans-serif; color: #000000;" class=""><span style="font-family: Arial, sans-serif; color:#000000" class="">email: </span><span style="font-family: Arial, sans-serif; color:#1793d2;" class=""><a href="mailto:{{$email_user}}" style="font-family: Arial, sans-serif; color:#1793d2;text-decoration:none" class="">{{$email_user}}</a></span></td>
																	</tr>
																	<tr class="">
																	<td style="font-size: 10pt; font-family: Arial, sans-serif; color: #000000;" class=""><span style="font-family: Arial, sans-serif; color:#000000" class="">Via Papa Giovanni XXI, 23</span></td>
																	</tr>
																	<tr class="">
																	<td style="font-size: 10pt; font-family: Arial, sans-serif; color: #000000;" class=""><span style="font-family: Arial, sans-serif; color:#000000" class="">01100 - Viterbo</span></td>
																	</tr>
																	<tr class="">
																	<td style="padding-top:6px;" class=""><span style="display:inline-block; height:26px;" class="">
																	<span class=""><a href="https://www.facebook.com/wecom.vt" target="_blank" class=""><img alt="Facebook icon" style="border:0; height:26px; width:26px" src="https://www.we-com.it/firme/v4/fb.png" class="" width="26" height="26" border="0"></a>&nbsp;&nbsp;</span>
																	<span class=""><a href="https://www.linkedin.com/company/we-com-s-r-l-" target="_blank" class=""><img alt="LinkedIn icon" style="border:0; height:26px; width:26px" src="https://www.we-com.it/firme/v4/ln.png" class="" width="26" height="26" border="0"></a>&nbsp;&nbsp;</span>
																	</span> 
																	</td>
																	</tr>
																</tbody>
																</table>
																</td>
																</tr>
																<tr class="">
																<td colspan="2" style="border-top:1px solid; border-top-color:#1793d2; width: 480px; padding-top:8px;  font-family:Arial,sans-serif; font-size: 10px; color:#9b9b9b; text-align:justify;" class=""><span style="font-family: Arial, sans-serif; color:#9b9b9b" class="">Questa e-mail, nonché qualsiasi file allegato alla presente, è destinata esclusivamente ai destinatari indicati in indirizzo o a chi sia stato da quelli autorizzato. Se avete ricevuto per errore questa e-mail, vi chiedo cortesemente di avvisarmi immediatamente e di distruggere permanentemente l’originale e qualsiasi copia della presente nonché qualsiasi stampa di questa.</span></td>
																</tr>
																</tbody>
															</table>
														</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
                <div class="tab-pane" id="password_tab">
                    <div class="box-body">
                        <h4>{{ trans('user::users.new password setup') }}</h4>
                        <div class="row">
                            <div class="col-md-6">
                                {{ Form::normalInputOfType('password', 'password', trans('user::users.form.new password'), $errors) }}
                            </div>
                            <div class="col-md-6">
                                {{ Form::normalInputOfType('password', 'password_confirmation', trans('user::users.form.new password confirmation'), $errors) }}
                            </div>
                        </div>

                        <div class="callout callout-warning">
                          <h4><i class="icon fa fa-warning"> </i> ATTENZIONE!</h4>
                          <p>La nuova password deve rispettare i seguenti criteri:</p>
                          <ul>
                            <li>min. 8 caratteri</li>
                            <li>almeno una lettera maiuscola</li>
                            <li>almeno un carattere speciale</li>
                            <li>almeno un numero</li>
                          </ul>
                        </div>
                    </div>
					<div class="box-footer">
                    	<button type="submit" class="btn btn-primary btn-flat">{{ trans('core::core.button.update') }}</button>
                	</div>
                </div>
				<div class="tab-pane" id="autovetture_tab">
					<div class="box-body">
						<div class="row">
							<div class="col-md-4">
								<button class="btn btn-info" data-toggle="modal" data-target="#autovettureModal" type="button"><i class="fa fa-car"></i> Aggiungi Autovettura</button>
							</div>
						</div>
						<br>
						@foreach ($autovetture as $autovettura )
						<div class="row">
							<div class="col-md-3">
								{!! Form::weText("auto[$autovettura->id][targa]",'Targa',$errors ,$autovettura->targa) !!}
							</div>
							<div class="col-md-4">
								{!! Form::weText("auto[$autovettura->id][modello]",'Modello',$errors ,$autovettura->modello) !!}
							</div>
							<div class="col-md-3">
								{!! Form::weCurrency("auto[$autovettura->id][costo_km]",'Costo Kilometrico (Tabella ACI)',$errors ,$autovettura->costo_km) !!}
							</div>
							<div class="col-md-2">
								<br>
								<a class="btn btn-danger" href="{{route('admin.account.profile.autovetture.delete',$autovettura->id)}}"><i class="fa fa-trash"></i></a>
							</div>
						</div>
						@endforeach
					</div>
					<div class="box-footer">
                    	<button type="submit" class="btn btn-primary btn-flat">{{ trans('core::core.button.update') }}</button>
                	</div>
				</div>
				<div class="tab-pane" id="notifiche_tab">
					<div class="box-body">
						<div class="box box-primary box-shadow">
							<div class="box-body">
								<a class="btn btn-md btn-primary" style="margin-bottom:12px;" href="{{ route('admin.account.profile.notifiche.markasread') }}">Segna come lette</a>
								<div class="table-responsive">
									<table class="table table-striped">
									<tr>
										<th>Tipologia</th>
										<th>Data</th>
										<th>Letta</th>
									</tr>
									<tbody>
										@foreach ($notifiche as $notifica)
											<tr>
												<td> {{ !empty($notifica->data['tipologia']) ? $notifica->data['tipologia'] : '' }}</td>
												<td> {{ get_date_hour_ita($notifica->created_at) }} </td>
												<td> {{ get_date_hour_ita($notifica->read_at) }} </td>
											</tr>
										@endforeach
									</tbody>
									</table>
								</div>
							</div>
							{{ $notifiche->links() }}
						</div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
{!! Form::close() !!}

<div class="modal fade" id="autovettureModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Inserisci un'Autovettura</h4>
			</div>
			<div class="modal-body">
			{!! Form::open(['route' => ['admin.account.profile.autovetture.create'], 'method' => 'post']) !!}
				<div class="row">
					<div class="col-md-3">
						{!! Form::weText('targa','Targa',$errors ,null) !!}
					</div>
					<div class="col-md-4">
						{!! Form::weText('modello','Modello',$errors ,null) !!}
					</div>
					<div class="col-md-3">
						{!! Form::weCurrency('costo_km','Costo Kilometrico (Tabella ACI)',$errors ,0) !!}
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Salva</button>
			</div>
			{!! Form::close() !!}
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->


@stop
@section('footer')
    <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp;
@stop
@section('shortcuts')
@stop


@push('js-stack')
<script>
function copyToClipboard(text) {
    var sampleTextarea = document.createElement("textarea");
    document.body.appendChild(sampleTextarea);
    sampleTextarea.value = text; //save main text in it
    sampleTextarea.select(); //select textarea contenrs
    document.execCommand("copy");
    document.body.removeChild(sampleTextarea);
}

$( document ).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
    $('input[type="checkbox"].flat-blue, input[type="radio"].flat-blue').iCheck({
        checkboxClass: 'icheckbox_flat-blue',
        radioClass: 'iradio_flat-blue'
    });

	$('#toggle').change(function() {
		if($('#toggle').parent().attr("class").includes('btn-primary'))
		{
			//checked
			$("#num_tel_aziendale").attr('style','display');
		}
		else
		{
			//uncheck
			$("#num_tel_aziendale").attr('style','display:none');
		}
    })

	$("#btn_copy_firma").click(function(){
		var cpy = $("#email_copy").html();
		copyToClipboard(cpy);
		
		$("#msg_firma_copiata").removeClass('hidden');

		$("#msg_firma_copiata").fadeIn(1500);

		$( "#msg_firma_copiata" ).fadeOut(1500, function() {
			$("#msg_firma_copiata").addClass('hidden');
		});
	})




});
</script>
@endpush
