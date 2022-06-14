@php
  use Modules\User\Entities\Sentinel\User;

  $users = ['-- Seleziona --'] + User::select(DB::raw("CONCAT(first_name, ' ', last_name) AS name"), 'id')
                ->pluck('name', 'id')
                ->toArray();
@endphp

<hr>

<h3>Reports</h3>

{!! Form::weSelectSearch('statistiche::reports_responsabile', 'Responsabile', $errors, $users, setting('statistiche::reports_responsabile')) !!}

