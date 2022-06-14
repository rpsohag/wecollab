@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('Attività') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('Attività') }}</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="btn-group pull-right" style="margin: 0 15px 15px 0;">
                    @if(auth_user()->hasAccess('tasklist.attivita.export'))
                        <a href="{{ route('admin.tasklist.attivita.exportexcel', request()->all()) }}" class="btn bg-olive btn-flat" style="padding: 4px 10px;">
                            <i class="fa fa-table"> </i> Esporta Excel
                        </a>
                    @endif
                    @if(auth_user()->hasAccess('tasklist.attivita.gantt'))
                        <a href="{{ route('admin.tasklist.attivita.gantt', request()->all()) }}" class="btn bg-orange btn-flat" style="padding: 4px 10px;">
                            <i class="fa fa-bar-chart"> </i> Gantt
                        </a>
                    @endif
                    <a href="{{ route('admin.tasklist.attivita.create') }}" class="btn btn-primary btn-flat" style="padding: 4px 10px;">
                        <i class="fa fa-pencil"></i> {{ trans('Crea Attività') }}
                    </a>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header">
                  {!! Form::open(['route' => ['admin.tasklist.attivita.index'], 'method' => 'get']) !!}
                  <div class="row">
                    <div class="col-md-10">
                      {!! Form::weText('cerca', '', $errors, old('cerca'), ['placeholder' => 'Cerca per: oggetto, note, richiedente, attività']) !!}
                    </div>
                    <div class="col-md-2 text-right">
                      {!! Form::weSubmit('Cerca') !!}
                      {!! Form::weReset('Svuota') !!}
                    </div>
                  </div>
                  <div class="box box-default bg-gray filters no-margin no-padding collapsed-box">
                    <div class="box-header with-border cursor-pointer" data-widget="collapse">
                      <h4 class="box-title no-icon">Filtri avanzati</h4>
                    </div>
                    <div class="box-body" style="display: none;">
                      <div class="row">
                          <div class="col-md-12">
                              <div class="row">
                                @if(Auth::user()->hasAccess('tasklist.attivita.all'))
                                  <div class="col-md-3">
                                    {!! Form::weSelect('all', 'Tutte le attività', $errors, config('wecore.sn')) !!}
                                  </div>
                                @endif
                                <div class="col-md-3">
                                     {!! Form::weText('oggetto', 'Oggetto', $errors) !!}
                                </div>
                                <div class="col-md-3">
                                  {!! Form::weTags('cliente', 'Clienti', $errors , $clienti) !!}
                                </div>
                                <div class="col-md-3">
                                  {!! Form::weTags('stato', 'Stati', $errors , $stati_filter ) !!}
                                </div>
                              </div>
                              <div class="row">
                                  <div class="col-md-4">
                                      {!! Form::weTags('procedura_id', 'Procedura', $errors, $procedure) !!}
                                  </div>
                                  <div class="col-md-4">
                                      {!! Form::weTags('area_id', 'Area di Intervento' , $errors, $aree) !!}
                                  </div>
                                  <div class="col-md-4">
                                      {!! Form::weTags('gruppo_id', 'Ambiti' , $errors , $gruppi) !!}
                                  </div>
                              </div>
                              <div class="row">
                                <div class="col-md-3">
                                    {!! Form::weSelectSearch('richiedente', 'Richiedente', $errors , [-1 => ''] + $utenti) !!}
                                </div>
                                <div class="col-md-3">
                                    {!! Form::weTags('assegnatari', 'Assegnato a', $errors , $utenti) !!}
                                </div>
                                <div class="col-md-2">
                                  {!! Form::weDate('data_inizio', 'Data di Inizio', $errors  ) !!}
                                </div>
                                <div class="col-md-2">
                                  {!! Form::weDate('data_fine', 'Data di Fine', $errors  ) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::weDate('data_chiusura', 'Data Chiusura', $errors  ) !!}
                                  </div>
                              </div>
                              <div class="row">
                                <div class="col-md-2">
                                    {!! Form::weSelectSearch('priorita', 'Priorità', $errors , $priorita_filter ) !!}
                                </div>
                                <div class="col-md-2">
                                  {!! Form::weSelect('ordinativo_sn', 'Con Ordinativo', $errors, [-1 => ''] + config('wecore.sn')) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::weSelect('lavorabili', 'Lavorabili', $errors, [-1 => 'Tutte'] + config('wecore.sn')) !!}
                                  </div>
                                <div class="col-md-3">
                                    {!! Form::weTags('ordinativo', 'Ordinativo', $errors, $ordinativi) !!}
                                  </div>
                                <div class="col-md-2">
                                  {!! Form::weSelect('azienda', 'Azienda', $errors, [0 => 'Tutte'] + config('wecore.aziende')) !!}
                                </div>
                              </div>
                          </div>
                      </div>
                      <input type="hidden" name="order[by]" value="{{ !empty(request('order')['by']) ? request('order')['by'] : '' }}">
                      <input type="hidden" name="order[sort]" value="{{ !empty(request('order')['sort']) ? request('order')['sort'] : '' }}">
                    </div>
                  </div>

                  {!! Form::close() !!}

                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="data-table table table-bordered table-hover">
                            <thead>
                                <tr>
                                    {!! order_th('percentuale_completamento', 'Info') !!}
                                    {!! order_th('cliente', 'Cliente') !!}
                                    {!! order_th('oggetto', 'Oggetto') !!}
                                    {{-- {!! order_th('percentuale_completamento', 'Completamento') !!} --}}
                                    <th class="text-primary">Ultima Nota</th>
                                    <th class="text-primary">Ultima Presa In Carico</th>
                                    {!! order_th('data_inizio', 'Data Inizio') !!}
                                    {!! order_th('data_fine', 'Data Fine') !!}
                                    {!! order_th('data_chiusura', 'Data Chiusura') !!}
                                    <th class="text-primary">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($attivita))
                                    @foreach($attivita as $task)
                                        @php $partecipanti_attivita = $task->partecipanti(); @endphp
                                        <tr class="{{ $task->pinnedBy() && $task->pinnedBy()->contains('id', Auth::id()) ? 'bg-info' : '' }}">
                                            <td style="width:10%;" class="text-center">
                                                @if($task->stato == 0 || $task->stato == 1)
                                                    <a target="_blank" href="{{ route('admin.tasklist.attivita.read', [$task->id]) }}">
                                                        <i class="fa fa-circle {{ $task->percentuale_completamento >= 1 && $task->percentuale_completamento <= 99 ? 'text-orange' : ($task->percentuale_completamento == 100 ? 'text-success' : 'text-danger') }}" aria-hidden="true" data-toggle="tooltip" data-placement="right" title="{{ $task->percentuale_completamento >= 1 && $task->percentuale_completamento <= 99 ? 'L\'attività è in lavorazione.' : ($task->percentuale_completamento == 100 ? 'L\'attività è stata completata.' : 'L\'attività deve essere lavorata.') }}"></i>
                                                        {{-- <i class="{{ $priorita_icone[$task->priorita]['icon'] }} {{ $priorita_icone[$task->priorita]['class'] }}" data-toggle="tooltip" data-placement="right" title="Priorità {{ $priorita[$task->priorita] }}."> </i> --}}                                              
                                                        @if($task->hasRequisiti())
                                                            <i class="fa fa-check-circle text-success" style="margin-left:5px;" data-toggle="tooltip" data-placement="right" title="L'attività può essere lavorata."> </i>
                                                        @else 
                                                            <a href="#modal-attivita-requisiti" class="requisiti-attivita" data-toggle="modal" data-target="#modal-attivita-requisiti" data-id="{{ $task->id }}" data-oggetto="{{ $task->oggetto }}" data-cliente="{{ $task->cliente->ragione_sociale }}"><i class="fa fa-ban text-danger" style="margin-left:5px;" data-toggle="tooltip" data-placement="right" title="Ha attività propedeutiche da completare."> </i></a>
                                                        @endif
                                                        @if(!$task->hasPresoVisione())
                                                            <i class="fa fa-hand-paper-o text-warning" style="margin-left:5px;" data-toggle="tooltip" data-placement="right" title="Devi ancora prendere in carico l'attività."> </i>
                                                        @elseif($partecipanti_attivita->contains('id', Auth::id()))
                                                            <i class="fa fa-thumbs-up text-success" style="margin-left:5px;" data-toggle="tooltip" data-placement="right" title="Hai preso in carico l'attività."> </i>
                                                        @endif
                                                        @if($partecipanti_attivita->contains('id', Auth::id()))
                                                            @if($task->pinnedBy() && $task->pinnedBy()->contains('id', Auth::id()))
                                                                <a href="{{ route('admin.tasklist.attivita.pin', $task) }}"><i class="fa fa-thumb-tack text-info" style="margin-left:5px;" data-toggle="tooltip" data-placement="right" title="Attività in risalto."> </i></a>
                                                            @else
                                                                <a href="{{ route('admin.tasklist.attivita.pin', $task) }}"><i class="fa fa-thumb-tack text-gray" style="margin-left:5px;" data-toggle="tooltip" data-placement="right" title="Premi per mettere in risalto l'attività."> </i></a>
                                                            @endif
                                                        @endif  
                                                    </a>
                                                    <i class="fa fa-building {{ $task->azienda == 'Digit Consulting' ? 'text-warning' : 'text-info' }}" style="margin-left:5px;" data-toggle="tooltip" data-placement="right" title="{{ $task->azienda }}"> </i>
                                                @elseif($task->stato == 2)
                                                    <span class="text-success"><strong>COMPLETATA</strong></span>
                                                @else 
                                                    <span class="text-danger"><strong> ANNULLATA</strong></span>
                                                @endif
                                            </td>
                                            <td style="width:20%;">
                                                <a target="_blank" href="{{ route('admin.tasklist.attivita.read', [$task->id]) }}">
                                                    {{ get_if_exist($task->cliente, 'ragione_sociale') }}
                                                </a>
                                            </td>
                                            <td style="width:20%;">
                                                <a target="_blank" href="{{ route('admin.tasklist.attivita.read', [$task->id]) }}" data-toggle="tooltip" title="Cliente: {{ get_if_exist($task->cliente, 'ragione_sociale') }}">
                                                    @if($task->stato == 3) <s class="text-danger"> @endif {{ get_if_exist($task, 'oggetto') }} @if($task->stato == 3) </s> @endif
                                                </a>
                                            </td>
                                            {{-- <td class="text-center">
                                                <a href="{{ route('admin.tasklist.attivita.read', [$task->id]) }}">
                                                    <div data-toggle="tooltip" data-placement="top" title="{{ $task->percentuale_completamento }}" class="progress progress-sm {{ ($task->percentuale_completamento != 100) ? 'active' : '' }}">
                                                        <div class="progress-bar progress-bar-{{ ($task->percentuale_completamento != 100) ? 'warning' : 'success' }} progress-bar-striped" role="progressbar" aria-valuenow="{{ $task->percentuale_completamento }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $task->percentuale_completamento }}%">
                                                            <span class="sr-only">{{ $task->percentuale_completamento }} completate</span>
                                                        </div>
                                                    </div>
                                                </a>
                                            </td> --}}
                                            <td style="width:10%;">{{ !empty(json_decode($task->notes->first())) ? $task->notes->first()->createdUser->full_name . ' : ' . json_decode($task->notes->first())->value : '' }}</td>
                                            <td style="width:10%;" class="text-center"><span class="label label-info">{{ optional(optional($task->preseVisioni())->last())->full_name }}</span></td>
                                            <td style="width:5%;"><a target="_blank" href="{{ route('admin.tasklist.attivita.read', [$task->id]) }}">{{ get_if_exist($task, 'data_inizio') }}</a></td>
                                            <td style="width:5%;"><a target="_blank" href="{{ route('admin.tasklist.attivita.read', [$task->id]) }}">{{ get_if_exist($task, 'data_fine') }}</a></td>
                                            <td style="width:5%;"><a target="_blank" href="{{ route('admin.tasklist.attivita.read', [$task->id]) }}">{{  get_if_exist($task, 'data_chiusura') }}</a></td>
                                            <td style="width:15%;">
                                                <a href="{{ route('admin.tasklist.attivita.read', $task->id) }}" class="btn btn-md btn-flat btn-info" type="button"><i class="fa fa-eye"></i></a>
                                                @if($partecipanti_attivita->contains('id', Auth::id()))
                                                    <a href="{{ route('admin.tasklist.attivita.edit', $task->id) }}" class="btn btn-md btn-flat btn-warning" type="button"><i class="fa fa-pencil"></i></a>
                                                @endif
                                                @if($task->hasRequisiti())
                                                    <button type="button" class="btn btn-default btn-flat btn-success timesheet-button" data-toggle="modal" data-id="{{ $task->id }}" data-target="#storeTimesheet"><i class="fa fa-calendar" style="color:white;"></i></button>
                                                    @if(!empty($task->supervisori()) && $task->supervisori()->count() > 0)
                                                        @if($task->supervisori()->contains('id', Auth::id()))
                                                            <button type="button" class="btn btn-md btn-flat bg-purple sollecita-button" data-toggle="modal" data-id="{{ $task->id }}" data-target="#sollecitaAssegnatari"><i class="fa fa-bell"></i></button>
                                                        @endif
                                                    @endif
                                                @endif
                                                @if(!empty($task->requisiti()) && $task->requisiti()->count() > 0)
                                                    <span data-toggle="tooltip" data-placement="top" title="Mostra requisiti per la lavorazione."><button class="btn btn-md btn-flat btn-danger requisiti-attivita" type="button" data-toggle="modal" data-target="#modal-attivita-requisiti" data-id="{{ $task->id }}" data-oggetto="{{ $task->oggetto }}" data-cliente="{{ $task->cliente->ragione_sociale }}"><i class="fa fa-ban"></i></button></span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        <div class="text-right pagination-container">
                          {{ $attivita->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-attivita-requisiti" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                  <h3 class="modal-title" id="modal-attivita-requisiti-header"></h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                          <div id="modal-attivita-requisiti-body"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                </div>
            </div>
        </div>
      </div>

      <div class="modal fade" id="storeTimesheet" tabindex="-1" role="dialog" aria-hidden="true">
        {!! Form::open(['route' => ['admin.tasklist.attivita.store.timesheet'], 'method' => 'post']) !!}
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Crea Timesheet</h3>
                </div>
                <div class="modal-body">
                    <input id="timesheet-id" type="hidden" value="" name="attivita_id">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::weDate('data', 'Data', $errors, date('d/m/Y')) !!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::weSelectSearch('tipologia', 'Tipologia *', $errors, $tipologie)!!}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::weTime('ora_inizio', 'Ora di inizio', $errors) !!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::weTime('ora_fine', 'Ora di fine', $errors) !!}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::weText('nota', 'Nota', $errors) !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-success btn-flat"><i class="fa fa-floppy-o"></i> {{ trans('core::core.button.save') }}</button>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>

      <div class="modal fade" id="sollecitaAssegnatari" tabindex="-1" role="dialog" aria-hidden="true">
        {!! Form::open(['route' => ['admin.tasklist.attivita.sollecita'], 'method' => 'post']) !!}
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Sollecita Assegnatari</h3>
                </div>
                <div class="modal-body">
                    <input id="sollecita-id" type="hidden" value="" name="attivita_id">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::weText('nota', 'Nota *', $errors) !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-success btn-flat"><i class="fa fa-bell"></i> Sollecita</button>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
