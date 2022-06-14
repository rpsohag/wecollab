@extends('layouts.master')

@section('content-header')
    <h1>Beni IT</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">Beni Strumentali</li>
    </ol>
@stop

@section('content')
<div class="box-body">
  <div class="row">
    <div class="btn-group pull-right" style="padding: 4px 10px; margin-bottom:5px; margin-right:5px;">
      @if(auth_user()->hasAccess('amministrazione.benistrumentali.create'))
        <button type="button" class="btn btn-primary btn-flat bene-nuovo"><i class="fa fa-pencil"></i> Crea Bene IT</button>
      @endif
    </div> 
  </div>
  <div class="box box-primary box-shadow">
    <div class="box-header with-border">
      <section class="bg-gray filters">
        {!! Form::open(['route' => ['admin.amministrazione.benistrumentali.index'], 'method' => 'get']) !!}
            <div class="row">
                <div class="col-md-10">
                    <div class="row">
                      <div class="col-md-3">
                        {!! Form::weSelectSearch('assegnatario_id_filter' , 'Assegnatario', $errors , $utenti) !!}
                      </div>
                      <div class="col-md-3">
                        {!! Form::weText('marca_filter' , 'Marca', $errors) !!}
                      </div>
                      <div class="col-md-3">
                        {!! Form::weText('modello_filter' , 'Modello', $errors) !!}
                      </div>
                      <div class="col-md-3">
                        {!! Form::weText('serial_number_filter' , 'Serial Number', $errors) !!}
                      </div>
                      <div class="col-md-3">
                        {!! Form::weText('imei_filter' , 'Imei', $errors) !!}
                      </div>
                      <div class="col-md-3">
                        {!! Form::weSelectSearch('tipologia_filter' , 'Tipologie', $errors, $tipologie) !!}
                      </div>
                      <div class="col-md-4">
                        {!! Form::weText('note_filter' , 'Note', $errors) !!}
                      </div>
                      <div class="col-md-2">
                        {!! Form::weDate('data_assegnazione_filter' , 'Data Consegna', $errors) !!}
                      </div>
                    </div>
                </div>
                <div class="col-md-2 text-right">
                    {!! Form::weSubmit('Cerca') !!}
                    {!! Form::weReset('Svuota') !!}
                </div>
            </div>
            <input type="hidden" name="order[by]" value="{{ request('order')['by'] }}">
            <input type="hidden" name="order[sort]" value="{{ request('order')['sort'] }}">
        {!! Form::close() !!}
      </section>
    </div>
    <div class="box-body">
      <div class="table-responsive">
        <table class="table table-striped">
          <tr>
            {!! order_th('tipologia', 'Tipologia') !!}
            {!! order_th('marca', 'Marca') !!}
            {!! order_th('modello', 'Modello') !!}
            {!! order_th('serial_number', 'Serial Number') !!}
            {!! order_th('imei', 'Imei') !!}
            {!! order_th('user_id', 'Assegnatario') !!}
            <th class="text-center">Azioni</th>
          </tr>
          <tbody>
          @if(!empty($beni))
            @foreach ($beni as $bene)
            <tr>
              <td>{{ $tipologie[$bene->tipologia] }}</td>
              <td>{{ $bene->marca }}</td>
              <td>{{ $bene->modello }}</td>
              <td>{{ $bene->serial_number }}</td>
              <td>{{ $bene->imei }}</td>
              <td>{{ $bene->assegnatario()->first()->full_name }}</td>
              <td class="text-center">
                <button class="btn btn-md btn-flat btn-info bene-dettaglio" data-id="{{ $bene->id }}" type="button"><i class="fa fa-eye"></i></button>
                <button class="btn btn-md btn-flat btn-warning bene-edit" data-id="{{ $bene->id }}" type="button"><i class="fa fa-pencil"></i></button>
                @if(Auth::user()->hasAccess('amministrazione.benistrumentali.destroy'))
                  <button class="btn btn-md btn-flat btn-danger" type="button" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{  route('admin.amministrazione.benistrumentali.destroy', $bene)  }}"><i class="fa fa-trash"></i></button>
                @endif
                <a href="{{ route('admin.amministrazione.benistrumentali.foglio', ['bene' => $bene]) }}"><button class="btn btn-md btn-flat bg-secondary" type="button"><i class="fa fa-file"></i></button></a>
              </td>
            </tr>
            @endforeach
          @endif
          </tbody>
        </table>
      </div>
    </div>
    @if(!empty($beni))
      <div class="text-right pagination-container" style="margin-right:12px;">
        {{ $beni->links() }}
      </div>
    @endif
  </div>
