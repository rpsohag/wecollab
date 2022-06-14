@php
    $stati_colori = json_decode(setting('commerciale::offerte::stati_colori'));
    
    $stati_colori = (empty($stati_colori)) ? [] : $stati_colori;
@endphp

<h3>Offerte <small> - colori per gli stati</small></h3>

<div class="row">
    @foreach (config('assistenza.richieste_intervento.stati') as $key => $stato)
        <div class="col-md-4">
            {!! Form::weColor("commerciale::offerte::stati_colori[" . $key . "]", 'Colore "' . $stato . '"', $errors, get_if_exist($stati_colori, $key)) !!}
        </div>
    @endforeach
</div>