@php
    $fatturazione = (empty($fatturazione)) ? '' : $fatturazione;

    if(!empty($ordinativo)){
        $cliente = !empty($ordinativo->cliente()) ? $ordinativo->cliente() : '';
        $cliente_indirizzi = !empty($ordinativo->cliente()) ? $ordinativo->cliente()->indirizzi->pluck('indirizzo_completo','indirizzo_completo')->toArray() : [];
    } else {
        $cliente = '';
        $cliente_indirizzi = array();
    }

    $iva = (get_if_exist($fatturazione, 'iva') ) ? get_if_exist($fatturazione, 'iva') : config('commerciale.offerte.iva');
    $iva_0 = config('commerciale.fatturazioni.iva_tipi');

    $iva_natura = $iva_0;
    $iva_natura[0] = '';

    $attivita_svolte = config('commerciale.fatturazioni.attivita_svolte');
    $azienda = get_azienda_dati();

    $c_voci = 1;
@endphp

@empty($fatturazione)
    <input type="hidden" id="numero_nota_di_credito_interna" value="{{ $fattura_numero_nota_di_credito_interna }}">
    <input type="hidden" id="numero_nota_di_credito_interna_codice" value="{{ $fattura_numero_nota_di_credito_interna_codice }}">
    <input type="hidden" id="numero_fattura" value="{{$fattura_numero}}">
    <input type="hidden" id="numero_fattura_fepa" value="{{$fattura_numero_fepa}}">
    <input type="hidden" id="numero_fattura_valore" value="{{$fattura_numero_next}}">
    <input type="hidden" id="numero_fattura_fepa_valore" value="{{$fattura_numero_fepa_next}}">
    <input type="hidden" id="n_fattura" name="n_fattura" value="{{ $fattura_numero_next }}">

    @if(!empty($fatturazione_scadenza))
      <input type="hidden" name="fatturazione_scadenza_id" value="{{ get_if_exist($fatturazione_scadenza, 'id') }}">
    @endif
@endempty

<!-- title row -->
<div class="row">
    <div class="col-xs-12">
        <h2 class="page-header">
            <i class="fa fa-globe"></i> {{ session('azienda') }}
            <small class="pull-right">{{ Form::weDate('data', 'Data fatturazione *', $errors, (empty(get_if_exist($fatturazione, 'data'))) ? date('d/m/Y') : $fatturazione->data) }}</small>
        </h2>
    </div>
    <div class="col-md-3">
        {{ Form::weSelect('macrocategoria', 'Macrocategoria', $errors, $macrocategorie, get_if_exist($fatturazione, 'macrocategoria')) }}
    </div>
    <!-- /.col -->
