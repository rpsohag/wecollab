<?php

use Illuminate\Support\HtmlString;
use Illuminate\Support\ViewErrorBag;

// Form's We-COM
Form::macro('weText', function ($name, $title, ViewErrorBag $errors, $object = null, array $options = []) {
    $options = array_merge(['class' => 'form-control'], $options);

    $string = "<div class='form-group " . ($errors->has(get_name_error($name)) ? ' has-error' : '') . "'>";
    if(!empty($title))
        $string .= Form::label($name, $title);

    if (is_object($object)) {
        $currentData = isset($object->{$name}) ? $object->{$name} : '';
    } else {
        $currentData = $object;
    }

    $string .= Form::text($name, old($name, $currentData), $options);
    $string .= $errors->first(get_name_error($name), '<span class="help-block">:message</span>');
    $string .= '</div>'; 

    return new HtmlString($string);
});

Form::macro('weEmail', function ($name, $title, ViewErrorBag $errors, $object = null, array $options = []) {
    $options = array_merge(['class' => 'form-control'], $options);

    $string = "<div class='form-group " . ($errors->has(get_name_error($name)) ? ' has-error' : '') . "'>";
    if(!empty($title))
        $string .= Form::label($name, $title);

    if (is_object($object)) {
        $currentData = isset($object->{$name}) ? $object->{$name} : '';
    } else {
        $currentData = $object;
    }

    $string .= Form::email($name, old($name, $currentData), $options);
    $string .= $errors->first(get_name_error($name), '<span class="help-block">:message</span>');
    $string .= '</div>';

    return new HtmlString($string);
});

Form::macro('weDateRange', function ($name, $title, ViewErrorBag $errors, $object = null, array $options = []) {
    $options = array_merge(['class' => 'form-control', 'autocomplete' => false], $options);

    $string = "<div class='form-group " . ($errors->has(get_name_error($name)) ? ' has-error' : '') . "'>";
    if(!empty($title))
        $string .= Form::label($name, $title);

    if (is_object($object)) {
        $currentData = isset($object->{$name}) ? $object->{$name} : '';
    } else {
        $currentData = $object;
    }

    $string .= Form::text($name, old($name, $currentData), $options);
    $string .= $errors->first(get_name_error($name), '<span class="help-block">:message</span>');
    $string .= '</div>'; 

    return new HtmlString($string);
});

Form::macro('weDate', function ($name, $title, ViewErrorBag $errors, $object = null, array $options = []) {
    $options = array_merge(['class' => 'form-control datepicker'], $options);

    $string = "<div class='form-group " . ($errors->has(get_name_error($name)) ? ' has-error' : '') . "'>";

    if(!empty($title))
        $string .= Form::label($name, $title);

    $string .= '<div class="input-group date">
                    <div class="input-group-addon" onclick="$(\'#'.$name.'\').focus()">
                        <i class="fa fa-calendar"></i>
                    </div>';

    if (is_object($object)) {
        $currentData = isset($object->{$name}) ? $object->{$name} : '';
    } else {
        $currentData = $object;
    }

    $string .= Form::text($name, old($name, $currentData), $options);

    $string .= $errors->first(get_name_error($name), '<span class="help-block">:message</span>');

    $string .= '</div>
            </div>';

    return new HtmlString($string);
});

Form::macro('weDatetime', function ($name, $title, ViewErrorBag $errors, $object = null, array $options = []) {
    $options = array_merge(['class' => 'form-control datetimepicker'], $options);

    $string = "<div class='form-group " . ($errors->has(get_name_error($name)) ? ' has-error' : '') . "'>";

    $string .= Form::label($name, $title);
    $string .= '<div class="input-group date">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>';

    if (is_object($object)) {
        $currentData = isset($object->{$name}) ? $object->{$name} : '';
    } else {
        $currentData = $object;
    }

    $string .= Form::text($name, old($name, $currentData), $options);

    $string .= $errors->first(get_name_error($name), '<span class="help-block">:message</span>');

    $string .= '</div>
            </div>';

    return new HtmlString($string);
});

