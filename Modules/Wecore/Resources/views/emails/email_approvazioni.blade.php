@php
$stati = [1 => 'Ferie',2=>'Permesso',3=>'Malattia',4=>'Rimborso Trasferte',5=>'Rimborso Chilometrico'];
$mesi = [1=>"Gennaio" , 2=>"Febbraio", 3=>"Marzo", 4=>"Aprile", 5=>"Maggio", 6=>"Giugno", 7=>"Luglio", 8=>"Agosto", 9=>"Settembre", 10=>"Ottobre", 11=>"Novembre", 12=>"Dicembre"];
@endphp
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background: #f1f1f1; margin: 0 auto; padding: 0; height: 100%; width: 100%;">
<head>
    <meta charset="utf-8"> <!-- utf-8 works for most cases -->
    <meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
    <meta name="x-apple-disable-message-reformatting">  <!-- Disable auto-scale in iOS 10 Mail entirely -->
    <title></title> <!-- The title tag shows in email notifications, like Android 4.4. -->

    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700" rel="stylesheet">

    <!-- CSS Reset : BEGIN -->
    <style>
@media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
  u ~ div .email-container {
    min-width: 320px !important;
  }
}
@media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
  u ~ div .email-container {
    min-width: 375px !important;
  }
}
@media only screen and (min-device-width: 414px) {
  u ~ div .email-container {
    min-width: 414px !important;
  }
}
</style>

    <!-- CSS Reset : END -->

    <!-- Progressive Enhancements : BEGIN -->
    <style>
@media screen and (max-width: 500px) {}
</style>


</head>

