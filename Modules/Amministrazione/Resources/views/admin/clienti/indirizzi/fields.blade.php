@php
    $indirizzo = (!empty($indirizzo)) ? $indirizzo : '';
@endphp

@if(empty($indirizzo->id))
    {!! Form::open(['route' => ['admin.amministrazione.clienti.indirizzi.store', $cliente->id], 'method' => 'post']) !!}
@else
    {!! Form::open(['route' => ['admin.amministrazione.clienti.indirizzi.update', $indirizzo->id], 'method' => 'put']) !!}
@endif
    <div class="row">
        <div class="col-md-12">
            {{ Form::weText('denominazione', 'Denominazione *', $errors, get_if_exist($indirizzo, 'denominazione')) }}
        </div>
        <div class="col-md-8">
            {{ Form::weText('nazione', 'Nazione *', $errors, get_if_exist($indirizzo, 'nazione')) }}
        </div>
        <div class="col-md-4">
            {{ Form::weText('cap', 'CAP *', $errors, get_if_exist($indirizzo, 'cap')) }}
        </div>
        <div class="col-md-5">
            {{ Form::weText('indirizzo', 'Indirizzo *', $errors, get_if_exist($indirizzo, 'indirizzo')) }}
        </div>
        <div class="col-md-5">
            {{ Form::weText('citta', 'Citta *', $errors, get_if_exist($indirizzo, 'citta')) }}
        </div>
        <div class="col-md-2">
            {{ Form::weText('provincia', 'Provincia *', $errors, get_if_exist($indirizzo, 'provincia')) }}
        </div>
        <div class="col-md-12">
            {{ Form::weText('email', 'Email', $errors, get_if_exist($indirizzo, 'email')) }}
        </div>
        <div class="col-md-6">
            {{ Form::weText('telefono', 'Telefono', $errors, get_if_exist($indirizzo, 'telefono')) }}
        </div>
        <div class="col-md-6">
            {{ Form::weText('fax', 'Fax', $errors, get_if_exist($indirizzo, 'fax')) }}
        </div>
    </div>

    {{ Form::weSubmit('Salva') }}

{!! Form::close() !!}

@include('wecore::partials.validation-modal')
