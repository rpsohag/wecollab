@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('profile::gruppi.title.gruppi') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('profile::gruppi.title.gruppi') }}</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="btn-group pull-right" style="margin: 0 15px 15px 0;">
                    <a href="{{ route('admin.profile.gruppo.create') }}" class="btn btn-primary btn-flat" style="padding: 4px 10px;">
                        <i class="fa fa-pencil"></i> {{ trans('profile::gruppi.button.create gruppo') }}
                    </a>
                </div>
            </div>
            <div class="box box-primary">
              <div class="box-header">
                <section class="bg-gray filters">
                    {!! Form::open(['route' => ['admin.profile.gruppo.index'], 'method' => 'get']) !!}
                        <div class="row">
                            <div class="col-md-10">
                                <div class="row">
                                  <div class="col-md-3">
                                      {!! Form::weText('gruppo', 'Gruppo', $errors) !!}
                                  </div>
                                  <div class="col-md-3">
                                      {!! Form::weSelectSearch('area', 'Area di Intervento', $errors, $aree) !!}
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
                    <div class="table-responsive">
                        <table class="data-table table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Nome Gruppo</th>
                                <th>Utenti</th>
                                <!-- <th>{{ trans('core::core.table.created at') }}</th> -->
                               <!--  <th data-sortable="false">{{ trans('core::core.table.actions') }}</th>-->
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($gruppi)): ?>
                            <?php foreach ($gruppi as $gruppo): ?>
                            <tr>
                                <td>
                                    <a href="{{ route('admin.profile.gruppo.edit', [$gruppo->id]) }}">
                                        {{ $gruppo->nome }}
                                    </a>
                                </td>
                                <td>
                                    @php $users = !empty($gruppo->users) ? $gruppo->users : '';
                                         $names = [];
                                         foreach($users as $user){
                                             $names[] = $user->full_name;
                                         }
                                         $names = implode(', ', $names);
                                    @endphp
                                    {{ $names }}
                                </td>
                                <!-- <td>
                                    <a href="{{ route('admin.profile.gruppo.edit', [$gruppo->id]) }}">
                                        {{ get_date_hour_ita($gruppo->created_at) }}
                                    </a>
                                </td>
                              -->
                             <!--    <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.profile.gruppo.edit', [$gruppo->id]) }}" class="btn btn-default btn-flat"><i class="fa fa-pencil"></i></a>
                                        <button class="btn btn-danger btn-flat" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.profile.gruppo.destroy', [$gruppo->id]) }}"><i class="fa fa-trash"></i></button>
                                    </div>
                             </td>-->
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                        <!-- /.box-body -->
                        <div class="text-right pagination-container">
                          {{ $gruppi->links() }}
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
        <dd>{{ trans('profile::gruppi.title.create gruppo') }}</dd>
    </dl>
@stop

@push('js-stack')
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'c', route: "<?= route('admin.profile.gruppo.create') ?>" }
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
