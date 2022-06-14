@php
  $aree_selected = !empty($procedura) ? $procedura->aree->pluck('id')->toArray() : [];
@endphp

<div class="box-body">
    <div class="row">
            <div class="col-md-12">
                {{ Form::weText('titolo', 'Titolo procedura *', $errors, get_if_exist($procedura, 'titolo')) }}
            </div>
    </div>
    <div class="row">
            <div class="col-md-12">
                {{ Form::weTags('aree' ,'Aree' , $errors, $aree, $aree_selected) }}
            </div>
    </div>
</div>