</div>

<div class="modal fade" id="modal-bene-edit" tabindex="-1" role="dialog" aria-hidden="true">
  {!! Form::open(['route' => ['admin.amministrazione.benistrumentali.store'], 'method' => 'post']) !!}
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" id="bene-modal-header"></h3>
          </div>
          <div class="modal-body">
              <div class="row">
                  <div class="col-md-6 hidden">
                    {!! Form::weText('id', '', $errors, '', ['id' => 'id']) !!}
                  </div>
                  <div class="col-md-4">
                    {!! Form::weSelectSearch('tipologia', 'Tipologia *', $errors, $tipologie, '', ['id' => 'tipologia']) !!}
                  </div>
                  <div class="col-md-4">
                      {!! Form::weText('marca', 'Marca *', $errors, '', ['id' => 'marca']) !!}
                  </div>
                  <div class="col-md-4">
                    {!! Form::weText('modello', 'Modello *', $errors, '', ['id' => 'modello']) !!}
                  </div>
                  <div class="col-md-4">
                      {!! Form::weSelectSearch('user_id', 'Assegnatario *', $errors, $utenti, '', ['id' => 'user_id'])!!}
                  </div>
                  <div class="col-md-4">
                    {!! Form::weDate('data_assegnazione', 'Data Consegna *', $errors, date('d/m/Y')) !!}
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-md-6">
                  {!! Form::weText('serial_number', 'Serial Number *', $errors, '', ['id' => 'serial_number']) !!}
                </div>
                <div class="col-md-6">
                  {!! Form::weText('imei', 'Imei', $errors, '', ['id' => 'imei']) !!}
                </div>
                <div class="col-md-4">
                  {!! Form::weText('processore', 'Processore', $errors, '', ['id' => 'processore']) !!}
                </div>
                <div class="col-md-4">
                  {!! Form::weText('hdd', 'HDD', $errors, '', ['id' => 'hdd']) !!}
                </div>
                <div class="col-md-4">
                  {!! Form::weText('memoria', 'Memoria', $errors, '', ['id' => 'memoria']) !!}
                </div>
              </div>
              <div class="row">
                  <div class="col-md-12">
                      {!! Form::weTextarea('note', 'Note', $errors, '', ['id' => 'note']) !!}
                  </div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
              <button type="submit" id="bene-modal-button" class="btn btn-flat"></button>
          </div>
      </div>
  </div>
  {!! Form::close() !!}
</div>

<div class="modal fade" id="modal-bene-dettaglio" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" id="bene-modal-header-dettaglio"></h3>
          </div>
          <div class="modal-body">
              <div class="row">
                  <div class="col-md-12"> 
                    <p><strong>Data Consegna: </strong><span class="modal-bene-dettaglio-dataConsegna"></span> </p>
                    <hr>
                    <h4>Caratteristiche</h4>
                    <p><strong>Imei: </strong><span class="modal-bene-dettaglio-imei"></span> </p>
                    <p><strong>Processore: </strong><span class="modal-bene-dettaglio-processore"></span> </p>
                    <p><strong>HDD: </strong><span class="modal-bene-dettaglio-hdd"></span> </p>
                    <p><strong>Memoria: </strong><span class="modal-bene-dettaglio-memoria"></span> </p>
                    <hr>
                    <div><p class="modal-bene-dettaglio"></p></div>
                  </div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
          </div>
      </div>
  </div>
</div>

@endsection