Form::macro('weTime', function ($name, $title, ViewErrorBag $errors, $object = null, array $options = []) {
    $options = array_merge(['class' => 'form-control timepicker'], $options);

    $string = "<div class='form-group " . ($errors->has(get_name_error($name)) ? ' has-error' : '') . "'>";

    if(!empty($title))
        $string .= Form::label($name, $title);

    $string .= '<div class="input-group date">
                    <!-- <div class="input-group-addon" onclick="$(\'#'.$name.'\').focus()">
                        <i class="fa fa-clock-o"></i>
                    </div> -->';

    if (is_object($object)) {
        $currentData = isset($object->{$name}) ? $object->{$name} : '';
    } else {
        $currentData = $object;
    }

    $string .= Form::text($name, old($name, $currentData), $options);

    $string .= $errors->first(get_name_error($name), '<span class="help-block">:message</span>');

    $string .= '</div>
            </div>';

    return new HtmlString($string);
});

Form::macro('weTimeRichieste', function ($name, $title, ViewErrorBag $errors, $object = null, array $options = []) {
    $options = array_merge(['class' => 'form-control timepicker_richieste'], $options);

    $string = "<div class='form-group " . ($errors->has(get_name_error($name)) ? ' has-error' : '') . "'>";

    if(!empty($title))
        $string .= Form::label($name, $title);

    $string .= '<div class="input-group date">
                    <!-- <div class="input-group-addon" onclick="$(\'#'.$name.'\').focus()">
                        <i class="fa fa-clock-o"></i>
                    </div> -->';

    if (is_object($object)) {
        $currentData = isset($object->{$name}) ? $object->{$name} : '';
    } else {
        $currentData = $object;
    }

    $string .= Form::text($name, old($name, $currentData), $options);

    $string .= $errors->first(get_name_error($name), '<span class="help-block">:message</span>');

    $string .= '</div>
            </div>';

    return new HtmlString($string);
});

Form::macro('weSelect', function ($name, $title, ViewErrorBag $errors, array $choice, $object = null, array $options = []) {
    if (array_key_exists('multiple', $options)) {
        $nameForm = $name . '[]';
    } else {
        $nameForm = $name;
    }

    $string = "<div class='form-group dropdown" . ($errors->has(get_name_error($name)) ? ' has-error' : '') . "'>";

    if(!empty($title))
        $string .= '<label for="'.$nameForm.'">'.$title.'</label><br>';

    if (is_object($object)) {
        $currentData = isset($object->$name) ? $object->$name : '';
    } else {
        $currentData = $object;
    }

    /* Bootstrap default class */
    $array_option = ['class' => 'form-control'];

    if (array_key_exists('class', $options)) {
        $array_option = ['class' => $array_option['class'] . ' ' . $options['class']];
        unset($options['class']);
    }

    $options = array_merge($array_option, $options);

    $string .= Form::select($nameForm, $choice, old($nameForm, $currentData), $options);
    $string .= $errors->first(get_name_error($name), '<span class="help-block">:message</span>');
    $string .= '</div>';

    return new HtmlString($string);
});

Form::macro('weSelectSearch', function ($name, $title, ViewErrorBag $errors, array $choice, $object = null, array $options = []) {
    if (array_key_exists('multiple', $options)) {
        $nameForm = $name . '[]';
    } else {
        $nameForm = $name;
    }

    $string = "<div class='form-group" . ($errors->has(get_name_error($name)) ? ' has-error' : '') . "'>";

    if(!empty($title))
        $string .= '<label for="'.$nameForm.'">'.$title.'</label><br>';

    if (is_object($object)) {
        $currentData = isset($object->$name) ? $object->$name : '';
    } else {
        $currentData = $object;
    }

    /* Bootstrap default class */
    $array_option = ['class' => 'form-control select2', 'style' => 'width:100%;'];

    if (array_key_exists('class', $options)) {
        $array_option = ['class' => $array_option['class'] . ' ' . $options['class']];
        unset($options['class']);
    }

    $options = array_merge($array_option, $options);

    $string .= Form::select($nameForm, $choice, old($nameForm, $currentData), $options);
    $string .= $errors->first(get_name_error($name), '<span class="help-block">:message</span>');
    $string .= '</div>';

    return new HtmlString($string);
});

