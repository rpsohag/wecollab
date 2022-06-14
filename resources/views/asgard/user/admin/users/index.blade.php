@php
    $aziende = json_decode(setting('profile::aziende'));
    $aziende = array_combine($aziende, $aziende);
    $aziende = array_merge(['' => ''], $aziende);
@endphp

@extends('layouts.master')

@section('content-header')
<h1>
    {{ trans('user::users.title.users') }}
</h1>
<ol class="breadcrumb">
    <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
    <li class="active">{{ trans('user::users.breadcrumb.users') }}</li>
</ol>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="row">
            <div class="btn-group pull-right" style="margin: 0 15px 15px 0;">
                <a href="{{ route('admin.profile.profile.create') }}" class="btn btn-primary btn-flat" style="padding: 4px 10px;">
                    <i class="fa fa-pencil"></i> {{ trans('user::users.button.new-user') }}
                </a>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-header">

                <section class="bg-gray filters">
                    {!! Form::open(['route' => ['admin.user.user.index'], 'method' => 'get']) !!}
                        <div class="row">
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-sm-3">
                                        {!! Form::weText('name', 'Nome/Cognome', $errors) !!}
                                    </div>
                                    <div class="col-sm-3">
                                        {!! Form::weSelect('azienda', 'Azienda', $errors, $aziende) !!}
                                    </div>
                                    <div class="col-sm-3">
                                        {!! Form::weSelect('deleted', 'Eliminati', $errors, ['No', 'Si']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 text-right">
                                {!! Form::weSubmit('Cerca') !!}
                                {!! Form::weReset('Svuota') !!}
                            </div>
                        </div>
                        <input type="hidden" name="order[by]" value="{{ request('order')['by'] }}">
                        <input type="hidden" name="order[sort]" value="{{ request('order')['sort'] }}">
                    {!! Form::close() !!}
                </section>

            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table class="data-table table table-bordered table-hover">
                    <thead>
                        <tr>
                            {!! order_th('id', 'Id') !!}
                            {!! order_th('first_name', trans('user::users.table.first-name')) !!}
                            {!! order_th('last_name', trans('user::users.table.last-name')) !!}
                            {!! order_th('email', trans('user::users.table.email')) !!}
                            {!! order_th('azienda', 'Azienda') !!}
                            <th>Attivo</th>
                            {{-- {!! order_th('attivo', 'Attivo') !!} --}}
                            {!! order_th('created_at', trans('user::users.table.created-at')) !!}
                            {{-- <th data-sortable="false">{{ trans('user::users.table.actions') }}</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($users)):
                            foreach ($users as $user):
                                $profile = $user;
                                $user = $user->user()->withTrashed()->first();

                                if(config('ldap.active'))
                                {
                                  $user_ldap = Adldap::search()->users()
                                                    ->where('sAMAccountName', get_profile_user($user->id)->username)
                                                    ->first();
                                }
                    ?>
                            <tr>
                                <td>
                                    <a href="{{ route('admin.profile.profile.edit', [$user->id]) }}">
                                        {{ $user->id }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.profile.profile.edit', [$user->id]) }}">
                                        {{ $user->first_name }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.profile.profile.edit', [$user->id]) }}">
                                        {{ $user->last_name }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.profile.profile.edit', [$user->id]) }}">
                                        {{ $user->email }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.profile.profile.edit', [$user->id]) }}">
                                        {{ $profile->azienda }}
                                    </a>
                                </td>
                                <td class="text-center">
                                  @if(config('ldap.active') && !empty($user_ldap))
                                    <a href="{{ route('admin.profile.profile.edit', [$user->id]) }}">
                                        {!! ($user_ldap->getUserAccountControl() != 514) ? '<i class="text-success fa fa-check-circle-o fa-2x"><span class="hidden">1</span></i>' : '<i class="text-danger fa fa-times-circle-o fa-2x"><span class="hidden">0</span></i>' !!}
                                    </a>
                                  @else
                                    <a href="{{ route('admin.profile.profile.edit', [$user->id]) }}">
                                        {!! ($user->isActivated()) ? '<i class="text-success fa fa-check-circle-o fa-2x"><span class="hidden">1</span></i>' : '<i class="text-danger fa fa-times-circle-o fa-2x"><span class="hidden">0</span></i>' !!}
                                    </a>
                                  @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.profile.profile.edit', [$user->id]) }}">
                                        {{ get_date_ita($user->created_at) }}
                                    </a>
                                </td>
                                {{-- <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.profile.profile.edit', [$user->id]) }}" class="btn btn-default btn-flat"><i class="fa fa-pencil"></i></a>
                                        @if($user->id != $currentUser->id && !$user->trashed())
                                            <button class="btn btn-danger btn-flat" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.user.user.destroy', [$user->id]) }}"><i class="fa fa-trash"></i></button>
                                        @endif

                                        @if($user->trashed())
                                            <a href="{{ route('admin.profile.profile.restore', [$user->id]) }}" class="btn btn-default btn-flat btn-warning"><i class="fa fa-unlock"></i></a>
                                        @endif
                                    </div>
                                </td>--}}
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>{{ trans('user::users.table.first-name') }}</th>
                            <th>{{ trans('user::users.table.last-name') }}</th>
                            <th>{{ trans('user::users.table.email') }}</th>
                            <th>Azienda</th>
                            <th>Attivo</th>
                            <th>{{ trans('user::users.table.created-at') }}</th>
                         {{--   <th>{{ trans('user::users.table.actions') }}</th>--}}
                        </tr>
                    </tfoot>
                </table>
            <!-- /.box-body -->
            <!-- Pagination -->
            <div class="text-right pagination-container">
              {{ $users->links() }}
            </div>
        </div>
        <!-- /.box -->
    </div>
<!-- /.col (MAIN) -->
</div>
</div>

@include('core::partials.delete-modal')
@stop

@push('js-stack')
<?php $locale = App::getLocale(); ?>
<script type="text/javascript">
    $( document ).ready(function() {
        $(document).keypressAction({
            actions: [
                { key: 'c', route: "<?= route('admin.profile.profile.create') ?>" }
            ]
        });
    });
    $(function () {
        $('.data-table').dataTable({
            "paginate": false,
            "lengthChange": true,
            "filter": false,
            "sort": false,
            "info": false,
            "autoWidth": true,
            "order": [[ 5, "desc" ]],
            "language": {
                "url": '<?php echo Module::asset("core:js/vendor/datatables/{$locale}.json") ?>'
            }
        });
    });
</script>
@endpush
