<div class="box-body">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header">
                    <h4>
                        <strong>Costi Fissi </strong> 
                        @if(empty($ordinativo->costi_fissi) && $ordinativo->offerta->voci->count() > 0)
                            <button class="btn btn-primary btn-sm btn-flat" type="submit">Sync Costi Fissi Offerta</button>
                        @endif
                    </h4>
                </div>
                <div class="box-body">
                    <div id="table-voci">
                        @if(!empty($ordinativo->costi_fissi))
                            @foreach($ordinativo->costi_fissi as $key => $voce)
                                <div class="row bg-odd riga-voci" data-id="{{ $key }}">
                                    <br>
                                    <div class="col-md-2">
                                        {{ Form::weText("voci['".$key."'][descrizione]", 'Descrizione *', $errors, $voce->descrizione, ['onkeyup' => 'calcolaTotale()']) }}
                                    </div>
                                    <div class="col-md-2">
                                        {{ Form::weCurrency("voci['".$key."'][importo_singolo]", 'Importo Singolo *', $errors, get_currency($voce->importo_singolo), ['onkeyup' => 'calcolaTotale()']) }}
                                    </div>
                                    <div class="col-md-1">
                                        {{ Form::weInt("voci['".$key."'][quantita]", 'Quantità *', $errors, $voce->quantita, ['min' => 0, 'onkeyup' => 'calcolaTotale()', 'onchange' => 'calcolaTotale()']) }}
                                    </div>
                                    <div class="col-md-1">
                                        {{ Form::weText("voci['".$key."'][iva]", 'Iva *', $errors, $voce->iva, ['onkeyup' => 'calcolaTotale()']) }}
                                    </div>
                                    <div class="col-md-2">
                                        {{ Form::weCurrency("voci['".$key."'][importo]", 'Importo *', $errors, get_currency($voce->importo)) }}
                                    </div>
                                    <div class="col-md-2">
                                        {{ Form::weCurrency("voci['".$key."'][importo_iva]", 'Importo Iva *', $errors, get_currency($voce->importo_iva)) }}
                                    </div>
                                    <div class="col-md-1">
                                        <br>
                                        <label>
                                            <input type="checkbox" name="voci['{{$key}}'][esente_iva]" {{ $voce->esente_iva == 1 ? 'checked' : ''}}>&nbsp;&nbsp;&nbsp; Esente Iva
                                        </label>
                                    </div>
                                    <div class="col-md-1">
                                        <button style="margin-top:22px; margin-left:18px;" class="btn btn-md btn-flat btn-danger" type="button" onclick="removeVoce({{$key}})"><i class="fa fa-trash"> </i></button>
                                        <button style="margin-top:22px; margin-left:18px;" class="btn btn-md btn-flat btn-warning" type="button" onclick="duplicateVoce({{$key}})"><i class="fa fa-copy"> </i></button>
                                    </div>
                                </div>
                            @endforeach
                        @else   
                            <div class="row bg-odd riga-voci" data-id="1">
                                <br>
                                <div class="col-md-2">
                                    {{ Form::weText("voci['1'][descrizione]", 'Descrizione *', $errors, null, ['onkeyup' => 'calcolaTotale()']) }}
                                </div>
                                <div class="col-md-2">
                                    {{ Form::weCurrency("voci['1'][importo_singolo]", 'Importo Singolo *', $errors, null, ['onkeyup' => 'calcolaTotale()']) }}
                                </div>
                                <div class="col-md-1">
                                    {{ Form::weInt("voci['1'][quantita]", 'Quantità *', $errors, 0, ['min' => 0, 'onkeyup' => 'calcolaTotale()', 'onchange' => 'calcolaTotale()']) }}
                                </div>
                                <div class="col-md-1">
                                    {{ Form::weText("voci['1'][iva]", 'Iva *', $errors, config('commerciale.offerte.iva'), ['onkeyup' => 'calcolaTotale()']) }}
                                </div>
                                <div class="col-md-2">
                                    {{ Form::weCurrency("voci['1'][importo]", 'Importo *', $errors, 0) }}
                                </div>
                                <div class="col-md-2">
                                    {{ Form::weCurrency("voci['1'][importo_iva]", 'Importo Iva *', $errors, 0) }}
                                </div>
                                <div class="col-md-1">
                                    <br>
                                    <label>
                                        <input type="checkbox" name="voci['1'][esente_iva]">&nbsp;&nbsp;&nbsp; Esente Iva
                                    </label>
                                </div>
                                <div class="col-md-1">
                                    <button style="margin-top:12px; margin-left:18px;" class="btn btn-md btn-flat btn-danger" type="button" onclick="removeVoce(1)"><i class="fa fa-trash"> </i></button>
                                    <button style="margin-top:12px; margin-left:18px;" class="btn btn-md btn-flat btn-warning" type="button" onclick="duplicateVoce(1)"><i class="fa fa-copy"> </i></button>
                                </div>
                            </div>
                        @endif
                    </div> 
                    <br>
                    <div class="text-center">
                        <button id="add-voce" type="button" class="btn btn-md btn-flat btn-primary" onclick="newVoce(1)"><i class="fa fa-plus"> </i> Aggiungi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js-stack')
