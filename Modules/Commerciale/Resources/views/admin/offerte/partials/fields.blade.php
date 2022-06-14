@if(!empty(request('analisi_id')))
  <input type="hidden" name="analisi_id" value="{{ request('analisi_id') }}">
@endif

<div class="row">
    <div class="col-md-12">
        @if(empty($offerta->created_at))
            <div id="create_approvazioni">
                <div class="col-md-12 bg-red">
                    <div class="col-md-1 text-center">
                      <i style="margin-top:24px;" class="icon fa fa-5x fa-ban text-danger"></i>
                    </div> 
                    <div class="col-md-11">
                      <h2 class="display-4">Approvazioni richieste</h2>
                      <p class="lead">Seleziona le approvazioni richieste per la nuova offerta.</p>
                    </div>
                </div>
                <div class="col-md-12">
                    <br>
                    <div class="box box-danger">
                        <div class="box-body">
                            <div class="col-md-6">
                                <br>
                                <div class="list-group">
                                    <span class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h4 class="mb-1">Amministratore</h4>
                                        <small>{{ $roles['amministrazione']['full_name'] }}</small>
                                    </div>
                                    <p class="mb-1">Email di notifica: <span class="text-bold" style="color: rgb(22, 22, 100);">{{ $roles['amministrazione']['email'] }}</span>.</p>
                                    <small>Richiesta approvazione: {{ Form::weCheckbox("approvazioni[amministrazione]", '', $errors, !empty(old("approvazioni") && old('approvazioni')['amministrazione'] == 1) ? 'checked' : '') }}</small>
                                    </span>
                                    <span class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h4 class="mb-1">Direttore PA</h4>
                                        <small class="text-muted">{{ $roles['direttore_pa']['full_name'] }}</small>
                                    </div>
                                    <p class="mb-1">Email di notifica: <span class="text-bold" style="color: rgb(22, 22, 100);">{{ $roles['direttore_pa']['email'] }}</span>.</p>
                                    <small>Richiesta approvazione: {{ Form::weCheckbox("approvazioni[direttore_pa]", '', $errors, !empty(old("approvazioni") && old('approvazioni')['direttore_pa'] == 1) ? 'checked' : '') }}</small>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <br>
                                <div class="list-group">
                                    <span class="list-group-item list-group-item-action flex-column align-items-start">
                                        <div class="d-flex w-100 justify-content-between">
                                        <h4 class="mb-1">Direttore Tecnico</h4>
                                        <small class="text-muted">{{ $roles['direttore_tecnico']['full_name'] }}</small>
                                        </div>
                                        <p class="mb-1">Email di notifica: <span class="text-bold" style="color: rgb(22, 22, 100);">{{ $roles['direttore_tecnico']['email'] }}</span>.</p>
                                        <small>Richiesta approvazione: {{ Form::weCheckbox("approvazioni[direttore_tecnico]", '', $errors, !empty(old("approvazioni") && old('approvazioni')['direttore_tecnico'] == 1) ? 'checked' : '') }}</small>
                                    </span>
                                    <span class="list-group-item list-group-item-action flex-column align-items-start">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h4 class="mb-1">Direttore Commerciale</h4>
                                            <small class="text-muted">{{ $roles['direttore_commerciale']['full_name'] }}</small>
                                        </div>
                                        <p class="mb-1">Email di notifica: <span class="text-bold" style="color: rgb(22, 22, 100);">{{ $roles['direttore_commerciale']['email'] }}</span>.</p>
                                        <small>Richiesta approvazione: {{ Form::weCheckbox("approvazioni[direttore_commerciale]", '', $errors, !empty(old("approvazioni") && old('approvazioni')['direttore_commerciale'] == 1) ? 'checked' : '') }}</small>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <a id="create_approvazioni_avanti" style="margin-bottom: 12px;" type="button" class="btn btn-primary">Avanti</a>
                </div>
            </div>
            <div id="create_campi" class="hidden">
                <div class="col-md-12 bg-green">
                    <div class="col-md-1 text-center">
                      <i style="margin-top:24px;" class="icon fa fa-5x fa-pencil-square-o text-success"></i>
                    </div> 
                    <div class="col-md-11">
                      <h2 class="display-4">Informazioni generali</h2>
                      <p class="lead">Compila le informazioni obbligatorie e consigliate per la nuova offerta.</p>
                    </div>
                </div>
                <div class="col-md-12">
                    <br>
                    <div class="box box-success">
                        <div class="box-body form-row">
                            <div class="form-group col-md-6">
                            {{ Form::weText('oggetto', 'Oggetto *', $errors, get_if_exist($offerta, 'oggetto')) }}
                            </div>
                            <div class="form-group col-md-3">
                                {{ Form::weSelectSearch('cliente_id', 'Cliente *', $errors, $clienti, get_if_exist($offerta, 'cliente_id')) }}
                            </div>
                            <div class="form-group col-md-3">
                                {{ Form::weDate('data_offerta', 'Data offerta *', $errors, (empty(get_if_exist($offerta, 'data_offerta'))) ? date('d/m/Y') : $offerta->data_offerta) }}
                            </div>
                        </div>
                        <div class="box-footer form-row">
                            <div class="col-md-10 col-md-offset-1 text-center">
                                {{ Form::weTextarea('note', 'Note', $errors, get_if_exist($offerta, 'note')) }}
                            </div>
                            <div class="col-md-12">
                                @include('wecore::admin.partials.filesdrop', ['model' => $offerta, 'type' => 'commerciale', 'model_name' => 'Offerta', 'model_path' => 'Commerciale']) 
                            </div>
                        </div>
                    </div>  
                </div>
                <div class="col-md-12 text-center">
                    <a id="create_campi_indietro" style="margin-bottom: 12px;" type="button" class="btn btn-primary">Indietro</a>
                    <a id="create_campi_avanti" style="margin-bottom: 12px;" type="button" class="btn btn-primary">Avanti</a>
                </div>
            </div>
            <div id="create_voci" class="hidden">
                <div class="col-md-12 bg-aqua">
                    <div class="col-md-1 text-center">
                      <i style="margin-top:24px;" class="icon fa fa-5x fa-align-center text-info"></i>
                    </div> 
                    <div class="col-md-11">
                      <h2 class="display-4">Voci Offerta</h2>
                      <p class="lead">Inserisci le voci necessarie per completare l'offerta.</p>
                    </div>
                </div>
                <div class="col-md-12">
                    <br>
                    <div class="box box-info">
                        <div class="box-body">
                            @php $c_voci = 1; @endphp 
                            <div class="callout callout-gray">
                                <h4>Attenzione!</h4>
                                <p>Tutti i campi sono obbligatori.</p>
                            </div>
                            <div class="col-xs-12 table-responsive">
                                <table class="table table-striped voci">
                                    <thead>
                                        <tr>
                                            <th style="width:2%;">#</th>
                                            <th>Descrizione *</th>
                                            <th style="width:10%;">Quantità *</th>
                                            <th style="width:15%;">Importo Singolo *</th>
                                            <th style="width:10%;">IVA *</th>
                                            <th style="width:15%;">Importo *</th>
                                            <th style="width:13%;">Importo con IVA *</th>
                                            <th style="width:2%;">Esente IVA</th>
                                            <th style="width:2%;"><button id="voce-add" onclick="voceAdd()" class="btn btn-xs btn-success btn-flat" type="button"><i class="fa fa-plus"> </i></button></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($offerta->voci) && empty(old('voci')))
                                            @foreach ($offerta->voci as $key => $voce)
                                                <tr data-id="{{ $c_voci }}">
                                                    <td>{{ $c_voci }}.</td>
                                                    <td>{{ Form::weText('voci['. $c_voci .'][descrizione]', '', $errors, get_if_exist($voce, 'descrizione'), ['onkeyup' => 'calcolaTotale()']) }}</td>
                                                    <td>{{ Form::weInt('voci['. $c_voci .'][quantita]', '', $errors, get_if_exist($voce, 'quantita'), ['onkeyup' => 'calcolaTotale()']) }}</td>
                                                    <td>{{ Form::weCurrency('voci['. $c_voci .'][importo_singolo]', '', $errors, get_if_exist($voce, 'importo_singolo'), ['onkeyup' => 'calcolaTotale()']) }}</td>
                                                    <td>{{ Form::weText('voci['. $c_voci .'][iva]', '', $errors, get_if_exist($voce, 'iva'), ['onkeyup' => 'calcolaTotale()']) }}</td>
                                                    <td>{{ Form::weCurrency('voci['. $c_voci .'][importo]', '', $errors, get_if_exist($voce, 'importo')) }}</td>
                                                    <td>{{ Form::weCurrency('voci['. $c_voci .'][importo_iva]', '', $errors, get_if_exist($voce, 'importo_iva')) }}</td>
                                                    <td>{{ Form::weCheckbox('voci['. $c_voci .'][esente_iva]', '', $errors, get_if_exist($voce, 'esente_iva'), 'onclick="esenteIva(this, '.$c_voci.')"', '') }}</td>
                                                    <td>
                                                        <input type="hidden" name="voci[{{$c_voci}}][accettata]" value="1">
                                                        <button onclick="voceDelete({{ $c_voci }})" class="btn btn-xs btn-default btn-flat voce-delete" type="button"><i class="fa fa-times"> </i></button>
                                                        <button onclick="voceDuplicate({{ $c_voci++ }})" class="btn btn-xs btn-warning btn-flat voce-duplicate" type="button"><i class="fa fa-copy" data-toggle="tooltip" data-original-title="Duplica voce"> </i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        @if(!empty(old('voci')))
                                            @foreach (old('voci') as $key => $voce)
                                                <tr data-id="{{ $c_voci }}">
                                                    <td>{{ $c_voci }}.</td>
                                                    <td>{{ Form::weText('voci['. $c_voci .'][descrizione]', '', $errors, get_if_exist($voce, 'descrizione'), ['onkeyup' => 'calcolaTotale()']) }}</td>
                                                    <td>{{ Form::weInt('voci['. $c_voci .'][quantita]', '', $errors, get_if_exist($voce, 'quantita'), ['onkeyup' => 'calcolaTotale()']) }}</td>
                                                    <td>{{ Form::weCurrency('voci['. $c_voci .'][importo_singolo]', '', $errors, get_if_exist($voce, 'importo_singolo'), ['onkeyup' => 'calcolaTotale()']) }}</td>
                                                    <td>{{ Form::weText('voci['. $c_voci .'][iva]', '', $errors, get_if_exist($voce, 'iva'), ['onkeyup' => 'calcolaTotale()']) }}</td>
                                                    <td>{{ Form::weCurrency('voci['. $c_voci .'][importo]', '', $errors, get_if_exist($voce, 'importo')) }}</td>
                                                    <td>{{ Form::weCurrency('voci['. $c_voci .'][importo_iva]', '', $errors, get_if_exist($voce, 'importo_iva')) }}</td>
                                                    <td>{{ Form::weCheckbox('voci['. $c_voci .'][esente_iva]', '', $errors, get_if_exist($voce, 'esente_iva'), 'onclick="esenteIva(this, '.$c_voci.')"', '') }}</td>
                                                    <td>
                                                        <input type="hidden" name="voci[{{$c_voci}}][accettata]" value="1">
                                                        <button onclick="voceDelete({{ $c_voci }})" class="btn btn-xs btn-default btn-flat voce-delete" type="button"><i class="fa fa-times"> </i></button>
                                                        <button onclick="voceDuplicate({{ $c_voci++ }})" class="btn btn-xs btn-warning btn-flat voce-duplicate" type="button"><i class="fa fa-copy" data-toggle="tooltip" data-original-title="Duplica voce"> </i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        @if( empty($offerta->voci) || get_if_exist($offerta,'voci') && $offerta->voci->count() == 0 && empty(old('voci')))
                                            <tr data-id="{{ $c_voci }}">
                                                <td>{{ $c_voci }}.</td>
                                                <td>{{ Form::weText('voci['. $c_voci .'][descrizione]', '', $errors, null, ['onkeyup' => 'calcolaTotale()']) }}</td>
                                                <td>{{ Form::weInt('voci['. $c_voci .'][quantita]', '', $errors, '', ['onkeyup' => 'calcolaTotale()']) }}</td>
                                                <td>{{ Form::weCurrency('voci['. $c_voci .'][importo_singolo]', '', $errors, null, ['onkeyup' => 'calcolaTotale()']) }}</td>
                                                <td>{{ Form::weText('voci['. $c_voci .'][iva]', '', $errors, $iva, ['onkeyup' => 'calcolaTotale()']) }}</td>
                                                <td>{{ Form::weCurrency('voci['. $c_voci .'][importo]', '', $errors, 0) }}</td>
                                                <td>{{ Form::weCurrency('voci['. $c_voci .'][importo_iva]', '', $errors, 0) }}</td>
                                                <td>{{ Form::weCheckbox('voci['. $c_voci .'][esente_iva]', '', $errors, null, 'onclick="esenteIva(this, '.$c_voci.')"', '') }}</td>
                                                <td>
                                                    <input type="hidden" name="voci[{{$c_voci}}][accettata]" value="1">
                                                    <button onclick="voceDelete({{ $c_voci }})" class="btn btn-xs btn-default btn-flat voce-delete" type="button"><i class="fa fa-times"> </i></button>
                                                    <button onclick="voceDuplicate({{ $c_voci }})" class="btn btn-xs btn-warning btn-flat voce-duplicate" type="button"><i class="fa fa-copy" data-toggle="tooltip" data-original-title="Duplica voce"> </i></button>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="box-footer">
                            <div class="col-md-7"><h4 class="text-bold pull-right" style="padding-top:22px;">TOTALI:</h4>&nbsp;</div>
                            <div class="col-md-1 hidden">
                                {{ Form::weInt('iva', 'IVA', $errors, (get_if_exist($offerta, 'iva') ) ? get_if_exist($offerta, 'iva') : $iva, ['readonly' => 'readonly']) }}
                            </div>
                            <div class="col-md-2">
                                {{ Form::weCurrency('importo_esente', 'Importo', $errors, get_if_exist($offerta, 'importo_esente'), ['readonly' => 'readonly']) }}
                            </div>
                            <div class="col-md-2">
                                {{ Form::weCurrency('importo_iva', 'Importo + IVA', $errors, get_if_exist($offerta, 'importo_iva'), ['readonly' => 'readonly']) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <a id="create_voci_indietro" style="margin-bottom: 12px;" type="button" class="btn btn-primary">Indietro</a>
                    <button id="create_voci_crea" style="margin-bottom: 12px;" type="submit" class="btn btn-success">Conferma & Crea</button>
                </div>
            </div>
        @else 
            @if($offerta->approvata())
                <div class="col-md-12 bg-info">
                    <div class="col-md-1 text-center">
                      <i style="margin-top:24px;" class="icon fa fa-5x  fa-pencil-square-o text-primary"></i>
                    </div> 
                    <div class="col-md-11">
                      <h2 class="display-4">Offerta {{$offerta->numero_offerta()}}</h2>
                      <p class="lead">Modifica le informazioni riguardanti l'offerta.</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <br>
                        <div class="box box-info">
                            <div class="progress active">
                                <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                    <span class="text-bold text-uppercase">Approvata</span>
                                </div>
                            </div>
                            <div class="box-body">
                                @if(!empty($offerta->ordinativo))  
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="info-box col-md-12">
                                                <span class="info-box-icon bg-blue"><i class="fa fa-file-text"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Ordinativo</span>    
                                                    <span class="info-box-number">{{ get_if_exist($offerta->ordinativo, 'oggetto') }}</span>
                                                    <a data-toggle="tooltip" title=""  data-original-title="Visualizza ordinativo" 
                                                        class="btn btn-sm btn-default" href = {{ route('admin.commerciale.ordinativo.edit', $offerta->ordinativo) }} >
                                                        Apri Ordinativo &nbsp;
                                                        <i class="fa fa-external-link-square" style="font-size:15px"> </i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif(!empty($offerta->stato) && $offerta->stato == 1)
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="info-box col-md-12">
                                                <span class="info-box-icon bg-blue"><i class="fa fa-file-text"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Ordinativo</span>    
                                                    <a style="margin-top:12px;" class="btn btn-primary btn-flat" href="{{ route('admin.commerciale.offerta.generaordinativo', $offerta->id)}}"><i class="fa fa-adjust"></i> Genera ordinativo</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <br>
                                @if(empty($offerta->ordinativo))
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            {{ Form::weText('oggetto', 'Oggetto *', $errors, get_if_exist($offerta, 'oggetto')) }}
                                        </div>
                                        <div class="form-group col-md-2">
                                            {{ Form::weSelectSearch('cliente_id', 'Cliente *', $errors, $clienti, get_if_exist($offerta, 'cliente_id')) }}
                                        </div>
                                        <div class="form-group col-md-2">
                                            {{ Form::weDate('data_offerta', 'Data offerta *', $errors, (empty(get_if_exist($offerta, 'data_offerta'))) ? date('d/m/Y') : $offerta->data_offerta) }}
                                        </div>
                                        <div class="form-group col-md-2">
                                            {{ Form::weSelectSearch('stato', 'Stato *', $errors, $stati, get_if_exist($offerta, 'stato')) }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="box-footer form-row">
                                <div class="col-md-10 col-md-offset-1 text-center">
                                    {{ Form::weTextarea('note', 'Note', $errors, get_if_exist($offerta, 'note')) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(empty($offerta->ordinativo))
                        <div class="col-md-12">
                            <div class="box box-info">
                                <div class="box-body">
                                    @php $c_voci = 1; @endphp 
                                    <div class="callout callout-info">
                                        <h4>Voci</h4>
                        
                                        <p>Tutti i campi sono obbligatori.</p>
                                    </div>
                                    <div class="col-xs-12 table-responsive">
                                        <table class="table table-striped voci">
                                            <thead>
                                                <tr>
                                                    <th style="width:2%;">#</th>
                                                    <th>Descrizione *</th>
                                                    <th style="width:10%;">Quantità *</th>
                                                    <th style="width:15%;">Importo Singolo *</th>
                                                    <th style="width:10%;">IVA *</th>
                                                    <th style="width:15%;">Importo *</th>
                                                    <th style="width:13%;">Importo con IVA *</th>
                                                    <th style="width:2%;">Esente IVA</th>
                                                    <th style="width:2%;"><button id="voce-add" onclick="voceAdd()" class="btn btn-xs btn-success btn-flat" type="button"><i class="fa fa-plus"> </i></button></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(!empty($offerta->voci) && empty(old('voci')))
                                                    @foreach ($offerta->voci as $key => $voce)
                                                        <tr data-id="{{ $c_voci }}">
                                                            <td>{{ $c_voci }}.</td>
                                                            <td>{{ Form::weText('voci['. $c_voci .'][descrizione]', '', $errors, get_if_exist($voce, 'descrizione'), ['onkeyup' => 'calcolaTotale()']) }}</td>
                                                            <td>{{ Form::weInt('voci['. $c_voci .'][quantita]', '', $errors, get_if_exist($voce, 'quantita'), ['onkeyup' => 'calcolaTotale()']) }}</td>
                                                            <td>{{ Form::weCurrency('voci['. $c_voci .'][importo_singolo]', '', $errors, get_if_exist($voce, 'importo_singolo'), ['onkeyup' => 'calcolaTotale()']) }}</td>
                                                            <td>{{ Form::weText('voci['. $c_voci .'][iva]', '', $errors, get_if_exist($voce, 'iva'), ['onkeyup' => 'calcolaTotale()']) }}</td>
                                                            <td>{{ Form::weCurrency('voci['. $c_voci .'][importo]', '', $errors, get_if_exist($voce, 'importo')) }}</td>
                                                            <td>{{ Form::weCurrency('voci['. $c_voci .'][importo_iva]', '', $errors, get_if_exist($voce, 'importo_iva')) }}</td>
                                                            <td>{{ Form::weCheckbox('voci['. $c_voci .'][esente_iva]', '', $errors, get_if_exist($voce, 'esente_iva'), 'onclick="esenteIva(this, '.$c_voci.')"', '') }}</td>
                                                            <td>
                                                                <input type="hidden" name="voci[{{$c_voci}}][accettata]" value="1">
                                                                <button onclick="voceDelete({{ $c_voci }})" class="btn btn-xs btn-default btn-flat voce-delete" type="button"><i class="fa fa-times"> </i></button>
                                                                <button onclick="voceDuplicate({{ $c_voci++ }})" class="btn btn-xs btn-warning btn-flat voce-duplicate" type="button"><i class="fa fa-copy" data-toggle="tooltip" data-original-title="Duplica voce"> </i></button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                @if(!empty(old('voci')))
                                                    @foreach (old('voci') as $key => $voce)
                                                        <tr data-id="{{ $c_voci }}">
                                                            <td>{{ $c_voci }}.</td>
                                                            <td>{{ Form::weText('voci['. $c_voci .'][descrizione]', '', $errors, get_if_exist($voce, 'descrizione'), ['onkeyup' => 'calcolaTotale()']) }}</td>
                                                            <td>{{ Form::weInt('voci['. $c_voci .'][quantita]', '', $errors, get_if_exist($voce, 'quantita'), ['onkeyup' => 'calcolaTotale()']) }}</td>
                                                            <td>{{ Form::weCurrency('voci['. $c_voci .'][importo_singolo]', '', $errors, get_if_exist($voce, 'importo_singolo'), ['onkeyup' => 'calcolaTotale()']) }}</td>
                                                            <td>{{ Form::weText('voci['. $c_voci .'][iva]', '', $errors, get_if_exist($voce, 'iva'), ['onkeyup' => 'calcolaTotale()']) }}</td>
                                                            <td>{{ Form::weCurrency('voci['. $c_voci .'][importo]', '', $errors, get_if_exist($voce, 'importo')) }}</td>
                                                            <td>{{ Form::weCurrency('voci['. $c_voci .'][importo_iva]', '', $errors, get_if_exist($voce, 'importo_iva')) }}</td>
                                                            <td>{{ Form::weCheckbox('voci['. $c_voci .'][esente_iva]', '', $errors, get_if_exist($voce, 'esente_iva'), 'onclick="esenteIva(this, '.$c_voci.')"', '') }}</td>
                                                            <td>
                                                                <input type="hidden" name="voci[{{$c_voci}}][accettata]" value="1">
                                                                <button onclick="voceDelete({{ $c_voci }})" class="btn btn-xs btn-default btn-flat voce-delete" type="button"><i class="fa fa-times"> </i></button>
                                                                <button onclick="voceDuplicate({{ $c_voci++ }})" class="btn btn-xs btn-warning btn-flat voce-duplicate" type="button"><i class="fa fa-copy" data-toggle="tooltip" data-original-title="Duplica voce"> </i></button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                @if( empty($offerta->voci) || get_if_exist($offerta,'voci') && $offerta->voci->count() == 0 && empty(old('voci')))
                                                    <tr data-id="{{ $c_voci }}">
                                                        <td>{{ $c_voci }}.</td>
                                                        <td>{{ Form::weText('voci['. $c_voci .'][descrizione]', '', $errors, null, ['onkeyup' => 'calcolaTotale()']) }}</td>
                                                        <td>{{ Form::weInt('voci['. $c_voci .'][quantita]', '', $errors, '', ['onkeyup' => 'calcolaTotale()']) }}</td>
                                                        <td>{{ Form::weCurrency('voci['. $c_voci .'][importo_singolo]', '', $errors, null, ['onkeyup' => 'calcolaTotale()']) }}</td>
                                                        <td>{{ Form::weText('voci['. $c_voci .'][iva]', '', $errors, $iva, ['onkeyup' => 'calcolaTotale()']) }}</td>
                                                        <td>{{ Form::weCurrency('voci['. $c_voci .'][importo]', '', $errors, 0) }}</td>
                                                        <td>{{ Form::weCurrency('voci['. $c_voci .'][importo_iva]', '', $errors, 0) }}</td>
                                                        <td>{{ Form::weCheckbox('voci['. $c_voci .'][esente_iva]', '', $errors, null, 'onclick="esenteIva(this, '.$c_voci.')"', '') }}</td>
                                                        <td>
                                                            <input type="hidden" name="voci[{{$c_voci}}][accettata]" value="1">
                                                            <button onclick="voceDelete({{ $c_voci }})" class="btn btn-xs btn-default btn-flat voce-delete" type="button"><i class="fa fa-times"> </i></button>
                                                            <button onclick="voceDuplicate({{ $c_voci }})" class="btn btn-xs btn-warning btn-flat voce-duplicate" type="button"><i class="fa fa-copy" data-toggle="tooltip" data-original-title="Duplica voce"> </i></button>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <div class="col-md-7"><h4 class="text-bold pull-right" style="padding-top:22px;">TOTALI:</h4>&nbsp;</div>
                                    <div class="col-md-1 hidden">
                                        {{ Form::weInt('iva', 'IVA', $errors, (get_if_exist($offerta, 'iva') ) ? get_if_exist($offerta, 'iva') : $iva, ['readonly' => 'readonly']) }}
                                    </div>
                                    <div class="col-md-2">
                                        {{ Form::weCurrency('importo_esente', 'Importo', $errors, get_if_exist($offerta, 'importo_esente'), ['readonly' => 'readonly']) }}
                                    </div>
                                    <div class="col-md-2">
                                        {{ Form::weCurrency('importo_iva', 'Importo + IVA', $errors, get_if_exist($offerta, 'importo_iva'), ['readonly' => 'readonly']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-12">
                        <div class="box box-success collapsed-box box-shadow">
                            <div class="box-header with-border">
                              <h3 class="box-title">Allegati</h3>
                              <div class="box-tools pull-right">
                                <span data-toggle="tooltip" title="" class="badge bg-green" data-original-title="{{ count($offerta->files) }} Files">{{ count($offerta->files) }}</span>
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                </button>
                              </div>
                            </div>
                            <div class="box-body">
                                <div class="file-loading">
                                    <input id="dropzone" type="file" name="file" multiple class="file" data-overwrite-initial="false" data-min-file-count="1">
                                </div>
                            </div>
                            <div id="allegati" class="box-footer">
                                @foreach($offerta->files as $key => $file)
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="box box-solid box-shadow">
                                                <div class="box-body {{ ($file->id == $offerta->offerta_definitiva_id) ? 'bg-success' : (($offerta->oda_determina_ids->contains($file->id)) ? 'bg-info' : '') }}">
                                                    <div class="row">
                                                    <div class="col-md-1">
                                                        <span class="fa-stack fa-2x">
                                                            <i class="fa fa-square-o fa-stack-2x"></i>
                                                            <i class="fa {{ file_icons($file->value->extension) }} fa-stack-1x"></i>
                                                        </span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-md-8">
                                                                <a href="{{ download_file($file->value->path, $file->value->client_name) }}" target="_blank"><strong>{{ $file->value->name }}</strong></a>
                                                                <button type="button" class="btn btn-xs btn-flat btn-default" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#modal-default" data-size="modal-lg" data-title="Anteprima {{ $file->value->name }}" data-action="https://docs.google.com/gview?url={{ url($file->value->path) }}&embedded=true" data-type="iframe">
                                                                    <i class="fa fa-eye"></i>
                                                                </button>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <i class="fa fa-calendar"> </i> {{ $file->updated_at }}
                                                            </div>
                                                            <div class="col-md-8">
                                                                <i class="fa fa-save"> </i> {{ mb($file->value->size) }} MB
                                                            </div>
                                                            <div class="col-md-4">
                                                                <i class="fa fa-user"> </i> {{ $file->updatedUser->first_name }} {{ $file->updatedUser->last_name }}
                                                            </div>
                                                        </div>
                                                    </div> 
                                                    <div class="col-md-5 text-center" style="margin-top:9px;">
                                                        @if($file->id != $offerta->offerta_definitiva_id && $file->id != $offerta->ordine_mepa_id && !$offerta->oda_determina_ids->contains($file->id))
                                                            <button class="btn btn-xs btn-flat btn-danger pull-right" type="button" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.commerciale.offerta.allegato.destroy', [$file->id]) }}"><i class="fa fa-trash"></i></button>
                                                        @endif

                                                        @if(empty($offerta->offerta_definitiva_id) && !$offerta->oda_determina_ids->contains($file->id) && $file->id != $offerta->ordine_mepa_id)
                                                            <a href="{{ route('admin.commerciale.offerta.definitiva', [$file->id]) }}" class="btn btn-md btn-flat btn-success" data-toggle="tooltip" data-original-title="Segna come offerta definitiva"><i class="fa fa-check"></i></a>
                                                        @endif

                                                        @if(!$offerta->oda_determina_ids->contains($file->id) && $file->id != $offerta->offerta_definitiva_id && $file->id != $offerta->ordine_mepa_id)
                                                            <a href="{{ route('admin.commerciale.offerta.oda_determina', [$file->id]) }}" class="btn btn-md btn-flat btn-info" data-toggle="tooltip" data-original-title="Segna come {{ $od }}">Segna come {{ $od }}</a>
                                                        @endif

                                                        @if(empty($offerta->ordine_mepa_id) && !$offerta->oda_determina_ids->contains($file->id) && $od == 'DETERMINA')
                                                            <a href="{{ route('admin.commerciale.offerta.ordine_mepa', [$file->id]) }}" class="btn btn-md btn-flat btn-warning">Segna come Ordine MEPA</a>
                                                        @endif
                                                        @if($file->id == $offerta->ordine_mepa_id)
                                                            <span class="label bg-yellow">ORDINE MEPA</span>

                                                            <a href="{{ route('admin.commerciale.offerta.ordine_mepa', [$file->id]) }}" class="btn btn-xs btn-flat btn-default pull-right" data-toggle="tooltip" data-original-title="Rimuovi ordine mepa"><i class="fa fa-remove"></i></a>
                                                        @endif

                                                        @if($file->id == $offerta->offerta_definitiva_id)
                                                            <span class="label bg-green">OFFERTA DEFINITIVA</span>

                                                            <a href="{{ route('admin.commerciale.offerta.definitiva', [$file->id]) }}" class="btn btn-xs btn-flat btn-default pull-right" data-toggle="tooltip" data-original-title="Rimuovi offerta definitiva"><i class="fa fa-remove"></i></a>
                                                        @endif

                                                        @if($offerta->oda_determina_ids->contains($file->id))
                                                            <span class="label bg-blue">{{ $od }}</span>

                                                            <a href="{{ route('admin.commerciale.offerta.oda_determina', [$file->id]) }}" class="btn btn-xs btn-flat btn-default pull-right" data-toggle="tooltip" data-original-title="Rimuovi {{ $od }}"><i class="fa fa-remove"></i></a>
                                                        @endif
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @else 
                <div class="col-md-12 bg-danger">
                    <div class="col-md-1 text-center">
                      <i style="margin-top:24px;" class="icon fa fa-5x fa-ban text-red"></i>
                    </div> 
                    <div class="col-md-11">
                      <h2 class="display-4">Approvazioni Richieste</h2>
                      <p class="lead">Per essere lavorata l'offerta necessita delle seguenti approvazioni.</p>
                    </div>
                </div>
                @php
                    $approvazioni_concesse = 0;
                    $approvazioni_richieste = 0;
                    foreach($offerta->approvazioni as $ruolo => $boolean){
                        $approvazioni_richieste++;
                        if($boolean == 1){
                            $approvazioni_concesse++;
                        }
                    }
                    $approvazioni_percentuale = substr((( $approvazioni_concesse / $approvazioni_richieste ) * 100), 0, 2);
                    $approvazioni_progress_bar = $approvazioni_percentuale;
                    if($approvazioni_progress_bar < 1){
                        $approvazioni_progress_bar  = 100;
                    }
                @endphp
                <div class="row">
                    <div class="col-md-12">
                        <br>
                        <div class="box box-danger">
                            <div class="progress active text-center">
                                <div class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" style="width: {{ $approvazioni_progress_bar }}%" aria-valuenow="{{ $approvazioni_progress_bar }}" aria-valuemin="0" aria-valuemax="100">
                                    <span class="text-bold text-uppercase">Stato approvazione: {{ $approvazioni_percentuale }}% <small>( {{ $approvazioni_concesse }} / {{ $approvazioni_richieste }} )</small></span>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="list-group">
                                    @foreach($offerta->approvazioni as $ruolo => $boolean)
                                        <span class="list-group-item list-group-item-action flex-column align-items-start {{ $boolean == 1 ? 'bg-green' : '' }} {{ Auth::user()->email !== $roles[$ruolo]['email'] && $boolean == 0 ? 'disabled' : '' }}">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h4 id="{{$ruolo}}" class="mb-1">{{ ucfirst(str_replace('_', ' ', $ruolo)) }}
                                                    @if(Auth::user()->email == $roles[$ruolo]['email'] && $boolean == 0)
                                                        <label for="{{$ruolo}}">{{ Form::weCheckbox('approvazioni['.$ruolo.']', '', $errors) }}</label>
                                                    @elseif($boolean == 1)
                                                        <label for="{{$ruolo}}"><i class="fa fa-check fa-2x" data-toggle="tooltip" data-placement="top" title="L'offerta è stata approvata da {{ $roles[$ruolo]['full_name'] }}." style="color:green;"></i></label>
                                                        <span class="hidden"> {{ Form::weCheckbox('approvazioni['.$ruolo.']', '', $errors, '1') }}</span>
                                                    @else
                                                        <label for="{{$ruolo}}"><i class="fa fa-question text-gray fa-2x" data-toggle="tooltip" data-placement="top" title="Non ricopri il ruolo necessario per eseguire l'approvazione." aria-hidden="true" style="pointer-events: visible;"></i></label>
                                                        <span class="hidden"> {{ Form::weCheckbox('approvazioni['.$ruolo.']', '', $errors, '') }}</span>
                                                    @endif
                                                </h4>
                                            </div>
                                            <p class="mb-1">Email di notifica: <span class="text-bold" style="{{ $boolean == 1 ? 'color: white;' : 'color: rgb(22, 22, 100);' }}">{{ $roles[$ruolo]['email'] }} <small>( {{ $roles[$ruolo]['full_name'] }} )</small></span></p>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h4 class="box-title">{{ $offerta->oggetto }}</h4>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                  </div>
                            </div>
                            <div class="box-body">
                                <br>
                                <div class="row">
                                  <div class="col-md-12">
                                    <div class="box box-info">
                                      <div class="box-header">
                                        <h4>Informazioni {{ $offerta->cliente->ragione_sociale }} <small>( Cliente {{ $offerta->cliente->tipologia }} )</small></h4>
                                      </div>
                                      <div class="box-body">
                                        <div class="table-responsive">
                                          <table class="table table-striped">
                                            <tr>
                                              <th style="width:20%">Sede Legale</th>
                                              <th style="width:10%">P.IVA</th>
                                              <th style="width:20%">Cod. Fiscale</th>
                                              <th style="width:20%">Email</th>
                                              <th style="width:20%">PEC</th>
                                              <th style="width:10%">Telefono</th>
                                            </tr>
                                            <tbody>
                                              <tr>
                                                <td>
                                                  @if(!empty($offerta->cliente->sedeLegale()))
                                                    {{ $offerta->cliente->sedeLegale()->indirizzo_completo }}
                                                  @endif
                                                </td>
                                                <td>
                                                  @if(get_if_exist($offerta->cliente, 'p_iva'))
                                                    {{ $offerta->cliente->p_iva }}
                                                  @endif
                                                </td>
                                                <td>
                                                  @if(get_if_exist($offerta->cliente, 'cod_fiscale'))
                                                    {{ $offerta->cliente->cod_fiscale }}
                                                  @endif                            
                                                </td>
                                                <td>
                                                  @if(get_if_exist($offerta->cliente->sedeLegale(), 'email'))
                                                    <a href="mailto:{{ $offerta->cliente->sedeLegale()->email }}">{{ $offerta->cliente->sedeLegale()->telefono }}</a>
                                                  @endif                           
                                                </td>
                                                <td>
                                                  @if(get_if_exist($offerta->cliente, 'pec'))
                                                    <a href="mailto:{{ $offerta->cliente->pec }}">{{ $offerta->cliente->pec }}</a>
                                                  @endif
                                                </td>
                                                <td>
                                                  @if(get_if_exist($offerta->cliente->sedeLegale(), 'telefono'))
                                                    <a href="tel:{{ $offerta->cliente->sedeLegale()->telefono }}">{{ $offerta->cliente->sedeLegale()->telefono }}</a>
                                                  @endif
                                                </td>
                                              </tr>
                                            </tbody>
                                          </table>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  @if(!empty($offerta->voci) && count($offerta->voci) > 0)
                                    <div class="col-md-12">
                                      <div class="box box-primary">
                                        <div class="box-header">
                                          <h4 class="box-title">Voci</h4>
                                        </div>
                                        <div class="box-body table-responsive">
                                          <table class="table table-striped voci">
                                            <thead>
                                              <tr>
                                                <th>Descrizione</th>
                                                <th style="width:10%;" class="text-center">Quantità</th>
                                                <th class="text-center" style="width:15%;">Importo Singolo</th>
                                                <th class="text-center" style="width:10%;">IVA</th>
                                                <th class="text-center" style="width:15%;">Importo</th>
                                                <th class="text-center" style="width:15%;">Importo con IVA</th>
                                                <th class="text-center" style="width:8%;">Esente IVA</th>
                                              </tr>
                                            </thead>
                                            <tbody>
                                              @foreach ($offerta->voci as $key => $voce)
                                                  <tr>
                                                      <td>{{  get_if_exist($voce, 'descrizione')  }}</td>
                                                      <td class="text-center">{{  get_if_exist($voce, 'quantita')   }}</td>
                                                      <td class="text-center">{{  get_if_exist($voce, 'importo_singolo')  }}</td>
                                                      <td class="text-center">{{  get_if_exist($voce, 'iva')  }}</td>
                                                      <td class="text-center">{{  get_if_exist($voce, 'importo')  }}</td>
                                                      <td class="text-center">{{  get_if_exist($voce, 'importo_iva')  }}</td>
                                                      <td class="text-center">{!!   ((get_if_exist($voce, 'esente_iva')==1 ) ? '<i class="fa fa-check fa-2x text-success" aria-hidden="true"></i>' : '<i class="fa fa-ban fa-2x text-danger" aria-hidden="true"></i>')  !!}</td>
                                                  </tr>
                                              @endforeach
                                            </tbody>
                                          </table>
                                        </div>
                                        <div class="box-footer">
                                          <div class="col-md-7"><h4 class="text-bold pull-right" style="padding-top:22px;">TOTALI:</h4>&nbsp;</div>
                                          <div class="col-md-1 hidden">
                                              {{ Form::weInt('iva', 'IVA', $errors, get_if_exist($offerta, 'iva') ?? $iva, ['readonly' => 'readonly']) }}
                                          </div>
                                          <div class="col-md-2">
                                              {{ Form::weCurrency('importo_esente', 'Importo', $errors, get_currency(get_if_exist($offerta, 'importo_esente')), ['readonly' => 'readonly']) }}
                                          </div>
                                          <div class="col-md-2">
                                              {{ Form::weCurrency('importo_iva', 'Importo + IVA', $errors, get_currency(get_if_exist($offerta, 'importo_iva')), ['readonly' => 'readonly']) }}
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  @endif
                                  <div class="col-md-12">
                                    <div class="box box-success box-shadow">
                                      <div class="box-header with-border">
                                        <h4 class="box-title">Allegati</h4>
                                        <div class="box-tools pull-right">
                                          <span data-toggle="tooltip" title="" class="badge bg-green" data-original-title="{{ count($offerta->files) }} Files">{{ count($offerta->files) }}</span>
                                          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                          </button>
                                        </div>
                                      </div>
                                      <div class="box-footer">
                                        @foreach($offerta->files as $key => $file)
                                          <div class="row">
                                            <div class="col-md-12">
                                                <div class="box box-solid box-shadow">
                                                    <div class="box-body {{ ($file->id == $offerta->offerta_definitiva_id) ? 'bg-success' : (($offerta->oda_determina_ids->contains($file->id)) ? 'bg-info' : '') }}">
                                                        <div class="row">
                                                        <div class="col-md-1">
                                                            <span class="fa-stack fa-2x">
                                                                <i class="fa fa-square-o fa-stack-2x"></i>
                                                                <i class="fa {{ file_icons($file->value->extension) }} fa-stack-1x"></i>
                                                            </span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <a href="{{ download_file($file->value->path, $file->value->client_name) }}" target="_blank"><strong>{{ $file->value->name }}</strong></a>
                                                                    <button type="button" class="btn btn-xs btn-flat btn-default" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#modal-default" data-size="modal-lg" data-title="Anteprima {{ $file->value->name }}" data-action="https://docs.google.com/gview?url={{ url($file->value->path) }}&embedded=true" data-type="iframe">
                                                                        <i class="fa fa-eye"></i>
                                                                    </button>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <i class="fa fa-calendar"> </i> {{ $file->updated_at }}
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <i class="fa fa-save"> </i> {{ mb($file->value->size) }} MB
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <i class="fa fa-user"> </i> {{ $file->updatedUser->first_name }} {{ $file->updatedUser->last_name }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 text-right">
                                                            @if($file->id == $offerta->ordine_mepa_id)
                                                                <span class="label bg-yellow">ORDINE MEPA</span>
                                                            @endif
                                
                                                            @if($file->id == $offerta->offerta_definitiva_id)
                                                                <span class="label bg-green">OFFERTA DEFINITIVA</span>
                                                            @endif
                                
                                                            @if($offerta->oda_determina_ids->contains($file->id))
                                                                <span class="label bg-blue">{{ $od }}</span>
                                                            @endif
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                          </div>
                                        @endforeach
                                      </div>
                                    </div> 
                                  </div>
                                </div>
                              </div> 
                            </div> 
                          </div> 
                        </div> 
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
@include('commerciale::admin.partials.voci_js')
@push('js-stack')
<script>
    $('#create_approvazioni_avanti').click(function() {
        $('#create_approvazioni').addClass('hidden');
        $('#create_campi').removeClass('hidden');
    });
    $('#create_campi_indietro').click(function() {
        $('#create_approvazioni').removeClass('hidden');
        $('#create_campi').addClass('hidden');
    });
    $('#create_campi_avanti').click(function() {
        $('#create_voci').removeClass('hidden');
        $('#create_campi').addClass('hidden');
    });
    $('#create_voci_indietro').click(function() {
        $('#create_campi').removeClass('hidden');
        $('#create_voci').addClass('hidden');
    });


    @if($offerta->approvata())
        $(document).ready(function() {
            // Submit form
            $("form").submit(function(e) {
                var inputs = $(this).serializeArray();

                $.each(inputs, function(i, input) {
                    if(input.name.indexOf("totale") >= 0 || input.name.indexOf("importo") >= 0 || input.name == 'iva') {
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

            if(!$.isNumeric(iva)) {
                iva = 0;
            }
            $('input[name="iva"]').val(iva);

            if($.isNumeric(iva)) {
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
                    } else {
                        $(this).attr('style', 'background-color: #f2dede');
                    }
                });

                $('input[name="importo_esente"]').val(formatNumber(importo, '€'));
                $('input[name="importo_iva"]').val(formatNumber(importoIva, '€'));
            }
        }
    @else   
        function calcolaTotale() {}
    @endif
</script>
@endpush
