@php
    $fatturazione = (empty($fatturazione)) ? '' : $fatturazione;

    $cliente = !(empty($ordinativo)) ? $ordinativo->offerta->cliente : '';

    $cliente_indirizzi = (empty($ordinativo)) ? [] : $ordinativo->offerta->cliente->indirizzi->pluck('indirizzo_completo','indirizzo_completo')->toArray();

    $iva = (get_if_exist($fatturazione, 'iva') ) ? get_if_exist($fatturazione, 'iva') : config('commerciale.offerte.iva');
    $iva_0 = config('commerciale.fatturazioni.iva_tipi');

    $iva_natura = $iva_0;
    $iva_natura[0] = '';

    $attivita_svolte = config('commerciale.fatturazioni.attivita_svolte');

    $azienda = get_azienda_dati();

    $c_voci = 1;
@endphp

<!-- title row -->
<div class="row">
    <div class="col-xs-12">
        <h2 class="page-header">
            <i class="fa fa-globe"></i> {{ session('azienda') }}
            <small class="pull-right text-right">
              <strong>Data</strong>
              <br>
              {{ get_if_exist($fatturazione, 'data') }}
            </small>
        </h2>
    </div>
    <div class="col-md-3">
      {{ $macrocategorie[get_if_exist($fatturazione, 'macrocategoria')] }}
    </div>
    <!-- /.col -->
</div>
<!-- info row -->
<div class="row invoice-info">
    <div class="col-sm-5 invoice-col">
        <h4 class="row">
            <span class="col-sm-12">
                Numero fattura:
                <br>
                <strong class="numero_fattura">{{ (!empty($fattura_numero)) ? $fattura_numero : $fatturazione->get_numero_fattura() }}</strong>
            </span>
        </h4>
        <br><br>
        <div class="row">
          <div class="col-md-9">
            @if(get_if_exist($fatturazione, 'ordinativo_id'))
                <strong>Ordinativo</strong>:
                <button type="button" class="btn btn-xs" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#modal-default" data-size="modal-xl" data-title="Dettagli" data-action="{{ route('admin.commerciale.ordinativo.read', $fatturazione->ordinativo_id) }}" data-element="form" data-ajax="true">
                </button>
                <br>
            @else
                <strong>Nessun ordinativo collegato</strong>
            @endif
          </div>
        </div>
    </div>
    <!-- /.col -->
    <div class="col-sm-3 invoice-col">
        <address>
            <strong>{{ $azienda->ragione_sociale }}</strong><br>
            {{ $azienda->indirizzo }}, {{ $azienda->numero_civico }}<br>
            {{ $azienda->citta }}, {{ $azienda->provincia }} {{ $azienda->cap }}<br>
            Telefono: {{ $azienda->telefono }}<br>
            Email: {{ $azienda->email }}
        </address>
    </div>
    <!-- /.col -->
    <div class="col-sm-4 invoice-col">
        <address>
            <strong>Cliente</strong>
            <br>
            @if(get_if_exist($fatturazione, 'cliente_id'))
              {{ get_if_exist($fatturazione->cliente, 'ragione_sociale') }}
            @else
              {{ !empty($ordinativo) ? $ordinativo->offerta->cliente->ragione_sociale : '' }}
            @endif
              <br>
              {{ get_if_exist($fatturazione, 'indirizzo') }}
        </address>
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

<hr>

<div class="row">
    <div class="col-md-3">
      @if(get_if_exist($fatturazione, 'id_tipologia_fornitura'))
        <strong>Tipo di fornitura</strong>:
        {{ config('commerciale.fatturazioni.tipologia_fornitura')[get_if_exist($fatturazione, 'id_tipologia_fornitura')] }}
      @endif
    </div>
    <div class="col-md-2">
      @if(get_if_exist($fatturazione, 'cig'))
        <strong>Cig</strong>:
        {{ get_if_exist($fatturazione, 'cig') }}
      @endif
    </div>
    <div class="col-md-2">
      @if(get_if_exist($fatturazione, 'rda'))
        <strong>Rda</strong>:
        {{ get_if_exist($fatturazione, 'rda') }}
      @endif
    </div>
    <div class="col-md-2">
      @if(get_if_exist($fatturazione, 'rda'))
        <strong>Data Rda</strong>:
        {{ get_if_exist($fatturazione, 'rda_data') }}
      @endif
    </div>
    <div class="col-md-3">
      @if(get_if_exist($fatturazione, 'codice_univoco'))
        <strong>Codice Univoco</strong>:
        {{ (!empty(get_if_exist($fatturazione, 'codice_univoco')) ? $fatturazione->codice_univoco : (!empty($cliente) ? $cliente->codice_univoco : '')) }}
      @endif
    </div>