Form::macro('weSelectSearchFix', function ($name, $title, ViewErrorBag $errors, array $choice, $object = null, array $options = []) {
    if (array_key_exists('multiple', $options)) {
        $nameForm = $name . '[]';
    } else {
        $nameForm = $name;
    }

    $string = "<div style='width: 110px;' class='form-group" . ($errors->has(get_name_error($name)) ? ' has-error' : '') . "'>";

    if(!empty($title))
        $string .= "<label for='$nameForm'>$title</label><br>";

    if (is_object($object)) {
        $currentData = isset($object->$name) ? $object->$name : '';
    } else {
        $currentData = $object;
    }

    /* Bootstrap default class */
    $array_option = ['class' => 'form-control select2', 'style' => 'width:100%;'];

    if (array_key_exists('class', $options)) {
        $array_option = ['class' => $array_option['class'] . ' ' . $options['class']];
        unset($options['class']);
    }

    $options = array_merge($array_option, $options);

    $string .= Form::select($nameForm, $choice, old($nameForm, $currentData), $options);
    $string .= $errors->first(get_name_error($name), '<span class="help-block">:message</span>');
    $string .= '</div>';

    return new HtmlString($string);
});

Form::macro('weTags', function ($name, $title, ViewErrorBag $errors, array $choice, $object = null, array $options = ['multiple' => 'multiple']) {
    if (array_key_exists('multiple', $options)) {
        $nameForm = $name . '[]';
    } else {
        $nameForm = $name;
    }

    $string = "<div class='form-group" . ($errors->has(get_name_error($name)) ? ' has-error' : '') . "'>";
    $string .= "<label for='$nameForm'>$title</label>";

    if (is_object($object)) {
        $currentData = isset($object->$name) ? $object->$name : '';
    } else {
        $currentData = $object;
    }

    /* Bootstrap default class */
    $array_option = ['class' => 'tags'];

    if (array_key_exists('class', $options)) {
        $array_option = ['class' => $array_option['class'] . ' ' . $options['class']];
        unset($options['class']);
    }

    $options = array_merge($array_option, $options);

    $string .= Form::select($nameForm, $choice, old($nameForm, $currentData), $options);
    $string .= $errors->first(get_name_error($name), '<span class="help-block">:message</span>');
    $string .= '</div>';

    return new HtmlString($string);
});

Form::macro('weCurrency', function ($name, $title, ViewErrorBag $errors, $object = null, array $options = []) {
    $options = array_merge(['class' => 'form-control currency',  'data-currency' => '€'], $options);

    $string = "<div class='form-group " . ($errors->has(get_name_error($name)) ? ' has-error' : '') . "'>";
    if(!empty($title))
        $string .= Form::label($name, $title);

    if (is_object($object)) {
        $currentData = isset($object->{$name}) ? $object->{$name} : '';
    } else {
        $currentData = $object;
    }

    $string .= Form::text($name, old($name, $currentData), $options);
    $string .= $errors->first(get_name_error($name), '<span class="help-block">:message</span>');
    $string .= '</div>';

    return new HtmlString($string);
});

Form::macro('weInt', function ($name, $title, ViewErrorBag $errors, $object = null, array $options = []) {
    $options = array_merge(['class' => 'form-control' ], $options);

    $string = "<div class='form-group " . ($errors->has(get_name_error($name)) ? ' has-error' : '') . "'>";
    if(!empty($title))
        $string .= Form::label($name, $title);

    if (is_object($object)) {
        $currentData = isset($object->{$name}) ? $object->{$name} : '';
    } else {
        $currentData = $object;
    }

    $string .= Form::input('number', $name, old($name, $currentData), $options);
    $string .= $errors->first(get_name_error($name), '<span class="help-block">:message</span>');
    $string .= '</div>';

    return new HtmlString($string);
});

Form::macro('weCheckbox', function ($name, $title, ViewErrorBag $errors = null, $object = null, $options = '', $flat = 'flat-blue') {
    $string = "<div class='" . (!empty($flat) ? "checkbox" : "") . (isset($errors) && $errors->has(get_name_error($name)) ? ' has-error' : '') . "'>";
    if(!empty($flat))
    {
        $string .= "<input type='hidden' value='0' name='{$name}'/>";
        $string .= "<label for='$name'>";
    }

    $string .= "<input mixed name='$name' type='checkbox' class='$flat $options'";

    if (is_object($object)) {
        $currentData = isset($object->$name) && (bool)$object->$name ? 'checked' : '';
    } else {
        $currentData = $object;
    }

    $oldInput = old($name, $currentData) ? 'checked' : ''; 
    $string .= "value='1' {$oldInput}>";
    $string .= $title;
    if($errors !== null){
        $string .= $errors->first(get_name_error($name), '<span class="help-block">:message</span>');
    }

    if(!empty($flat))
        $string .= '</label>';

    $string .= '</div>';

    return new HtmlString($string);
});

