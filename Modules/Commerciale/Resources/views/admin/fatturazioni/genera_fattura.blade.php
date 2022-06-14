@php
    $azienda = get_azienda_dati();
    $importo_iva = 0;

    foreach($fatturazione->voci as $voce)
    {
        $importo_iva += clean_currency($voce->importo) * $voce->iva / 100;
    }
    $importo_iva = htmlentities(get_currency($importo_iva));
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
         <div class="col-xs-5">
            <p>
                <strong>{{ $azienda->ragione_sociale }}</strong>
                <br>
                <strong>P.Iva</strong> {{ $azienda->p_iva }}
            </p>
            <p>
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
                 <br>
                 <strong>Web</strong> <a href="{{$azienda->sito_web}}" target="_blank">{{$azienda->sito_web}}</a>
             </p>
             <p>
                 DATA
                 <br>
                 {{$fatturazione->data}}
             </p>
          </div>

          <table class="col-xs-6 {{ get_azienda_class('bg-info', 'bg-warning') }}">
              <tr>
                  <td valign="bottom" class="text-right" style="width: 50%;">
                  @if(empty($fatturazione->nota_di_credito_interna))
                    <strong>Fattura Nr.</strong>
                  @else
                    <strong>Nota di Credito Nr.</strong>
                  @endif

                  <hr class="border-bottom">

                  </td>
                  <td valign="bottom" style="width: 50%;">
                      {{$fatturazione->get_numero_fattura()}}{{$anno}}
                       <hr class="border-bottom">
                  </td>
              </tr>
              <tr>
                  <td valign="bottom" class="text-right">
                      <strong>Cliente</strong>
                      <hr class="border-bottom">
                  </td>
                  <td valign="bottom">
                      {{$fatturazione->cliente->ragione_sociale}}
                      <hr class="border-bottom">
                  </td>
              </tr>
              <tr>
                  <td valign="bottom" class="text-right">
                      <strong>{{(!empty($fatturazione->cliente->p_iva)) ? 'P.Iva' : 'Cod.Fiscale'}}</strong>
                      <hr class="border-bottom">
                  </td>
                  <td valign="bottom">
                      {{(!empty($fatturazione->cliente->p_iva)) ? $fatturazione->cliente->p_iva : $fatturazione->cliente->cod_fiscale}}
                      <hr class="border-bottom">
                  </td>
              </tr>
              <tr>
                  <td valign="bottom" valign="bottom" class="text-right">
                      <strong>Indirizzo</strong>
                      <hr class="border-bottom">
                  </td>
                  <td valign="bottom">
                      {{$fatturazione->indirizzo}}
                      <hr class="border-bottom">
                  </td>
              </tr>
              <tr>
                  <td valign="bottom" class="text-right">
                      <strong>Telefono</strong>
                      <hr class="border-bottom">
                  </td>
                  <td valign="bottom">
                      {!! (empty($fatturazione->cliente->telefono) ? '&nbsp;' : $fatturazione->cliente->telefono) !!}
                      <hr class="border-bottom">
                  </td>
              </tr>
              <tr>
                  <td> &nbsp;</td>
                  <td>&nbsp;</td>
              </tr>
              <tr>
                  <td valign="bottom" class="text-right">
                      <strong>Acconto</strong>
                      <hr class="border-bottom">
                  </td>
                  <td valign="bottom" class="text-right">
                      {!! htmlentities($fatturazione->acconto) !!}
                      <hr class="border-bottom">
                  </td>
              </tr>
              <tr>
                  <td valign="bottom" class="text-right">
                      <strong>Totale netto</strong>
                      <hr class="border-bottom">
                  </td>
                  <td valign="bottom" class="text-right">
                      {!! htmlentities($fatturazione->totale_netto) !!}
                      <hr class="border-bottom">
                  </td>
              </tr>
              <tr>
                  <td valign="bottom" class="text-right">
                      <strong>IVA</strong>
                      <hr class="border-bottom">
                  </td>
                  <td valign="bottom" class="text-right">
                      {!! htmlentities($fatturazione->iva) !!}%
                      <hr class="border-bottom">
                  </td>
              </tr>
              <tr>
                  <td valign="bottom" class="text-right">
                      <strong>Totale IVA</strong>
                      <hr class="border-bottom">
                  </td>
                  <td valign="bottom" class="text-right">
                      {!! $importo_iva !!}
                      <hr class="border-bottom">
                  </td>
              </tr>
              <tr>
                  <td valign="bottom" class="text-right">
                      <strong>Totale fattura</strong>
                      <hr class="border-bottom">
                  </td>
                  <td valign="bottom" class="text-right">
                      {!! htmlentities($fatturazione->totale_fattura) !!}
                      <hr class="border-bottom">
                  </td>
              </tr>
              <tr>
                  <td valign="bottom" class="text-right">
                      <strong>Totale importo dovuto</strong>
                      <hr class="border-bottom">
                  </td>
                  <td valign="bottom" class="text-right">
                      {!! htmlentities($fatturazione->totale_importo_dovuto) !!}
                      <hr class="border-bottom">
                  </td>
              </tr>
              <tr>
                  <td valign="bottom" class="text-right">
                      <strong>Termini del Pagamento</strong>
                      <hr class="border-bottom">
                  </td>
                  <td valign="bottom" class="text-right">
                      {{ config('commerciale.fatturazioni.n_giorni')[$fatturazione->n_giorni] }} GG {{ config('commerciale.fatturazioni.tipologia_pagamento_abbreviazione')[$fatturazione->tipo_pagamento] }}
                      <hr class="border-bottom">
                  </td>
              </tr>
              <tr>
                  <td valign="bottom" class="text-right">
                      <strong>Iva esigibile all'incasso</strong>
                      <hr class="border-bottom">
                  </td>
                  <td valign="bottom" class="text-right">
                      {!! config('wecore.sn')[$fatturazione->iva_esigibile] !!}
                      <hr class="border-bottom">
                  </td>
              </tr>
          </table>

          {{-- <div class="col-xs-3 text-right {{ get_azienda_class('bg-info', 'bg-warning') }}" style="padding: 5px 3px;">
              <div class="border-bottom-dotted strong">Fattura Nr.</div>
              <div class="border-bottom-dotted strong">Cliente</div>
              <div class="border-bottom-dotted strong">P.Iva</div>
              <div class="border-bottom-dotted strong">Indirizzo</div>
              <div class="border-bottom-dotted strong">Telefono</div>
              <br>
              <div class="border-bottom-dotted strong">Acconto</div>
              <div class="border-bottom-dotted strong">Totale netto</div>
              <div class="border-bottom-dotted strong">IVA</div>
              <div class="border-bottom-dotted strong">Totale IVA</div>
              <div class="border-bottom-dotted strong">Totale fattura</div>
              <div class="border-bottom-dotted strong">Totale importo dovuto</div>
              <div class="border-bottom-dotted strong">Termini del Pagamento</div>
              <div class="border-bottom-dotted strong">Iva esigibile all'incasso</div>
              <br>
          </div>
          <div class="col-xs-3 {{ get_azienda_class('bg-info', 'bg-warning') }}" style="padding: 5px 3px;">
              <div class="border-bottom-dotted">{{$fatturazione->get_numero_fattura()}}{{$anno}}</div>
              <div class="border-bottom-dotted">{{$fatturazione->cliente->ragione_sociale}}</div>
              <div class="border-bottom-dotted">{{(!empty($fatturazione->cliente->p_iva)) ? $fatturazione->cliente->p_iva : '&nbsp;'}}</div>
              <div class="border-bottom-dotted">{{$fatturazione->indirizzo}}</div>
              <div class="border-bottom-dotted">{{(empty($fatturazione->cliente->telefono) ? '&nbsp;' : $fatturazione->cliente->telefono)}}</div>
              <br>
              <div class="text-right">
                  <div class="border-bottom-dotted">{{ htmlentities($fatturazione->acconto) }}</div>
                  <div class="border-bottom-dotted">{{ htmlentities($fatturazione->totale_netto) }}</div>
                  <div class="border-bottom-dotted">{{ $fatturazione->iva }}%</div>
                  <div class="border-bottom-dotted">{{ $importo_iva }}</div>
                  <div class="border-bottom-dotted">{{ htmlentities($fatturazione->totale_fattura) }}</div>
                  <div class="border-bottom-dotted">{{ htmlentities($fatturazione->totale_importo_dovuto) }}</div>
                  <div class="border-bottom-dotted">{{ $fatturazione->n_giorni }} GG {{ config('commerciale.fatturazioni.tipologia_pagamento_abbreviazione')[$fatturazione->tipo_pagamento] }}</div>
                  <div class="border-bottom-dotted">{{ config('wecore.sn')[$fatturazione->iva_esigibile] }}</div>
              </div>
              <br>
          </div> --}}
      </div>

      <br>

      <div class="row">
          <table class="table border">
              <caption class="bg-grey text-center"><h5>DETTAGLIO</h5></caption>
              <tr class="{{ get_azienda_class('bg-primary', 'bg-orange') }}">
                  <th class="text-center text-white">DESCRIZIONE</th>
                  <th class="text-center text-white" style="width: 20%;">IVA</th>
                  <th class="text-right text-white" style="width: 20%;">IMPONIBILE</th>
                  <th class="text-right text-white" style="width: 20%;">IMPORTO</th>
              </tr>
              @foreach ($fatturazione->voci as $voce)
                  <tr>
                      <td>{{ $voce->descrizione }}</td>
                      <td align="center">{{ $voce->iva }}%</td>
                      <td class="text-right">{!! htmlentities($voce->importo) !!}</td>
                      <td class="text-right">{!! htmlentities($voce->importo_iva) !!}</td>
                  </tr>
              @endforeach
              <tr>
                  <td colspan="2" rowspan="3">
                      <strong>CIG</strong>: {{ $fatturazione->cig }}
                      <br>
                      <strong>Codice Univoco</strong>: {{ $fatturazione->codice_univoco }}
                  </td>
                  <td class="{{ get_azienda_class('bg-info', 'bg-warning') }} text-right">IMPONIBILE</td>
                  <td class="text-right" class="{{ get_azienda_class('bg-info', 'bg-warning') }}">{!! htmlentities($fatturazione->totale_netto) !!}</td>
              </tr>
              <tr class="{{ get_azienda_class('bg-info', 'bg-warning') }}">
                  <td class="text-right">IVA</td>
                  <td class="text-right">{!! $importo_iva !!}</td>
              </tr>
              <tr class="{{ get_azienda_class('bg-info', 'bg-warning') }}">
                  <td class="text-right">TOTALE FATTURA</td>
                  <td class="text-right">{!! htmlentities($fatturazione->totale_fattura) !!}</td>
              </tr>
          </table>
      </div>

      @if(!empty($fatturazione->note))
          <div class="row">
              <h5>NOTE</h5>
              <div class="border padding">
                  {{$fatturazione->note}}
              </div>
          </div>
      @endif

      <br>

      @if ($fatturazione->iva_erario == 1)
          <div class="row">
              <h5>IVA</h5>
              <div class="border padding">
                  L’IVA esposta in questa fattura deve essere versata all’erario dal destinatario ai sensi dell’art. 17-ter, DPR N. 633/72.
              </div>
          </div>
      @endif

      <br>

      <div class="row text-center">
          <h5 class="{{ get_azienda_class('bg-info', 'bg-warning') }}">S. E. & O.</h5>
      </div>

      <br>

      <div class="row text-center">
          <h5 class="{{ get_azienda_class('bg-info', 'bg-warning') }}">IBAN</h5>
          {{ $fatturazione->iban }}
      </div>
  </main>
@stop