<body width="100%" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background: #f1f1f1; font-family: 'Poppins', sans-serif; font-weight: 400; font-size: 15px; line-height: 1.8; color: rgba(0,0,0,.4); mso-line-height-rule: exactly; background-color: #f1f1f1; margin: 0 auto; height: 100%; width: 100%; padding: 0;">
	<center style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; width: 100%; background-color: #f1f1f1;">
    <div style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; display: none; font-size: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
      &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
    </div>
    <div style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; max-width: 600px; margin: 0 auto;" class="email-container">
    	<!-- BEGIN BODY -->
      <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0; border-collapse: collapse; table-layout: fixed; margin: 0 auto;">
      	<tr style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
          <td valign="top" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background: #367fa9; padding: 1em 2.5em 0 2.5em; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" class="bg_info">
          	<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0; border-collapse: collapse; table-layout: fixed; margin: 0 auto;">
          		<tr style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
          			{!! $header !!}
          		</tr>
          	</table>
          </td>
	      </tr><!-- end tr -->
				<tr style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
          <td valign="middle" class="hero bg_white" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background: #ffffff; position: relative; z-index: 0; padding: 2em 0 4em 0; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0; border-collapse: collapse; table-layout: fixed; margin: 0 auto;">
            	<tr style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
            		<td style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding: 0 2.5em; text-align: center; padding-bottom: 3em; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" align="center">
            			<div class="text" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: rgba(0,0,0,.3);">
            				<h2 style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-family: 'Poppins', sans-serif; margin-top: 0; color: #000; font-size: 34px; margin-bottom: 0; font-weight: 200; line-height: 1.4;">{{$stati[$content->tipologia]}}<br style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">{{ucwords($content->user->fullname)}}</h2>
            			</div>
            		</td>
            	</tr>
            	<tr style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
					<td style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; text-align: center; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" align="center">
						@if($content->stato == 1)
							<div class="text-author" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; bordeR: 1px solid rgba(0,0,0,.05); max-width: 50%; margin: 0 auto; padding: 2em;">
								<h3 class="name" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-family: 'Poppins', sans-serif; color: #000000; margin-top: 0; font-weight: 400; margin-bottom: 0;">Approvata</h3>
							</div>
						@elseif($content->stato == 2)
							<div class="text-author" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; bordeR: 1px solid rgba(0,0,0,.05); max-width: 50%; margin: 0 auto; padding: 2em;">
								<h3 class="name" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-family: 'Poppins', sans-serif; color: #000000; margin-top: 0; font-weight: 400; margin-bottom: 0;">Bocciata</h3>
							</div>
						@else
							@if($content->tipologia == 1 || $content->tipologia == 2 || $content->tipologia == 3)
								<div class="text-author" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; bordeR: 1px solid rgba(0,0,0,.05); max-width: 50%; margin: 0 auto; padding: 2em;">
									@if(!empty($content->meta))
										@foreach ($content->meta as $chiave => $valore )
											<h3 class="name" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-family: 'Poppins', sans-serif; color: #000000; margin-top: 0; font-weight: 400; margin-bottom: 0;">Tipo Permesso: {{ucfirst($valore)}}</h3>
										@endforeach
									@endif
									<h3 class="name" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-family: 'Poppins', sans-serif; color: #000000; margin-top: 0; font-weight: 400; margin-bottom: 0;">Note: {{ucfirst($content->note)}}</h3>
									<div style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; display: grid; grid-template-columns: 1fr 1fr 1fr;">
										<div style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
											<p style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">Data Inizio</p>
											<p style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">{{ucfirst(read_data_richieste($content->from,$content->tipologia))}}</p>
										</div>
										<div style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;"></div>
										<div style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
											<p style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">Data Fine</p>
											<p style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">{{ucfirst(read_data_richieste($content->to,$content->tipologia))}}</p>
										</div>
									</div>
								</div>
							@endif
							@if($content->tipologia == 4 || $content->tipologia == 5)
								<div class="text-author" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; bordeR: 1px solid rgba(0,0,0,.05); max-width: 50%; margin: 0 auto; padding: 2em;">
									<h3 class="name" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-family: 'Poppins', sans-serif; color: #000000; margin-top: 0; font-weight: 400; margin-bottom: 0;">
									Le è stata inoltrata una richiesta di Rimborso Trasferte da {{ucfirst($content->user->full_name)}} in data {{ucfirst(read_data_richieste($content->from,$content->tipologia))}} per il mese di <strong>{{$mesi[$content->mese]}} {{$content->anno}}</strong>
									@if($content->tipologia == 5)
									 ,per il percorso svolto con la propria auto <strong>{{ucfirst($content->modello)}}</strong> che ha un costo pari a <strong>{{$content->costo_km}}</strong> al km
									@endif
									.
									<br>Note: {{$content->note}}
									</h3>
								</div>
							@endif
						@endif
					</td>
				</tr>
				@if($content->tipologia == 4 || $content->tipologia == 5)
				<tr style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
					<td style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
						<p style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; text-align: center;">
							<a href="{{route('admin.account.richieste.read',$content->id)}}" class="btn btn-warning" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; text-decoration: none; padding: 10px 15px; display: inline-block; border-radius: 5px; background: #3c8dbc; color: #ffffff;">&#128064; Gestisci</a>
						</p>
					</td>
				</tr>
				@else
				<tr style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
					<td style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
						<p style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; text-align: center;">
						@if($content->stato == 1)
								<a href="{{route('admin.account.richieste.read',$content->id)}}" class="btn btn-success" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; text-decoration: none; padding: 10px 15px; display: inline-block; border-radius: 5px; background: #00a65a; color: #ffffff;">&#128077; Approvata</a>
						@elseif($content->stato == 2)
								<a href="{{route('admin.account.richieste.read',$content->id)}}" class="btn btn-danger" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; text-decoration: none; padding: 10px 15px; display: inline-block; border-radius: 5px; background: #dd4b39; color: #ffffff;">&#128078; Rifiutata</a>
						@else
							<a href="{{route('admin.account.richieste.approva',$content->id)}}" class="btn btn-success" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; text-decoration: none; padding: 10px 15px; display: inline-block; border-radius: 5px; background: #00a65a; color: #ffffff;">&#128077; Approva</a>&nbsp;&nbsp;&nbsp;<a href="{{route('admin.account.richieste.rifiuta',$content->id)}}" class="btn btn-danger" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; text-decoration: none; padding: 10px 15px; display: inline-block; border-radius: 5px; background: #dd4b39; color: #ffffff;">&#128078; Rifiuta</a>
						@endif
						</p>
					</td>
				</tr>
				@endif
            </table>
          </td>
	      </tr><!-- end tr -->
      <!-- 1 Column Text + Button : END -->
      </table>
      <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0; border-collapse: collapse; table-layout: fixed; margin: 0 auto;">
      	<tr style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
          <td valign="middle" class="bg_light footer email-section" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background: #f7fafa; padding: 2.5em; border-top: 1px solid rgba(0,0,0,.05); color: rgba(0,0,0,.5); mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
            <table style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0; border-collapse: collapse; table-layout: fixed; margin: 0 auto;">
				<tr style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
					{!! $footer !!}
              	</tr>
            </table>
          </td>
        </tr><!-- end: tr -->
      </table>

    </div>
  </center>
</body>
</html>