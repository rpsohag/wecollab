@php
    $stati_colori = json_decode(setting('commerciale::fatturazione::colori'));
    $stati_colori = (empty($stati_colori)) ? [] : $stati_colori;
@endphp

<h3>Fatturazione <small> - colori per gli stati</small></h3>

<div class="row">
    @foreach (config('commerciale.fatturazioni.stati') as $key => $stato)
        <div class="col-md-4">
            {!! Form::weColor("commerciale::fatturazione::colori[" . $key . "]", 'Colore "' . $stato . '"', $errors, get_if_exist($stati_colori, $key)) !!}
        </div>
    @endforeach
</div>
