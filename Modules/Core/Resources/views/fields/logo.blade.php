<div class="form-group">
    {!! Form::weFilemanager("core::logo", 'Logo', ['field_id' => 'core-logo', 'type' =>'image'], $errors, (!empty(get_if_exist($dbSettings, $settingName)) ? setting('core::logo') : '')) !!}
</div>
