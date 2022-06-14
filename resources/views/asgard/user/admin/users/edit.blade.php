@php
//dd($aree);
$selected_groups = [];
$selected_aree = [];
$selected_partner = [];
$selected_approvatori_fpm = [];
$selected_approvatori_rimborsi = [];
$selected_visualizzatori = [];

$utenti_fpm = $utenti;
$utenti_rimborsi = $utenti;
$utenti_visualizzatori = $utenti;

if(!empty($user))
{
    foreach ($user->gruppi as $key => $group)
    {
        $selected_groups[] = $group->id;
    }
    foreach ($user->aree as $key => $area)
    {
        $selected_aree[] = $area->id;
    }

    if(!empty($user->profile->partner))
    {
        foreach (json_decode($user->profile->partner) as $part)
        {
            $selected_partner[] = $part;
        }
    }

	if(!empty($user->profile->approvatori_fpm))
    {
        foreach (json_decode($user->profile->approvatori_fpm) as $approv)
        {
			$utenti_fpm = resort_array_by_key($utenti_fpm,$approv);
			$selected_approvatori_fpm[] = $approv;
        }
    }

	if(!empty($user->profile->approvatori_rimborsi))
    {
        foreach (json_decode($user->profile->approvatori_rimborsi) as $approv)
        {
			$utenti_rimborsi = resort_array_by_key($utenti_rimborsi,$approv);
            $selected_approvatori_rimborsi[] = $approv;
        }
    }

	if(!empty($user->profile->visualizzatori))
    {
        foreach (json_decode($user->profile->visualizzatori) as $approv)
        {
			$utenti_visualizzatori = resort_array_by_key($utenti_visualizzatori,$approv);
            $selected_visualizzatori[] = $approv;
        }
    }
}
$partner = json_decode(setting('clienti::partner'));
$partner = (empty($partner)) ? [] : array_combine($partner, $partner);
@endphp

@extends('layouts.master')

@section('content-header')
<h1>
    {{ trans('user::users.title.edit-user') }} <small>{{ $user->present()->fullname() }}</small>
</h1>
<ol class="breadcrumb">
    <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
    <li class=""><a href="{{ route('admin.user.user.index') }}">{{ trans('user::users.breadcrumb.users') }}</a></li>
    <li class="active">{{ trans('user::users.breadcrumb.edit-user') }}</li>
</ol>
@stop