Form::macro('weCheckboxFatture', function ($name, $title, ViewErrorBag $errors = null, $object = null, $options = '', $flat = 'flat-blue') {
    $string = "<div class='" . (!empty($flat) ? "checkbox" : "") . (isset($errors) && $errors->has(get_name_error($name)) ? ' has-error' : '') . "'>";
    if(!empty($flat))
    {
        $string .= "<input type='hidden' value='0' name='{$name}'/>";
        $string .= "<label for='$name'>";
    }

    $string .= "<input mixed name='$name' type='checkbox' class='$flat' $options";

    if (is_object($object)) {
        $currentData = isset($object->$name) && (bool)$object->$name ? 'checked' : '';
    } else {
        $currentData = $object;
    }

    $oldInput = old($name, $currentData) ? 'checked' : ''; 
    $string .= "value='1' {$oldInput}>";
    $string .= $title;
    if($errors !== null){
        $string .= $errors->first(get_name_error($name), '<span class="help-block">:message</span>');
    }

    if(!empty($flat))
        $string .= '</label>';

    $string .= '</div>';

    return new HtmlString($string);
});

Form::macro('weRadio', function ($name, $title, ViewErrorBag $errors, array $choise, $object = null, array $options = [])
{
    $string = "<div class='form-group dropdown" . ($errors->has(get_name_error($name)) ? ' has-error' : '') . "'>";
    $string .= "<label>$title</label><br>";

    if (is_object($object))
        $currentData = isset($object->$name) ? $object->$name : '';
    else
        $currentData = $object;

    foreach ($choise as $value => $label)
    {
        $checked = false;
        if($value == $currentData)
            $checked = true;

		if(array_key_exists('id', $options)  ){
			$options2 = $options;
			$options2['id'].= $value;

	        $string .= Form::radio($name, $value, $checked, $options2) . "<label for='{$options2['id']}'>&nbsp;&nbsp;$label&nbsp;&nbsp;&nbsp;&nbsp;</label>";

		}else{
	        $string .= Form::radio($name, $value, $checked, $options) . "&nbsp;&nbsp;$label&nbsp;&nbsp;&nbsp;&nbsp;";

		}
    }

    $string .= $errors->first(get_name_error($name), '<span class="help-block">:message</span>');
    $string .= '</div>';

    return new HtmlString($string);
});



Form::macro('weRadio2', function ($name, $title, ViewErrorBag $errors, array $choise, $object = null, array $options = [])
{
    $string = "<div class='form-group dropdown" . ($errors->has(get_name_error($name)) ? ' has-error' : '') . "'>";
    $string .= "<label>$title</label><br>";

    if (is_object($object))
        $currentData = isset($object->$name) ? $object->$name : '';
    else
        $currentData = $object;

    foreach ($choise as $value => $label)
    {
        $checked = false;
        if($value == $currentData)
            $checked = true;
        $string .= Form::radio($name, $value, $checked, $options) . "&nbsp;&nbsp;$label&nbsp;&nbsp;&nbsp;&nbsp;";
    }

    $string .= $errors->first(get_name_error($name), '<span class="help-block">:message</span>');
    $string .= '</div>';

    return new HtmlString($string);
});
Form::macro('weTextarea', function ($name, $title, ViewErrorBag $errors, $object = null, array $options = []) {
    $options = array_merge(['class' => 'form-control', 'rows' => 5], $options);

    $string = "<div class='form-group " . ($errors->has($name) ? ' has-error' : '') . "'>";
    $string .= Form::label($name, $title);

    if (is_object($object)) {
        $currentData = $object->{$name} ?: '';
    } else {
        $currentData = $object;
    }

    $string .= Form::textarea($name, old($name, $currentData), $options);
    $string .= $errors->first($name, '<span class="help-block">:message</span>');
    $string .= '</div>';

    return new HtmlString($string);
});

