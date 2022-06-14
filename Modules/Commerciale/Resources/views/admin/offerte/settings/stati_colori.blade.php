@php
    $stati_colori = json_decode(setting('commerciale::offerte::stati_colori'));
    
    $stati_colori = (empty($stati_colori)) ? [] : $stati_colori;
@endphp

<h3>Offerte <small> - colori per gli stati</small></h3>

<div class="row">
    @foreach (config('commerciale.offerte.stati') as $key => $stato)
        <div class="col-md-4">
            {!! Form::weColor("commerciale::offerte::stati_colori[" . $key . "]", 'Colore "' . $stato . '"', $errors, get_if_exist($stati_colori, $key)) !!}
        </div>
    @endforeach
    <div class="col-md-4">
        {!! Form::weColor("commerciale::offerte::stati_colori[101]", 'Colore "Accettata senza Determina/ODA"', $errors, get_if_exist($stati_colori, 101)) !!}
    </div>
    <div class="col-md-4">
        {!! Form::weColor("commerciale::offerte::stati_colori[102]", 'Colore "Accettata senza Ordine e Determina"', $errors, get_if_exist($stati_colori, 102)) !!}
    </div>
</div>










