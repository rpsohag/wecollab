@php
    $simaziendali = (!empty($simaziendali) ? $simaziendali : []);

    $tipi_sim = config('commerciale.simaziendali.tipi');

    $visibile = '';

    if(empty($simaziendali) || empty($simaziendali->cod_esim))
    {
        $visibile = 'hidden';
    }
@endphp
<div class="box-body">
    <div class="row">
        <div class="col-md-4">
             {!! Form::weText('numero_contratto','N. Contratto *' , $errors , get_if_exist($simaziendali, 'numero_contratto')) !!}
        </div>
        <div class="col-md-4">
             {!! Form::weList('operatore','Operatore Telefonico *' , $errors , $operatori_telefonici ,get_if_exist($simaziendali, 'operatore')) !!}
        </div>
         <div class="col-md-4">
             {!! Form::weText('telefono','Numero Telefonico *' , $errors , get_if_exist($simaziendali, 'telefono')) !!}
        </div>
         <div class="col-md-4">
             {!! Form::weSelectSearch('assegnatario','Assegnatario *' , $errors , $utenti, get_if_exist($simaziendali, 'assegnatario')) !!}
        </div>
        <div class="col-md-4">
             {!! Form::weSelect('tipo_sim','Tipo Sim *' , $errors , $tipi_sim ,get_if_exist($simaziendali, 'tipo_sim'),['id' => 'tipo_sim']) !!}
        </div>
        <div class="col-md-4 esim {{$visibile}}">
            {!! Form::weText('cod_esim','Codice E-Sim *' , $errors , get_if_exist($simaziendali, 'cod_esim'),['id' => 'cod_esim']) !!}
        </div>
        @if(!empty($simaziendali && $simaziendali->cod_esim))
            <div class="col-md-4 esim">
                {!! QrCode::size(250)->Color(33,150,243)->generate(get_if_exist($simaziendali, 'cod_esim')) !!}
            </div>
        @endif
        <div class="col-md-4">
            {!! Form::weText('iccid','ICC ID *' , $errors , get_if_exist($simaziendali, 'iccid')) !!}
        </div>
        <div class="col-md-4">
            {!! Form::weText('profilo','Profilo' , $errors , get_if_exist($simaziendali, 'profilo')) !!}
        </div>
        <div class="col-md-4">
            {!! Form::weText('puk','PUK' , $errors , get_if_exist($simaziendali, 'puk')) !!}
        </div>
    </div>
</div>

@push('js-stack')
    <script type="text/javascript">
        $(function () {
            //VERIFICO SE SONO E-SIM
            $( "#tipo_sim" ).change(function() {
                if($('#tipo_sim').val() == 1)
                {
                    $('.esim').removeClass('hidden');
                }
                else
                {
                    $('#cod_esim').val('');
                    $('.esim').addClass('hidden');
                }
            });
        });
    </script>
@endpush
