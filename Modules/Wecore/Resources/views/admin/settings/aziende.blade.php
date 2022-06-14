@php
    $aziende = json_decode(setting('wecore::aziende'));

    $key = get_azienda();
    $azienda = (!empty($aziende)) ? $aziende->$key : null;
@endphp

<h3>Azienda <small> - dati dell'azienda</small></h3>

<div class="row">
    @foreach ($aziende as $k => $a)
        @if($k != get_azienda())
            @foreach ($a as $field => $value)
                <input type="hidden" name="wecore::aziende[{{ $k }}][{{ $field }}]" value="{{ $value }}">
            @endforeach
        @endif
    @endforeach
    <h3 class="col-md-4">{{ session('azienda') }}</h3>
    <div class="col-md-8">
        {!! Form::weFilemanager("wecore::aziende[$key][logo]", 'Logo', ['field_id' => get_azienda() . '-logo', 'type' =>'image'], $errors, get_if_exist($azienda, 'logo')) !!}
    </div>

    <div class="col-md-4">
        {!! Form::weText("wecore::aziende[$key][ragione_sociale]", 'Ragione Sociale', $errors, get_if_exist($azienda, 'ragione_sociale')) !!}
    </div>
    <div class="col-md-4">
        {!! Form::weText("wecore::aziende[$key][p_iva]", 'Partita IVA', $errors, get_if_exist($azienda, 'p_iva')) !!}
    </div>
    <div class="col-md-4">
        {!! Form::weText("wecore::aziende[$key][sito_web]", 'Sito Web', $errors, get_if_exist($azienda, 'sito_web')) !!}
    </div>
    <div class="col-md-6">
        {!! Form::weText("wecore::aziende[$key][email]", 'Email', $errors, get_if_exist($azienda, 'email')) !!}
    </div>
    <div class="col-md-6">
        {!! Form::weText("wecore::aziende[$key][pec]", 'PEC', $errors, get_if_exist($azienda, 'pec')) !!}
    </div>
    <div class="col-md-6">
        {!! Form::weText("wecore::aziende[$key][telefono]", 'Telefono', $errors, get_if_exist($azienda, 'telefono')) !!}
    </div>
    <div class="col-md-6">
        {!! Form::weText("wecore::aziende[$key][fax]", 'Fax', $errors, get_if_exist($azienda, 'fax')) !!}
    </div>
    <div class="col-md-3">
        {!! Form::weText("wecore::aziende[$key][indirizzo]", 'Indirizzo', $errors, get_if_exist($azienda, 'indirizzo')) !!}
    </div>
    <div class="col-md-2">
        {!! Form::weInt("wecore::aziende[$key][numero_civico]", 'Numero civico', $errors, get_if_exist($azienda, 'numero_civico')) !!}
    </div>
    <div class="col-md-2">
        {!! Form::weText("wecore::aziende[$key][cap]", 'CAP', $errors, get_if_exist($azienda, 'cap')) !!}
    </div>
    <div class="col-md-2">
        {!! Form::weText("wecore::aziende[$key][citta]", 'Città', $errors, get_if_exist($azienda, 'citta')) !!}
    </div>
    <div class="col-md-1">
        {!! Form::weText("wecore::aziende[$key][provincia]", 'Provincia', $errors, get_if_exist($azienda, 'provincia')) !!}
    </div>
    <div class="col-md-2">
        {!! Form::weText("wecore::aziende[$key][nazione]", 'Nazione', $errors, get_if_exist($azienda, 'nazione')) !!}
    </div>
    <div class="col-md-6">
        {!! Form::weText("wecore::aziende[$key][iso]", 'ISO', $errors, get_if_exist($azienda, 'iso')) !!}
    </div>
    <div class="col-md-6">
        {!! Form::weFilemanager("wecore::aziende[$key][iso_img]", 'Immagine ISO', ['field_id' => get_azienda() . '-iso-img', 'type' =>'image'], $errors, get_if_exist($azienda, 'iso_img')) !!}
    </div>

    <div class="col-md-12">
        <h3>Fatturazione Elettronica</h3>
    </div>
    <div class="col-md-2">
        {!! Form::weText("wecore::aziende[$key][id_paese]", 'ID Paese', $errors, get_if_exist($azienda, 'id_paese')) !!}
    </div>
    <div class="col-md-4">
        {!! Form::weText("wecore::aziende[$key][id_codice]", 'ID Codice', $errors, get_if_exist($azienda, 'id_codice')) !!}
    </div>
    <div class="col-md-3">
        {!! Form::weText("wecore::aziende[$key][regime_fiscale]", 'Regime Fiscale', $errors, get_if_exist($azienda, 'regime_fiscale')) !!}
    </div>
    <div class="col-md-2">
        {!! Form::weText("wecore::aziende[$key][divisa]", 'Divisa', $errors, get_if_exist($azienda, 'divisa')) !!}
    </div>
    <div class="col-md-12">
        <h4>Iscrizione REA</h4>
    </div>
    <div class="col-md-2">
        {!! Form::weText("wecore::aziende[$key][rea_ufficio]", 'Ufficio', $errors, get_if_exist($azienda, 'rea_ufficio')) !!}
    </div>
    <div class="col-md-3">
        {!! Form::weText("wecore::aziende[$key][rea_numero]", 'Numero REA', $errors, get_if_exist($azienda, 'rea_numero')) !!}
    </div>
    <div class="col-md-3">
        {!! Form::weCurrency("wecore::aziende[$key][rea_capitale_sociale]", 'Capitale Sociale', $errors, get_if_exist($azienda, 'rea_capitale_sociale')) !!}
    </div>
    <div class="col-md-2">
        {!! Form::weText("wecore::aziende[$key][rea_stato_liquidazione]", 'Stato Liquidazione', $errors, get_if_exist($azienda, 'rea_stato_liquidazione')) !!}
    </div>

    <div class="col-md-12">
        <h3>Rappresentante Legale</h3>
    </div>
    <div class="col-md-3">
        {!! Form::weText("wecore::aziende[$key][rl_nome]", 'Nome', $errors, get_if_exist($azienda, 'rl_nome')) !!}
    </div>
    <div class="col-md-3">
        {!! Form::weText("wecore::aziende[$key][rl_cognome]", 'Cognome', $errors, get_if_exist($azienda, 'rl_cognome')) !!}
    </div>
    <div class="col-md-3">
        {!! Form::weText("wecore::aziende[$key][rl_telefono]", 'Telefono', $errors, get_if_exist($azienda, 'rl_telefono')) !!}
    </div>
    <div class="col-md-3">
        {!! Form::weText("wecore::aziende[$key][rl_email]", 'Email', $errors, get_if_exist($azienda, 'rl_email')) !!}
    </div>
</div>