</div>
<hr>


<!-- Table row -->
<div class="row">
    <div class="col-xs-12 table-responsive">
        <table class="table table-striped voci">
            <thead>
                <tr>
                    <th style="width:2%;">#</th>
                    <th>Descrizione</th>
                    <th style="width:15%;">Attività svolta</th>
                    <th style="width:5%;" class="text-right">Quantità</th>
                    <th style="width:12%;" class="text-right">Importo Singolo</th>
                    <th style="width:9%;" class="text-right">IVA</th>
                    <th style="width:12%;" class="text-right">Importo</th>
                    <th style="width:12%;" class="text-right">Importo con IVA</th>
                    <th style="width:2%;">Esente IVA</th>
                </tr>
            </thead>
            <tbody>
                @if(count(get_if_exist($fatturazione, 'voci')) > 0)
                  @foreach ($fatturazione->voci as $key => $voce)
                      @php
                          $iva_00 = $iva_0;

                          if($voce->iva == 0)
                              unset($iva_00[0]);
                      @endphp
                      <tr data-id="{{ $c_voci }}">
                          <td>{{ $c_voci++ }}.</td>
                          <td>{{ get_if_exist($voce, 'descrizione') }}</td>
                          <td>{{ $attivita_svolte[get_if_exist($voce, 'attivita_svolta')] }}</td>
                          <td class="text-right">{{ get_if_exist($voce, 'quantita') }}</td>
                          <td class="text-right">{{ get_if_exist($voce, 'importo_singolo') }}</td>
                          <td class="text-right">{{ $iva_00[get_if_exist($voce, 'iva_tipo')] }}</td>
                          <td class="text-right">{{ get_if_exist($voce, 'importo') }}</td>
                          <td class="text-right">{{ get_if_exist($voce, 'importo_iva') }}</td>
                          <td >{{ sn(get_if_exist($voce, 'esente_iva') ? $voce->esente_iva : 0) }}</td>
                      </tr>
                  @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

