@extends('layouts.master')

@section('content-header')
    <h1>Ambienti</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">Ambienti</li>
    </ol>
@stop

@section('content')
<div class="box-body">
  <div class="box box-primary box-shadow">
    <div class="box-header with-border">
      <h3 class="box-title">Lista</h3>
    </div>
    <div class="box-body">
      <div class="table-responsive">
        <table class="table table-striped">
          <tr>
            <th>Cliente</th>
            <th>Admin</th>
            <th>Password Admin</th>
            <th>Adm</th>
            <th>Password Adm</th>
            <th>N_db</th>
            <th>Ambiente</th>
          </tr>
          <tbody>
          @foreach ($clienti as $cliente)
          <tr>
            
            <td>{{ $cliente->ragione_sociale }}</td>
            <td>{{ $cliente->ambiente->admin }}</td>
            <td>{{ $cliente->ambiente->password_admin }}</td>
            <td>{{ $cliente->ambiente->adm }}</td>
            <td>{{ $cliente->ambiente->password_adm }}</td>
            <td>{{ $cliente->ambiente->n_db }}</td>
            <td><a href="{{ $cliente->ambiente->ambiente }}" target="_blank">{{ $cliente->ambiente->ambiente }}</a></td>
          </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
    {{-- $ambienti->links() --}}
  </div>
</div>
@endsection
