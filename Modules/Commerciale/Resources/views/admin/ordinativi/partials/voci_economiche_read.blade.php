@php $offerte = $ordinativo->offerte()->get()->pluck('oggetto', 'id')->toArray(); @endphp
<div class="box-body">  
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-body">
                    @if(!empty($offerte))
                        @foreach($offerte as $id => $oggetto)
                            <h4><strong class="text-primary">{{ $oggetto }}</strong></h4>
                            <br>
                            <div id="table">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tr>
                                            <th>Descrizione</th>
                                            <th>Categoria</th>
                                            <th>Anno Di Riferimento</th>
                                            <th>Importo Singolo</th>
                                            <th>Quantità</th>
                                            <th>Iva</th>
                                            <th>Importo</th>
                                            <th>Importo Iva</th>
                                            <th>Esente Iva</th>
                                            <th>Costo Fisso</th>
                                        </tr>
                                        <tbody> 
                                            @if(!empty($ordinativo->voci_economiche)) 
                                                @foreach($ordinativo->voci_economiche as $key => $voce)
                                                    @if(!empty($voce->offerta_id))
                                                        @if($voce->offerta_id == $id)
                                                            <tr>
                                                                <td>{{ $voce->descrizione }}</td>
                                                                <td>{{ !empty($voce->categoria) ? $categorie_voci[$voce->categoria] : 'Non inserita' }}</td>
                                                                <td>{{ !empty($voce->anno_di_riferimento) ? $voce->anno_di_riferimento : date('Y') }}</td>
                                                                <td>{{ get_currency($voce->importo_singolo) }}</td>
                                                                <td>{{ $voce->quantita }}</td>
                                                                <td>{{ $voce->iva }}%</td>
                                                                <td>{{ get_currency($voce->importo) }}</td>
                                                                <td>{{ get_currency($voce->importo_iva) }}</td>
                                                                <td>{{ $voce->esente_iva == 1 ? 'Esente' : 'Non Esente' }}</td>
                                                                <td>{{ !empty($voce->costo_fisso) && $voce->costo_fisso == 1 ? 'Si' : 'No' }}</td>
                                                            </tr>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @else 
                                                <div class="alert alert-info alert-dismissible">
                                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                                    <h4><i class="icon fa fa-info"></i> Voci non presenti</h4>
                                                    Questa offerta non ha alcuna voce economica inserita.</strong>
                                                </div>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <hr> 
                        @endforeach
                    @endif 
                    @if(!empty($ordinativo->voci_economiche)) 
                        <h4><strong class="text-danger">NON COLLEGATE</strong></h4>
                        <br>
                        <div id="table">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tr>
                                        <th>Descrizione</th>
                                        <th>Categoria</th>
                                        <th>Anno Di Riferimento</th>
                                        <th>Importo Singolo</th>
                                        <th>Quantità</th>
                                        <th>Iva</th>
                                        <th>Importo</th>
                                        <th>Importo Iva</th>
                                        <th>Esente Iva</th>
                                        <th>Costo Fisso</th>
                                    </tr>
                                    <tbody> 
                                        @foreach($ordinativo->voci_economiche as $key => $voce)
                                            @if(empty($voce->offerta_id) || $voce->offerta_id == 0)
                                                <tr>
                                                    <td>{{ $voce->descrizione }}</td>
                                                    <td>{{ !empty($voce->categoria) ? $categorie_voci[$voce->categoria] : 'Non inserita' }}</td>
                                                    <td>{{ !empty($voce->anno_di_riferimento) ? $voce->anno_di_riferimento : date('Y') }}</td>
                                                    <td>{{ get_currency($voce->importo_singolo) }}</td>
                                                    <td>{{ $voce->quantita }}</td>
                                                    <td>{{ $voce->iva }}%</td>
                                                    <td>{{ get_currency($voce->importo) }}</td>
                                                    <td>{{ get_currency($voce->importo_iva) }}</td>
                                                    <td>{{ $voce->esente_iva == 1 ? 'Esente' : 'Non Esente' }}</td>
                                                    <td>{{ !empty($voce->costo_fisso) && $voce->costo_fisso == 1 ? 'Si' : 'No' }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-info"></i> Fatturazione</h4>
                            L'ordinativo non ha alcuna voce economica inserita.</strong>
                        </div>                     
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>