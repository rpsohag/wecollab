@php
    $count_scadenze = 1;
    
    $fatture = [''];
    foreach ($ordinativo->fatture as $k => $ft)
      $fatture[$ft->id] = $ft->get_numero_fattura()
@endphp

<div class="box-body">
    <table id="scadenze" class="table">
        <thead>
            <tr>
                <th>#</th>
                <th style="width: 50%;">Descrizione *</th>
                <th>Data *</th>
                <th>Data Avviso *</th>
                <th>Importo *</th>
                <th>Offerta *</th>
                <th>Fatturata</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($ordinativo->fatturazioni_scadenze as $key => $scadenza)
            @php 
                $offerte = null;
                $offerte = $ordinativo->offerte()->pluck('oggetto', 'id')->prepend('Seleziona una offerta', 0)->toArray();
                if(count($offerte) == 2){
                    unset($offerte[0]);
                }
            @endphp
                <tr data-id="{{ $count_scadenze }}">
                    <td>{{ $count_scadenze }}</td>
                    <td>{{ Form::weText('fatturazioni_scadenze[' . $count_scadenze . '][descrizione]', '', $errors, get_if_exist($scadenza, 'descrizione')) }}</td>
                    <td>{{ Form::weDate('fatturazioni_scadenze[' . $count_scadenze . '][data]', '', $errors, get_if_exist($scadenza, 'data')) }}</td>
                    <td>{{ Form::weDate('fatturazioni_scadenze[' . $count_scadenze . '][data_avviso]', '', $errors, get_if_exist($scadenza, 'data_avviso')) }}</td>
                    <td>{{ Form::weCurrency('fatturazioni_scadenze[' . $count_scadenze . '][importo]', '', $errors, get_if_exist($scadenza, 'importo')) }}</td>
                    <td>{{ Form::weSelect('fatturazioni_scadenze[' . $count_scadenze . '][offerta_id]', '', $errors, $offerte, get_if_exist($scadenza, 'offerta_id')) }}</td>
                    <td>{{ Form::weSelect('fatturazioni_scadenze[' . $count_scadenze . '][fattura_id]', '', $errors, $fatture, get_if_exist($scadenza, 'fattura_id')) }}</td>
                    <td>
                      @if(empty(get_if_exist($scadenza, 'fattura_id')) && !empty(get_if_exist($scadenza, 'offerta_id')))
                        <a href="{{ route('admin.commerciale.fatturazione.create', ['ordinativo_id' => $ordinativo->id, 'fatturazione_scadenza_id' => $scadenza->id]) }}" class="btn btn-success btn-flat genera-fattura-scadenza" data-toggle="tooltip" data-original-title="Genera fattura"><i class="fa fa-file-text-o"> </i></a>
                      @endif
                      <button type="button" class="btn btn-xs btn-danger btn-flat" onclick="delFatturazioneScadenza({{ $count_scadenze++ }})"><i class="fa fa-trash"> </i></button>
                    </td>
                </tr>
            @endforeach
            @if($count_scadenze < 2)
                @php
                    $offerte = null;
                    $offerte = $ordinativo->offerte()->pluck('oggetto', 'id')->prepend('Seleziona una offerta', 0)->toArray();
                    if(count($offerte) == 2){
                        unset($offerte[0]);
                    }
                @endphp
                <tr data-id="{{ $count_scadenze }}">
                    <td>
                        {{ $count_scadenze }}
                    </td>
                    <td>{{ Form::weText('fatturazioni_scadenze[' . $count_scadenze . '][descrizione]', '', $errors) }}</td>
                    <td>{{ Form::weDate('fatturazioni_scadenze[' . $count_scadenze . '][data]', '', $errors) }}</td>
                    <td>{{ Form::weDate('fatturazioni_scadenze[' . $count_scadenze . '][data_avviso]', '', $errors) }}</td>
                    <td>{{ Form::weCurrency('fatturazioni_scadenze[' . $count_scadenze . '][importo]', '', $errors) }}</td>
                    <td>{{ Form::weSelect('fatturazioni_scadenze[' . $count_scadenze . '][offerta_id]', '', $errors, $offerte) }}</td>
                    <td>{{ Form::weSelect('fatturazioni_scadenze[' . $count_scadenze . '][fattura_id]', '', $errors, $fatture) }}</td>
                    <td>
                        <button type="button" class="btn btn-xs btn-danger btn-flat" onclick="delFatturazioneScadenza({{ $count_scadenze }})"><i class="fa fa-trash"> </i></button>
                    </td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td class="text-center" colspan="6">
                    <button type="button" id="add-fatturazione-scadenza" class="btn btn-default btn-flat">
                        <i class="fa fa-plus"></i>
                        Aggiungi Scadenza
                    </button>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

@push('js-stack')
    <script>
        $(document).ready(function() {
            $('#add-fatturazione-scadenza').click(function(e) {
                var row = $('#scadenze tbody tr').last().clone();
                var lastId = row.data('id');
                var id = lastId + 1;

                row.attr('data-id', id);
                row.find('td').first().html(id);
                row.find('.help-block').remove();
                row.find('.genera-fattura-scadenza').remove();
                var counter = 0;
                row.find('input, select').each(function(i, el) {
                    var input = $(el);
                    var name = input.attr('name').replace('['+lastId+']', '['+id+']');

                    input.parent().removeClass('has-error');
                    input.attr('name', name);
                    input.val('');
                });



                row.find('button').attr('onclick', 'delFatturazioneScadenza('+id+')');

                $('#scadenze tbody').append(row);

                bootJs();
            });
        });

        function delFatturazioneScadenza(id) {
            var nScadenze = $('#scadenze tbody tr').length;
            var row = $('#scadenze tbody tr[data-id="'+id+'"]');

            row.find('input, select').val('');

            if(nScadenze > 1)
                row.remove();
            else
                alert('AVVISO: non è possibile eliminare questa riga.');
        }
    </script>
@endpush
