@php
$selected_groups = [];
$selected_partner = [];
if(!empty($user))
{
    foreach ($user->gruppi as $key => $group)
    {
        $selected_groups[] = $group->id;
    }
}
$partner = json_decode(setting('clienti::partner'));
$partner = (empty($partner)) ? [] : array_combine($partner, $partner);
@endphp

@extends('layouts.master')

@section('content-header')
<h1>
    {{ trans('user::users.title.new-user') }}
</h1>
<ol class="breadcrumb">
    <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
    <li class=""><a href="{{ route('admin.user.user.index') }}">{{ trans('user::users.breadcrumb.users') }}</a></li>
    <li class="active">{{ trans('user::users.breadcrumb.new') }}</li>
</ol>
@stop

@section('footer')
    <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp;
@stop
@section('shortcuts')
    <dl class="dl-horizontal">
        <dt><code>b</code></dt>
        <dd>{{ trans('user::users.navigation.back to index') }}</dd>
    </dl>
@stop
@section('content')
{!! Form::open(['route' => 'admin.profile.profile.store', 'method' => 'post']) !!}
<div class="row">
    <div class="col-md-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1-1" data-toggle="tab">{{ trans('user::users.tabs.data') }}</a></li>
                <li class=""><a href="#tab_2-2" data-toggle="tab">{{ trans('user::users.tabs.roles') }}</a></li>
                <li class=""><a href="#tab_3-3" data-toggle="tab">{{ trans('user::users.tabs.permissions') }}</a></li>
                <li class=""><a href="#gruppi_tab" data-toggle="tab">Attività</a></li>
                @if(config('ldap.active'))
                  <li class=""><a href="#gruppildap_tab" data-toggle="tab">Gruppi LDAP</a></li>
                @endif
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1-1">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                                    {!! Form::label('first_name', trans('user::users.form.first-name') . '*') !!}
                                    {!! Form::text('first_name', old('first_name'), ['class' => 'form-control', 'placeholder' => trans('user::users.form.first-name')]) !!}
                                    {!! $errors->first('first_name', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                                    {!! Form::label('last_name', trans('user::users.form.last-name') . '*') !!}
                                    {!! Form::text('last_name', old('last_name'), ['class' => 'form-control', 'placeholder' => trans('user::users.form.last-name')]) !!}
                                    {!! $errors->first('last_name', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    {!! Form::label('email', trans('user::users.form.email') . '*') !!}
                                    {!! Form::email('email', old('email'), ['class' => 'form-control', 'placeholder' => trans('user::users.form.email')]) !!}
                                    {!! $errors->first('email', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                {!! Form::weSelectSearch('responsabile_id', 'Responsabile', $errors, $utenti) !!}
                            </div>
                            <div class="col-md-3">
                                {!! Form::weInt('ore_lavorative_settimanali', 'Ore Lavorative Settimanali', $errors, 40) !!}
                            </div>
                            <div class="col-md-3">
                                {!! Form::weCurrency('costo_interno', 'Costo Interno', $errors) !!}
                            </div>
                            <div class="col-md-3">
                                {!! Form::weCurrency('importo_di_vendita', 'Importo Di Vendita', $errors) !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    {!! Form::label('password', trans('user::users.form.password')) . ' <i class="fa fa-info-circle text-info" data-toggle="tooltip" data-title="La password deve essere composta da minimo 8 caratteri, maiuscole, minuscole, numeri, e caratteri speciali. Non Accetta ( Spazio, | )"> </i>' !!}
                                    {!! Form::password('password', ['class' => 'form-control']) !!}
                                    {!! $errors->first('password', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                    {!! Form::label('password_confirmation', trans('user::users.form.password-confirmation')) !!}
                                    {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
                                    {!! $errors->first('password_confirmation', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                        @include('user::admin.partials.profile-fields')
                    </div>
                </div>
                <div class="tab-pane" id="tab_2-2">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('user::users.tabs.roles') }}</label>
                                    <select multiple="" class="form-control" name="roles[]">
                                        <?php foreach ($roles as $role): ?>
                                            <option value="{{ $role->id }}" {{ (($role->default) ? 'selected' : '') }}>{{ $role->name }}</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tab_3-3">
                    @include('user::admin.partials.permissions-create')
                </div>
                <div class="tab-pane" id="gruppi_tab">
                    <div class="row">
                            <div class="col-md-8">
                                {{ Form::weTags('gruppi' ,'Attività' , $errors, $gruppi , $selected_groups) }}
                            </div>
                    </div>
                </div>
                @if(config('ldap.active'))
                  <div class="tab-pane" id="gruppildap_tab">
                      <div class="row">
                              <div class="col-md-8">
                                  {{ Form::weTags('gruppi_ldap', 'Gruppi LDAP', $errors, gruppi_ldap(), $selected_groups_ldap) }}
                              </div>
                      </div>
                  </div>
                @endif
                <div class="box-footer">
                    <button type="submit" class="btn btn-success btn-flat">  <i class="fa fa-floppy-o"></i> {{ trans('user::button.create') }}</button>
                    <a class="btn btn-warning pull-right btn-flat" href="{{ route('admin.user.user.index')}}"><i class="fa fa-arrow-left"></i> Indietro</a>
                </div>
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
        <dt><code>b</code></dt>
        <dd>{{ trans('user::users.navigation.back to index') }}</dd>
    </dl>
@stop

@push('js-stack')
<script>
$( document ).ready(function() {
    $(document).keypressAction({
        actions: [
            { key: 'b', route: "<?= route('admin.user.user.index') ?>" }
        ]
    });
    $('input[type="checkbox"].flat-blue, input[type="radio"].flat-blue').iCheck({
        checkboxClass: 'icheckbox_flat-blue',
        radioClass: 'iradio_flat-blue'
    });
});
</script>
@endpush
