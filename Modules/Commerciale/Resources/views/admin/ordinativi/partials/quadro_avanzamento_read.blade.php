<div class="box-body">  
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header">
                    <h3><strong>Quadro Avanzamento</strong></h4>
                </div>
                <div class="box-body">
                    <h4><strong class="text-danger">TOTALI</strong></h4>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <strong class="text-primary">Timesheets</strong>
                                    </thead>
                                    <tr>
                                        <th></th>
                                        <th>Previsto <small>(in ore)</small></th>
                                        <th>Effettuato <small>(in ore)</small></th>
                                        <th>%</th>
                                    </tr>
                                    <tbody> 
                                        <tr>
                                            <td><strong>NO HD</strong></td>
                                            <td>{{ $totali['timesheets']['previsti_no_hd'] }}</td>
                                            <td>{{ $totali['timesheets']['effettuati_no_hd'] }}</td>
                                            <td><span class="label {{ $totali['timesheets']['percentuale_no_hd'] == 100 ? 'label-success' : ($totali['timesheets']['percentuale_no_hd'] > 100 || $totali['timesheets']['percentuale_no_hd'] < 0 ? 'label-warning' : 'label-info') }}">{{ $totali['timesheets']['previsti_no_hd'] == 0 && $totali['timesheets']['effettuati_no_hd'] == 0 ? '-' : $totali['timesheets']['percentuale_no_hd'] }} %</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>HD</strong></td>
                                            <td>{{ $totali['timesheets']['previsti_hd'] }}</td>
                                            <td>{{ $totali['timesheets']['effettuati_hd'] }}</td>
                                            <td><span class="label {{ $totali['timesheets']['percentuale_hd'] == 100 ? 'label-success' : ($totali['timesheets']['percentuale_hd'] > 100 || $totali['timesheets']['percentuale_hd'] < 0 ? 'label-warning' : 'label-info') }}">{{ $totali['timesheets']['previsti_hd'] == 0 && $totali['timesheets']['effettuati_hd'] == 0 ? '-' : $totali['timesheets']['percentuale_hd'] }} %</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <strong class="text-primary">Interventi</strong>
                                    </thead>
                                    <tr>
                                        <th></th>
                                        <th>Previsto</th>
                                        <th>Effettuato</th>
                                        <th>%</th>
                                    </tr>
                                    <tbody> 
                                        <tr>
                                            <td><strong>GIORNATE</strong></td>
                                            <td>{{ $totali['interventi']['previsti_giornate'] }}</td>
                                            <td>{{ $totali['interventi']['effettuati_giornate'] }}</td>
                                            <td><span class="label {{ $totali['interventi']['percentuale_giornate'] == 100 ? 'label-success' : ($totali['interventi']['percentuale_giornate'] > 100 || $totali['interventi']['percentuale_giornate'] < 0 ? 'label-warning' : 'label-info') }}">{{ $totali['interventi']['previsti_giornate'] == 0 && $totali['interventi']['effettuati_giornate'] == 0 ? '-' : $totali['interventi']['percentuale_giornate'] }} %</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>ORE</strong></td>
                                            <td>{{ $totali['interventi']['previsti_ore'] }}</td>
                                            <td>{{ $totali['interventi']['effettuati_ore'] }}</td>
                                            <td><span class="label {{ $totali['interventi']['percentuale_ore'] == 100 ? 'label-success' : ($totali['interventi']['percentuale_ore'] > 100 || $totali['interventi']['percentuale_ore'] < 0 ? 'label-warning' : 'label-info') }}">{{ $totali['interventi']['previsti_ore'] == 0 && $totali['interventi']['effettuati_ore'] == 0 ? '-' : $totali['interventi']['percentuale_ore'] }} %</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <strong class="text-primary">Attività</strong>
                                    </thead>
                                    <tr>
                                        <th>Totali</th>
                                        <th>Completate</th>
                                        <th>Avanzamento</th>
                                    </tr>
                                    <tbody> 
                                        <tr>
                                            <td>{{ $totali['attivita']['totale'] }}</td>
                                            <td>{{ $totali['attivita']['completate'] }}</td>
                                            <td><span class="label {{ $totali['attivita']['percentuale'] == 100 ? 'label-success' : ($totali['attivita']['percentuale'] > 100 || $totali['attivita']['percentuale'] < 0 ? 'label-warning' : 'label-info') }}">{{ $totali['attivita']['totale'] == 0 && $totali['attivita']['completate'] == 0 ? '-' : $totali['attivita']['percentuale'] }} %</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>
                    @foreach($quadro_avanzamento as $key => $area)
                        @if($area['timesheets']['helpdesk']['previsto'] > 0 || $area['timesheets']['no_helpdesk']['previsto'] > 0 || $area['timesheets']['helpdesk']['effettuato'] > 0 ||  $area['timesheets']['no_helpdesk']['effettuato'] > 0 || $area['timesheets']['helpdesk']['effettuato'] > 0 || $area['attivita']['totale'] > 0 || $area['interventi']['giornate']['previsto'] > 0 || $area['interventi']['giornate']['effettuati'] > 0 || $area['interventi']['ore']['previsto'] > 0 || $area['interventi']['ore']['effettuati'] > 0 )
                            <h4><strong class="text-info">{{ $key }}</strong></h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <strong class="text-primary text-center justify-content-center" style="width:50%"><a href="{{ route('admin.tasklist.timesheet.manage', ['ordinativo' => [$ordinativo->id], 'area' => [$area['area_id']]]) }}" target="_blank">Timesheets <i class="fa fa-search" aria-hidden="true"></i></a></strong>
                                            </thead>
                                            <tr>
                                                <th></th>
                                                <th>Previsto <small>(in ore)</small></th>
                                                <th>Effettuato <small>(in ore)</small></th>
                                                <th>%</th>
                                            </tr>
                                            <tbody> 
                                                <tr>
                                                    <td><strong>NO HD</strong></td>
                                                    <td>{{ $area['timesheets']['no_helpdesk']['previsto'] }}</td>
                                                    <td>{{ $area['timesheets']['no_helpdesk']['effettuato'] }}</td>
                                                    <td><span class="label {{ $area['timesheets']['no_helpdesk']['percentuale'] == 100 ? 'label-success' : ($area['timesheets']['no_helpdesk']['percentuale'] > 100 || $area['timesheets']['no_helpdesk']['percentuale'] < 0 ? 'label-warning' : 'label-info') }}">{{ $area['timesheets']['no_helpdesk']['previsto'] == 0 && $area['timesheets']['no_helpdesk']['effettuato'] == 0 ? '-' : $area['timesheets']['no_helpdesk']['percentuale'] }} %</span></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>HD</strong></td>
                                                    <td>{{ $area['timesheets']['helpdesk']['previsto'] }}</td>
                                                    <td>{{ $area['timesheets']['helpdesk']['effettuato'] }}</td>
                                                    <td><span class="label {{ $area['timesheets']['helpdesk']['percentuale'] == 100 ? 'label-success' : ($area['timesheets']['helpdesk']['percentuale'] > 100 || $area['timesheets']['helpdesk']['percentuale'] < 0 ? 'label-warning' : 'label-info') }}">{{ $area['timesheets']['helpdesk']['effettuato'] == 0 && $area['timesheets']['helpdesk']['previsto'] == 0 ? '-' : $area['timesheets']['helpdesk']['percentuale'] }} %</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <strong class="text-primary text-center justify-content-center" style="width:50%"><a href="{{ route('admin.assistenza.ticketintervento.index', ['ordinativo' => $ordinativo->id, 'area' => [$area['area_id']]]) }}" target="_blank">Interventi <i class="fa fa-search" aria-hidden="true"></i></a></strong>
                                            </thead>
                                            <tr>
                                                <th></th>
                                                <th>Previsto</th>
                                                <th>Effettuato</th>
                                                <th>%</th>
                                            </tr>
                                            <tbody> 
                                                <tr>
                                                    <td><strong>GIORNATE</strong></td>
                                                    <td>{{ $area['interventi']['giornate']['previsto'] }}</td>
                                                    <td>{{ $area['interventi']['giornate']['effettuati'] }}</td>
                                                    <td><span class="label {{ $area['interventi']['giornate']['percentuale'] == 100 ? 'label-success' : ($area['interventi']['giornate']['percentuale'] > 100 || $area['interventi']['giornate']['percentuale'] < 0 ? 'label-warning' : 'label-info') }}">{{ $area['interventi']['giornate']['previsto'] == 0 && $area['interventi']['giornate']['effettuati'] == 0 ? '-' : $area['interventi']['giornate']['percentuale'] }} %</span></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>ORE</strong></td>
                                                    <td>{{ $area['interventi']['ore']['previsto'] }}</td>
                                                    <td>{{ $area['interventi']['ore']['effettuati'] }}</td>
                                                    <td><span class="label {{ $area['interventi']['ore']['percentuale'] == 100 ? 'label-success' : ($area['interventi']['ore']['percentuale'] > 100 || $area['interventi']['ore']['percentuale'] < 0 ? 'label-warning' : 'label-info') }}">{{ $area['interventi']['ore']['previsto'] == 0 && $area['interventi']['ore']['effettuati'] == 0 ? '-' : $area['interventi']['ore']['percentuale'] }} %</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <strong class="text-primary text-center justify-content-center" style="width:50%"><a href="{{ route('admin.tasklist.attivita.index', ['ordinativo' => [$ordinativo->id], 'area_id' => [$area['area_id']], 'stato' => [-1], 'lavorabili' => 1, 'all' => 1]) }}" target="_blank">Attività <i class="fa fa-search" aria-hidden="true"></i></a></strong>
                                            </thead>
                                            <tr>
                                                <th>Totali</th>
                                                <th>Completate</th>
                                                <th>Avanzamento</th>
                                            </tr>
                                            <tbody> 
                                                <tr>
                                                    <td>{{ $area['attivita']['totale'] }}</td>
                                                    <td>{{ $area['attivita']['completate'] }}</td>
                                                    <td><span class="label {{ $area['attivita']['percentuale'] == 100 ? 'label-success' : ($area['attivita']['percentuale'] > 100 || $area['attivita']['percentuale'] < 0 ? 'label-warning' : 'label-info') }}">{{ $area['attivita']['totale'] == 0 && $area['attivita']['completate'] == 0 ? '-' : $area['attivita']['percentuale'] }} %</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>