@extends('layouts.master')

@section('content-header')
    <h1>Ambienti Di Conversione</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">Ambienti Di Conversione</li>
    </ol>
@stop

@section('content')
<div class="box-body">
  <div class="row">
    <div class="btn-group pull-right" style="padding: 4px 10px; margin-bottom:5px; margin-right:5px;">
      @if(auth_user()->hasAccess('assistenza.ambienticonversioni.create'))
        <button type="button" class="btn btn-primary btn-flat ambiente-nuovo"><i class="fa fa-pencil"></i> Crea Ambiente</button>
      @endif
    </div> 
  </div>
  <div class="box box-primary box-shadow">
    <div class="box-header with-border">
      <section class="bg-gray filters">
        {!! Form::open(['route' => ['admin.assistenza.ambienticonversioni.index'], 'method' => 'get']) !!}
            <div class="row">
                <div class="col-md-10">
                    <div class="row">
                      <div class="col-md-3">
                        {!! Form::weSelectSearch('cliente_id' , 'Cliente', $errors , $clienti) !!}
                      </div>
                      <div class="col-md-3">
                          {!! Form::weText('admin', 'User Admin', $errors) !!}
                      </div>
                      <div class="col-md-3">
                          {!! Form::weSelect('chiuso', 'Chiuso', $errors , [-1 =>'', 0 => 'NO', 1 => 'SI']) !!}
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
            <th>Nome</th>
            <th>Cliente</th>
            <th>Admin</th>
            <th>Password Admin</th>
            <th>Adm</th>
            <th>Password Adm</th>
            <th class="text-center">Chiuso</th>
            <th class="text-center">Azioni</th>
          </tr>
          <tbody>
          @foreach ($ambienti as $ambiente)
          <tr>
            <td>{{ optional($ambiente)->nome }}</td>
            <td>{{ optional($ambiente->cliente)->ragione_sociale }}</td>
            <td>{{ optional($ambiente)->user_admin }}</td>
            <td>{{ optional($ambiente)->password_admin }}</td>
            <td>{{ optional($ambiente)->user_adm }}</td>
            <td>{{ optional($ambiente)->password_adm }}</td>
            <td class="text-center">{!! $ambiente->chiuso == true ? '<i class="fa fa-check text-success" aria-hidden="true"></i>' : '<i class="fa fa-minus text-default" aria-hidden="true"></i>' !!}</td>
            <td class="text-center">
              <button class="btn btn-md btn-flat btn-info ambiente-dettaglio" data-id="{{ $ambiente->id }}" type="button"><i class="fa fa-eye"></i></button>
              <button class="btn btn-md btn-flat btn-warning ambiente-edit" data-id="{{ $ambiente->id }}" type="button"><i class="fa fa-pencil"></i></button>
              @if(Auth::user()->hasAccess('assistenza.ambienticonversioni.destroy'))
                <button class="btn btn-md btn-flat btn-danger" type="button" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{  route('admin.assistenza.ambienticonversioni.destroy', $ambiente)  }}"><i class="fa fa-trash"></i></button>
              @endif
            </td>
          </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <div class="text-right pagination-container" style="margin-right:12px;">
      {{ $ambienti->links() }}
    </div>
  </div>
</div>

<div class="modal fade" id="modal-ambiente-edit" tabindex="-1" role="dialog" aria-hidden="true">
  {!! Form::open(['route' => ['admin.assistenza.ambienticonversioni.store'], 'method' => 'post']) !!}
  <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" id="ambiente-modal-header"></h3>
          </div>
          <div class="modal-body">
              <div class="row">
                  <div class="col-md-6 hidden">
                    {!! Form::weText('id', '', $errors, '', ['id' => 'id']) !!}
                  </div>
                  <div class="col-md-6">
                      {!! Form::weText('nome', 'Nome *', $errors, '', ['id' => 'nome']) !!}
                  </div>
                  <div class="col-md-6">
                      {!! Form::weSelectSearch('cliente_id', 'Cliente ', $errors, $clienti, '', ['id' => 'cliente_id'])!!}
                  </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  {!! Form::weText('user_admin', 'User Admin', $errors, '', ['id' => 'user_admin']) !!}
                </div>
                <div class="col-md-6">
                  {!! Form::weText('password_admin', 'Password Admin', $errors, '', ['id' => 'password_admin']) !!}
                </div>
                <div class="col-md-6">
                  {!! Form::weText('user_adm', 'User Adm', $errors, '', ['id' => 'user_adm']) !!}
                </div>
                <div class="col-md-6">
                  {!! Form::weText('password_adm', 'Password Adm', $errors, '', ['id' => 'password_adm']) !!}
                </div>
              </div>
              <div class="row">
                  <div class="col-md-12">
                      {!! Form::weTextarea('dettaglio_conversioni', 'Dettaglio Conversioni', $errors, '', ['id' => 'dettaglio_conversioni']) !!}
                  </div>
                  <div class="col-md-12">
                    {{ Form::weCheckbox('chiuso', 'L\'ambiente di conversione è chiuso?', $errors, '', 'id="chiuso"') }}
                  </div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
              <button type="submit" id="ambiente-modal-button" class="btn btn-flat"></button>
          </div>
      </div>
  </div>
  {!! Form::close() !!}
