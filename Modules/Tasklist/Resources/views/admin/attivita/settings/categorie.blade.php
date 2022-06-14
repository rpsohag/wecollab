@php
    $categorie = json_decode(setting('tasklist::categorie'));
    $categorie = (empty($categorie)) ? [] : array_combine($categorie, $categorie);
@endphp

{!! Form::weTags('tasklist::categorie', 'Categorie', $errors, $categorie, $categorie) !!}
