@php
    $aree = json_decode(setting('profile::aree'));
    $aree = (empty($aree)) ? [] : array_combine($aree, $aree);
@endphp

{!! Form::weTags('profile::aree', 'Aree', $errors, $aree, $aree) !!}
