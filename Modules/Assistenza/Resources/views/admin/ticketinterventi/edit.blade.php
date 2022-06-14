@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('assistenza::ticketinterventi.title.edit ticketintervento') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ route('admin.assistenza.ticketintervento.index') }}">{{ trans('assistenza::ticketinterventi.title.ticketinterventi') }}</a></li>
        <li class="active">{{ trans('assistenza::ticketinterventi.title.edit ticketintervento') }}</li>
    </ol>
@stop

@section('content')
    {!! Form::open(['route' => ['admin.assistenza.ticketintervento.update', $ticketintervento->id], 'method' => 'put']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                @include('partials.form-tab-headers')
                <div class="tab-content">
                    <?php $i = 0; ?>
                    @foreach (LaravelLocalization::getSupportedLocales() as $locale => $language)
                        <?php $i++; ?>
                        <div class="tab-pane {{ locale() == $locale ? 'active' : '' }}" id="tab_{{ $i }}">
                            @include('assistenza::admin.ticketinterventi.partials.fields', ['lang' => $locale])
                        </div>
                    @endforeach

                    <div class="box-footer">
                         <button type="submit" class="btn btn-success btn-flat"><i class="fa fa-floppy-o"></i> {{ trans('core::core.button.save') }}</button>
                         <a class="btn btn-info pull-left btn-flat" href="{{ route('admin.assistenza.ticketintervento.read', [$ticketintervento->id])}} "><i class="fa fa-eye"></i> Vedi </a>
                         <button type="button" class="btn btn-default btn-flat" data-toggle="modal" data-target="#storeTimesheet"><i class="fa fa-pencil"></i> Crea Timesheet</button>
                       	<button  type="button"  class="btn btn-danger pull-right btn-flat" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.assistenza.ticketintervento.destroy', [$ticketintervento->id]) }}"><i class="fa fa-trash"></i> Elimina</button>
                        <a class="btn btn-warning pull-right btn-flat" href="{{ route('admin.assistenza.ticketintervento.index')}}"><i class="fa fa-arrow-left"></i> Indietro</a>
                    </div>
                </div>
            </div> {{-- end nav-tabs-custom --}}
        </div>
    </div>
    {!! Form::close() !!}

    <div class="modal fade" id="storeTimesheet" tabindex="-1" role="dialog" aria-hidden="true">
        {!! Form::open(['route' => ['admin.assistenza.ticketintervento.store.timesheet'], 'method' => 'post']) !!}
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Crea Timesheet</h3>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="ticket_id" value="{{ $ticketintervento->id }}"/>
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::weDate('data', 'Data', $errors, date('d/m/Y')) !!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::weSelectSearch('tipologia', 'Tipologia *', $errors, $tipologie)!!}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::weTime('ora_inizio', 'Ora di inizio', $errors) !!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::weTime('ora_fine', 'Ora di fine', $errors) !!}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::weText('nota', 'Nota', $errors, optional($ticketintervento)->note) !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-success btn-flat"><i class="fa fa-floppy-o"></i> {{ trans('core::core.button.save') }}</button>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>

 @stop

@section('footer')
    <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp;
@stop
@section('shortcuts')
    <dl class="dl-horizontal">
        <dt><code>b</code></dt>
        <dd>{{ trans('core::core.back to index') }}</dd>
    </dl>
@stop

@push('js-stack')
    @if($errors->has('ora_inizio') || $errors->has('ora_fine'))
        <script>
            $(function() {
                $('#storeTimesheet').modal('show');
            });
        </script>
    @endif
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'b', route: "<?= route('admin.assistenza.ticketintervento.index') ?>" }
                ]
            });
        });
    </script>
    <script>
        $( document ).ready(function() {
            $('input[type="checkbox"].flat-blue, input[type="radio"].flat-blue').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                radioClass: 'iradio_flat-blue'
            });
        });
    </script>
@endpush
