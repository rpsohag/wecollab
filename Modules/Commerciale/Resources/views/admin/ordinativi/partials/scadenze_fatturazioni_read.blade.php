@php 

$offerte = $ordinativo->offerte()->get()->pluck('oggetto', 'id')->toArray(); 
    
$fatture = [''];
foreach ($ordinativo->fatture as $k => $ft)
    $fatture[$ft->id] = $ft->get_numero_fattura()
@endphp
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
                                            <th style="width: 35%;">Descrizione</th>
                                            <th style="width: 15%;">Data</th>
                                            <th style="width: 15%;">Data Avviso</th>
                                            <th style="width: 15%;">Importo</th> 
                                            <th style="width: 10%;">Fatturata</th>
                                        </tr>
                                        <tbody> 
                                            @if(!empty($ordinativo->fatturazioni_scadenze)) 
                                                @foreach($ordinativo->fatturazioni_scadenze as $key => $scadenza)
                                                    @if(!empty($scadenza->offerta_id))
                                                        @if($scadenza->offerta_id == $id)
                                                            <tr>
                                                                <td>{{   get_if_exist($scadenza, 'descrizione') }}</td>
                                                                <td>{{ get_if_exist($scadenza, 'data') }}</td>
                                                                <td>{{  get_if_exist($scadenza, 'data_avviso') }}</td>
                                                                <td>{{ get_if_exist($scadenza, 'importo') }}</td>
                                                                <td>{{  ((!empty(get_if_exist($scadenza, 'fattura_id'))) ?  $fatture[get_if_exist($scadenza, 'fattura_id')] : '')  }}</td>
                                                            </tr>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @else 
                                                <div class="alert alert-info alert-dismissible">
                                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                                    <h4><i class="icon fa fa-info"></i> Scadenze Fatturazioni non presenti</h4>
                                                    Questa offerta non ha alcuna scadenza inserita.</strong>
                                                </div>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <hr> 
                        @endforeach
                    @endif 
                    @if(!empty($ordinativo->fatturazioni_scadenze)) 
                        <h4><strong class="text-danger">NON COLLEGATE</strong></h4>
                        <br>
                        <div id="table">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tr>
                                        <th style="width: 35%;">Descrizione</th>
                                        <th style="width: 15%;">Data</th>
                                        <th style="width: 15%;">Data Avviso</th>
                                        <th style="width: 15%;">Importo</th> 
                                        <th style="width: 10%;">Fatturata</th>
                                    </tr>
                                    <tbody> 
                                        @foreach($ordinativo->fatturazioni_scadenze as $key => $scadenza)
                                            @if(empty($scadenza->offerta_id) || $scadenza->offerta_id == 0)
                                                <tr>
                                                    <td>{{   get_if_exist($scadenza, 'descrizione') }}</td>
                                                    <td>{{ get_if_exist($scadenza, 'data') }}</td>
                                                    <td>{{  get_if_exist($scadenza, 'data_avviso') }}</td>
                                                    <td>{{ get_if_exist($scadenza, 'importo') }}</td>
                                                    <td>{{  ((!empty(get_if_exist($scadenza, 'fattura_id'))) ?  $fatture[get_if_exist($scadenza, 'fattura_id')] : '')  }}</td>
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
                            <h4><i class="icon fa fa-info"></i> Scadenze Fatturazioni</h4>
                            L'ordinativo non ha alcuna scadenza inserita.</strong>
                        </div>                     
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
