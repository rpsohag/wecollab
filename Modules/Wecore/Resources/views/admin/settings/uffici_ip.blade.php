@php
    $aziende_ip = json_decode(setting('wecore::aziende_ip'));
 
    $aziende_ip = (empty($aziende_ip)) ? [] : array_combine($aziende_ip, $aziende_ip);
@endphp

<h3>  Ip degli uffici</h3>

<div class="row">
    <div class="col-md-12">
        {!! Form::weTags("wecore::aziende_ip", 'Ip degli uffici', $errors,  $aziende_ip  ,  $aziende_ip ) !!}
    </div> 
</div>