</div>
<!-- info row -->
<div class="row invoice-info">
    <div class="col-sm-5 invoice-col">
        <h4 class="row">
            <span class="col-sm-5">
                Numero fattura:
                <br>
                <strong class="numero_fattura">{{ (!empty($fattura_numero)) ? $fattura_numero : ''}}</strong>
            </span>
            <span class="col-sm-7">
                <input type="hidden" name="fepa" value="1">

                @if(empty($fatturazione))
                  {{ Form::weCheckboxFatture('fattura_pa', 'Fattura PA', $errors, get_if_exist($fatturazione, 'fattura_pa') ? get_if_exist($fatturazione, 'fattura_pa') : 1, 'id="fepa"') }}
                @endif
            </span>
        </h4>
        <br><br>
        <div class="row">
          <div class="col-md-9">
            @if(get_if_exist($fatturazione, 'ordinativo_id'))
                <input type="hidden" id = "ordinativo_id" name="ordinativo_id" value="{{ $fatturazione->ordinativo_id }}" >
                <strong>Ordinativo</strong>:
                <button type="button" class="btn btn-xs" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#modal-default" data-size="modal-xl" data-title="Dettagli" data-action="{{ route('admin.commerciale.ordinativo.read', $fatturazione->ordinativo_id) }}" data-element="form" data-ajax="true">
                    <i class="fa fa-eye"> </i>
                </button>
                <br>
            @else
                {{ Form::weSelectSearch('ordinativo_id', 'Ordinativo', $errors, $ordinativi, (!empty($ordinativo) ? $ordinativo->id : ''), ['id' => 'ordinativo_id']) }}
            @endif
          </div>
          {{-- <div class="col-md-7">
            {{ Form::weSelectSearch('attivita_svolta', 'Attività Svolta', $errors, $attivita_svolte, get_if_exist($fatturazione, 'attivita_svolta')) }}
          </div> --}}
        </div>
    </div>
    <!-- /.col -->
    <div class="col-sm-3 invoice-col">
        <address>
            <strong>{{ $azienda->ragione_sociale }}</strong><br>
            {{ $azienda->indirizzo }}, {{ $azienda->numero_civico }}<br>
            {{ $azienda->citta }}, {{ $azienda->provincia }} {{ $azienda->cap }}<br>
            Telefono: {{ $azienda->telefono }}<br>
            Email: {{ $azienda->email }}
        </address>
    </div>
    <!-- /.col -->
    <div class="col-sm-4 invoice-col">
        <address>
            <div class="col-md-10">
            {{ Form::weSelectSearch('cliente_id', 'Cliente *', $errors, $clienti, get_if_exist($fatturazione, 'cliente_id'), ['id'=>'cliente_id']) }}
            </div>
            <div class="col-md-2">
                <br>
                <button type="button" class="btn btn-xs btn-flat btn-default" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#modal-default" data-size="modal-lg" data-title="Crea nuovo cliente" data-action="{{ route('admin.amministrazione.clienti.create') }}" data-element="form" data-ajax="true" data-parent="true">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
            <div class="col-md-10">
                {{ Form::weSelect('indirizzo', 'Indirizzo *', $errors, $cliente_indirizzi , get_if_exist($fatturazione, 'indirizzo'),['id'=>'indirizzo']) }}
            </div>
            <div class="col-md-2">
                <br>
                <button id="btn-create-indirizzo" type="button" class="btn btn-xs btn-flat btn-default {{ ((get_if_exist($cliente, 'id')) ? '' : 'hidden' ) }}" data-toggle="modal" data-target="#modal-default" data-title="Aggiungi indirizzo" data-action="{{ (get_if_exist($cliente, 'id')) ? route('admin.amministrazione.clienti.indirizzi.create', $cliente->id) : '' }}" data-ajax="true" data-size="">
                    <i class="fa fa-plus"></i>
                </button>
                <input id="route-create-indirizzo" type="hidden" value="{{ route('admin.amministrazione.clienti.indirizzi.create', 0) }}">
                <input id="indirizzo-selected" type="hidden" value="{{ (get_if_exist($fatturazione, 'indirizzo')) }}">
            </div>
        </address>
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

<hr>

<div class="row">
    <div class="col-md-3">
         {{ Form::weSelect('id_tipologia_fornitura', 'Tipo di fornitura', $errors, config('commerciale.fatturazioni.tipologia_fornitura'), get_if_exist($fatturazione, 'id_tipologia_fornitura')) }}
    </div>
    <div class="col-md-2">
        {{ Form::weText('cig', 'Cig', $errors, get_if_exist($fatturazione, 'cig')) }}
    </div>
    <div class="col-md-2">
        {{ Form::weText('rda', 'Rda', $errors, get_if_exist($fatturazione, 'rda')) }}
    </div>
    <div class="col-md-2">
        {{ Form::weDate('rda_data', 'Data Rda', $errors, get_if_exist($fatturazione, 'rda_data')) }}
    </div>
    <div class="col-md-3">
        {{ Form::weText('codice_univoco', 'Codice Univoco', $errors, (!empty(get_if_exist($fatturazione, 'codice_univoco')) ? $fatturazione->codice_univoco : (!empty($cliente) ? $cliente->codice_univoco : ''))) }}
    </div>
</div>