<div class="row">
    <div class="col-xs-6">
        <p class="lead">Info:</p>

        <strong>Oggetto</strong>:
        <br>
        {{ get_if_exist($fatturazione, 'oggetto') ? $fatturazione->oggetto : (!empty($ordinativo) ? $ordinativo->oggetto : '') }}

        <br><br>

        @if(get_if_exist($fatturazione, 'note'))
          <strong>Note</strong>:
          <br>
          {{ get_if_exist($fatturazione, 'note') }}
        @endif

        <hr>

        <div class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
            <div class="col-sm-4">
              <strong>Iva Erario per PA</strong>:
              {{ sn(get_if_exist($fatturazione, 'iva_erario') ? $fatturazione->iva_erario : 0) }}
            </div>
            <div class="col-sm-4">
              <strong>Iva Esigibile</strong>:
              {{ sn(get_if_exist($fatturazione, 'iva_esigibile') ? $fatturazione->iva_esigibile : 0) }}
            </div>
            <div class="col-sm-4">
              <strong>Iva Esigibile</strong>:
              {{ sn(get_if_exist($fatturazione, 'nota_di_credito') ? $fatturazione->nota_di_credito : 0) }}
            </div>
            <br class="clearfix"><br>
            <div class="col-sm-6">
              <strong>Nota di Credito Interna</strong>:
              {{ sn(get_if_exist($fatturazione, 'nota_di_credito_interna') ? $fatturazione->nota_di_credito_interna : 0) }}
            </div>
            <br class="clearfix"><br>
        </div>
        <br>
        <div class="row">
            <div class="col-md-4">
              @if(get_if_exist($fatturazione, 'n_giorni'))
                <strong>Numero di Giorni</strong>:
                {{ get_if_exist($fatturazione, 'n_giorni') }}
              @endif
            </div>
            <div class="col-md-8">
              @if(get_if_exist($fatturazione, 'tipo_pagamento'))
                <strong>Tipo di Pagamento</strong>:
                <br>
                {{ config('commerciale.fatturazioni.tipologia_pagamento')[get_if_exist($fatturazione, 'tipo_pagamento')] }}
              @endif
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-4">
              @if(get_if_exist($fatturazione, 'anticipata_id'))
                <strong>Anticipata</strong>:
                <br>
                {{ config('commerciale.fatturazioni.anticipata')[get_if_exist($fatturazione, 'anticipata_id')] }}
              @endif
            </div>
            <div class="col-md-8">
              @if(get_if_exist($fatturazione, 'iban'))
                <strong>IBAN</strong>:
                <br>
                {{ get_if_exist($fatturazione, 'iban') }}
              @endif
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
              @if(get_if_exist($fatturazione, 'riferimento_normativo'))
                <strong>Riferimento normativo</strong>:
                <br>
                {{ get_if_exist($fatturazione, 'riferimento_normativo') }}
              @endif
            </div>
        </div>
    </div>
    <!-- /.col -->
    <div class="col-xs-6">
        <p class="lead">Importo</p>

        <div class="table-responsive">
            <table class="table">
                <tbody>
                    <tr>
                        <th style="width:50%">Acconto:</th>
                        <td class="text-right">{{ get_if_exist($fatturazione, 'acconto') }}</td>
                    </tr>
                    <tr>
                        <th>Totale Netto:</th>
                        <td class="text-right">{{ get_if_exist($fatturazione, 'totale_netto') }}</td>
                    </tr>
                    <tr>
                        <th>IVA:</th>
                        <td class="text-right">{{ $iva }}</td>
                    </tr>
                    <tr>
                        <th>Natura esente IVA:</th>
                        <td class="text-right">{{ $iva_natura[get_if_exist($fatturazione, 'iva_natura')] }}</td>
                    </tr>
                    <tr>
                        <th>Totale Fattura:</th>
                        <td class="text-right">{{ get_if_exist($fatturazione, 'totale_fattura') }}</td>
                    </tr>
                    <tr>
                        <th>Totale Importo Dovuto:</th>
                        <td class="text-right">{{ get_if_exist($fatturazione, 'totale_importo_dovuto') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

<hr>

<!-- this row will not appear when printing -->
@if(!empty($fatturazione))
    <div class="row no-print">
        <div class="col-xs-12">
            @if($fatturazione->pagata == 1)
              <span class="label bg-blue">Pagata</span>
            @endif

            @if($fatturazione->consegnata == 1)
              <span class="label bg-blue">Consegnata</span>
            @endif

            @if($fatturazione->anticipata == 1)
              <span class="label bg-blue">Anticipata</span>
            @endif

            <a class="btn btn-success btn-flat pull-right" style="margin-right: 5px;" target="_blank" href="{{ route('admin.commerciale.fatturazione.pdf', $fatturazione->id) }}">
                <i class="fa fa-download"></i> Genera PDF
            </a>
            <a class="btn btn-warning btn-flat pull-right" href="{{ route('admin.commerciale.fatturazione.xml', $fatturazione->id) }}">
                <i class="fa fa-download"></i> Genera XML
            </a>
        </div>
    </div>
    <br>
@endif


@include('commerciale::admin.partials.voci_js')
@push('js-stack')
    <script type="text/javascript">
        $(document).ready(function() {
            // Ordinativo
            @if(!get_if_exist($fatturazione, 'ordinativo_id'))
                $('#ordinativo_id').change(function() {
                    var ordinativoId = $(this).val();

                    @if(get_if_exist($fatturazione, 'id'))
                      location.href = "{{ route(\Request::route()->getName(), $fatturazione->id) }}?ordinativo_id=" + ordinativoId;
                    @else
                      location.href = "{{ route(\Request::route()->getName()) }}?ordinativo_id=" + ordinativoId;
                    @endif
                });
            @endif

            // Nota di credito interna
            $("#nota_di_credito_interna").on('ifChecked', function(event) {
              var nci_valore = $('#numero_nota_di_credito_interna').val();
              var nci_codice = $('#numero_nota_di_credito_interna_codice').val();

              $('.numero_fattura').html(nci_codice);
              $('#n_fattura').val(nci_valore);
            });
            $("#nota_di_credito_interna").on('ifUnchecked', function(event) {
               if($("#fepa").prop('checked')) {
                 var codice = $('#numero_fattura_fepa').val();
                 var valore = $('#numero_fattura_fepa_valore').val();
               } else {
                 var codice = $('#numero_fattura').val();
                 var valore = $('#numero_fattura_valore').val();
               }

               $('.numero_fattura').html(codice);
               $('#n_fattura').val(valore);
            });

            // Fepa
            $("#fepa").on('ifChecked', function(event){
              var fepa_codice = $('#numero_fattura_fepa').val();
              var fepa_valore = $('#numero_fattura_fepa_valore').val();

              $('.numero_fattura').html(fepa_codice);
              $('#n_fattura').val(fepa_valore);
            });
            $("#fepa").on('ifUnchecked', function(event){
               var codice = $('#numero_fattura').val();
               var valore = $('#numero_fattura_valore').val();

               $('.numero_fattura').html(codice);
               $('#n_fattura').val(valore);
            });
            $("#fepa").on('ifCreated', function(e) {
                $(this).iCheck('toggle');
                $(this).iCheck('toggle');
            });

            // Indirizzi
            $('#cliente_id').change(function() {
                var token = $('input[name="_token"]').val();
                var clienteId = $(this).val();

                $('#btn-create-indirizzo').removeClass('hidden');

                if(clienteId > 0) {
                    var urlCreateIndirizzo = $('#route-create-indirizzo').val();

                    urlCreateIndirizzo = urlCreateIndirizzo.replace("/0/", "/" + clienteId + "/");
                    $('#btn-create-indirizzo').attr('data-action', urlCreateIndirizzo);

                    $.post('{{route('admin.amministrazione.clienti.cliente.json')}}', { _token : token , cliente_id : clienteId })
                        .done(function( data ) {
                            var cliente = JSON.parse(data);
                            var codUnivoco = $('#codice_univoco');

                            if(codUnivoco.val() == '')
                              $('#codice_univoco').val(cliente.codice_univoco);
                        });

                    $.post('{{route('admin.amministrazione.clienti.indirizzi.json')}}', { _token : token , cliente_id : clienteId })
                        .done(function( data ) {
                            var indirizzi = JSON.parse(data);
                            var indirizzoSelected = $('#indirizzo-selected').val();

                            $('#indirizzo').html('');

                            $.each(indirizzi, function(i, item) {
                                 var optionIndirizzo = indirizzi[i].indirizzo_completo;
                                 var htmlIndirizzi = '<option value="'+optionIndirizzo+'">'+optionIndirizzo+'</option>';

                                 $('#indirizzo').append(htmlIndirizzi);
                            });

                            $('#indirizzo').val(indirizzoSelected);
                        });
                } else {
                    $('#btn-create-indirizzo').addClass('hidden');
                    $('#indirizzo').html('');
                }
            });
            $('#cliente_id').change();

            // Stati
            $("#pagata").click(function() {
                if($(this).hasClass('active'))
                {
                     $('input[name="pagata"]').val(0);
                     $(this).removeClass('active');
                     $('#pagata_icona').addClass('hidden');
                }
                else
                {
                    $('input[name="pagata"]').val(1);
                    $(this).addClass('active');
                    $('#pagata_icona').removeClass('hidden');
                }
            });
            $("#consegnata").click(function() {
                if($(this).hasClass('active'))
                {
                     $('input[name="consegnata"]').val(0);
                     $(this).removeClass('active');
                     $('#consegnata_icona').addClass('hidden');
                }
                else
                {
                    $('input[name="consegnata"]').val(1);
                    $(this).addClass('active');
                    $('#consegnata_icona').removeClass('hidden');
                }
            });
            $("#anticipata").click(function() {
                if($(this).hasClass('active'))
                {
                     $('input[name="anticipata"]').val(0);
                     $(this).removeClass('active');
                     $('#anticipata_icona').addClass('hidden');
                }
                else
                {
                    $('input[name="anticipata"]').val(1);
                    $(this).addClass('active');
                    $('#anticipata_icona').removeClass('hidden');
                }
            });

            // Submit form
            $("form").submit(function(e) {
                var inputs = $(this).serializeArray();

                $.each(inputs, function(i, input) {
                    if(input.name.indexOf("totale") >= 0 || input.name.indexOf("importo") >= 0 || input.name == 'iva' || input.name == 'acconto') {
                        $('input[name="'+input.name+'"]').val(cleanCurrency(input.value));
                    }
                });
            });
        });

       function calcolaTotale(ctrlAvvio = false) {
           var importo = 0;
           var importoIva = 0;
           var voci = $('table.voci tbody tr');
           var iva = cleanCurrency($('input[name="iva"]').val());
           var acconto = cleanCurrency($('input[name="acconto"]').val());

           if(!$.isNumeric(iva)) {
               iva = 0;
           }
           $('input[name="iva"]').val(iva);

           if($.isNumeric(iva) && $.isNumeric(acconto)) {
               voci.each(function(index) {
                   $(this).removeAttr('style');

                   if($(this).find('input[name*="descrizione"]').length > 0) {
                       if($(this).find('input[name*="descrizione"]').val().trim() !== '') {
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
                       } else {
                           $(this).attr('style', 'background-color: #f2dede');
                       }
                   }
               });

               if(ctrlAvvio) {
                    $('input[name="acconto"]').val(formatNumber(acconto, '€'));
               }

               $('input[name="totale_netto"]').val(formatNumber(importo, '€'));
               $('input[name="totale_fattura"]').val(formatNumber(importoIva, '€'));
               $('input[name="totale_importo_dovuto"]').val(formatNumber((importoIva - acconto), '€'));
           }
       }
   </script>
@endpush
