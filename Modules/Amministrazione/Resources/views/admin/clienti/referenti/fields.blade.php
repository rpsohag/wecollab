@php
    $referente = (!empty($referente)) ? $referente : '';
@endphp

@if(empty($referente->id))
    {!! Form::open(['route' => ['admin.amministrazione.clienti.referenti.store', $cliente->id], 'method' => 'post']) !!}
@else
    {!! Form::open(['route' => ['admin.amministrazione.clienti.referenti.update', $referente->id], 'method' => 'put']) !!}
@endif
    <div class="row">
        <div class="col-md-6">
            {{ Form::weText('nome', 'Nome *', $errors, get_if_exist($referente, 'nome')) }}
        </div>
        <div class="col-md-6">
            {{ Form::weText('cognome', 'Cognome *', $errors, get_if_exist($referente, 'cognome')) }}
        </div>
        <div class="col-md-6">
            {{ Form::weText('telefono', 'Telefono', $errors, get_if_exist($referente, 'telefono')) }}
        </div>
        <div class="col-md-6">
            {{ Form::weText('email', 'Email', $errors, get_if_exist($referente, 'email')) }}
        </div>
        <div class="col-md-12">
            {{ Form::weText('mansione', 'Mansione', $errors, get_if_exist($referente, 'mansione')) }}
        </div>
    </div>

    {{ Form::weSubmit('Salva') }}

{!! Form::close() !!}

@include('wecore::partials.validation-modal')
