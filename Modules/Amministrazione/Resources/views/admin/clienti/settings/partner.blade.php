@php
    $partner = json_decode(setting('clienti::partner'));
    $partner = (empty($partner)) ? [] : array_combine($partner, $partner);
@endphp

{!! Form::weTags('clienti::partner', 'CLIENTI - Partner', $errors, $partner, $partner) !!}