<script>

    // Create new voce
    function newVoce(id) {
        var oldrow = $('#table-voci .row[data-id="'+id+'"]');
        var row = oldrow.clone();
        var newId = parseInt($('#table-voci .row:last').attr('data-id')) + 1;
        row.attr('data-id', newId);
        row.find('input[data-row]').attr('data-row', newId);
        row.find('input').val('');
        row.find('input').prop( "checked", false );
        row.find('input[name*="[quantita]"]').val(0);
        row.find('input[name*="[iva]"]').val({{config('commerciale.offerte.iva')}});

        row.find('input[name*="[descrizione]"]').attr('name', "voci['"+newId+"'][descrizione]");
        row.find('input[name*="[quantita]"]').attr('name', "voci['"+newId+"'][quantita]");
        row.find('input[name*="[importo_singolo]"]').attr('name', "voci['"+newId+"'][importo_singolo]");
        row.find('input[name*="[iva]"]').attr('name', "voci['"+newId+"'][iva]");
        row.find('input[name*="[importo]"]').attr('name', "voci['"+newId+"'][importo]");
        row.find('input[name*="[importo_iva]"]').attr('name', "voci['"+newId+"'][importo_iva]");
        row.find('input[name*="[esente_iva]"]').attr('name', "voci['"+newId+"'][esente_iva]");

        row.find('input[name*="[descrizione]"]').attr('id', "voci['"+newId+"'][descrizione]");
        row.find('input[name*="[quantita]"]').attr('id', "voci['"+newId+"'][quantita]");
        row.find('input[name*="[importo_singolo]"]').attr('id', "voci['"+newId+"'][importo_singolo]");
        row.find('input[name*="[iva]"]').attr('id', "voci['"+newId+"'][iva]");
        row.find('input[name*="[importo]"]').attr('id', "voci['"+newId+"'][importo]");
        row.find('input[name*="[importo_iva]"]').attr('id', "voci['"+newId+"'][importo_iva]");
        row.find('input[name*="[esente_iva]"]').attr('id', "voci['"+newId+"'][esente_iva]");

        row.find('.btn-danger').attr('onclick', 'removeVoce(' + newId + ')');
        row.find('.btn-warning').attr('onclick', 'duplicateVoce(' + newId + ')');

        $('#table-voci').append(row);

        bootJs();
    }

    // Duplicate voce
    function duplicateVoce(id) {
        var oldrow = $('#table-voci .row[data-id="'+id+'"]');
        var row = oldrow.clone();
        var newId = parseInt($('#table-voci .row:last').attr('data-id')) + 1;
        row.attr('data-id', newId);

        row.find('input[name*="[descrizione]"]').attr('name', "voci['"+newId+"'][descrizione]");
        row.find('input[name*="[quantita]"]').attr('name', "voci['"+newId+"'][quantita]");
        row.find('input[name*="[importo_singolo]"]').attr('name', "voci['"+newId+"'][importo_singolo]");
        row.find('input[name*="[iva]"]').attr('name', "voci['"+newId+"'][iva]");
        row.find('input[name*="[importo]"]').attr('name', "voci['"+newId+"'][importo]");
        row.find('input[name*="[importo_iva]"]').attr('name', "voci['"+newId+"'][importo_iva]");
        row.find('input[name*="[esente_iva]"]').attr('name', "voci['"+newId+"'][esente_iva]");

        row.find('input[name*="[descrizione]"]').attr('id', "voci['"+newId+"'][descrizione]");
        row.find('input[name*="[quantita]"]').attr('id', "voci['"+newId+"'][quantita]");
        row.find('input[name*="[importo_singolo]"]').attr('id', "voci['"+newId+"'][importo_singolo]");
        row.find('input[name*="[iva]"]').attr('id', "voci['"+newId+"'][iva]");
        row.find('input[name*="[importo]"]').attr('id', "voci['"+newId+"'][importo]");
        row.find('input[name*="[importo_iva]"]').attr('id', "voci['"+newId+"'][importo_iva]");
        row.find('input[name*="[esente_iva]"]').attr('id', "voci['"+newId+"'][esente_iva]");

        row.find('.btn-danger').attr('onclick', 'removeVoce(' + newId + ')');
        row.find('.btn-warning').attr('onclick', 'duplicateVoce(' + newId + ')');

        $('#table-voci').append(row);

        bootJs();
    }

    // Remove voce
    function removeVoce(id) {
        var ctrlRows = $('#table-voci .row').length;
        var row = $('.row[data-id="'+id+'"]');

        if(ctrlRows > 1) {
            row.remove();
        } else {
            row.find('input').val('');
            row.find('input').attr('checked', false);
            row.find('input[name*="[quantita]"]').val(0);
            row.find('input[name*="[iva]"]').val({{config('commerciale.offerte.iva')}});
        }
    }

        function calcolaTotale(ctrlAvvio = false) {
            var importo = 0;
            var importoIva = 0;
            var voci = $('.riga-voci');
            voci.each(function(index) {
                $(this).removeAttr('style');

                if($(this).find('input[name*="descrizione"]').val() !== undefined && $(this).find('input[name*="descrizione"]').val().trim() !== '') {
                    var id = $(this).data('id');
                    var quantita = $(this).find('input[name*="[quantita]"]').val();
                    var importoSingolo = cleanCurrency($(this).find('input[name*="[importo_singolo]"]').val());
                    var iva = cleanCurrency($(this).find('input[name*="[iva]"]').val());

                    if(!$.isNumeric(iva)) {
                        iva = 0;
                    }
                    $(this).find('input[name*="[iva]"]').val(iva);

                    if($.isNumeric(importoSingolo)) {
                        if(ctrlAvvio)
                            $(this).find('input[name*="[importo_singolo]"]').val(formatNumber(importoSingolo, '€'));

                        var importoNew = importoSingolo * quantita;
                        $(this).find('input[name*="[importo]"]').val(formatNumber(importoNew, '€'));

                        var importoIvaNew = importoNew + (importoNew * iva / 100);
                        $(this).find('input[name*="[importo_iva]"]').val(formatNumber(importoIvaNew, '€'));

                        importo += importoNew;
                        importoIva += importoIvaNew;
                    } else {
                        $(this).find('input[name*="[importo]"]').val(formatNumber(0, '€'));
                        $(this).find('input[name*="[importo_iva]"]').val(formatNumber(0, '€'));
                    }
                }
            });

            $('input[name="importo_esente"]').val(formatNumber(importo, '€'));
            $('input[name="importo_iva"]').val(formatNumber(importoIva, '€'));
        }
</script>
@endpush