<style>

    .turn-icon {
        -webkit-transform: rotate(90deg);
        -ms-transform: rotate(90deg);   
        transform: rotate(90deg);    
    }
    
    </style>
    
    
    <div class="box-body">
        <div class="row">
            <div class="col-md-12" style="margin-bottom:10px;">
                <a href="{{ route('admin.tasklist.attivita.create', ['cliente_id' => $ordinativo->cliente_id, 'ordinativo_id' => $ordinativo->id]) }}" target="_blank" type="button" class="btn btn btn-success btn-flat">
                    <i class="fa fa-plus"></i>
                    Crea nuova attività
                </a>
                <a href="{{ route('admin.tasklist.attivita.multicreate', ['cliente_id' => $ordinativo->cliente_id, 'ordinativo_id' => $ordinativo->id]) }}" target="_blank" type="button" class="btn btn btn-success btn-flat">
                    <i class="fa fa-plus"></i>
                    Crea attività multiple
                </a>
                <a href="{{ route('admin.tasklist.attivita.index', ['cliente' => array($ordinativo->cliente_id), 'stato' => array(-1), 'ordinativo' => array($ordinativo->id), 'lavorabili' => -1, 'all' => 1]) }}" target="_blank" type="button" class="btn btn btn-primary btn-flat">
                    <i class="fa fa-search"></i>
                    Filtri Avanzati
                </a>
                @if(auth_user()->hasAccess('commerciale.ordinativi.export.sal'))
                    <a href="{{ route('admin.tasklist.attivita.exportsalexcel', ['ordinativo_id' => $ordinativo->id]) }}" class="btn bg-olive btn-flat pull-right">
                        <i class="fa fa-table"> </i> Esporta SAL
                    </a> 
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box-group" id="accordion">
                    @if(count($ordinativo->attivita) > 0)
                        @foreach ($ordinativo->attivita as $key => $attivita)
                            <div class="panel box {{ ($attivita->percentuale_completamento == 100 ? 'box-success' : ($attivita->percentuale_completamento >= 1 ? 'box-warning' : 'box-danger')) }}">
                                <div class="box-header with-border">
                                    <h4 class="box-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse-{{ $attivita->id }}">
                                            <span class="label label-info">{{ get_if_exist($attivita->area, 'titolo') }} - {{ get_if_exist($attivita->gruppo, 'nome') }}</span>  → <span>{{ get_if_exist($attivita, 'oggetto') }}</span>
                                        </a> 
                                    </h4>
                                    <div class="box-tools pull-right">
                                        <span data-toggle="tooltip" title="" class="label {{ ($attivita->percentuale_completamento == 100 ? 'bg-green' : ($attivita->percentuale_completamento >= 1 ? 'bg-orange' : 'bg-red')) }}" data-original-title="Completato al {{ $attivita->percentuale_completamento() }}">{{ $attivita->percentuale_completamento() }}</span>
                                        {{-- <button type="button" class="btn btn-sm" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#modal-default" data-size="modal-lg" data-title="Modifica attivita" data-action="{{ route('admin.commerciale.ordinativo.attivita.edit' , [$attivita->id , $ordinativo->id]) }}"><i class="fa fa-edit"></i>
                                        </button> --}}
                                        @if($attivita->percentuale_completamento != 100 && $attivita->partecipanti()->contains('id', Auth::id()) || $attivita->percentuale_completamento == 100 && $attivita->supervisori() && $attivita->supervisori()->contains('id', Auth::id()))
                                            <a href="{{ route('admin.tasklist.attivita.edit', $attivita->id) }}" target="_blank" type="button" class="btn btn-sm" ><i class="fa fa-edit"></i></a>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-info" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#modal-default" data-size="modal-xl" data-title="Lettura attività" data-action="{{ route('admin.tasklist.attivita.read', $attivita->id) }}" data-element="form" data-ajax="true" data-parent="true"><i class="fa fa-eye"></i></button>
                                        <a href="{{ route('admin.tasklist.attivita.create', ['attivita_padre' => $attivita->id, 'cliente_id' => $ordinativo->cliente_id, 'ordinativo_id' => $ordinativo->id]) }}" target="_blank" type="button" class="btn btn-sm btn-success"><i class="fa turn-icon fa-level-up"></i></a>
                                    </div>
                                </div>
                                <div id="collapse-{{ $attivita->id }}" class="panel-collapse collapse">
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                @if(count($attivita->users) > 0)
                                                    <h4><small class="fa fa-users"> </small> Assegnatari</h4>
                                                    <ul>
                                                        @foreach ($attivita->users as $key => $assegnatario)
                                                            <li>{{ $assegnatario->first_name }} {{ $assegnatario->last_name }}</li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                            <div class="col-md-4">
                                                <h4><small class="fa fa-clock-o"> </small> Tempistiche</h4>
                                                <p>Inzio in data: <strong>{{ $attivita->data_inizio }}</strong></p>
                                                @if(!empty($attivita->data_fine))
                                                    <p>Data di scadenza: <strong>{{ $attivita->data_fine }}</strong></p>
                                                @endif
                                            </div>
                                            <div class="col-md-12">
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-warning progress-bar-striped" role="progressbar" aria-valuenow="{{ $attivita->percentuale_completamento }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $attivita->percentuale_completamento }}%">
                                                    <span class="sr-only">{{ $attivita->percentuale_completamento }}% Completato</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="callout callout-danger">
                            <h4>ATTENZIONE!</h4>
                            <p>Non sono state create attività per questo ordinativo.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>  