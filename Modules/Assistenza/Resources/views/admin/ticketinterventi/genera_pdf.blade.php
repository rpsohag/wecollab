@php
$azienda = get_azienda_dati();
$cnt = 0;
$somma_giornate = 0;
$vocis = '';

$tipologie_intervento = config('assistenza.ticket_intervento.tipologie');
$settori = config('assistenza.ticket_intervento.settori');
$intervento_tipi = config('commerciale.interventi.tipi');

$area_intervento = $ticket->ordinativo->giornate->where('gruppo_id', $ticket->gruppo_id)->first();



$intervento_tipo = !empty($area_intervento) ? $intervento_tipi[$area_intervento->tipo] : '';

$ticket->voci_all = $ticket->giornate();

/*
$ntik_expl = explode('_' , $ticket -> n_di_intervento);
//dd($ntik_expl);
$ntik_expl[0] = str_pad($ntik_expl[0] , 4,'0', STR_PAD_LEFT);
//dd($ntik_expl);
$new = implode('-',$ntik_expl);
//dd($new);


$array_diviso = explode('-' , $new);
//dd($array_diviso);
$str1 = $array_diviso[1];
//dd($str1);
$str2 = $array_diviso[0];
//dd($str2);
$risultato = $str1.'-'.$str2;
//dd($risultato);
*/

$risultato = $ticket->numero_ticket();


foreach ($ticket->voci_all as $voce)
{
  $vocis = $voce->id;
  $somma_giornate += $voce->quantita;
}

//dd($ticket);

@endphp

@extends('layouts.master_pdf')

@section('content')


<style>
  hr.border-bottom {
    margin: 0;
    padding: 0;
    border: none;
    border-bottom: 1px dotted;
  }
</style>

