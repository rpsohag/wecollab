@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('profile::figureprofessionali.title.figureprofessionali') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('profile::figureprofessionali.title.figureprofessionali') }}</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="btn-group pull-right" style="margin: 0 15px 15px 0;">
                    <a href="{{ route('admin.profile.figuraprofessionale.create') }}" class="btn btn-primary btn-flat" style="padding: 4px 10px;">
                        <i class="fa fa-pencil"></i> {{ trans('profile::figureprofessionali.button.create figuraprofessionale') }}
                    </a>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header">
                    <section class="bg-gray filters">
                        {!! Form::open(['route' => ['admin.profile.figuraprofessionale.index'], 'method' => 'get']) !!}
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="row">
                                      <div class="col-md-6">
                                          {!! Form::weText('descrizione', 'Descrizione', $errors) !!}
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
                                {!! order_th('descrizione', 'Descrizione') !!}
                                {!! order_th('costo_interno', 'Costo Interno') !!}
                                {!! order_th('importo_vendita', 'Importo Di Vendita') !!}
                                <th data-sortable="false">{{ trans('core::core.table.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($figureprofessionali)): ?>
                            <?php foreach ($figureprofessionali as $figuraprofessionale): ?>
                            <tr>
                                <td>
                                    <a href="{{ route('admin.profile.figuraprofessionale.edit', [$figuraprofessionale->id]) }}">
                                        {{ $figuraprofessionale->descrizione }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.profile.figuraprofessionale.edit', [$figuraprofessionale->id]) }}">
                                        {{ $figuraprofessionale->costo_interno }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.profile.figuraprofessionale.edit', [$figuraprofessionale->id]) }}">
                                        {{ $figuraprofessionale->importo_vendita }}
                                    </a>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.profile.figuraprofessionale.edit', [$figuraprofessionale->id]) }}" class="btn btn-default btn-flat"><i class="fa fa-pencil"></i></a>                                    
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                        <div class="text-right pagination-container">
                            {{ $figureprofessionali->links() }}
                        </div>
                    </div>
                </div>
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
        <dd>{{ trans('profile::figureprofessionali.title.create figuraprofessionale') }}</dd>
    </dl>
@stop

@push('js-stack')
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'c', route: "<?= route('admin.profile.figuraprofessionale.create') ?>" }
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
                "sort": false,
                "info": true,
                "autoWidth": true,
                "order": [[ 0, "desc" ]],
                "language": {
                    "url": '<?php echo Module::asset("core:js/vendor/datatables/{$locale}.json") ?>'
                }
            });
        });
    </script>
@endpush
