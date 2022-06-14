@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('Wecloud') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('Wecloud') }}</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-md-10">
                  @if(auth_user()->hasAccess('wecloud.file.create'))
                      <div class="btn-group pull-left" style="margin: 0 15px 15px 0;">
                          <button class="btn btn-primary btn-flat" data-toggle="modal" data-target="#uploadFile" style="padding: 4px 10px;">
                              <i class="fa fa-upload"></i> Carica file
                          </button>
                      </div>
                  @endif
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header">
                    {!! Form::open(['route' => ['admin.wecloud.file.index'], 'method' => 'get']) !!}

                    <div class="row" style="margin-top: 10px;">
                        <div class="col-md-4">
                            {!! Form::weText('cerca', 'Cerca', $errors, old('cerca'), ['placeholder' => 'Cerca per: nome, estensione, azienda..']) !!}
                        </div>
                        <div class="col-md-2">
                            {!! Form::weSelectSearch('procedura', 'Procedura', $errors, $procedure) !!}
                        </div>
                        <div class="col-md-2">
                            {!! Form::weSelectSearch('area', 'Area di Intervento', $errors, $aree) !!}
                        </div>
                        <div class="col-md-2">
                            {!! Form::weSelectSearch('gruppo', 'Gruppo', $errors, $gruppi) !!}
                        </div>
                        <div class="col-md-2 text-right">
                            {!! Form::weSubmit('Cerca') !!}
                            {!! Form::weReset('Svuota') !!}
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
                <div class="box-body">
                    <div class="box box-success box-shadow">
                        <div class="box-header with-border">
                            <h3 class="box-title">Files</h3>
                        </div>
                        <div class="box-body" id="allegati">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tr>
                                        <th>Nome</th>
                                        <th class="text-center">Estensione</th>
                                        <th class="text-center">Size</th>
                                        <th class="text-center">Caricato da</th>
                                        <th class="text-center">Procedura</th>
                                        <th class="text-center">Area</th>
                                        <th class="text-center">Gruppo</th>
                                        <th class="text-center">Data di Caricamento</th>
                                        <th class="text-center">Scarica</th>
                                        <th class="text-center">Elimina</th>
                                    </tr>
                                    <tbody>
                                        @foreach ($files as $file)
                                            <tr>
                                                <td><a href="{{ download_file($file->value->path, $file->value->client_name) }}" target="_blank"><strong>{{ $file->value->name }}</strong></a></td>
                                                <td class="text-center"><i class="fa {{ file_icons($file->value->extension) }} fa-2x text-primary"></i></td>
                                                <td class="text-center">{{ mb($file->value->size) }} MB</td>
                                                <td class="text-center">{{ $file->user->full_name }}</td>
                                                <td class="text-center">{{ !empty(optional($file->value)->procedura_id) ? $procedure[$file->value->procedura_id] : '' }}</td>
                                                <td class="text-center">{{ !empty(optional($file->value)->area_id) ? $aree[$file->value->area_id] : '' }}</td>
                                                <td class="text-center">{{ !empty(optional($file->value)->gruppo_id) ? $gruppi[$file->value->gruppo_id] : '' }}</td>
                                                <td class="text-center">{{ $file->created_at }}</td>
                                                <td class="text-center"><a href="{{ download_file($file->value->path, $file->value->client_name) }}" target="_blank"><i class="fa fa-download fa-2x text-success" aria-hidden="true"></i></a></td>
                                                @if(Auth::user()->hasAccess('wecloud.file.destroy'))
                                                    <td class="text-center"> <button class="btn btn-md btn-flat btn-danger" type="button" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{  route('admin.wecloud.file.destroy', $file->id)  }}"><i class="fa fa-trash"></i></button> </td>
                                                @else 
                                                    <td></td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{ $files->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('core::partials.delete-modal')
    {!! Form::open(['route' => ['admin.wecloud.uploadFile'], 'method' => 'post', 'files' => true]) !!}
        @csrf
        <div class="modal fade" id="uploadFile" tabindex="2" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Carica File</h3>
                    </div>
                    <div class="modal-content">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                {{ Form::weText('file_nome', 'Nome *', $errors) }}
                                </div>
                                <div class="col-md-12">
                                {{ Form::weFile('file', 'Documento *', $errors) }}
                                </div>
                                <div class="col-md-4">
                                    {{ Form::weSelectSearch('file_procedura_id', 'Procedura', $errors, $procedure, '', ['id'=>'procedura_select']) }}
                                </div>
                                <div class="col-md-4">
                                    {{ Form::weSelectSearch('file_area_id', 'Area Intervento', $errors, $aree, '', ['id'=>'area_select']) }}
                                </div>
                                <div class="col-md-4">
                                    {{ Form::weSelectSearch('file_gruppo_id', 'Attività', $errors, $gruppi, '', ['id'=>'gruppo_select']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {!! Form::weSubmit('Carica') !!}
                    </div>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
@stop

@section('footer')
    <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp;
@stop
@section('shortcuts')
    <dl class="dl-horizontal">
        <dt><code>c</code></dt>
        <dd>{{ trans('Carica') }}</dd>
    </dl>
@stop

@push('js-stack')
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'c', route: "<?= route('admin.wecloud.file.index') ?>" }
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
                "order": [[ 7, "asc" ]],
                "language": {
                    "url": '<?php echo Module::asset("core:js/vendor/datatables/{$locale}.json") ?>'
                }
            });
        });
    </script>
@endpush

@push('js-stack')
<script type="text/javascript">
    var procedura_select = $('#procedura_select');
    var area_select = $('#area_select');
    var gruppo_select = $('#gruppo_select');
    var aree = $.parseJSON(atob("{{ get_json_aree() }}"));
    var gruppi = $.parseJSON(atob("{{ get_json_gruppi() }}"));
    // Selezione procedura
    procedura_select.change(function(e) {
      area_select.empty();
      var procedura_selezionata = procedura_select.val();
      aree.forEach(element => {
          //scorro tutto l'array e controllo quelli che hanno il procedura id  =  alla procedura selezionata
          if(element.procedura_id == procedura_selezionata){
              var newOption = new Option(element.titolo, element.id, false, false);
              area_select.append(newOption);
          }
      });

      //assegnare nuove_aree_di_intervento alla select delle aree di intervento
      area_select.trigger('change');
      area_select.select2('open');
      gruppo_select.select2('close');
    });

    // Selezione area
    area_select.change(function(e) {
      gruppo_select.empty();
      var area_selezionata = area_select.val();
      gruppi.forEach(element => {
          //scorro tutto l'array e controllo quelli che hanno il procedura id  =  alla procedura selezionata
          if(element.area_id == area_selezionata){
              var newOption = new Option(element.nome, element.id, false, false);
              gruppo_select.append(newOption);
          }
      });
      //assegnare nuove_aree_di_intervento alla select delle attivita
      gruppo_select.trigger('change');
      gruppo_select.select2('open');
      area_select.select2('close');
    });
</script>
@endpush
