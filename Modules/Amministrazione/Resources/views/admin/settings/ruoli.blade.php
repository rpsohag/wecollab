@php
  use Modules\User\Entities\Sentinel\User;

  $ruoli = config('amministrazione.ruoli');
  $users = ['-- Seleziona --'] + User::select(DB::raw("CONCAT(first_name, ' ', last_name) AS name"), 'id')
                ->pluck('name', 'id')
                ->toArray();
@endphp

<hr>

<h3>Ruoli</h3>
<div class="row">
  @foreach ($ruoli as $key => $ruolo)
    <div class="col-md-3">
      {!! Form::weSelectSearch('admin::'.$key, $ruolo, $errors, $users, setting('admin::'.$key)) !!}
    </div>
  @endforeach
</div>
