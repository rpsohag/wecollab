@php
    $aziende = json_decode(setting('wecore::aziende'));
    $key = get_azienda();

    $docs_base = json_decode(setting("commerciale::offerte::doc_base"));
    $doc_base = get_if_exist($docs_base, $key) ? $docs_base->$key : '';
@endphp

<h3>Offerte <small> - documento base</small></h3>

<div class="row">
    @if(!empty($docs_base))
        @foreach ($docs_base as $k => $value)
            @if($k != get_azienda())
                <input type="hidden" name="commerciale::offerte::doc_base[{{ $k }}]" value="{{ $value }}">
            @endif
        @endforeach
    @endif

    <div class="col-md-12">
        {!! Form::weFilemanager("commerciale::offerte::doc_base[$key]", 'Documento base', ['field_id' => get_azienda() . '-doc_base'], $errors, $doc_base, ['placeholder' => 'link documento base']) !!}
    </div>
</div>