Form::macro('weTextareaEditor', function ($name, $title, ViewErrorBag $errors, $object = null, array $options = []) {
    $subfolder = (!empty($options['subfolder'])) ? $options['subfolder'] : null;
    filemanager_create_or_set_folder($subfolder);

    $options = array_merge(['class' => 'tinymce', 'rows' => 10, 'cols' => 10], $options);

    $string = "<div class='form-group " . ($errors->has($name) ? ' has-error' : '') . "'>";
    $string .= Form::label($name, $title);

    if (is_object($object)) {
        $currentData = $object->{$name} ?: '';
    } else {
        $currentData = $object;
    }

    $string .= Form::textarea($name, old($name, $currentData), $options);
    $string .= $errors->first($name, '<span class="help-block">:message</span>');
    $string .= '</div>';

    return new HtmlString($string);
});

Form::macro('weTextareaEditorCk', function ($name, $title, ViewErrorBag $errors, $object = null, array $options = []) {
    $options = array_merge(['class' => 'ckeditor', 'rows' => 10, 'cols' => 10], $options);

    $string = "<div class='form-group " . ($errors->has($name) ? ' has-error' : '') . "'>";
    $string .= Form::label($name, $title);

    if (is_object($object)) {
        $currentData = $object->{$name} ?: '';
    } else {
        $currentData = $object;
    }

    $string .= Form::textarea($name, old($name, $currentData), $options);
    $string .= $errors->first($name, '<span class="help-block">:message</span>');
    $string .= '</div>';

    return new HtmlString($string);
});

Form::macro('weFile', function ($name, $title, ViewErrorBag $errors, $object = null, array $options = [], $icon = null) {
    // $options = array_merge(['placeholder' => $title], $options);

    $string = "<div class='form-group " . ($errors->has(get_name_error($name)) ? ' has-error' : '') . "'>";
    if(!empty($icon))
        $string .= '<div class="col-md-10" style="overflow: hidden;">';
    $string .= Form::label($name, $title, ['class' => 'we-file-label']);
    $string .= '<div class="we-file">';

    if (is_object($object)) {
        $currentData = isset($object->{$name}) ? $object->{$name} : '';
    } else {
        $currentData = $object;
    }

    $string .= Form::input('file', $name, old($name, $currentData), $options);

    $string .= '<span class="we-file-input"></span>';

    $string .= '</div>';

    if(!empty($icon))
    {
        $string .= '</div>';
        $string .= '<div class="col-md-2">';
        $string .= '<br><a onclick="eliminaFile(\'' . $name . '\')"><i class="btn btn-danger fa fa-trash '. $icon .'"> </i></a>';
        $string .= '</div>';
    }
    $string .= '<input type="hidden" id="elimina-' . $name . '" name="elimina_' . $name . '" value="0">';
    $string .= $errors->first(get_name_error($name), '<span class="help-block">:message</span>');
    $string .= '</div>';

    return new HtmlString($string);
});


Form::macro('weFileDrop', function ($name, $title, ViewErrorBag $errors, $object = null, array $options = []) {
    $options = array_merge(['multiple'], $options);

    $string = "<div id='dropzone' class='dropzone " . ($errors->has(get_name_error($name)) ? ' has-error' : '') . "'>";

    $string .= '<div class="fallback">';
    // $string .= Form::label($name, $title);

    if (is_object($object)) {
        $currentData = isset($object->{$name}) ? $object->{$name} : '';
    } else {
        $currentData = $object;
    }

    $string .= Form::input('file', $name, old($name, $currentData), $options);

    $string .= $errors->first(get_name_error($name), '<span class="help-block">:message</span>');
    $string .= '</div>';

    return new HtmlString($string);
});

Form::macro('weColor', function ($name, $title, ViewErrorBag $errors, $object = null, array $options = []) {
    $options = array_merge(['class' => 'form-control'], $options);

    $string = "<div class='form-group " . ($errors->has(get_name_error($name)) ? ' has-error' : '') . "'>";
    $string .= Form::label($name, $title);

    if (is_object($object)) {
        $currentData = isset($object->{$name}) ? $object->{$name} : '';
    } else {
        $currentData = $object;
    }

    // $string .= $currentData;
    $string .= '<div class="input-group colorpicker">';
    $string .=      Form::text($name, old($name, $currentData), $options);
    $string .=      '<div class="input-group-addon">
                        <i></i>
                    </div>';
    $string .= '</div>';
    $string .= $errors->first(get_name_error($name), '<span class="help-block">:message</span>');
    $string .= '</div>';

    return new HtmlString($string);
});