<main class="container">
  <div class="row">
    <div style="padding:0px;" class="col-xs-6 text-left">
      <p>
        <strong>{{ $azienda->ragione_sociale }}</strong>
        <br>
        <strong>P.Iva</strong> {{ $azienda->p_iva }}
        <br>
        {{ $azienda->indirizzo }}, {{ $azienda->numero_civico }}
        <br>
        {{ $azienda->cap }} {{ $azienda->citta }} ({{ $azienda->provincia }})
      </p>
      <p>
        <strong>Telefono</strong> {{$azienda->telefono}}
        <br>
        <strong>Fax</strong> {{$azienda->fax}}
      </p>
      <p>
        <strong>E-mail</strong> <a href="mailto:{{$azienda->email}}" target="_blank">{{$azienda->email}}</a>
         -
        <strong>Web</strong> <a href="{{$azienda->sito_web}}" target="_blank">{{$azienda->sito_web}}</a>
      </p>
    </div>
    <div style="padding:0px;" class="col-xs-1"></div>

    <table style="padding:0px;" class="col-xs-5 {{ get_azienda_class('bg-info', 'bg-warning') }}">
      <tr>
        <td valign="bottom" class="text-right">
          <strong>Cliente</strong>
          <hr class="border-bottom">
        </td>
        <td valign="bottom">
          {{$ticket->cliente->ragione_sociale}}
          <hr class="border-bottom">
        </td>
      </tr>
      <tr>
        <td valign="bottom" class="text-right">
          <strong>{{(!empty($ticket->cliente->p_iva)) ? 'P.Iva' : 'Cod.Fiscale'}}</strong>
          <hr class="border-bottom">
        </td>
        <td valign="bottom">
          {{ strtoupper(!empty($ticket->cliente->p_iva) ? $ticket->cliente->p_iva : $ticket->cliente->cod_fiscale) }}
          <hr class="border-bottom">
        </td>
      </tr>
      <tr>
        <td valign="bottom" class="text-right">
          <strong>Indirizzo</strong>
          <hr class="border-bottom">
        </td>
        <td valign="bottom">
          @if(!empty($ticket->cliente->indirizzi()) && !empty($ticket->cliente->indirizzi()->first()))
              {{$ticket->cliente->indirizzi()->first()->indirizzo_completo}}
          @endif
          <hr class="border-bottom">
        </td>
      </tr>
      <tr>
        <td valign="bottom" class="text-right">
          <strong>Telefono</strong>
          <hr class="border-bottom">
        </td>
        <td valign="bottom">
          {!! (empty($ticket->cliente->indirizzi()->first()->telefono) ? '&nbsp;' :$ticket->cliente->indirizzi()->first()->telefono) !!}
          <hr class="border-bottom">
        </td>
      </tr>
    </table>
  </div>
  <br>
  <div class="row">
    <h5 class="{{ get_azienda_class('bg-info', 'bg-warning') }}">RAPPORTO DI INTERVENTO DI ASSISTENZA TECNICA N <strong>{{$ticket->codice_ticket}}</strong> DEL {{get_date_ita($ticket->data)}}</h5>
    <div style="padding:0" class="col-xs-6 text-left">
      Numero di intervento: <strong>{{ $risultato }}</strong>
    </div>
    <div style="padding:0" class="col-xs-6 text-right">
      Data Documento: <strong>{{get_date_ita($ticket->data)}}</strong>
    </div>
  </div>
  <br>
  <div class="row">
    <h5 class="{{ get_azienda_class('bg-info', 'bg-warning') }}">MOTIVO D'INTERVENTO / DESCRIZIONE DEL RAPPORTO</h5>
    <div style="padding:0" class="col-xs-12">
      @if($ticket->formazione == 1)
        Tipo: <strong>{{$tipologie_intervento[$ticket->tipologia_id]}}</strong>
      @endif
      {{-- Settore: --}}
      @if(!empty($ticket->materiale_consegnato))
        <br>
        Materiale Consegnato: <strong>{{$ticket->materiale_consegnato}}</strong>
      @endif
      @if(!empty($ticket->area_di_intervento_id) && !empty($ticket->procedura_id))<!--qui-->
        <br>
        Attività: <strong>{{$ticket->procedura->titolo}} - {{$ticket->area->titolo}} - {{$ticket->gruppo->nome}}</strong>
      @endif
      @if(!empty($ticket->note))
        <br>
        Note: <strong>{{$ticket->note}}</strong>
      @endif
    </div>
  </div>
  <br>
  @if(!empty($vocis))
  <div class="row">
    <h5 class="text-center {{ get_azienda_class('bg-info', 'bg-warning') }}">STORICO INTERVENTI</h5>
    <table class="table border">
      <tr class="{{ get_azienda_class('bg-primary', 'bg-orange') }}">
        <th class="text-center text-white" style="width: 10%;">Step</th>
        <th class="text-center text-white" style="width: 50%;">Descrizione</th>
        <th class="text-center text-white" style="width: 30%;">Data</th>
        <th class="text-center text-white" style="width: 10%;">{{ $intervento_tipo }} Lavorate</th>
      </tr>
      @foreach ($ticket->voci as $voce)
      <tr>
        <td class="text-center">{{ ++$cnt }}</td>
        <td>
          <p>{{ $voce->descrizione }}</p>
        </td>
        <td class="text-center">{{ get_date_ita($voce->data_intervento) }}</td>
        <td class="text-center">{{ $voce->quantita }}</td>
      </tr>
      @endforeach
    </table>
  </div>
  @endif
  <br>
  <div class="row">
    <div style="padding:0" class="border padding">
      Si attesta che i servizi di cui al presente intervento sono avvenuti in piena osservanza di quanto previsto dal D.lgs 196/2003 in materia
      di sicurezza e protezione dei dati personali.
    </div>
  </div>
  <div class="row" style="margin-top:125px;">
    <h5 class="text-center {{ get_azienda_class('bg-info', 'bg-warning') }}">CONVALIDA</h5>
    <div style="padding:0" class="col-xs-6 text-left">
      Firma del Tecnico
      <br><br><br>---------------------------------------------
    </div>
    <div style="padding:0" class="col-xs-6 text-right">
      Timbro e Firma Cliente
      <br><br><br>---------------------------------------------
    </div>
  </div>

</main>
@stop