</div>

<div class="modal fade" id="modal-ambiente-dettaglio" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" id="ambiente-modal-header-dettaglio"></h3>
          </div>
          <div class="modal-body">
              <div class="row">
                  <div class="col-md-12">
                    <div><p class="modal-ambiente-dettaglio"></p></div>
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
    @if($errors->has('nome'))
        <script>
            $(function() {
              $('#ambiente-modal-header').html('<span class="label label-danger" style="margin-right:5px;">Attenzione!</span> Ambiente Di Conversione');
                $('#ambiente-modal-button').removeClass('btn-success');
                $('#ambiente-modal-button').addClass('btn-warning');
                $('#ambiente-modal-button').html('<i class="fa fa-floppy-o"></i> Salva Correzioni');
                $('#modal-ambiente-edit').modal('show');
            });
        </script>
    @endif
    <script>
      $( document ).ready(function() {
          $('input[type="checkbox"].flat-blue, input[type="radio"].flat-blue').iCheck({
              checkboxClass: 'icheckbox_flat-blue',
              radioClass: 'iradio_flat-blue'
          });

          var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

          $(".ambiente-nuovo").click(function(){
            var modal = $('#modal-ambiente-edit');

            $('#id').val('');
            $('#nome').val('');
            $('#cliente_id').val(81).trigger('change');
            $('#user_admin').val('');
            $('#password_admin').val('');
            $('#user_adm').val('');
            $('#password_adm').val('');
            $('#dettaglio_conversioni').val('');

            $('#ambiente-modal-header').html('<span class="label label-success" style="margin-right:5px;">Nuovo</span> Ambiente Di Conversione');
            $('#ambiente-modal-button').removeClass('btn-warning');
            $('#ambiente-modal-button').addClass('btn-success');
            $('#ambiente-modal-button').html('<i class="fa fa-floppy-o"></i> Crea');
            modal.modal('show');
          });

          $(".ambiente-edit").click(function(){
            var id = $(this).data('id');
            var modal = $('#modal-ambiente-edit');

            $.ajax({
                url: "{{ route('admin.assistenza.ambienticonversioni.informations') }}",
                type: 'GET',
                data: {_token: CSRF_TOKEN, type: 'Modifica', id: id},
                dataType: 'JSON' 
                }).done(function(data) {
                    $('#ambiente-modal-header').html('<span class="label label-warning" style="margin-right:5px;">Modifica</span> Ambiente Di Conversione');
                    $('#ambiente-modal-button').removeClass('btn-success');
                    $('#ambiente-modal-button').addClass('btn-warning');
                    $('#ambiente-modal-button').html('<i class="fa fa-floppy-o"></i> Salva');

                    $('#id').val('');
                    $('#nome').val('');
                    $('#cliente_id').val(81).trigger('change');
                    $('#user_admin').val('');
                    $('#password_admin').val('');
                    $('#user_adm').val('');
                    $('#password_adm').val('');
                    $('#dettaglio_conversioni').val('');

                    $('#id').val(data.ambiente.id);
                    $('#nome').val(data.ambiente.nome);
                    $('#cliente_id').val(data.ambiente.cliente_id).trigger('change');
                    $('#user_admin').val(data.ambiente.user_admin);
                    $('#password_admin').val(data.ambiente.password_admin);
                    $('#user_adm').val(data.ambiente.user_adm);
                    $('#password_adm').val(data.ambiente.password_adm);
                    $('#dettaglio_conversioni').val(data.ambiente.dettaglio_conversioni);
                    if(data.ambiente.chiuso == 1){
                      $('#chiuso').iCheck('check');
                    } else {
                      $('#chiuso').iCheck('uncheck');
                    }
                    modal.modal('show');
                }); 
          });

          $(".ambiente-dettaglio").click(function(){
            var id = $(this).data('id');
            var modal = $('#modal-ambiente-dettaglio');

            $.ajax({
                url: "{{ route('admin.assistenza.ambienticonversioni.informations') }}",
                type: 'GET',
                data: {_token: CSRF_TOKEN, type: 'Dettaglio', id: id},
                dataType: 'JSON' 
                }).done(function(data) {
                    $('.modal-ambiente-dettaglio').html(data.ambiente.dettaglio_conversioni);
                    $('#ambiente-modal-header-dettaglio').html('<span class="label label-info" style="margin-right:5px;">Dettaglio</span> ' + data.ambiente.nome);
                    modal.modal('show');
                }); 
          });

      });
  </script>
@endpush
