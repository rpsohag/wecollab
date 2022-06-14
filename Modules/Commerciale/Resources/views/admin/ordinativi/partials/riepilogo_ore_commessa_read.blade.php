<div class="box-body">
    <h3><strong>Totali <small>(Tutte le aree d'intervento)</small></strong></h3>
    <hr>
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-orange"><i class="fa fa-exclamation-triangle"></i></span>

                <div class="info-box-content">
                <span class="info-box-text">Previsto</span>
                <span class="info-box-number">{{ $commessa_results_totals['previsto'] }} ORE</span>
                </div>

            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-calendar"></i></span>

                <div class="info-box-content">
                <span class="info-box-text">Dai Timesheets</span>
                <span class="info-box-number">{{ $commessa_results_totals['daitimesheets'] }} ORE</span>
                </div>

            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon {{ (!empty($commessa_results_totals['saldo']) && $commessa_results_totals['saldo'] < 0) ? 'bg-red' : 'bg-green' }}"><i class="fa fa-archive"></i></span>

                <div class="info-box-content">
                <span class="info-box-text">Saldo</span>
                <span class="info-box-number {{ (!empty($commessa_results_totals['saldo']) && $commessa_results_totals['saldo'] < 0) ? 'text-danger' : '' }}">{{ $commessa_results_totals['saldo'] }} ORE</span>
                </div>

            </div>
        </div>
    </div>
    @if(!empty($aree_commessa_ids) && count($aree_commessa_ids) > 0)
        <h3><strong>Per area d'intervento <small>(in ore)</small></strong></h3>
        <hr>
        <div class="box-body">
            <div class="box box-primary box-shadow">
                <div class="box-body">
                    @foreach($aree_commessa as $area)
                        <h4 style="margin-bottom:6px;"><strong>{{ $area->titolo }}</strong></h4>
                        <hr>
                        <div class="row">
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="info-box info-box-sm" style="margin-left:15px;">
                                    <span class="info-box-icon bg-orange"><i class="fa fa-exclamation-triangle"></i></span>
                    
                                    <div class="info-box-content">
                                    <span class="info-box-text">Previsto</span>
                                    <span class="info-box-number">{{ (!empty($commessa_results_aree_totals[$area->id]['previsto'])) ? $commessa_results_aree_totals[$area->id]['previsto'] : 0 }} ORE</span>
                                    </div>
                    
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="info-box info-box-sm">
                                    <span class="info-box-icon bg-aqua"><i class="fa fa-calendar"></i></span>
                    
                                    <div class="info-box-content">
                                    <span class="info-box-text">Dai Timesheets</span>
                                    <span class="info-box-number">{{ (!empty($commessa_results_aree_totals[$area->id]['daitimesheets'])) ? $commessa_results_aree_totals[$area->id]['daitimesheets'] : 0 }} ORE</span>
                                    </div>
                    
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="info-box info-box-sm" style="margin-right:15px;">
                                    <span class="info-box-icon {{ (!empty($commessa_results_aree_totals[$area->id]['saldo']) && $commessa_results_aree_totals[$area->id]['saldo'] < 0) ? 'bg-red' : 'bg-green' }}"><i class="fa fa-archive"></i></span>
                    
                                    <div class="info-box-content">
                                    <span class="info-box-text">Saldo</span>
                                    <span class="info-box-number {{ (!empty($commessa_results_aree_totals[$area->id]['saldo']) && $commessa_results_aree_totals[$area->id]['saldo'] < 0) ? 'text-danger' : '' }}">{{ (!empty($commessa_results_aree_totals[$area->id]['saldo'])) ? $commessa_results_aree_totals[$area->id]['saldo'] : 0 }} ORE</span>
                                    </div>
                    
                                </div>
                            </div>
                        </div>
                        @foreach($area->attivita as $attivita)
                            @if(!empty($commessa_results[$attivita->id]) && $commessa_results[$attivita->id]['previsto']['totale'] > 0 || !empty($commessa_results[$attivita->id]) && $commessa_results[$attivita->id]['daitimesheets']['totale'])
                                <br>
                                <div class="table-responsive" style="margin-left:12px;">
                                    <table class="table table-striped">
                                        <caption><span class="badge bg-green" style="margin-left:12px;"><i class="fa fa-arrow-right" aria-hidden="true"></i><strong> {{ $attivita->nome }}</strong></span></caption>
                                        <tr>
                                            <td></td>
                                            @foreach($tipologie as $key => $value)
                                                <td scope="col">{{ $value }}</td>
                                            @endforeach
                                            <td scope="col"> Totale </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Previsto</th>
                                            @foreach($commessa_results[$attivita->id]['previsto'] as $key => $value)
                                                @if(!$loop->last)
                                                    <td scope="col">{{ $value }}</td>
                                                @endif
                                            @endforeach        
                                            <td>{{ $commessa_results[$attivita->id]['previsto']['totale'] }}</td>                                    
                                        </tr>
                                        <tr>
                                            <th scope="row">Dai Timesheets</th>
                                            @foreach($commessa_results[$attivita->id]['daitimesheets'] as $key => $value)
                                                @if(!$loop->last)
                                                    <td scope="col">{{ $value }}</td>
                                                @endif
                                            @endforeach        
                                            <td>{{ $commessa_results[$attivita->id]['daitimesheets']['totale'] }}</td>                                    
                                        </tr>
                                        <tr>
                                            <th class="{{ ($commessa_results[$attivita->id]['saldo']['totale'] < 0 ? 'bg-red' : '') }}" scope="row">Saldo</th>
                                            @foreach($commessa_results[$attivita->id]['saldo'] as $key => $value)
                                                @if(!$loop->last)
                                                    <td class="{{ ($commessa_results[$attivita->id]['saldo']['totale'] < 0 ? 'bg-red' : '') }}" scope="col">{{ $value }}</td>
                                                @endif
                                            @endforeach        
                                            <td class="{{ ($commessa_results[$attivita->id]['saldo']['totale'] < 0 ? 'bg-red' : '') }}">{{ $commessa_results[$attivita->id]['saldo']['totale'] }}</td>                                    
                                        </tr>
                                    </table>
                                </div>
                            @endif
                        @endforeach
                    @endforeach
                </div>
            </div>      
        </div>
    @else
        <div class="alert alert-info alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-ban"></i> {{ $ordinativo->oggetto }}</h4>
            Non vi è alcun dato da poter mostrare. <small>(Analisi vendita e/o timesheets)</small>
        </div>
    @endif
</div>