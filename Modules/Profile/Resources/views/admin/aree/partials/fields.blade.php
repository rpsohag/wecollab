@php
  $area = !empty($area) ? $area : '';
  $attivita_selected = !empty($area) ? $area->attivita->pluck('id')->toArray() : [];
@endphp

<div class="box-body">
  <div class="row">
    <div class="col-md-4">
      {{ Form::weSelect('procedura_id', 'Procedura *', $errors, $procedure, get_if_exist($area, 'procedura_id')) }}
    </div>
    <div class="col-md-8">
      {{ Form::weText('titolo', 'Titolo area *', $errors, get_if_exist($area, 'titolo')) }}
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      {{ Form::weTags('gruppi' ,'Attività' , $errors, $attivita, $attivita_selected) }}
    </div>
  </div>
</div>
