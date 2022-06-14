<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<HTML>
<HEAD>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<TITLE>Foglio Assegnazione</TITLE>
<STYLE type="text/css">

@page { margin: 0px;}

#page_1 { margin: -700px 0px -0px 38px; padding: 0px;border: none;width: 793px;}

#page_1 #p1dimg1 {position:absolute;top:0px;left:0px;z-index:-1;width:793px;height:1020px;}
#page_1 #p1dimg1 #p1img1 {width:793px;height:1020px;}

.dclr {clear:both;float:none;height:1px;margin:0px;padding:0px;overflow:hidden;}

.ft0{font: bold 11px 'Century Gothic';color: #0070c0;line-height: 15px;}
.ft1{font: 11px 'Century Gothic';color: #595959;line-height: 16px;}
.ft2{font: 13px 'Century Gothic';line-height: 17px;}
.ft3{font: 1px 'Century Gothic';line-height: 1px;}
.ft4{font: bold 12px 'Century Gothic';line-height: 16px;}
.ft5{font: bold 13px 'Century Gothic';color: #3f5461;line-height: 16px;}
.ft6{font: bold 13px 'Century Gothic';line-height: 23px;}
.ft7{font: 13px 'Century Gothic';line-height: 24px;}
.ft8{font: bold 13px 'Century Gothic';line-height: 16px;}
.ft9{font: bold 13px 'Century Gothic';line-height: 22px;}
.ft10{font: 13px 'Century Gothic';line-height: 23px;}
.ft11{font: 13px 'Century Gothic';margin-left: 10px;line-height: 17px;}
.ft12{font: 13px 'Century Gothic';margin-left: 10px;line-height: 23px;}
.ft13{font: 13px 'Century Gothic';margin-left: 11px;line-height: 24px;}
.ft14{font: bold 13px 'Century Gothic';color: #607a8a;line-height: 16px;}
.ft15{font: bold 11px 'Century Gothic';color: #607a8a;line-height: 15px;}
.ft16{font: 1px 'Century Gothic';line-height: 8px;}
.ft17{font: 12px 'Century Gothic';color: #607a8a;line-height: 15px;}
.ft18{font: 12px 'Century Gothic';color: #607a8a;line-height: 17px;}

.p0{text-align: left;padding-left: 0px;margin-top: 86px;margin-bottom: 0px;}
.p1{text-align: left;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
.p2{text-align: right;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
.p3{text-align: left;padding-left: 0px;margin-top: 54px;margin-bottom: 0px;}
.p4{text-align: justify;padding-left: 0px;padding-right: 75px;margin-top: 19px;margin-bottom: 0px;}
.p5{text-align: left;padding-left: 32%;margin-top: 26px;margin-bottom: 0px;}
.p6{text-align: justify;padding-left: 0px;padding-right: 75px;margin-top: 34px;margin-bottom: 0px;}
.p7{text-align: left;padding-left: 0px;margin-top: 27px;margin-bottom: 0px;}
.p8{text-align: left;padding-left: 0px;margin-top: 34px;margin-bottom: 0px;}
.p9{text-align: left;padding-left: 38px;margin-top: 7px;margin-bottom: 0px;}
.p10{text-align: left;padding-left: 62px;padding-right: 75px;margin-top: 8px;margin-bottom: 0px;text-indent: -24px;}
.p11{text-align: justify;padding-left: 62px;padding-right: 75px;margin-top: 0px;margin-bottom: 0px;text-indent: -24px;}
.p12{text-align: left;padding-left: 0px;margin-top: 29px;margin-bottom: 0px;}
.p13{text-align: center;padding-right: 0px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
.p14{text-align: center;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}

.td0{padding: 0px;margin: 0px;width: 503px;vertical-align: bottom;}
.td1{padding: 0px;margin: 0px;width: 125px;vertical-align: bottom;}
.td2{padding: 0px;margin: 0px;width: 625px;vertical-align: bottom;}
.td3{padding: 0px;margin: 0px;width: 86px;vertical-align: bottom;}
.td4{padding: 0px;margin: 0px;width: 0px;vertical-align: bottom;}
.td5{padding: 0px;margin: 0px;width: 179px;vertical-align: bottom;}
.td6{padding: 0px;margin: 0px;width: 231px;vertical-align: bottom;}
.td7{padding: 0px;margin: 0px;width: 103px;vertical-align: bottom;}

.tr0{height: 23px;}
.tr1{height: 24px;}
.tr2{height: 19px;}
.tr3{height: 20px;}
.tr4{height: 29px;}
.tr5{height: 9px;}
.tr6{height: 17px;}
.tr7{height: 8px;}
.tr8{height: 15px;}

.t0{width: 628px;margin-left: 0px;margin-top: 50px;font: 13px 'Century Gothic';}
.t1{width: 511px;margin-left: 0px;margin-top: 33px;font: 13px 'Century Gothic';}
.t2{width: 513px;margin-left: 0px;margin-top: 133px;font: 12px 'Century Gothic';color: #607a8a;}

body {margin-top: 0px;margin-left: 0px; font-family: century-gothic, sans-serif !important;}

</STYLE>
</HEAD>

@php 
$bene_title = $bene->marca.' - '.$bene->modello;
$bene_title = \Str::of($bene_title)->limit('30', '...');
@endphp
<BODY>
<img src="{{ public_path('assets/wecom_foglio_assegnazione_header.png') }}" width=793px>
<DIV id="page_1">
<DIV class="dclr"></DIV>
<TABLE cellpadding=0 cellspacing=0 class="t0">
<TR>
	<TD class="tr0 td0"><P class="p1 ft2">Viterbo, {{ $bene->data_assegnazione }}</P></TD>
	<TD class="tr0 td1"><P class="p2 ft2">All’attenzione di</P></TD>
</TR>
<TR>
	<TD class="tr1 td0"><P class="p1 ft3">&nbsp;</P></TD>
	<TD class="tr1 td1"><P class="p2 ft4">{{ $bene->assegnatario()->first()->full_name }}</P></TD>
</TR>
</TABLE>
<P class="p3 ft5">VERBALE DI CONCESSIONE IN USO E CONSEGNA {{ $bene_title }} aziendale</P>
<hr style="margin-left:0px; width:75%">
<P class="p4 ft7">La società <span style="white-space: nowrap;"><SPAN class="ft6">We-com</SPAN></span><SPAN class="ft6"> Srl</SPAN>, con sede in Viterbo, Via Papa Giovanni XXI n. 23, partita IVA 01446590554, nella veste del rappresentante legale Sig. Luca Provvedi, nato a Orvieto il 31/10/1980, codice fiscale PRVLCU80R31G148V.</P>
<P class="p5 ft8">CONCEDE IN USO E CONSEGNA</P>
<P class="p6 ft10">Al {{ $bene->assegnatario()->first()->profile()->first()->titolo }} <SPAN class="ft9">{{ $bene->assegnatario()->first()->full_name }} </SPAN>nato a {{ $bene->assegnatario()->first()->profile()->first()->comune_di_nascita }} il {{ $bene->assegnatario()->first()->profile()->first()->data_di_nascita }}, codice fiscale {{ $bene->assegnatario()->first()->profile()->first()->codice_fiscale }}</P>
<P class="p7 ft8">{{ $bene->marca }} - {{ $bene->modello }} Seriale n. {{ $bene->serial_number }}</P>
<P class="p8 ft2">alle seguenti condizioni:</P>
<P class="p9 ft2"><SPAN class="ft2">a)</SPAN><SPAN class="ft11">l’uso si intende autorizzato dal {{ $bene->data_assegnazione }};</SPAN></P>
<P class="p10 ft10"><SPAN class="ft2">b)</SPAN><SPAN class="ft12">i beni sopra descritti debbono servire esclusivamente per adempiere le mansioni lavorative assegnate a {{ $bene->assegnatario()->first()->full_name }};</SPAN></P>
<P class="p11 ft7"><SPAN class="ft2">c)</SPAN><SPAN class="ft13">{{ $bene->assegnatario()->first()->full_name }} si obbliga a conservare e a custodire i beni in oggetto con cura e massima diligenza, e a non destinarli ad altri usi che non siano quelli sopra previsti, a non cedere neppure temporaneamente l’uso dei beni sopra individuati a terzi, né a titolo gratuito né a titolo oneroso, nello stato attuale, salvo il normale deterioramento d’uso;</SPAN></P>
{{-- <P style="margin-top:15px;" class="p12 ft2">Letto, approvato e sottoscritto a Roma il {{ $bene->data_assegnazione }}</P> --}}
<img style="margin-top:100px;" src="{{ public_path('assets/wecom_foglio_assegnazione_footer.jpeg') }}" width=793px height=270px>
{{-- <div style="position:absolute;left:517px;top:865.40px" class="ft15"><span class="ft15">ISO 9001:2015</span></div>
<div style="position:absolute;left:140px;top:870.20px" class="ft13"><span class="ft13">Sede Legale</span></div>
<div style="position:absolute;left:295px;top:870.20px" class="ft13"><span class="ft13">Contatti</span></div>
<div style="position:absolute;left:505px;top:875.48px" class="ft15"><span class="ft15">SERIE N° IT-126441</span></div>
<div style="position:absolute;left:161.04px;top:894.40px" class="ft17"><span class="ft17">Via Papa Giovanni XXI, 23</span></div> 
<div style="position:absolute;left:318px;top:894.40px" class="ft17"><span class="ft17">Tel: +39 0761 1763771</span></div>
<div style="position:absolute;left:161.04px;top:905.44px" class="ft17"><span class="ft17">01100 Viterbo (VT)</span></div> 
<div style="position:absolute;left:318px;top:905.44px" class="ft17"><span class="ft17">Fax: +39 0761 1810143</span></div>
<div style="position:absolute;left:161.04px;top:916.48px" class="ft17"><span class="ft17">P.IVA 01446590554</span></div> 
<div style="position:absolute;left:318px;top:916.48px" class="ft17"><span class="ft17">E-mail: info@we-com.it</span></div> 
<div style="position:absolute;left:318px;top:927.52px" class="ft17"><span class="ft17">PEC: we-com@pec.it</span></div> 
<div style="position:absolute;left:318px;top:949.56px" class="ft17"><span class="ft17">Web: https://www.we-com.it</span></div> --}}
</DIV>
</BODY>
</HTML>

