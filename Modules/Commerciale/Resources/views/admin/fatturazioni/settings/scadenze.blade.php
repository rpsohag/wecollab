@php
  use Modules\User\Entities\Sentinel\User;

  $email = User::select(DB::raw("CONCAT(first_name,' ',last_name) AS name"), 'email')
              ->pluck('name', 'email')
              ->toArray();

  $email_selected = json_decode(setting('commerciale::fatturazione::scadenze_notifica'));
  $email_selected = empty($email_selected) ? [] : $email_selected;
@endphp

{!! Form::weTags('commerciale::fatturazione::scadenze_notifica', '<h3>Fatturazione Scadenza <small>- email di notifica</small></h3>', $errors, $email, $email_selected) !!}