Form::macro('weSlider', function ($name, $title, ViewErrorBag $errors, $object = 0, array $options = []) {
    $options_default = [
        'class' => 'slider form-control',
        'data-slider-min' => "0",
        'data-slider-max' => "100",
        'data-slider-step' => "5",
        'data-slider-value' => (empty($object)) ? 0 : $object,
        'data-slider-orientation' => "horizontal",
        'data-slider-tooltip' => "show",
        'data-slider-id' => "aqua"
    ];
    $options = array_merge($options_default, $options);

    $string = "<div class='form-group " . ($errors->has(get_name_error($name)) ? ' has-error' : '') . "'>";
    $string .= "<p>" . Form::label($name, $title) . "</p>";

    if (is_object($object)) {
        $currentData = isset($object->{$name}) ? $object->{$name} : '';
    } else {
        $currentData = $object;
    }

    $string .= Form::text($name, old($name, $currentData), $options);
    $string .= $errors->first(get_name_error($name), '<span class="help-block">:message</span>');
    $string .= '</div>';

    return new HtmlString($string);
});

Form::macro('weFilemanager', function ($name, $title, $fm, ViewErrorBag $errors, $object = null, array $options = []) {
    $subfolder = (!empty($fm['subfolder'])) ? $fm['subfolder'] : null;
    filemanager_create_or_set_folder($subfolder);

    $options = array_merge([
                    'class' => 'form-control',
                    'id' => (empty($fm['field_id'])) ? $name : $fm['field_id']
                ], $options);

    $string = "<div class='form-group " . ($errors->has(get_name_error($name)) ? ' has-error' : '') . "'>";
    if(!empty($title))
        $string .= Form::label($name, $title);

    if (is_object($object)) {
        $currentData = isset($object->{$name}) ? $object->{$name} : '';
    } else {
        $currentData = $object;
    }

    $fm_type = 0;
    if(!empty($fm['type']))
    {
        switch($fm['type'])
        {
            case 'image':
                $fm_type = 1;
                break;

            case 'file':
                $fm_type = 2;
                break;

            case 'video':
                $fm_type = 3;
                break;
        }
    }

    $string .= '<div class="input-group">';
    $string .= Form::text($name, old($name, $currentData), $options);

    $string .= '<span class="input-group-btn">';
    $string .= '<button type="button" class="btn btn-info btn-flat"
                    data-fancybox
                    data-type="iframe"
                    data-src="' . env('FILE_MANAGER_PATH_EXECUTE')
                        . '?field_id=' . ((empty($fm['field_id'])) ? $name : $fm['field_id'])
                        . '&type=' . $fm_type
                        . '&akey=' . env('FILE_MANAGER_APP_KEY')
                . '" href="javascript:;">
                	Carica File
                </button>';
    $string .= '</span>';
    $string .= '</div>';
    $string .= $errors->first(get_name_error($name), '<span class="help-block">:message</span>');
    $string .= '</div>';

    return new HtmlString($string);
});

Form::macro('weSubmit', function ($name, $options = 'class="btn btn-primary btn-flat"') {
    $string = '<button type="submit" ' . $options . '>' . $name . '</button>';

    return new HtmlString($string);
});

Form::macro('weReset', function ($name, $options = 'class="btn btn-default btn-flat btn-reset"') {
    $string = '<a href="' . url()->current() . '" ' . $options . '>' . $name . '</a>';

    return new HtmlString($string);
});

Form::macro('weList', function ($name, $title, ViewErrorBag $errors, array $choice, $object = null, array $options = []) {
    $options = array_merge(['class' => 'form-control', 'list' => $name.'s', 'autocomplete' => 'off'], $options);

    $string = "<div class='form-group " . ($errors->has(get_name_error($name)) ? ' has-error' : '') . "'>";

    if(!empty($title))
        $string .= Form::label($name, $title);

    if (is_object($object)) {
        $currentData = isset($object->{$name}) ? $object->{$name} : '';
    } else {
        $currentData = $object;
    }

    $string .= Form::text($name, old($name, $currentData), $options);

    $string .='<datalist id="'.$name.'s">';
    foreach($choice as $scelta)
    {
        $string .= '<option value="'.$scelta.'">';
    }
    $string .='</datalist>';

    $string .= $errors->first(get_name_error($name), '<span class="help-block">:message</span>');

    $string .= '</div>';


    return new HtmlString($string);
});
