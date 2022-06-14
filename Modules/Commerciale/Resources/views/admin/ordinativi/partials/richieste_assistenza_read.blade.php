<br>
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Riepilogo</h3>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover table-striped">
                        <tbody>
                        <tr>
                            <th>RICHIESTE ATTIVE</th>
                            <th>RICHIESTE RISOLTE</th>
                            <th>TEMPO MEDIO RISOLUZIONE LAVORATIVA</th>
                            <th>TEMPO MEDIO RISOLUZIONE LAVORATIVA ( < 3 ORE )</th>
                            <th>RICHIESTE RISOLTE ( < 3 ORE )</th>
                        </tr>
                        <tr>
                            <td>{{$numero_aperte}}</td>
                            <td>{{$numero_chiuse}}</td>
                            <td>{{secondsToTime($tempo_medio_risoluzione)}}</td>
                            <td>{{secondsToTime($media_chiuse_min3)}}</td>
                            <td>{{$min3}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Dettaglio per Attività</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover table-striped">
                        <tbody>
                        <tr>
                            <th>ATTIVITA'</th>
                            <th>ATTIVE</th>
                            <th>RISOLTE</th>
                            <th>TEMPO MEDIO LAVORAZIONE</th>
                            <th>TEMPO ATTESA MEDIO CLIENTE</th>
                        </tr>
                        @foreach ($tbl_riepilogo as $key => $dati)
                            <tr>
                                <td>{{$dati['nome']}}</td>
                                <td>{{$dati['attive']}}</td>
                                <td>{{$dati['risolte']}}</td>
                                <td>{{secondsToTime($dati['tempo_medio'])}}</td>
                                <td>{{get_seconds_to_his($dati['tempo_attesa'])}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>



