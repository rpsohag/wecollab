<div class="box-body">
    <div class="row">
        <div class="col-md-12">
            {{ Form::weText('descrizione', 'Descrizione *', $errors, get_if_exist($figuraprofessionale, 'descrizione')) }}
        </div>
    </div>

    <br>

    <div class="row bg-success">
        <div class="col-md-1">
            <h3>Costi</h3>
        </div>
        <div class="col-md-4">
            {{ Form::weCurrency('costo_interno', 'Costo Interno', $errors, get_if_exist($figuraprofessionale, 'costo_interno')) }}
        </div>
        <div class="col-md-4">
            {{ Form::weCurrency('importo_vendita', 'Importo Vendita', $errors, get_if_exist($figuraprofessionale, 'importo_vendita')) }}
        </div>
    </div>
</div>