@push('js-stack')
    @if($errors->has('tipologia') || $errors->has('user_id') || $errors->has('serial_number') || $errors->has('marca') || $errors->has('modello'))
        <script>
            $(function() {
              $('#bene-modal-header').html('<span class="label label-danger" style="margin-right:5px;">Attenzione!</span> Bene IT');
                $('#bene-modal-button').removeClass('btn-success');
                $('#bene-modal-button').addClass('btn-warning');
                $('#bene-modal-button').html('<i class="fa fa-floppy-o"></i> Salva Correzioni');
                $('#modal-bene-edit').modal('show');
            });
        </script>
    @endif
    <script>
      $( document ).ready(function() {
          var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

          $(".bene-nuovo").click(function(){
            var modal = $('#modal-bene-edit');

            $('#id').val('');
            $('#marca').val('');
            $('#modello').val('');
            $('#tipologia').val(0).trigger('change');
            $('#user_id').val(0).trigger('change');
            $('#imei').val('');
            $('#serial_number').val('');
            $('#note').val('');
            $('#processore').val('');
            $('#hdd').val('');
            $('#memoria').val('');

            $('#bene-modal-header').html('<span class="label label-success" style="margin-right:5px;">Nuovo</span> Bene IT');
            $('#bene-modal-button').removeClass('btn-warning');
            $('#bene-modal-button').addClass('btn-success');
            $('#bene-modal-button').html('<i class="fa fa-floppy-o"></i> Crea');
            modal.modal('show');
          });

          $(".bene-edit").click(function(){
            var id = $(this).data('id');
            var modal = $('#modal-bene-edit');

            $.ajax({
                url: "{{ route('admin.amministrazione.benistrumentali.informations') }}",
                type: 'GET',
                data: {_token: CSRF_TOKEN, type: 'Modifica', id: id},
                dataType: 'JSON' 
                }).done(function(data) {
                    $('#bene-modal-header').html('<span class="label label-warning" style="margin-right:5px;">Modifica</span> ' + data.bene.marca + ' - ' + data.bene.modello);
                    $('#bene-modal-button').removeClass('btn-success');
                    $('#bene-modal-button').addClass('btn-warning');
                    $('#bene-modal-button').html('<i class="fa fa-floppy-o"></i> Salva');

                    $('#id').val('').val(data.bene.id);
                    $('#marca').val('').val(data.bene.marca);
                    $('#modello').val('').val(data.bene.modello);
                    $('#tipologia').val(0).trigger('change').val(data.bene.tipologia).trigger('change');
                    $('#user_id').val(0).trigger('change').val(data.bene.user_id).trigger('change');
                    $('#imei').val('').val(data.bene.imei);
                    $('#serial_number').val('').val(data.bene.serial_number);
                    $('#note').val('').val(data.bene.note);
                    $('#processore').val('').val(data.bene.processore);
                    $('#hdd').val('').val(data.bene.hdd);
                    $('#memoria').val('').val(data.bene.memoria);

                    modal.modal('show');
                }); 
          });

          $(".bene-dettaglio").click(function(){
            var id = $(this).data('id');
            var modal = $('#modal-bene-dettaglio');

            $.ajax({
                url: "{{ route('admin.amministrazione.benistrumentali.informations') }}",
                type: 'GET',
                data: {_token: CSRF_TOKEN, type: 'Dettaglio', id: id},
                dataType: 'JSON' 
                }).done(function(data) {
                    $('.modal-bene-dettaglio-imei').html(data.bene.imei);
                    $('.modal-bene-dettaglio-processore').html(data.bene.processore);
                    $('.modal-bene-dettaglio-hdd').html(data.bene.hdd);
                    $('.modal-bene-dettaglio-memoria').html(data.bene.memoria);
                    $('.modal-bene-dettaglio-dataConsegna').html(data.bene.data_assegnazione);
                    $('.modal-bene-dettaglio').html(data.bene.note);
                    $('#bene-modal-header-dettaglio').html('<span class="label label-info" style="margin-right:5px;">Dettaglio</span> ' + data.bene.marca + ' - ' + data.bene.modello);
                    modal.modal('show');
                }); 
          });

      });
  </script>
@endpush
