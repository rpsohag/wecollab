@php
$simaziendali = (!empty($simaziendali) ? $simaziendali : []);

$tipi_sim = config('commerciale.simaziendali.tipi');

$visibile = '';

if(empty($simaziendali) || empty($simaziendali->cod_esim))
{
    $visibile = 'hidden';
}

$persona = $utenti[$simaziendali->assegnatario];
$tipo_sim = $tipi_sim[$simaziendali->tipo_sim];

$PUK = (!empty($simaziendali->puk)) ?   $simaziendali->puk  : '/';

@endphp
<div class="box-body">
        <div class="col-md-6">
            <div class="col-md-4">
                    {!!"Numero di Contratto : "!!}
                <hr>
                    {!!"Operatore Telefonico : "!!}
                <hr>
                    {!!"Numero Telefonico : "!!}
                <hr>
                    {!!"Assegnatario : "!!}
                <hr>
                    {!!"Tipo Sim : "!!}
                    @if(!empty($simaziendali && $simaziendali->cod_esim))
                    <hr>
                        {!!"Codice E-Sim : "!!}
                    @endif
                <hr>
                    {!!"Codice ICC ID : "!!} 
                <hr>
                    {!!"Profilo : "!!}
                <hr>
                    {!!"Codice PUK : "!!}
            </div>
            <div class="col-md-8">
                    {!!"<b>$simaziendali->numero_contratto</b>"!!}
                <hr>
                    {!!"<b>$simaziendali->operatore</b>"!!}
                <hr>
                    {!!"<b>$simaziendali->telefono</b>"!!}
                <hr>
                    {!!"<b>$persona</b>"!!}
                <hr>
                    {!!"<b>$tipo_sim</b>"!!}
                    @if(!empty($simaziendali && $simaziendali->cod_esim))
                    <hr>
                        {!!"<b>$simaziendali->cod_esim</b>"!!}
                    @endif
                <hr>
                    {!!"<b>$simaziendali->iccid</b>"!!}
                <hr>
                    {!!"<b>$simaziendali->profilo</b>"!!}
                <hr>
                    {!!"<b>$PUK</b>"!!}
            </div>
        </div>
        @if(!empty($simaziendali && $simaziendali->cod_esim))
            <div class="col-md-6 esim">
                {!! QrCode::size(250)->Color(33,150,243)->generate(get_if_exist($simaziendali, 'cod_esim')) !!}
            </div>
        @endif
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
