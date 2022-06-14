@push('js-stack')
     <script type="text/javascript">
         $(document).ready(function() {
             calcolaTotale(true);

             $('#importo_esente, #iva').focusout(function() {
                 calcolaIva();
             });
         });

        function calcolaIva()
        {
            var numberReplace = $('#importo_esente').val().replace(/\u20ac/g, '').replace('.','').replace(',','.');
            var numero = Number(numberReplace);
            var iva = Number($('#iva').val());
            var iva_calcolata = numero*iva/100;
            var totale = numero + iva_calcolata;

            totale = totale.toString().replace('.', ',');

            $('#importo_iva').val(formatNumber(totale, '€'));
        }

        // Voci
        function esenteIva(el, id)
        {
            var iva = $('input[name="voci['+id+'][iva]"]');
            var iva_tipo = $('select[name="voci['+id+'][iva_tipo]"]');
            var ivaDefault = $('#iva').val();

            if(el.checked)
            {
                iva.val(0);
                iva.attr('readonly', 'readonly');

                if(iva_tipo.length > 0)
                {
                    iva_tipo.find('option').first().remove();
                    iva_tipo.removeAttr('readonly');
                    iva_tipo.removeClass('disabled');
                }
            }
            else
            {
                iva.val(ivaDefault);
                iva.removeAttr('readonly');

                if(iva_tipo.length > 0)
                {
                    iva_tipo.prepend('<option value="0">22%</option>');
                    iva_tipo.val('0');
                    iva_tipo.attr('readonly', 'readonly');
                    iva_tipo.addClass('disabled');
                }
            }

            calcolaTotale();
        }

        function voceAdd() {
            var iva = $('input[name="iva"]').val();
            var countVoci = $('table.voci tbody tr').length;
            var voce = $('table.voci tbody tr:last-child').clone();
            var lastId = voce.data('id');
            var newId = lastId + 1;

            voce.attr('data-id', newId);
            voce.find('td:first-child()').html((countVoci + 1) + '.');
            voce.find('input:not([type="checkbox"])').val('');
            voce.find('input[name*="[quantita]"]').val(1);
            voce.find('input[name*="[iva]"]').val(iva);
            voce.find('input[name*="[esente_iva]"]').attr('onclick', 'esenteIva(this, ' + newId + ')').attr('checked', false);
            voce.find('.voce-delete').attr('onclick', 'voceDelete(' + newId + ')');

            voce.find('input, select').each(function(index) {
                var attr = $(this).attr('name').replace('[' + lastId + ']', '[' + newId + ']');
                $(this).attr('name', attr);
            });

            $('table.voci tbody').append(voce);

            vociNumerazione();
            bootJs();
        }

        function voceDelete(id) {
            var countVoci = $('table.voci tbody tr').length;

            if(countVoci > 1) {
                $('table.voci tbody tr[data-id="' + id + '"]').remove();

                vociNumerazione();
            } else {
                $('table.voci tbody tr[data-id="' + id + '"] input').val('');
                $('table.voci tbody tr[data-id="' + id + '"] input[name="voci[' + id + '][iva]"]').val($('#iva').val());
            }

            calcolaTotale();
        }

        function voceDuplicate(id) {
            var countVoci = $('table.voci tbody tr').length;
            var voce = $('table.voci tbody tr[data-id="'+id+'"]').clone();
            var voceLast = $('table.voci tbody tr:last-child');
            var lastId = voceLast.data('id');
            var newId = lastId + 1;

            voce.attr('data-id', newId);
            voce.find('td:first-child()').html((countVoci + 1) + '.');
            voce.find('input[name*="[esente_iva]"]').attr('onclick', 'esenteIva(this, ' + newId + ')');
            voce.find('.voce-delete').attr('onclick', 'voceDelete(' + newId + ')');
            voce.find('.voce-duplicate').attr('onclick', 'voceDuplicate(' + newId + ')');

            voce.find('input, select').each(function(index) {
                var attr = $(this).attr('name').replace('[' + id + ']', '[' + newId + ']');
                $(this).attr('name', attr);
            });

            $('table.voci tbody').append(voce);

            vociNumerazione();
            bootJs();

            calcolaTotale();
        }

        function vociNumerazione() {
             var n = 1;

             $('table.voci tbody tr').each(function(index) {
                 $(this).find('td:first-child()').html(n++);
             });
        }
    </script>
@endpush
