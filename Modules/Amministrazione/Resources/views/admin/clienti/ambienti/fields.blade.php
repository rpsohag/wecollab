@php
    $ambiente = (!empty($ambiente)) ? $ambiente : '';
@endphp

@if(empty($ambiente->id))
    {!! Form::open(['route' => ['admin.amministrazione.clienti.ambienti.store', $cliente->id], 'method' => 'post']) !!}
@else
    {!! Form::open(['route' => ['admin.amministrazione.clienti.ambienti.update', $ambiente->id], 'method' => 'put']) !!}
@endif
    <div class="row">
        <div class="col-md-6">
            {{ Form::weText('ambiente', 'Ambiente', $errors, get_if_exist($ambiente, 'ambiente')) }}
        </div>
         <div class="col-md-6">
            {{ Form::weText('n_db', 'N° DB', $errors, get_if_exist($ambiente, 'n_db')) }}
        </div>
        <div class="col-md-8">
            {{ Form::weText('admin', 'Admin', $errors, get_if_exist($ambiente, 'admin')) }}
        </div>
        <div class="col-md-4">
            {{ Form::weText('password_admin', 'Password Admin', $errors, get_if_exist($ambiente, 'password_admin')) }}
        </div>
        <div class="col-md-5">
            {{ Form::weText('adm', 'ADM', $errors, get_if_exist($ambiente, 'adm')) }}
        </div>
        <div class="col-md-5">
            {{ Form::weText('password_adm', 'Password ADM', $errors, get_if_exist($ambiente, 'password_adm')) }}
        </div>
        <div class="col-md-2">
            {{ Form::weSelect('api_sso', 'API', $errors,[0=>'NO',1=>'SI'],get_if_exist($ambiente, 'api_sso')) }}
        </div>
    </div>

    {{ Form::weSubmit('Salva') }}

{!! Form::close() !!}

@include('wecore::partials.validation-modal')