<!-- Table row -->
<div class="row">
    <div class="col-xs-12 table-responsive">
        <table class="table table-striped voci">
            <thead>
                <tr>
                    <th style="width:2%;">#</th>
                    <th>Descrizione</th>
                    <th style="width:15%;">Attività svolta</th>
                    <th style="width:5%;">Quantità</th>
                    <th style="width:12%;">Importo Singolo</th>
                    <th style="width:9%;">IVA</th>
                    <th style="width:12%;">Importo</th>
                    <th style="width:12%;">Importo con IVA</th>
                    <th style="width:2%;">Esente IVA</th>
                    <th style="width:2%;"><button id="voce-add" onclick="voceAdd()" class="btn btn-xs btn-success btn-flat" type="button"><i class="fa fa-plus"> </i></button></th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($ordinativo_voci))
                  @foreach ($ordinativo_voci as $key => $voce)
                      @php
                          $iva_readonly = 'readonly';
                          $iva_class = 'disabled';
                          $iva_00 = $iva_0;

                          if($voce->iva == 0)
                          {
                              $iva_class = '';
                              $iva_readonly = '';
                              unset($iva_00[0]);
                          }
                      @endphp
                      <tr data-id="{{ $c_voci }}">
                          <td>{{ $c_voci }}.</td>
                          <td>{{ Form::weText('voci['. $c_voci .'][descrizione]', '', $errors, get_if_exist($voce, 'descrizione'), ['onkeyup' => 'calcolaTotale()']) }}</td>
                          <td>{{ Form::weSelect('voci['. $c_voci .'][attivita_svolta]', '', $errors, $attivita_svolte, get_if_exist($voce, 'attivita_svolta')) }}</td>
                          <td>{{ Form::weInt('voci['. $c_voci .'][quantita]', '', $errors, get_if_exist($voce, 'quantita'), ['onchange' => 'calcolaTotale()', 'onkeyup' => 'calcolaTotale()']) }}</td>
                          <td>{{ Form::weCurrency('voci['. $c_voci .'][importo_singolo]', '', $errors, get_if_exist($voce, 'importo_singolo'), ['onkeyup' => 'calcolaTotale()']) }}</td>
                          <td>
                              {{ Form::weSelect('voci['. $c_voci .'][iva_tipo]', '', $errors, $iva_00, get_if_exist($voce, 'iva_tipo'), [$iva_readonly, 'class' => $iva_class]) }}

                              <input type="hidden" name="voci[{{ $c_voci }}][iva]" value="{{ get_if_exist($voce, 'iva') }}" onchange="calcolaTotale()">
                          </td>
                          <td>{{ Form::weCurrency('voci['. $c_voci .'][importo]', '', $errors, get_if_exist($voce, 'importo'), ['disabled' => 'disabled']) }}</td>
                          <input type="hidden" name="{{'voci['. $c_voci .'][importo]'}}" value="{{get_if_exist($voce, 'importo')}}" >
                          <td>{{ Form::weCurrency('voci['. $c_voci .'][importo_iva]', '', $errors, get_if_exist($voce, 'importo_iva'), ['disabled' => 'disabled']) }}</td>
                          <input type="hidden" name="{{'voci['. $c_voci .'][importo_iva]'}}" value="{{get_if_exist($voce, 'importo_iva')}}" >
                          <td>{{ Form::weCheckboxFatture('voci['. $c_voci .'][esente_iva]', '', $errors, get_if_exist($voce, 'esente_iva'), 'onclick="esenteIva(this, '.$c_voci.')"', '') }}</td>
                          <td><button onclick="voceDelete({{ $c_voci++ }})" class="btn btn-xs btn-default btn-flat voce-delete" type="button"><i class="fa fa-times"> </i></button></td>
                      </tr>
                  @endforeach

                @elseif(!empty($fatturazione->voci) && $fatturazione->voci->count() > 0)

                  @if(!empty($fatturazione->voci) && empty(old('voci')))
                      @foreach ($fatturazione->voci as $key => $voce)
                          @php
                              $iva_readonly = 'readonly';
                              $iva_class = 'disabled';
                              $iva_00 = $iva_0;

                              if($voce->iva == 0)
                              {
                                  $iva_readonly = '';
                                  $iva_class = '';
                                  unset($iva_00[0]);
                              }
                          @endphp
                          <tr data-id="{{ $c_voci }}">
                              <td>{{ $c_voci }}.</td>
                              <td>{{ Form::weText('voci['. $c_voci .'][descrizione]', '', $errors, get_if_exist($voce, 'descrizione'), ['onkeyup' => 'calcolaTotale()']) }}</td>
                              <td>{{ Form::weSelect('voci['. $c_voci .'][attivita_svolta]', '', $errors, $attivita_svolte, get_if_exist($voce, 'attivita_svolta')) }}</td>
                              <td>{{ Form::weInt('voci['. $c_voci .'][quantita]', '', $errors, get_if_exist($voce, 'quantita'), ['onchange' => 'calcolaTotale()', 'onkeyup' => 'calcolaTotale()']) }}</td>
                              <td>{{ Form::weCurrency('voci['. $c_voci .'][importo_singolo]', '', $errors, get_if_exist($voce, 'importo_singolo'), ['onkeyup' => 'calcolaTotale()']) }}</td>
                              <td>
                                  {{ Form::weSelect('voci['. $c_voci .'][iva_tipo]', '', $errors, $iva_00, get_if_exist($voce, 'iva_tipo'), [$iva_readonly, 'class' => $iva_class]) }}

                                  <input type="hidden" name="voci[{{ $c_voci }}][iva]" value="{{ get_if_exist($voce, 'iva') }}" onchange="calcolaTotale()">
                              </td>
                              <td>{{ Form::weCurrency('voci['. $c_voci .'][importo]', '', $errors, get_if_exist($voce, 'importo'), ['disabled' => 'disabled']) }}</td>
                              <input type="hidden" name="{{'voci['. $c_voci .'][importo]'}}" value="{{get_if_exist($voce, 'importo')}}" >
                              <td>{{ Form::weCurrency('voci['. $c_voci .'][importo_iva]', '', $errors, get_if_exist($voce, 'importo_iva'), ['disabled' => 'disabled']) }}</td>
                              <input type="hidden" name="{{'voci['. $c_voci .'][importo_iva]'}}" value="{{get_if_exist($voce, 'importo_iva')}}" >
                              <td>{{ Form::weCheckboxFatture('voci['. $c_voci .'][esente_iva]', '', $errors, get_if_exist($voce, 'esente_iva'), 'onclick="esenteIva(this, '.$c_voci.')"', '') }}</td>
                              <td><button onclick="voceDelete({{ $c_voci++ }})" class="btn btn-xs btn-default btn-flat voce-delete" type="button"><i class="fa fa-times"> </i></button></td>
                          </tr>
                      @endforeach
                  @endif
                @else
                  @if(!empty(old('voci')))
                      @foreach (old('voci') as $key => $voce)
                          @php
                              $iva_readonly = 'readonly';
                              $iva_class = 'disabled';
                              $iva_00 = $iva_0;

                              if($voce['iva'] == 0)
                              {
                                  $iva_readonly = '';
                                  $iva_class = '';
                                  unset($iva_00[0]);
                              }
                          @endphp
                          <tr data-id="{{ $c_voci }}">
                              <td>{{ $c_voci }}.</td>
                              <td>{{ Form::weText('voci['. $c_voci .'][descrizione]', '', $errors, get_if_exist($voce, 'descrizione'), ['onkeyup' => 'calcolaTotale()']) }}</td>
                              <td>{{ Form::weSelect('voci['. $c_voci .'][attivita_svolta]', '', $errors, $attivita_svolte, get_if_exist($voce, 'attivita_svolta')) }}</td>
                              <td>{{ Form::weInt('voci['. $c_voci .'][quantita]', '', $errors, get_if_exist($voce, 'quantita'), ['onchange' => 'calcolaTotale()', 'onkeyup' => 'calcolaTotale()']) }}</td>
                              <td>{{ Form::weCurrency('voci['. $c_voci .'][importo_singolo]', '', $errors, get_if_exist($voce, 'importo_singolo'), ['onkeyup' => 'calcolaTotale()']) }}</td>
                              <td>
                                {{ Form::weSelect('voci['. $c_voci .'][iva_tipo]', '', $errors, $iva_00, get_if_exist($voce, 'iva_tipo'), [$iva_readonly, 'class' => $iva_class]) }}

                                <input type="hidden" name="voci[{{ $c_voci }}][iva]" value="{{ get_if_exist($voce, 'iva') }}" onchange="calcolaTotale()">
                                {{-- {{ Form::weText('voci['. $c_voci .'][iva]', '', $errors, get_if_exist($voce, 'iva'), ['onkeyup' => 'calcolaTotale()']) }} --}}
                              </td>
                              <td>{{ Form::weCurrency('voci['. $c_voci .'][importo]', '', $errors, get_if_exist($voce, 'importo'), ['disabled' => 'disabled']) }}</td>
                              <input type="hidden" name="{{'voci['. $c_voci .'][importo]'}}" value="{{get_if_exist($voce, 'importo')}}" >
                              <td>{{ Form::weCurrency('voci['. $c_voci .'][importo_iva]', '', $errors, get_if_exist($voce, 'importo_iva'), ['disabled' => 'disabled']) }}</td>
                              <input type="hidden" name="{{'voci['. $c_voci .'][importo_iva]'}}" value="{{get_if_exist($voce, 'importo_iva')}}" >
                              <td>{{ Form::weCheckboxFatture('voci['. $c_voci .'][esente_iva]', '', $errors, get_if_exist($voce, 'esente_iva'), 'onclick="esenteIva(this, '.$c_voci.')"', '') }}</td>
                              <td><button onclick="voceDelete({{ $c_voci++ }})" class="btn btn-xs btn-default btn-flat voce-delete" type="button"><i class="fa fa-times"> </i></button></td>
                          </tr>
                      @endforeach
                  @endif
                  @if(!empty($fatturazione->voci) && $fatturazione->voci->count() < 1 || empty($fatturazione->voci))
                      <tr data-id="{{ $c_voci }}">
                          <td>{{ $c_voci }}.</td>
                          <td>{{ Form::weText('voci['. $c_voci .'][descrizione]', '', $errors, null, ['onkeyup' => 'calcolaTotale()']) }}</td>
                          <td>{{ Form::weSelect('voci['. $c_voci .'][attivita_svolta]', '', $errors, $attivita_svolte) }}</td>
                          <td>{{ Form::weInt('voci['. $c_voci .'][quantita]', '', $errors, 1, ['onchange' => 'calcolaTotale()', 'onkeyup' => 'calcolaTotale()']) }}</td>
                          <td>{{ Form::weCurrency('voci['. $c_voci .'][importo_singolo]', '', $errors, null, ['onkeyup' => 'calcolaTotale()']) }}</td>
                          <td>
                            {{ Form::weSelect('voci['. $c_voci .'][iva_tipo]', '', $errors, $iva_0, '', ['readonly', 'class' => 'disabled']) }}

                            <input type="hidden" name="voci[{{ $c_voci }}][iva]" value="{{ $iva }}" onchange="calcolaTotale()">
                            {{-- {{ Form::weText('voci['. $c_voci .'][iva]', '', $errors, $iva, ['onkeyup' => 'calcolaTotale()']) }} --}}
                          </td>
                          <td>{{ Form::weCurrency('voci['. $c_voci .'][importo]', '', $errors, 0, ['disabled' => 'disabled']) }}</td>
                          <input type="hidden" name="{{'voci['. $c_voci .'][importo]'}}" value="" >
                          <td>{{ Form::weCurrency('voci['. $c_voci .'][importo_iva]', '', $errors, 0, ['disabled' => 'disabled']) }}</td>
                          <input type="hidden" name="{{'voci['. $c_voci .'][importo_iva]'}}" value="" >
                          <td>{{ Form::weCheckboxFatture('voci['. $c_voci .'][esente_iva]', '', $errors, null, 'onclick="esenteIva(this, '.$c_voci.')"', '') }}</td>
                          <td><button onclick="voceDelete({{ $c_voci }})" class="btn btn-xs btn-default btn-flat voce-delete" type="button"><i class="fa fa-times"> </i></button></td>
                      </tr>
                  @endif
                @endif
            </tbody>
        </table>
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

