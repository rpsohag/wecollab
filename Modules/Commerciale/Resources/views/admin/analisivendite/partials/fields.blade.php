<div class="box-body">
    <div class="row">
        <div class="pull-right"><button type="submit" class="btn btn-success btn-md btn-flat"> <i class="fa fa-floppy-o"></i> Salva Modifiche</button></div>
        @if(!empty($analisivendita->created_at))
            <div class="pull-right"><a class="btn btn-info   btn-flat" style="margin-right:4px;" href="{{ route('admin.commerciale.analisivendita.read', [$analisivendita->id])}} "><i class="fa fa-eye"></i> Vedi</a></div>
        @endif
        <div class="col-md-3">
            <div class="info-box bg-gray">
                <span class="info-box-icon bg-aqua"><i class="fa fa-building"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Censimento Cliente</span>
                    <strong class="info-box-text">
                        @if(!empty($analisivendita->created_at))
                            <a href="{{ route('admin.commerciale.censimentocliente.read', $analisivendita->censimento_id) }}">{{ optional(optional(optional(optional($analisivendita->censimento_cliente())->first())->cliente())->first())->ragione_sociale }}</a>
                        @endif

                        <input type="hidden" name="censimento_id" value="{{ $analisivendita->censimento_id ?? request('censimentocliente_id') }}">
                    </strong>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="col-md-8">
                {{ Form::weText('titolo', 'Oggetto *', $errors, !empty($analisivendita && $analisivendita->titolo) ? get_if_exist($analisivendita, 'titolo') : '') }}
            </div>
            <div class="col-md-4">
                {{ Form::weDate('data', 'Data *', $errors, !empty($analisivendita && $analisivendita->data) ? get_if_exist($analisivendita, 'data') : date('d/m/Y')) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-9">
            {{ Form::weTags('segnalazione[]', 'Segnalazioni Opportunità di provenienza *', $errors, $segnalazioni, $segnalazioni_selected, ['id' => 'segnalazioni', 'multiple']) }}
        </div>
        <div class="col-md-3">
            {{ Form::weSelectSearch('commerciale_id', 'Commerciale assegnato *', $errors, $commerciali, get_if_exist($analisivendita, 'commerciale_id')) }}
        </div>
    </div>
    {{-- Costi fissi --}}
    <div id="costi-fissi" class="box box-info box-solid">
        <div class="box-header with-border">
            <div class="row">
                <div class="col-md-11">
                    <h3 class="no-margin">
                        Costi fissi
                        <span class="label label-success pull-right">
                            Totale: <span class="totale-costi-fissi">€ 0,00</span>
                        </span>
                    </h3>
                </div>
                <div class="col-md-1 text-right">
                    <button class="btn btn-warning btn-flat" type="button" data-toggle="tooltip" title="Aggiungi costo fisso" onclick="addCostoFisso()">
                        <i class="fa fa-plus"> </i>
                    </button>
                </div> 
            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th class="text-center">Descrizione</th>
                        <th class="text-center" style="width: 10%;">Quantità</th>
                        <th class="text-center">Costo unitario</th>
                        <th class="text-center">Costo di uscita</th>
                        <th class="text-center">Link per l'acquisto</th>
                        <th class="text-center" style="width: 10%;">% di rincaro</th>
                        <th class="text-center" style="width: 20%;">Totale</th>
                        <th class="text-center">#</th>
                    </tr>
                    @php $c = 0 @endphp
                    @if(!empty($analisivendita->costi_fissi))
                        @foreach($analisivendita->costi_fissi as $key => $costo_fisso)
                            <tr class="costo-fisso" data-row="{{ $c }}">
                                <td>
                                    {{ Form::weText('costi_fissi['.$c.'][descrizione]', '', $errors, get_if_exist($costo_fisso, 'descrizione')) }}
                                </td>
                                <td>
                                    {{ Form::weInt('costi_fissi['.$c.'][quantita]', '', $errors, get_if_exist($costo_fisso, 'quantita') ?? 1, ['onchange' => 'totaleCostiFissi()', 'min' => 1]) }}
                                </td>
                                <td>
                                    {{ Form::weCurrency('costi_fissi['.$c.'][costo_unitario]', '', $errors, get_if_exist($costo_fisso, 'costo_unitario'), ['onchange' => 'totaleCostiFissi()']) }}
                                </td>
                                <td>
                                    {{ Form::weCurrency('costi_fissi['.$c.'][prezzo_di_uscita]', '', $errors, get_if_exist($costo_fisso, 'prezzo_di_uscita'), ['onchange' => 'calcoloPercentualeRincaro()']) }}
                                </td>
                                <td>
                                    {{ Form::weText('costi_fissi['.$c.'][link_acquisto]', '', $errors, get_if_exist($costo_fisso, 'link_acquisto')) }}
                                </td>
                                <td>
                                    {{ Form::weText('costi_fissi['.$c.'][percentuale_rincaro]', '', $errors, get_if_exist($costo_fisso, 'percentuale_rincaro') ?? 20, ['onchange' => 'totaleCostiFissi()']) }}
                                </td>
                                <td class="text-center">
                                    <h4 class="no-margin totale"></h4>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-xs btn-flat btn-danger remove-costo-fisso" type="button" onclick="removeCostoFisso({{$c++}})">
                                        <i class="fa fa-times"> </i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="costo-fisso" data-row="{{ $c }}">
                            <td>
                                {{ Form::weText('costi_fissi['.$c.'][descrizione]', '', $errors) }}
                            </td>
                            <td>
                                {{ Form::weInt('costi_fissi['.$c.'][quantita]', '', $errors, 1, ['onchange' => 'totaleCostiFissi()', 'min' => 1]) }}
                            </td>
                            <td>
                                {{ Form::weCurrency('costi_fissi['.$c.'][costo_unitario]', '', $errors, '', ['onchange' => 'totaleCostiFissi()']) }}
                            </td>
                            <td>
                                {{ Form::weCurrency('costi_fissi['.$c.'][prezzo_di_uscita]', '', $errors, '', ['onchange' => 'calcoloPercentualeRincaro()']) }}
                            </td>
                            <td>
                                {{ Form::weText('costi_fissi['.$c.'][link_acquisto]', '', $errors) }}
                            </td>
                            <td>
                                {{ Form::weText('costi_fissi['.$c.'][percentuale_rincaro]', '', $errors, 20, ['onchange' => 'totaleCostiFissi()']) }}
                            </td>
                            <td class="text-center">
                                <h4 class="no-margin totale">€ 0,00</h4>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-flat btn-danger remove-costo-fisso" type="button" onclick="removeCostoFisso({{$c++}})">
                                    <i class="fa fa-times"> </i>
                                </button>
                            </td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- Canoni annuali --}}
    @if(!empty($analisivendita->canoni) && !empty($analisivendita->canoni->anni))
        <div id="canoni" class="box box-success box-solid">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-md-6">
                        <h3>Canoni annuali</h3>
                    </div>
                    <div class="col-md-3">
                        {{ Form::weInt('canoni[anno_inizio]', 'Anno inizio', $errors, (get_if_exist($analisivendita->canoni, 'anno_inizio') ? $analisivendita->canoni->anno_inizio : date('Y')), ['onchange' => 'reloadCanoniAnni()']) }}
                    </div>
                    <div class="col-md-3">
                        {{ Form::weInt('canoni[durata]', 'Durata (anni)', $errors, (get_if_exist($analisivendita->canoni, 'durata') ? $analisivendita->canoni->durata : 1), ['onchange' => 'reloadCanoniAnni()', 'min' => 1]) }}
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    @if(!empty($analisivendita->canoni) && !empty($analisivendita->canoni->anni))
                        @foreach($analisivendita->canoni->anni as $anno => $canone)
                            <div class="col-md-2 canone">
                                {{ Form::weCurrency('canoni[anni]['.$anno.'][canone]', $anno, $errors, get_if_exist($canone, 'canone'), ['data-anno' => $anno]) }}
                            </div>
                        @endforeach
                    @else
                        <div class="col-md-2 canone">
                            {{ Form::weCurrency('canoni[anni]['.date("Y").'][canone]', date("Y"), $errors, '€ 0,00', ['data-anno' => date('Y')]) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Attività --}}
    <input id="figure-professionali" type="hidden" value="{{ $figureprofessionali->toJSON() }}">

    @foreach ($procedure as $procedura)
        <div class="col-md-12">
            <div class="table-responsive table-procedura">
                <table class="table table-bordered procedura" data-procedura="{{ $procedura->id }}">
                    <caption>
                        <h3>
                            Checklist <strong>{!! $procedura->titolo !!}</strong>
                            <span class="label label-info pull-right">
                                Totale: <span class="totale-procedura-{{ $procedura->id }}">€ 0,00</span>
                            </span>
                        </h3>
                    </caption>
                    <tr>
                        <td class="padding">
                            @foreach ($procedura->aree as $area)
                                @php
                                $test_tmp = 0;
                                foreach($area->attivita as $tmp_attivita)
                                {
                                    if(!empty($analisivendita->attivita) > 0 && property_exists($analisivendita->attivita
                                    , $tmp_attivita->id ) && $analisivendita->attivita->{$tmp_attivita->id}->selected == 1)
                                        $test_tmp = 1;
                                }
                                @endphp
                                @if($test_tmp == 0)
                                    <div class="box box-white box-solid collapsed-box">
                                @else
                                    <div class="box box-white box-solid">
                                @endif
                                <div class="box-header with-border">
                                    <h4>{!! $area->titolo !!}</h4>
                                    <div class="box-tools pull-right">
                                        <span class="label label-info">
                                            Totale: <span class="totale-area-{{ $area->id }}">€ 0,00</span>
                                        </span>
                                        @if ($test_tmp == 0)
                                            <button type="button" class="btn btn-box-tool " data-widget="collapse"><i class="fa fa-plus"></i></button>
                                        @else
                                            <button type="button" class="btn btn-box-tool " data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        @endif
                                    </div>
                                </div>
                                <div class="box-body">
                                    <table class="table table-hover area" data-area="{{ $area->id }}">
                                        @foreach ($area->attivita as $attivita)
                                            @if (!empty($attivita->id))
                                                <tr>
                                                    <td style="width: 2%">
                                                        {{ Form::weCheckbox('attivita[' . $attivita->id . '][selected]', '', $errors, !empty($analisivendita->attivita->{$attivita->id}) ? get_if_exist($analisivendita->attivita->{$attivita->id}, 'selected') : '', 'data-attivita-id="'.$attivita->id.'"') }}
                                                    </td>
                                                    <td>
                                                        <h5>
                                                            {!! $attivita->nome !!}

                                                            <span class="label label-info">
                                                                Totale: <span class="totale-attivita-{{ $attivita->id }}">€ 0,00</span>
                                                            </span>
                                                        </h5>
                                                    </td>
                                                    <td class="text-right">
                                                        <div class="btn-group">
                                                            {{-- Attività in carico cliente --}}
                                                            <button
                                                              class="btn btn-md {{ !empty($attivita->id) && (!empty($analisivendita->attivita->{$attivita->id}->{'plus_emersi_demo'}) || !empty($analisivendita->attivita->{$attivita->id}->{'criticita_emerse_demo'}) || !empty($analisivendita->attivita->{$attivita->id}->{'analisi_note_tecnico'}) || !empty($analisivendita->attivita->{$attivita->id}->{'attivita_carico_cliente'})) ? 'btn-primary' : 'btn-default' }}"
                                                              type="button" data-toggle="modal"
                                                              data-target="#modal-attivita-carico-cliente-{{ $attivita->id }}">
                                                              <i
                                                                  class="fa fa-list" data-toggle="tooltip"
                                                                  title="Scrivi/Leggi le attività in carico cliente">
                                                              </i>
                                                            </button>
                                                            {{-- Modal carico cliente --}}
                                                            <div class="modal fade"
                                                              id="modal-attivita-carico-cliente-{{ $attivita->id }}"
                                                              tabindex="-1" role="dialog"
                                                              aria-labelledby="Attività in carico cliente">
                                                              <div class="modal-dialog" role="document">
                                                                  <div class="modal-content">
                                                                      <div class="modal-header">
                                                                          <button type="button" class="close"
                                                                              data-dismiss="modal"
                                                                              aria-label="Close"><span
                                                                                  aria-hidden="true">&times;</span></button>
                                                                          <h4 class="modal-title"
                                                                              id="modal-attivita-carico-cliente-label-{{ $attivita->id }}">
                                                                              {{ $attivita->nome }}
                                                                          </h4>
                                                                      </div>
                                                                      <div class="modal-body">
                                                                          {{ Form::weTextarea('attivita[' . $attivita->id . '][plus_emersi_demo]', 'Plus Emersi da Demo', $errors, !empty($analisivendita->attivita->{$attivita->id}) ? get_if_exist($analisivendita->attivita->{$attivita->id}, 'plus_emersi_demo') : '') }}
                                                                      </div>
                                                                      <div class="modal-body">
                                                                          {{ Form::weTextarea('attivita[' . $attivita->id . '][criticita_emerse_demo]', 'Criticità Emerse da demo', $errors, !empty($analisivendita->attivita->{$attivita->id}) ? get_if_exist($analisivendita->attivita->{$attivita->id}, 'criticita_emerse_demo') : '') }}
                                                                      </div>
                                                                      <div class="modal-body">
                                                                          {{ Form::weTextarea('attivita[' . $attivita->id . '][analisi_note_tecnico]', 'Analisi Note Tecnico', $errors, !empty($analisivendita->attivita->{$attivita->id}) ? get_if_exist($analisivendita->attivita->{$attivita->id}, 'analisi_note_tecnico') : '') }}
                                                                      </div>
                                                                      <div class="modal-body">
                                                                          {{ Form::weTextarea('attivita[' . $attivita->id . '][attivita_carico_cliente]', 'Attività in carico cliente', $errors, !empty($analisivendita->attivita->{$attivita->id}) ? get_if_exist($analisivendita->attivita->{$attivita->id}, 'attivita_carico_cliente') : '') }}
                                                                      </div>
                                                                      <div class="modal-footer">
                                                                          <button type="button"
                                                                              class="btn btn-primary"
                                                                              data-dismiss="modal">Salva</button>
                                                                      </div>
                                                                  </div>
                                                              </div>
                                                            </div>
                                                            {{-- Note --}}
                                                            <button
                                                              class="btn btn-md {{ !empty($attivita->id) && !empty($analisivendita->attivita->{$attivita->id}->{'nota'}) ? 'btn-primary' : 'btn-default' }}"
                                                              type="button" data-toggle="modal"
                                                              data-target="#modal-note-{{ $attivita->id }}">
                                                              <i class="fa fa-edit" data-toggle="tooltip"
                                                                  title="Scrivi/Leggi le note"> </i>
                                                            </button>
                                                            {{-- Modal note --}}
                                                            <div class="modal fade"
                                                              id="modal-note-{{ $attivita->id }}" tabindex="-1"
                                                              role="dialog" aria-labelledby="Note">
                                                              <div class="modal-dialog" role="document">
                                                                  <div class="modal-content">
                                                                      <div class="modal-header">
                                                                          <button type="button" class="close"
                                                                              data-dismiss="modal"
                                                                              aria-label="Close"><span
                                                                                  aria-hidden="true">&times;</span></button>
                                                                          <h4 class="modal-title"
                                                                              id="modal-note-label-{{ $attivita->id }}">
                                                                              {{ $attivita->nome }}
                                                                          </h4>
                                                                      </div>
                                                                      <div class="modal-body">
                                                                          {{ Form::weTextarea('attivita[' . $attivita->id . '][nota]', 'Nota', $errors, !empty($analisivendita->attivita->{$attivita->id}) ? get_if_exist($analisivendita->attivita->{$attivita->id}, 'nota') : '') }}
                                                                      </div>
                                                                      <div class="modal-footer">
                                                                          <button type="button"
                                                                              class="btn btn-primary"
                                                                              data-dismiss="modal">Salva</button>
                                                                      </div>
                                                                  </div>
                                                              </div>
                                                            </div>
                                                            <button class="btn btn-md btn-info" type="button" onclick="addFiguraProfessionale({{$attivita->id}})" data-toggle="tooltip" title="Aggiungi risorsa">
                                                                <i class="fa fa-plus"> </i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="bg-warning">
                                                    <td colspan="3">
                                                        @php $i = 0 @endphp

                                                        <table class="table figure-professionali" data-attivita="{{$attivita->id}}">
                                                        @if(!empty($analisivendita->attivita->{$attivita->id}) && !empty($analisivendita->attivita->{$attivita->id}->figure_professionali))
                                                                @foreach($analisivendita->attivita->{$attivita->id}->figure_professionali as $figura_professionale)
                                                                <tr class="figura-professionale" data-row="{{$i}}">
                                                                    <td style="width: 75%" class="text-center">Risorsa
                                                                        {{ Form::weSelect('attivita[' . $attivita->id . '][figure_professionali]['.$i.'][figura_professionale_id]', '', $errors, $figureprofessionali_select, get_if_exist($figura_professionale, 'figura_professionale_id'), ['auto-check', 'onchange' => 'calcolaTotale()', (empty(get_if_exist($analisivendita->attivita->{$attivita->id}, 'selected')) ? 'readonly' : '')]) }}
                                                                    </td>
                                                                    <td class="text-center">Ore 
                                                                        {{ Form::weInt('attivita[' . $attivita->id . '][figure_professionali]['.$i.'][ore]', '', $errors, !empty($figura_professionale->ore) ? get_if_exist($figura_professionale, 'ore') : '', ['min' => 0, 'auto-check', 'onchange' => 'calcolaTotale()', (empty(get_if_exist($analisivendita->attivita->{$attivita->id}, 'selected')) ? 'readonly' : '')]) }}
                                                                    </td>
                                                                    <td class="text-right" style="width: 10%">
                                                                        <button class="btn btn-xs btn-flat btn-danger remove-figura-professionale" type="button" onclick="removeFiguraProfessionale({{$attivita->id}}, {{$i++}})">
                                                                            <i class="fa fa-times"> </i>
                                                                        </button>
                                                                        <br>
                                                                        <div class="text-center">
                                                                            <strong>
                                                                                Totale
                                                                                <br>
                                                                                <span class="totale-figura-professionale">0</span>
                                                                            </strong>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr class="figura-professionale" data-row="{{$i}}">
                                                                <td style="width: 75%" class="text-center">Risorsa
                                                                    {{ Form::weSelect('attivita[' . $attivita->id . '][figure_professionali]['.$i.'][figura_professionale_id]', '', $errors, $figureprofessionali_select, '', ['auto-check', 'onchange' => 'calcolaTotale()']) }}
                                                                </td>
                                                                <td class="text-center">Ore
                                                                    {{ Form::weInt('attivita[' . $attivita->id . '][figure_professionali]['.$i.'][ore]', '', $errors, '', ['min' => 0, 'auto-check', 'onchange' => 'calcolaTotale()']) }}
                                                                </td>

                                                                <td style="width: 10%" >
                                                                    <button class="btn btn-xs btn-flat btn-danger remove-figura-professionale" type="button" onclick="removeFiguraProfessionale({{$attivita->id}}, {{$i++}})">
                                                                        <i class="fa fa-times"> </i>
                                                                    </button>
                                                                    <br>
                                                                    <div class="text-center">
                                                                        <strong>
                                                                            Totale
                                                                            <br>
                                                                            <span class="totale-figura-professionale">0</span>
                                                                        </strong>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        </table>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </table>
                                </div>
              </div>
            @endforeach
            </td>
          </tr>
        </table>
      </div>
    </div>
  @endforeach
