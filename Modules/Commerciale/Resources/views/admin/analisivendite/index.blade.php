@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('commerciale::analisivendite.title.analisivendite') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('commerciale::analisivendite.title.analisivendite') }}</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <section class="bg-gray filters">
                        {!! Form::open(['route' => ['admin.commerciale.analisivendita.index'], 'method' => 'get']) !!}
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="col-md-3">
                                            {!! Form::weText('oggetto', 'Oggetto' , $errors) !!}
                                        </div>     
                                        <div class="col-md-3">
                                            {!! Form::weSelectSearch('cliente', 'Cliente' , $errors , $clienti) !!}
                                        </div>
                                        <div class="col-md-3">
                                            {!! Form::weSelectSearch('commerciale', 'Commerciale' , $errors , $commerciali) !!}
                                        </div>
                                        <div class="col-md-3">
                                            {!! Form::weDateRange('range', 'Intervallo', $errors) !!}
                                        </div>
                                        <div class="col-md-3">
                                            {!! Form::weSelect('with_offerta', 'Offerta' , $errors , [0 => 'Tutte', 1 => 'Con Offerta', 2 => 'Senza Offerta']) !!}
                                        </div>
                                        <div class="col-md-3">
                                            {!! Form::weDate('data_creazione', 'Dal' , $errors) !!}
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
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Legenda colori:</h4>
                            <div class="col-md-1" style="height:15px; background-color:green;">  </div>
                            <div class="col-md-3">Con Offerta</div>
                            <div class="col-md-1" style="height:15px; background-color:gray;">  </div>
                            <div class="col-md-3">Senza Offerta</div>
                            <div class="col-md-1" style="height:15px; background-color:red;">  </div>
                            <div class="col-md-3">Offerta Rifiutata</div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="data-table table table-bordered table-hover">
                            <thead>
                            <tr>
                              {!! order_th('titolo', 'Oggetto') !!}
                              {!! order_th('censimento', 'Cliente') !!}
                              {!! order_th('offerta', 'Offerta') !!}
                              {!! order_th('commerciale', 'Commerciale') !!}
                              {!! order_th('created_at', 'Data Creazione') !!}
                            </tr>
                            </thead>
                            <tbody>
                                @if(isset($analisivendite))
                                    @foreach($analisivendite as $analisivendita)
                                        <tr style="{{ !empty($analisivendita->offerta_id) ? ($analisivendita->offerta->stato == '2' ? 'background-color:red;' : 'background-color:green;') : 'background-color:gray;' }}">
                                            <td>
                                                <a style="color:white;" href="{{ route('admin.commerciale.analisivendita.read', [$analisivendita->id]) }}">
                                                    {{ $analisivendita->titolo }}
                                                </a>
                                            </td>
                                            <td>
                                                <a style="color:white;" href="{{ route('admin.commerciale.analisivendita.read', [$analisivendita->id]) }}">
                                                {{ $analisivendita->censimento_cliente()->first()->cliente()->first()->ragione_sociale }}
                                                </a>
                                            </td>
                                            <td>
                                                <a style="color:white;" href="{{ route('admin.commerciale.analisivendita.read', [$analisivendita->id]) }}">
                                                {{ optional($analisivendita->offerta)->oggetto }}
                                                </a>
                                            </td>
                                            <td>
                                                <a style="color:white;" href="{{ route('admin.commerciale.analisivendita.read', [$analisivendita->id]) }}">
                                                    {{ $analisivendita->commerciale->full_name }}
                                                </a>
                                            </td>
                                            <td>
                                                <a style="color:white;" href="{{ route('admin.commerciale.analisivendita.read', [$analisivendita->id]) }}">
                                                    {{ get_date_ita($analisivendita->created_at) }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach 
                                @endif
                            </tbody>
                        </table>
                        <div class="text-right pagination-container">
                          {{ $analisivendite->links() }}
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
        <dd>{{ trans('commerciale::analisivendite.title.create analisivendita') }}</dd>
    </dl>
@stop

@push('js-stack')
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'c', route: "<?= route('admin.commerciale.analisivendita.create') ?>" }
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
                "order": [[ 3, "desc" ]],
                "language": {
                    "url": '<?php echo Module::asset("core:js/vendor/datatables/{$locale}.json") ?>'
                }
            });
        });

        $('input[name="range"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: 'D/M/Y'
        }
        }).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('D/M/Y') + ' - ' + picker.endDate.format('D/M/Y'));
        });
    </script>
@endpush