<div class="row">
    <!-- accepted payments column -->
    <div class="col-xs-6">
        <p class="lead">Info:</p>

        {{ Form::weText('oggetto', 'Oggetto *', $errors, (get_if_exist($fatturazione, 'oggetto') ? $fatturazione->oggetto : (!empty($ordinativo) ? $ordinativo->oggetto : ''))) }}
        {{ Form::weText('note', 'Note ', $errors, get_if_exist($fatturazione, 'note')) }}
        <div class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
            <div class="col-sm-4">
                {{ Form::weCheckbox('iva_erario', 'Iva Erario per PA', $errors, get_if_exist($fatturazione, 'iva_erario')) }}
            </div>
            <div class="col-sm-4">
                {{ Form::weCheckbox('iva_esigibile', 'Iva Esigibile', $errors, get_if_exist($fatturazione, 'iva_esigibile')) }}
            </div>
            <div class="col-sm-4">
                {{ Form::weCheckbox('nota_di_credito', 'Nota di Credito', $errors, get_if_exist($fatturazione, 'nota_di_credito')) }}
            </div>
            <br class="clearfix"><br>
            <div class="col-sm-6">
                {{ Form::weCheckboxFatture('nota_di_credito_interna', 'Nota di Credito Interna', $errors, get_if_exist($fatturazione, 'nota_di_credito_interna'), 'id="nota_di_credito_interna"') }}
            </div>
            <br class="clearfix"><br>
        </div>
        <div class="row">
            <div class="col-md-4">
                {{ Form::weSelect('n_giorni', 'Numero di Giorni', $errors, config('commerciale.fatturazioni.n_giorni'), get_if_exist($fatturazione, 'n_giorni')) }}
            </div>
            <div class="col-md-8">
                 {{ Form::weSelect('tipo_pagamento', 'Tipo di Pagamento', $errors, config('commerciale.fatturazioni.tipologia_pagamento'), get_if_exist($fatturazione, 'tipo_pagamento')) }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                {{ Form::weSelect('anticipata_id', 'Anticipata', $errors, config('commerciale.fatturazioni.anticipata'), get_if_exist($fatturazione, 'anticipata_id')) }}
            </div>
            <div class="col-md-8">
                {{ Form::weSelect('iban', 'IBAN', $errors, array_merge(['' => ''], config('commerciale.fatturazioni.'.session('azienda').'.iban')), get_if_exist($fatturazione, 'iban')) }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                {{ Form::weText('riferimento_normativo', 'Riferimento normativo', $errors, get_if_exist($fatturazione, 'riferimento_normativo')) }}
            </div>
        </div>
    </div>
    <!-- /.col -->
    <div class="col-xs-6">
        <p class="lead">Importo</p>

        <div class="table-responsive">
            <table class="table">
                <tbody>
                    <tr>
                        <th style="width:50%">Acconto:</th>
                        <td>{{ Form::weCurrency('acconto', '', $errors, get_if_exist($fatturazione, 'acconto'), ['onkeyup' => 'calcolaTotale()']) }}</td>
                    </tr>
                    <tr>
                        <th>Totale Netto:</th>
                        <td>{{ Form::weCurrency('totale_netto', '', $errors, get_if_exist($fatturazione, 'totale_netto'), ['disabled' => 'disabled']) }}</td>
                        <input type="hidden" name="totale_netto" value="{{get_if_exist($fatturazione, 'totale_netto')}}" >
                    </tr>
                    <tr>
                        <th>IVA:</th>
                        <td>{{ Form::weText('iva', '', $errors, $iva, ['onkeyup' => 'calcolaTotale()', 'readonly', 'id' => 'iva']) }}</td>
                    </tr>
                    <tr>
                        <th>Natura esente IVA:</th>
                        <td>{{ Form::weSelect('iva_natura', '', $errors, $iva_natura, get_if_exist($fatturazione, 'iva_natura')) }}</td>
                    </tr>
                    <tr>
                        <th>Totale Fattura:</th>
                        <td>{{ Form::weCurrency('totale_fattura', '', $errors, get_if_exist($fatturazione, 'totale_fattura'), ['disabled' => 'disabled']) }}</td>
                        <input type="hidden" name="totale_fattura" value="{{get_if_exist($fatturazione, 'totale_fattura')}}" >
                    </tr>
                    <tr>
                        <th>Totale Importo Dovuto:</th>
                        <td>{{ Form::weCurrency('totale_importo_dovuto', '', $errors, get_if_exist($fatturazione, 'totale_importo_dovuto'), ['disabled' => 'disabled']) }}</td>
                        <input type="hidden" name="totale_importo_dovuto" value="{{get_if_exist($fatturazione, 'totale_importo_dovuto')}}" >
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

<!-- this row will not appear when printing -->
@if(!empty($fatturazione))
    <div class="row no-print">
        <div class="col-xs-12">
            @php
            $pagata_active = ($fatturazione->pagata == 1) ? "active" : "";
            $icona_pagata_hidden = ($fatturazione->pagata == 1 ) ? "" : "hidden";

            $consegnata_active = ($fatturazione->consegnata == 1) ? "active" : "";
            $icona_consegnata_hidden = ($fatturazione->consegnata == 1 ) ? "" : "hidden";

            $anticipata_active = ($fatturazione->anticipata == 1) ? "active" : "";
            $icona_anticipata_hidden = ($fatturazione->anticipata == 1 ) ? "" : "hidden";
            @endphp

            <button id="pagata" class="btn btn-info btn-flat {{$pagata_active}}">Pagata <i id="pagata_icona" class="fa fa-check {{$icona_pagata_hidden}}"></i></button>
            <input type="hidden" name="pagata" value="{{  (empty($fatturazione->pagata)) ? 0 : $fatturazione->pagata }}" >

            <button id="consegnata" class="btn btn-info btn-flat {{$consegnata_active}}">Consegnata <i id="consegnata_icona" class="fa fa-check {{$icona_consegnata_hidden}}"></i></button>
            <input type="hidden" name="consegnata" value="{{ (empty($fatturazione->consegnata)) ? 0 : $fatturazione->consegnata  }}" >

            <button id="anticipata" class="btn btn-info btn-flat {{$anticipata_active}}">Anticipata <i id="anticipata_icona" class="fa fa-check {{$icona_anticipata_hidden}}"></i></button>
            <input type="hidden" name="anticipata" value="{{ (empty($fatturazione->anticipata)) ? 0 : $fatturazione->anticipata }}" >

            <a class="btn btn-success btn-flat pull-right" style="margin-right: 5px;" target="_blank" href="{{ route('admin.commerciale.fatturazione.pdf', $fatturazione->id) }}">
                <i class="fa fa-download"></i> Genera PDF
            </a>
            <a class="btn btn-warning btn-flat pull-right" href="{{ route('admin.commerciale.fatturazione.xml', $fatturazione->id) }}">
                <i class="fa fa-download"></i> Genera XML
            </a>
        </div>
    </div>
    <br>
@endif


@include('commerciale::admin.partials.voci_js')
@push('js-stack')
    <script type="text/javascript">
        $(document).ready(function() {
            // Ordinativo
            @if(!get_if_exist($fatturazione, 'ordinativo_id'))
                $('#ordinativo_id').change(function() {
                    var ordinativoId = $(this).val();

                    @if(get_if_exist($fatturazione, 'id'))
                      location.href = "{{ route(\Request::route()->getName(), $fatturazione->id) }}?ordinativo_id=" + ordinativoId;
                    @else
                      location.href = "{{ route(\Request::route()->getName()) }}?ordinativo_id=" + ordinativoId;
                    @endif
                });
            @endif

            // Nota di credito interna
            $("#nota_di_credito_interna").on('ifChecked', function(event) {
              var nci_valore = $('#numero_nota_di_credito_interna').val();
              var nci_codice = $('#numero_nota_di_credito_interna_codice').val();

              $('.numero_fattura').html(nci_codice);
              $('#n_fattura').val(nci_valore);
            });
            $("#nota_di_credito_interna").on('ifUnchecked', function(event) {
               if($("#fepa").prop('checked')) {
                 var codice = $('#numero_fattura_fepa').val();
                 var valore = $('#numero_fattura_fepa_valore').val();
               } else {
                 var codice = $('#numero_fattura').val();
                 var valore = $('#numero_fattura_valore').val();
               }

               $('.numero_fattura').html(codice);
               $('#n_fattura').val(valore);
            });

            // Fepa
            $("#fepa").on('ifChecked', function(event){
              var fepa_codice = $('#numero_fattura_fepa').val();
              var fepa_valore = $('#numero_fattura_fepa_valore').val();

              $('.numero_fattura').html(fepa_codice);
              $('#n_fattura').val(fepa_valore);
            });

            $("#fepa").on('ifUnchecked', function(event){
               var codice = $('#numero_fattura').val();
               var valore = $('#numero_fattura_valore').val();

               $('.numero_fattura').html(codice);
               $('#n_fattura').val(valore);
            });
			
            $("#fepa").on('ifCreated', function(e) {
				
                $(this).iCheck('toggle');
                $(this).iCheck('toggle');
            });

            // Indirizzi
            $('#cliente_id').change(function() {
                var token = $('input[name="_token"]').val();
                var clienteId = $(this).val();

                $('#btn-create-indirizzo').removeClass('hidden');

                if(clienteId > 0) {
                    var urlCreateIndirizzo = $('#route-create-indirizzo').val();

                    urlCreateIndirizzo = urlCreateIndirizzo.replace("/0/", "/" + clienteId + "/");
                    $('#btn-create-indirizzo').attr('data-action', urlCreateIndirizzo);

                    $.post('{{route('admin.amministrazione.clienti.cliente.json')}}', { _token : token , cliente_id : clienteId })
                        .done(function( data ) {
                            var cliente = JSON.parse(data);
                            var codUnivoco = $('#codice_univoco');

                            if(codUnivoco.val() == '')
                              $('#codice_univoco').val(cliente.codice_univoco);
                        });

                    $.post('{{route('admin.amministrazione.clienti.indirizzi.json')}}', { _token : token , cliente_id : clienteId })
                        .done(function( data ) {
                            var indirizzi = JSON.parse(data);
                            var indirizzoSelected = $('#indirizzo-selected').val();

                            $('#indirizzo').html('');

                            $.each(indirizzi, function(i, item) {
                                 var optionIndirizzo = indirizzi[i].indirizzo_completo;
                                 var htmlIndirizzi = '<option value="'+optionIndirizzo+'">'+optionIndirizzo+'</option>';

                                 $('#indirizzo').append(htmlIndirizzi);
                            });

                            $('#indirizzo').val(indirizzoSelected);
                        });
                } else {
                    $('#btn-create-indirizzo').addClass('hidden');
                    $('#indirizzo').html('');
                }
            });
            $('#cliente_id').change();

            // Stati
            $("#pagata").click(function() {
                if($(this).hasClass('active'))
                {
                     $('input[name="pagata"]').val(0);
                     $(this).removeClass('active');
                     $('#pagata_icona').addClass('hidden');
                }
                else
                {
                    $('input[name="pagata"]').val(1);
                    $(this).addClass('active');
                    $('#pagata_icona').removeClass('hidden');
                }
            });
            $("#consegnata").click(function() {
                if($(this).hasClass('active'))
                {
                     $('input[name="consegnata"]').val(0);
                     $(this).removeClass('active');
                     $('#consegnata_icona').addClass('hidden');
                }
                else
                {
                    $('input[name="consegnata"]').val(1);
                    $(this).addClass('active');
                    $('#consegnata_icona').removeClass('hidden');
                }
            });
            $("#anticipata").click(function() {
                if($(this).hasClass('active'))
                {
                     $('input[name="anticipata"]').val(0);
                     $(this).removeClass('active');
                     $('#anticipata_icona').addClass('hidden');
                }
                else
                {
                    $('input[name="anticipata"]').val(1);
                    $(this).addClass('active');
                    $('#anticipata_icona').removeClass('hidden');
                }
            });

            // Submit form
            $("form").submit(function(e) {
                var inputs = $(this).serializeArray();

                $.each(inputs, function(i, input) {
                    if(input.name.indexOf("totale") >= 0 || input.name.indexOf("importo") >= 0 || input.name == 'iva' || input.name == 'acconto') {
                        $('input[name="'+input.name+'"]').val(cleanCurrency(input.value));
                    }
                });
            });
        });

       function calcolaTotale(ctrlAvvio = false) {
           var importo = 0;
           var importoIva = 0;
           var voci = $('table.voci tbody tr');
           var iva = cleanCurrency($('input[name="iva"]').val());
           var acconto = cleanCurrency($('input[name="acconto"]').val());

           if(!$.isNumeric(iva)) {
               iva = 0;
           }
           $('input[name="iva"]').val(iva);

           if($.isNumeric(iva) && $.isNumeric(acconto)) {
               voci.each(function(index) {
                   $(this).removeAttr('style');

                   if($(this).find('input[name*="descrizione"]').length > 0) {
                       if($(this).find('input[name*="descrizione"]').val().trim() !== '') {
                           var id = $(this).data('id');
                           var quantita = $(this).find('input[name*="[quantita]"]').val();
                           var importoSingolo = cleanCurrency($(this).find('input[name*="[importo_singolo]"]').val());
                           var iva = cleanCurrency($(this).find('input[name*="[iva]"]').val());

                           if(!$.isNumeric(iva)) {
                               iva = 0;
                           }
                           $(this).find('input[name*="[iva]"]').val(iva);

                           if($.isNumeric(importoSingolo)) {
                               if(ctrlAvvio)
                                    $(this).find('input[name*="[importo_singolo]"]').val(formatNumber(importoSingolo, '€'));

                               var importoNew = importoSingolo * quantita;
                               $(this).find('input[name*="[importo]"]').val(formatNumber(importoNew, '€'));

                               var importoIvaNew = importoNew + (importoNew * iva / 100);
                               $(this).find('input[name*="[importo_iva]"]').val(formatNumber(importoIvaNew, '€'));

                               importo += importoNew;
                               importoIva += importoIvaNew;
                           } else {
                               $(this).find('input[name*="[importo]"]').val(formatNumber(0, '€'));
                               $(this).find('input[name*="[importo_iva]"]').val(formatNumber(0, '€'));
                           }
                       } else {
                           $(this).attr('style', 'background-color: #f2dede');
                       }
                   }
               });

               if(ctrlAvvio) {
                    $('input[name="acconto"]').val(formatNumber(acconto, '€'));
               }

               $('input[name="totale_netto"]').val(formatNumber(importo, '€'));
               $('input[name="totale_fattura"]').val(formatNumber(importoIva, '€'));
               $('input[name="totale_importo_dovuto"]').val(formatNumber((importoIva - acconto), '€'));
           }
       }
   </script>
@endpush
