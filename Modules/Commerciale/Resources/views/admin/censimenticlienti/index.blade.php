@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('commerciale::censimenticlienti.title.censimenticlienti') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('commerciale::censimenticlienti.title.censimenticlienti') }}</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <section class="bg-gray filters">
                        {!! Form::open(['route' => ['admin.commerciale.censimentocliente.index'], 'method' => 'get']) !!}
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="row">
                                      <div class="col-md-3">
                                          {!! Form::weSelectSearch('cliente','Cliente' , $errors , $clienti) !!}
                                      </div>
                                      <div class="col-md-3">
                                        {!! Form::weSelectSearch('commerciale','Commerciale' , $errors , $commerciali) !!}
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
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="data-table table table-bordered table-hover">
                            <thead>
                            <tr>
                              {!! order_th('cliente', 'Cliente') !!}
                              {!! order_th('commerciale_id', 'Commerciale') !!}
                              {!! order_th('created_user', 'Creato da') !!}
                              {!! order_th('created_at', trans('core::core.table.created at')) !!}
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($censimenticlienti))
                                @foreach($censimenticlienti as $censimentocliente)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.commerciale.censimentocliente.read', [$censimentocliente->id]) }}">
                                                {{ $censimentocliente->cliente()->first()->ragione_sociale }}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.commerciale.censimentocliente.read', [$censimentocliente->id]) }}">
                                                {{ optional(optional($censimentocliente->cliente()->first())->commerciale)->full_name }}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.commerciale.censimentocliente.read', [$censimentocliente->id]) }}">
                                                {{ $censimentocliente->created_user->first_name }} {{ $censimentocliente->created_user->last_name }}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.commerciale.censimentocliente.read', [$censimentocliente->id]) }}">
                                                {{ $censimentocliente->created_at }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        <div class="text-right pagination-container">
                          {{ $censimenticlienti->links() }}
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
        <dd>{{ trans('commerciale::censimenticlienti.title.create censimentocliente') }}</dd>
    </dl>
@stop

@push('js-stack')
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'c', route: "<?= route('admin.commerciale.censimentocliente.create') ?>" }
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
                "info": false,
                "autoWidth": true,
                "order": [[ 2, "desc" ]],
                "language": {
                    "url": '<?php echo Module::asset("core:js/vendor/datatables/{$locale}.json") ?>'
                }
            });
        });
    </script>
@endpush