</div>

{{-- Templates --}}
<div class="box box-solid templates-box">
    <div class="box-header">
        <h3>Templates</h3>
        <div class="row">
            <div class="col-md-6">
                <button class="btn bg-green btn-flat" type="button" onclick="saveTemplate()">
                    <i class="fa fa-floppy-o" aria-hidden="true"></i> </i> Salva Template
                </button>
                <button class="btn bg-blue btn-flat" type="button" onclick="loadTemplate()">
                    <i class="fa fa-upload" aria-hidden="true"></i> Carica Template
                </button>
            </div>
        </div>
    </div>
    <div class="box-body templates-boxbody">
        <div class="row hidden templates-input-nome">
            <div class="col-md-3">
                {{ Form::weText('template_nome', 'Nome Template', $errors) }}
            </div>
        </div>
        <div class="row hidden templates-input-select">
            <div class="col-md-3">
                {{ Form::weSelectSearch('template_caricato_id', 'Template', $errors, $templates, '', ['onchange' => 'loadTemplatePage()', 'id' => 'template_caricato_id']) }}
            </div>
        </div>
    </div>
</div>

@push('js-stack')
    <script>
        $(document).ready(function() {
            //$('.table-procedura input:not(input:checkbox), .table-procedura select').attr('readonly', 'readonly').addClass('disabled');
            $('input:checkbox').on('ifChanged', function (event) {
                var attivitaId = $(this).attr('data-attivita-id');
                var figureProfessionali = $('.figure-professionali[data-attivita="'+attivitaId+'"]').find('input, select');

                if(event.target.checked) {
                    figureProfessionali.removeAttr('readonly').removeClass('disabled');
                } else {
                    figureProfessionali.attr('readonly', 'readonly').addClass('disabled');
                }
            });
            //SCRIPT PER IL CHECK AUTO
            $("input[auto-check]").change(function() {
                $(this).parent().parent().parent().find($('input[mixed]')).iCheck('check');
            });

            // Calcola totali
            calcolaTotale();
            totaleCostiFissi();
        });

        function saveTemplate() {
            $('.templates-boxbody').removeClass('hidden');
            $('.templates-input-select').addClass('hidden');
            $('.templates-input-nome').removeClass('hidden');
            $('.templates-box').addClass('box-info');
        }

        function loadTemplate() {
            $('.templates-boxbody').removeClass('hidden');
            $('.templates-input-nome').addClass('hidden');
            $('.templates-input-select').removeClass('hidden');
            $('.templates-box').addClass('box-info');
        }

        function loadTemplatePage() {
            window.location.href =  document.location.protocol + "//" + document.location.hostname + document.location.pathname + "?template_caricato_id=" + $('#template_caricato_id').val();
        }

        // Costi fissi
        function addCostoFisso() {
            var containerCostiFissi = $('#costi-fissi');
            var costoFissoClone = containerCostiFissi.find('.costo-fisso').last().clone();
            var rowId = parseInt(costoFissoClone.attr('data-row'));
            var rowIdNew = rowId + 1;

            costoFissoClone.attr('data-row', rowIdNew);
            costoFissoClone.find('.remove-costo-fisso').attr('onclick', 'removeCostoFisso(' + rowIdNew + ')');
            costoFissoClone.find('.totale').html('€ 0,00');
            costoFissoClone.find('input, select').val('');
            costoFissoClone.find('[name*="[percentuale_rincaro]"]').val(20);
            costoFissoClone.find('[name*="[quantita]"]').val(1);

            costoFissoClone.find('input, select').each(function(index) {
                var attr = $(this).attr('name').replace('costi_fissi[' + rowId + ']', 'costi_fissi[' + rowIdNew + ']');
                $(this).attr('name', attr);
            });

            containerCostiFissi.find('table').append(costoFissoClone);

            //bootJs();
        }

        function removeCostoFisso(rowId) {
            if(confirm("Sicuro di voler eliminare questa riga?")) {
                var countCostiFissi = $('.costo-fisso').length;
                var costoFisso = $('.costo-fisso[data-row="'+rowId+'"]');

                if(countCostiFissi > 1)
                    costoFisso.remove();
                else {
                    costoFisso.find('input, select').val('');
                    costoFisso.find('.totale').html('€ 0,00');
                }
                totaleCostiFissi();
            }
        }

        function totaleCostiFissi() {
            var costiFissi = $('.costo-fisso');
            var totaleCostiFissi = 0;

            costiFissi.each(function() {
                var totCostoFisso = 0
                var quantita = parseInt($(this).find('input[name*="quantita"]').val());
                var costoUnitario = cleanCurrency($(this).find('input[name*="costo_unitario"]').val());
                var importoRincaro = ((quantita * costoUnitario) * parseFloat($(this).find('input[name*="percentuale_rincaro"]').val())) / 100;
                
                if(isNaN(importoRincaro))
                    importoRincaro = 0;

                if(quantita && costoUnitario) {
                    totCostoFisso = (quantita * costoUnitario) + importoRincaro;
                    totaleCostiFissi += totCostoFisso;

                    var prezzoUscita =  totCostoFisso / quantita;

                    $(this).find('input[name*="prezzo_di_uscita"]').val(formatNumber(prezzoUscita, '€'));

                    $(this).find('.totale').html(formatNumber(totCostoFisso, '€'));
                }
            });

            $('.totale-costi-fissi').html(formatNumber(totaleCostiFissi, '€'));
        }

        function calcoloPercentualeRincaro() {
            var costiFissi = $('.costo-fisso');
            costiFissi.each(function() {
                var costoUnitario = cleanCurrency($(this).find('input[name*="costo_unitario"]').val());
                var prezzoUscita = cleanCurrency($(this).find('input[name*="prezzo_di_uscita"]').val());
                if(costoUnitario > 0 && prezzoUscita > 0){
                    var percentuale_rincaro = ((prezzoUscita - costoUnitario) / costoUnitario) * 100;
                    $(this).find('input[name*="percentuale_rincaro"]').val((Math.round(percentuale_rincaro * 100) / 100).toFixed(2));
                }
            });
            totaleCostiFissi();
        }

        // Canoni
        function reloadCanoniAnni() {
            var annoInizio = parseInt($('input[name="canoni[anno_inizio]"]').val());
            var durata = $('input[name="canoni[durata]"]').val();
            var canoneDefault = $('#canoni').find('.canone').first().clone();
            var checkAnno =  canoneDefault.find('input').first().attr('data-anno');

            if(durata > 0) {
                $('#canoni .box-body .canone').remove();

                for(var i = 0; i < durata; i++) {
                    var canone = canoneDefault.clone();
                    canone.find('input').val('€ 0,00');

                    var html = canone[0].outerHTML.replaceAll(checkAnno, annoInizio + i);

                    $('#canoni .box-body .row').append(html);
                }

                bootJs();
            } else
                alert('ATTENZIONE: inserire una durata maggiore di 0');
        }

        // Figure professionali
        function calcolaTotale() {
            var valFigureProfessionali = JSON.parse($('#figure-professionali').val());
            var procedure = $('.procedura');

            procedure.each(function() {
                var totProcedura = 0
                var aree = $(this).find('.area');
                var proceduraId = $(this).attr('data-procedura');

                aree.each(function() {
                    var totArea = 0;
                    var attivita = $(this).find('.figure-professionali');
                    var areaId = $(this).attr('data-area');

                    attivita.each(function() {
                        var totAttivita = 0;
                        var figureProfessionali = $(this).find('.figura-professionale');
                        var attivitaId = $(this).attr('data-attivita');

                        figureProfessionali.each(function() {
                            var totFiguraProfessionale = 0;
                            var input = $(this).find('input');
                            var selectRisorsa = $(this).find('select[name*="figura_professionale_id"]');
                            var dataFiguraProfessionale = findJSON(valFigureProfessionali, 'id', selectRisorsa.val())

                            if(dataFiguraProfessionale.length > 0) {
                                input.each(function() {
                                    var name = $(this).attr('name');
                                    var val = parseInt($(this).val());

                                    if(isNaN(val))
                                        val = 0;

                                    val = val * cleanCurrency(dataFiguraProfessionale[0].costo_interno);

                                    totFiguraProfessionale += val;
                                });
                            }

                            $(this).find('.totale-figura-professionale').html(formatNumber(totFiguraProfessionale, '€'));
                            totAttivita += totFiguraProfessionale;
                        });

                        $('.totale-attivita-'+attivitaId).html(formatNumber(totAttivita, '€'));
                        totArea += totAttivita;
                    });

                    $('.totale-area-'+areaId).html(formatNumber(totArea, '€'));
                    totProcedura += totArea;
                });

                $('.totale-procedura-'+proceduraId).html(formatNumber(totProcedura, '€'));
            });
        }

        function addFiguraProfessionale(attivitaId) {
            var figureProfessionali = $('.figure-professionali[data-attivita="'+attivitaId+'"]');
            var figuraProfessionaleClone = figureProfessionali.find('.figura-professionale').last().clone();
            var rowId = parseInt(figuraProfessionaleClone.attr('data-row'));
            var rowIdNew = rowId + 1;

            figuraProfessionaleClone.attr('data-row', rowIdNew);
            figuraProfessionaleClone.find('.remove-figura-professionale').attr('onclick', 'removeFiguraProfessionale(' + attivitaId + ', ' + rowIdNew + ')');
            figuraProfessionaleClone.find('.totale-figura-professionale').html('€ 0,00');
            figuraProfessionaleClone.find('.select2-container').remove();
            figuraProfessionaleClone.find('input, select').val('');

            figuraProfessionaleClone.find('input, select').each(function(index) {
                var attr = $(this).attr('name').replace('[figure_professionali][' + rowId + ']', '[figure_professionali][' + rowIdNew + ']');
                $(this).attr('name', attr);
            });

            figureProfessionali.append(figuraProfessionaleClone);
            //bootJs();
        }

        function removeFiguraProfessionale(attivitaId, rowId) {
            if(confirm("Sicuro di voler eliminare questa riga?")) {
                var countFigureProfessionali = $('.figure-professionali[data-attivita="'+attivitaId+'"] .figura-professionale').length;
                var figuraProfessionale = $('.figure-professionali[data-attivita="'+attivitaId+'"] .figura-professionale[data-row="'+rowId+'"]');

                if(countFigureProfessionali > 1)
                    figuraProfessionale.remove();
                else {
                    figuraProfessionale.find('input, select').val('');
                    figuraProfessionale.find('.totale-figura-professionale').html('€ 0,00');
                    //bootJs();
                }
            }
        }
    </script>
@endpush
