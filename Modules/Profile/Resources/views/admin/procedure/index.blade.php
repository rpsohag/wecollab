@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('profile::procedure.title.procedure') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('profile::procedure.title.procedure') }}</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="btn-group pull-right" style="margin: 0 15px 15px 0;">
                    <a href="{{ route('admin.profile.procedura.create') }}" class="btn btn-primary btn-flat" style="padding: 4px 10px;">
                        <i class="fa fa-pencil"></i> {{ trans('profile::procedure.button.create procedura') }}
                    </a>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header">
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="data-table table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Nome Procedura</th>
                                <th>{{ trans('core::core.table.created at') }}</th>
                              <!--   <th data-sortable="false">{{ trans('core::core.table.actions') }}</th>-->
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($procedure)): ?>
                            <?php foreach ($procedure as $procedura): ?>
                            <tr>
                                <td>
                                    <a href="{{ route('admin.profile.procedura.edit', [$procedura->id]) }}">
                                        {{ $procedura->titolo }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.profile.procedura.edit', [$procedura->id]) }}">
                                        {{ get_date_hour_ita($procedura->created_at) }}
                                    </a>
                                </td>
                            <!--     <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.profile.procedura.edit', [$procedura->id]) }}" class="btn btn-default btn-flat"><i class="fa fa-pencil"></i></a>
                                        <button class="btn btn-danger btn-flat" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.profile.procedura.destroy', [$procedura->id]) }}"><i class="fa fa-trash"></i></button>
                                    </div>
                             </td>-->
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>Nome Procedura</th>
                                <th>{{ trans('core::core.table.created at') }}</th>
                             <!--    <th>{{ trans('core::core.table.actions') }}</th>-->
                            </tr>
                            </tfoot>
                        </table>
                        <!-- /.box-body -->
                        <!-- Pagination -->
                        <div class="text-right pagination-container">
                          {{ $procedure->links() }}
                        </div>
                    </div>
                </div>
                <!-- /.box -->
            </div>
        </div>
    </div>
    @include('core::partials.delete-modal')
@stop

@section('footer')
    <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp;
@stop
@section('shortcuts')
    <dl class="dl-horizontal">
        <dt><code>c</code></dt>
        <dd>{{ trans('profile::procedure.title.create procedura') }}</dd>
    </dl>
@stop

@push('js-stack')
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'c', route: "<?= route('admin.profile.procedura.create') ?>" }
                ]
            });
        });
    </script>
    <?php $locale = locale(); ?>
    <script type="text/javascript">
        $(function () {
            $('.data-table').dataTable({
                "paginate": false,
                "lengthChange": true,
                "filter": false,
                "sort": true,
                "info": false,
                "autoWidth": true,
                "order": [[ 0, "asc" ]],
                "language": {
                    "url": '<?php echo Module::asset("core:js/vendor/datatables/{$locale}.json") ?>'
                }
            });
        });
    </script>
@endpush
