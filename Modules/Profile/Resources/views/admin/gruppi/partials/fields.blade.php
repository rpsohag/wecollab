@php
  $selected_users = !empty($gruppo) ? $gruppo->users->pluck('id')->toArray() : [];
@endphp

<div class="box-body">
  <div class="row">
    <div class="col-md-4">
      {{ Form::weSelectSearch('area_id', 'Area *', $errors, $aree, get_if_exist($gruppo, 'area_id')) }}
    </div>
    <div class="col-md-8">
      {{ Form::weText('nome', 'Nome Gruppo *', $errors, get_if_exist($gruppo, 'nome')) }}
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      {{ Form::weTags('utenti' ,'Utenti' , $errors, $users, $selected_users) }}
    </div>
    @if(!empty($gruppo))
      <div class="col-md-4">
        {!! Form::weCheckbox('notifiche', 'Notifiche via email', $errors, ($gruppo->notifiche == 1 ? 'checked' : '')    ) !!}
      </div>
    @endif
  </div> 
</div>