@stop

@section('footer')
    <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp;
@stop
@section('shortcuts')
    <dl class="dl-horizontal">
        <dt><code>c</code></dt>
        <dd>{{ trans('tasklist::attivita.title.create attivita') }}</dd>
    </dl>
@stop

@push('js-stack')
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'c', route: "<?= route('admin.tasklist.attivita.create') ?>" }
                ]
            });
        });
    </script>
    <?php $locale = locale(); ?>
    <script type="text/javascript">
        $(function () {
            $('.data-table').dataTable({
                "paginate": false,
                "lengthChange": false,
                "filter": false,
                "sort": false,
                "info": false,
                "autoWidth": true,
                "language": {
                    "url": '<?php echo Module::asset("core:js/vendor/datatables/{$locale}.json") ?>'
                }
            });
        });
    </script>
    <script> 
        $(".requisiti-attivita").click(function(){
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var id = $(this).data('id');
        var oggetto = $(this).data('oggetto');
        var cliente = $(this).data('cliente');
        var modal = $('#modal-attivita-requisiti');
        var header = $('#modal-attivita-requisiti-header');
        var body = $('#modal-attivita-requisiti-body');
        var content = "<div class='box box-danger'><div class='box-body'><div class='row'>";
        $.ajax({
            url: "{{ route('admin.tasklist.attivita.requisiti') }}",
            type: 'GET',
            data: {_token: CSRF_TOKEN, id: id},
            dataType: 'JSON' 
            }).done(function(data) {
                requisiti = data.requisiti;
                header.html('Requisiti <strong class="text-danger">' + oggetto + '</strong> - <small>( ' + cliente + ' )</small>');
                requisiti.forEach(function(attivita) {
                    var link =  "{{ route('admin.tasklist.attivita.read', ':id') }}";
                    link = link.replace(':id', attivita.id);
                    content += "<div class='col-md-12'><div class='info-box'>";
                    if(attivita.percentuale_completamento == 100) { content += "<span class='info-box-icon bg-green'><a class='text-success' target='_blank' href='" + link + "'>" + "<i class='fa fa-external-link'></i></a></span>"; }
                    if(attivita.percentuale_completamento < 100 && attivita.percentuale_completamento >= 1) { content += "<span class='info-box-icon bg-orange'><a class='text-warning' target='_blank' href='" + link + "'>" + "<i class='fa fa-external-link'></i></a></span>"; }
                    if(attivita.percentuale_completamento < 1) { content += "<span class='info-box-icon bg-red'><a class='text-danger' target='_blank' href='" + link + "'>" + "<i class='fa fa-external-link'></i></a></span>"; }
                    content += "<div class='info-box-content'>";
                    if(attivita.percentuale_completamento == 100) { content += "<span class='info-box-text'>COMPLETAMENTO ATTIVITA' ( <span class='text-bold text-success'> Progresso " + attivita.percentuale_completamento + "% </span>)</span>"; }
                    if(attivita.percentuale_completamento < 100 && attivita.percentuale_completamento >= 1) { content += "<span class='info-box-text'>COMPLETAMENTO ATTIVITA' ( <span class='text-bold text-orange'> Progresso " + attivita.percentuale_completamento + "% </span>)</span>"; }
                    if(attivita.percentuale_completamento < 1) { content += "<span class='info-box-text'>COMPLETAMENTO ATTIVITA' ( <span class='text-bold text-danger'> Progresso " + attivita.percentuale_completamento + "% </span>)</span>"; }
                    content += "<span class='info-box-number'><a target='_blank' href='" + link + "'>" + attivita.oggetto + "</a></span>";
                    content += "</div></div></div><br>";
                });
                content += "</div></div></div>";
                body.html(content);
            }); 
        });
        $(".timesheet-button").click(function(){
            var id = $(this).data('id');
            $('#timesheet-id').val(id);
        });
        $(".sollecita-button").click(function(){
            var id = $(this).data('id');
            $('#sollecita-id').val(id);
        });
    </script>
@endpush