@section('content')
{!! Form::open(['route' => ['admin.profile.profile.update', $user->id], 'method' => 'put']) !!}
<input type="hidden" name="_id" value="{{ $user->id }}">
<div class="row">
    <div class="col-md-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1-1" data-toggle="tab">{{ trans('user::users.tabs.data') }}</a></li>
                <li class=""><a href="#tab_2-2" data-toggle="tab">{{ trans('user::users.tabs.roles') }}</a></li>
                <li class=""><a href="#tab_3-3" data-toggle="tab">{{ trans('user::users.tabs.permissions') }}</a></li>
                <li class=""><a href="#password_tab" data-toggle="tab">{{ trans('user::users.tabs.new password') }}</a></li>
                {{-- <li class=""><a href="#aree_di_intervento_tab" data-toggle="tab">{{ "Aree di intervento" }}</a></li> --}}
                <li class=""><a href="#gruppi_tab" data-toggle="tab">Attività</a></li>
                @if(config('ldap.active'))
                  <li class=""><a href="#gruppildap_tab" data-toggle="tab">Gruppi LDAP</a></li>
                @endif
				<li class=""><a href="#approvatori_visualizzatori_tab" data-toggle="tab">Approvatori / Visualizzatori</a></li>
				<li class=""><a href="#gestione_economica_tab" data-toggle="tab">Gestione Economica</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1-1">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                                    {!! Form::label('first_name', trans('user::users.form.first-name') . '*') !!}
                                    {!! Form::text('first_name', old('first_name', $user->first_name), ['class' => 'form-control', 'placeholder' => trans('user::users.form.first-name')]) !!}
                                    {!! $errors->first('first_name', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                                    {!! Form::label('last_name', trans('user::users.form.last-name') . '*') !!}
                                    {!! Form::text('last_name', old('last_name', $user->last_name), ['class' => 'form-control', 'placeholder' => trans('user::users.form.last-name')]) !!}
                                    {!! $errors->first('last_name', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    {!! Form::label('email', trans('user::users.form.email') . '*') !!}
                                    {!! Form::email('email', old('email', $user->email), ['class' => 'form-control', 'placeholder' => trans('user::users.form.email')]) !!}
                                    {!! $errors->first('email', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                {!! Form::weSelectSearch('responsabile_id', 'Responsabile', $errors, $utenti, get_if_exist($user, 'responsabile_id')) !!}
                            </div>
                            <div class="col-md-2">
                                {!! Form::weInt('ore_lavorative_settimanali', 'Ore Lavorative Settimanali', $errors, get_if_exist($user, 'ore_lavorative_settimanali')) !!}
                            </div>
                            <div class="col-md-2">
                              @if(empty($user_ldap))
                                <div class="checkbox{{ $errors->has('activated') ? ' has-error' : '' }}">
                                    <input type="hidden" value="{{ $user->id === $currentUser->id ? '1' : '0' }}" name="activated"/>
                                    <?php $oldValue = (bool) $user->isActivated() ? 'checked' : ''; ?>
                                    <label for="activated">
                                        <input id="activated"
                                               name="activated"
                                               type="checkbox"
                                               class="flat-blue"
                                               {{ $user->id === $currentUser->id ? 'disabled' : '' }}
                                               {{ old('activated', $oldValue) }}
                                               value="1" />
                                        {{ trans('user::users.form.is activated') }}
                                        {!! $errors->first('activated', '<span class="help-block">:message</span>') !!}
                                    </label>
                                </div>
                              @else
                                {!! Form::weCheckbox('activated', trans('user::users.form.is activated'), $errors, ($user_ldap->getUserAccountControl() != 514 ? 1 : 0)) !!}
                              @endif
                            </div>
                            <div class="col-md-2">
                                {!! Form::weCheckbox('timesheets_report', 'Report settimanale timesheets', $errors, ($user->timesheets_report == 1 ? 'checked' : '')    ) !!}
                            </div>
                            <div class="col-md-2">
                                {!! Form::weCheckbox('switch_is_active', 'Switch aziendale', $errors, ($user->switch_is_active == 1 ? 'checked' : '')    ) !!}
                            </div>
							<div class="col-sm-2">
								{!! Form::weCheckbox('profile[avvisi_task]', 'Avvisi task', $errors, get_if_exist($user->profile, 'avvisi_task')) !!}
							</div>
							<div class="col-sm-2">
								{!! Form::weCheckbox('profile[rendicontabile]', 'Rendicontabile', $errors, get_if_exist($user->profile, 'rendicontabile')) !!}
							</div>
                        </div>
                        @include('user::admin.partials.profile-fields')
                    </div>
                </div>
                <div class="tab-pane" id="tab_2-2">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ trans('user::users.tabs.roles') }}</label>
                                <select multiple="" class="form-control" name="roles[]">
                                    <?php foreach ($roles as $role): ?>
                                        <option value="{{ $role->id }}" <?php echo $user->hasRoleId($role->id) ? 'selected' : '' ?>>{{ $role->name }}</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tab_3-3">
                    @include('user::admin.partials.permissions', ['model' => $user])
                </div>
                <div class="tab-pane" id="password_tab">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>{{ trans('user::users.new password setup') }}</h4>
                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    {!! Form::label('password', trans('user::users.form.new password')) . ' <i class="fa fa-info-circle text-info" data-toggle="tooltip" data-title="La password deve essere composta da minimo 8 caratteri, maiuscole, minuscole, numeri, e caratteri speciali.. Non Accetta ( Spazio , | )"> </i>' !!}
                                    {!! Form::input('password', 'password', '', ['class' => 'form-control']) !!}
                                    {!! $errors->first('password', '<span class="help-block">:message</span>') !!}
                                </div>
                                <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                    {!! Form::label('password_confirmation', trans('user::users.form.new password confirmation')) !!}
                                    {!! Form::input('password', 'password_confirmation', '', ['class' => 'form-control']) !!}
                                    {!! $errors->first('password_confirmation', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4>{{ trans('user::users.tabs.or send reset password mail') }}</h4>
                                <a href="{{ route("admin.profile.profile.sendResetPassword", $user->id) }}" class="btn btn-flat bg-maroon">
                                    {{ trans('user::users.send reset password email') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div class="tab-pane" id="aree_di_intervento_tab">
                    <div class="row">
                            <div class="col-md-8">
                                {{ Form::weTags('aree' ,'Aree' , $errors, $aree , $selected_aree) }}
                            </div>
                            
                    </div>
                </div> --}}
                <div class="tab-pane" id="gruppi_tab">
                    <div class="row">
                            <div class="col-md-8">
                                {{ Form::weTags('gruppi' ,'Gruppi' , $errors, $gruppi , $selected_groups) }}
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
				<div class="tab-pane" id="approvatori_visualizzatori_tab">
					<div class="row">
						<div class="col-md-4">
							{{ Form::weTags('approvatori_fpm', 'Approvatori Ferie / Permesso / Malattia', $errors, $utenti_fpm ,$selected_approvatori_fpm) }}
						</div>
						<div class="col-md-4">
							{{ Form::weTags('approvatori_rimborsi', 'Approvatori Rimborsi', $errors, $utenti_rimborsi ,$selected_approvatori_rimborsi) }}
						</div>
						<div class="col-md-4">
							{{ Form::weTags('visualizzatori', 'Visualizzatori', $errors, $utenti_visualizzatori , $selected_visualizzatori) }}
						</div>
					</div>
				</div>
				<div class="tab-pane" id="gestione_economica_tab">
					<div class="row">
						<div class="col-md-2">
							{!! Form::weCurrency('profile[ral]', 'RAL', $errors,  get_if_exist($user->profile, 'ral')) !!}
						</div>
						<div class="col-sm-2">
							{!! Form::weCurrency('profile[indennita_pernottamento]', 'Indennità con pernottamento', $errors, get_if_exist($user->profile, 'indennita_pernottamento')) !!}
						</div>
						<div class="col-sm-2">
							{!! Form::weCurrency('profile[indennita_giornaliera]', 'Indennità giornaliera', $errors, get_if_exist($user->profile, 'indennita_giornaliera')) !!}
						</div>
						<div class="col-md-2">
							{!! Form::weCurrency('costo_interno', 'Costo Interno', $errors, get_if_exist($user, 'costo_interno')) !!}
						</div>
						<div class="col-md-2">
							{!! Form::weCurrency('importo_di_vendita', 'Importo Di Vendita', $errors, get_if_exist($user, 'importo_di_vendita')) !!}
						</div>
					</div>
				</div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-flat" name="button" value="index">
                        <i class="fa fa-angle-left"></i>
                        {{ trans('core::core.button.update and back') }}
                    </button>
                    <button type="submit" class="btn btn-success btn-flat">  <i class="fa fa-floppy-o"></i> {{ trans('core::core.button.save') }}</button>
                    <button type="reset" class="btn btn-default btn-flat" name="button">{{ trans('core::core.button.reset') }}</button>
                    
                    <button  type="button"  class="btn btn-danger pull-right btn-flat" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.user.user.destroy', [$user->id])  }}"><i class="fa fa-trash"></i> {{ trans('core::core.button.delete') }}</button>

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

    $('[data-toggle="tooltip"]').tooltip();

		$(document).keypressAction({
			actions: [
				{ key: 'b', route: "<?= route('admin.user.role.index') ?>" }
			]
		});

		$('input[type="checkbox"].flat-blue, input[type="radio"].flat-blue').iCheck({
			checkboxClass: 'icheckbox_flat-blue',
			radioClass: 'iradio_flat-blue'
		});

});

</script>
@endpush
