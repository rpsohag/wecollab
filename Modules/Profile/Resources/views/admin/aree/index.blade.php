@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('profile::aree.title.aree') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('profile::aree.title.aree') }}</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="btn-group pull-right" style="margin: 0 15px 15px 0;">
                    <a href="{{ route('admin.profile.area.create') }}" class="btn btn-primary btn-flat" style="padding: 4px 10px;">
                        <i class="fa fa-pencil"></i> {{ trans('profile::aree.button.create area') }}
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
                                <th>Procedura</th>
                                <th>Nome Area</th>
                                <th>{{ trans('core::core.table.created at') }}</th>
                             <!--    <th data-sortable="false">{{ trans('core::core.table.actions') }}</th>-->
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($aree)): ?>
                            <?php foreach ($aree as $area): ?>
                            <tr>
                                <td>
                                    <a href="{{ route('admin.profile.area.edit', [$area->id]) }}">
                                        {{ $area->procedura->titolo }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.profile.area.edit', [$area->id]) }}">
                                        {{ $area->titolo }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.profile.area.edit', [$area->id]) }}">
                                        {{ get_date_hour_ita($area->created_at) }}
                                    </a>
                                </td>
                             <!--    <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.profile.area.edit', [$area->id]) }}" class="btn btn-default btn-flat"><i class="fa fa-pencil"></i></a>
                                        <button class="btn btn-danger btn-flat" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.profile.area.destroy', [$area->id]) }}"><i class="fa fa-trash"></i></button>
                                    </div>
                             </td>-->
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>Procedura</th>
                                <th>Nome Area</th>
                                <th>{{ trans('core::core.table.created at') }}</th>
                            <!--     <th>{{ trans('core::core.table.actions') }}</th>-->
                            </tr>
                            </tfoot>
                        </table>
                        <!-- /.box-body -->
                        <!-- Pagination -->
                        <div class="text-right pagination-container">
                          {{ $aree->links() }}
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
        <dd>{{ trans('profile::aree.title.create area') }}</dd>
    </dl>
@stop

@push('js-stack')
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'c', route: "<?= route('admin.profile.area.create') ?>" }
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
