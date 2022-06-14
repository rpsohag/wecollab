@php
    $collaborazioni = json_decode(setting('profile::collaborazioni'));
    $collaborazioni = (empty($collaborazioni)) ? [] : array_combine($collaborazioni, $collaborazioni);
@endphp

{!! Form::weTags('profile::collaborazioni', 'Collaborazioni', $errors, $collaborazioni, $collaborazioni) !!}
