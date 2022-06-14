@php
    $aziende = json_decode(setting('profile::aziende'));
    $aziende = (empty($aziende)) ? [] : array_combine($aziende, $aziende);
@endphp

{!! Form::weTags('profile::aziende', 'Aziende', $errors, $aziende, $aziende) !!}
