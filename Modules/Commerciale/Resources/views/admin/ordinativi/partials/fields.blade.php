@php

$ordinativo = (empty($ordinativo)) ? '' : $ordinativo;

$offerte = [-1 => ''] + $offerte;

@endphp

<div class="box-body">
    <div class="row">
        <div class="col-md-4">
            {!! Form::weText('oggetto','Oggetto *' , $errors , get_if_exist($ordinativo, 'oggetto')) !!}
        </div>
        <div class="col-md-2">
            {!! Form::weSelectSearch('cliente_id','Cliente *' , $errors , $clienti, get_if_exist($ordinativo, 'cliente_id')) !!}
        </div>
        <div class="col-md-3">
            {!! Form::weDate('data_inizio','Data di Inizio *' , $errors , get_if_exist($ordinativo, 'data_inizio')) !!}
        </div>
        <div class="col-md-3">
            {!! Form::weDate('data_fine','Data di Fine' , $errors , get_if_exist($ordinativo, 'data_fine')) !!}
        </div>
        <hr>
        <div class="col-md-12">
            {!! Form::weTextarea('note','Note' , $errors , get_if_exist($ordinativo, 'note')) !!}
        </div>
        <hr>
        <h5 style="margin-left:15px;">Crea un'attività per i responsabili sotto inseriti.</h5>
        <div class="col-md-9">
            {!! Form::weTags('responsabili', 'Responsabili', $errors, $utenti) !!}
        </div>
        <div class="col-md-3">
            <button style="margin-top:25px;" type="submit" class="btn btn-success btn-flat"> Inoltra & Crea</button>
        </div>
        <div class="col-md-12">
            {!! Form::weTextarea('motivo_responsabili','Descrizione Attività Responsabili' , $errors) !!}
        </div>
    </div>
    <hr>
    <div class="row display-flex">
        <h5 style="margin-left:15px;">Offerte agganciate all'ordinativo.</h5>
        <div class="col-md-12">
            {!! Form::weTags('offerte_ids','Offerte *' , $errors , $offerte, $ordinativo->offerte()->pluck('id')->toArray()) !!}
        </div>
    </div>
</div>
