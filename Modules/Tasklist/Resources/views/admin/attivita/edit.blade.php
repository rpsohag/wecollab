@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('tasklist::attivita.title.edit attivita') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ route('admin.tasklist.attivita.index') }}">{{ trans('tasklist::attivita.title.attivita') }}</a></li>
        <li class="active">{{ trans('tasklist::attivita.title.edit attivita') }}</li>
    </ol>
@stop

@section('content')
    {!! Form::open(['route' => ['admin.tasklist.attivita.update', $attivita->id], 'method' => 'put', 'files'=> true]) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                @include('partials.form-tab-headers')
                <div class="tab-content">
                    <?php $i = 0; ?>
                    @foreach (LaravelLocalization::getSupportedLocales() as $locale => $language)
                        <?php $i++; ?>
                        <div class="tab-pane {{ locale() == $locale ? 'active' : '' }}" id="tab_{{ $i }}">
                            @include('tasklist::admin.attivita.partials.fields', ['lang' => $locale])
                            {{-- @include('wecore::admin.partials.activities', ['activities' => $activities]) --}}
                        </div>
                    @endforeach
                    <div class="box-footer">
                        @if(!empty($timesheet_message))
                            <div class="alert alert-success" role="alert">
                                {{ $timesheet_message }}
                            </div>
                        @endif
                        @if($attivita->hasRequisiti() || Auth::id() == $attivita->richiedente->id || !empty($attivita->supervisori()) && $attivita->supervisori()->contains('id', Auth::id()))
                            <button type="submit" class="btn btn-success btn-flat"><i class="fa fa-floppy-o"></i> {{ trans('core::core.button.save') }}</button>
                        @endif
                        @if($attivita->hasRequisiti())
                            <button type="button" class="btn btn-default btn-flat" data-toggle="modal" data-target="#storeTimesheet"><i class="fa fa-pencil"></i> Crea Timesheet</button>
                            @if(!empty($attivita->supervisori()) && $attivita->supervisori()->count() > 0)
                                @if($attivita->supervisori()->contains('id', Auth::id()))
                                    <button type="button" class="btn btn-danger btn-flat" data-toggle="modal" data-target="#sollecitaAssegnatari"><i class="fa fa-bell"></i> Sollecita Assegnatari</button>
                                @endif
                            @endif
                        @endif
                        <a type="button" class="btn btn-info pull-left btn-flat" href="{{ route('admin.tasklist.attivita.read', [$attivita->id])}} "><i class="fa fa-eye"></i> Vedi </a>
                        <button type="button"  class="btn btn-danger pull-right btn-flat" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.tasklist.attivita.destroy', [$attivita->id]) }}"><i class="fa fa-trash"></i> Elimina</button>

                        <a class="btn btn-warning pull-right btn-flat" href="{{ route('admin.tasklist.attivita.index')}}"><i class="fa fa-arrow-left"></i> Lista</a>
                    </div>
                </div>
            </div> {{-- end nav-tabs-custom --}}
        </div>
    </div>
    {!! Form::close() !!}

    <div class="modal fade" id="storeTimesheet" tabindex="-1" role="dialog" aria-hidden="true">
        {!! Form::open(['route' => ['admin.tasklist.attivita.store.timesheet'], 'method' => 'post']) !!}
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Crea Timesheet</h3>
                </div>
                <div class="modal-body">
                    <input type="hidden" value="{{$attivita->id}}" name="attivita_id">
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
                            {!! Form::weText('nota', 'Nota', $errors) !!}
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
    <div class="modal fade" id="sollecitaAssegnatari" tabindex="-1" role="dialog" aria-hidden="true">
        {!! Form::open(['route' => ['admin.tasklist.attivita.sollecita'], 'method' => 'post']) !!}
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Sollecita Assegnatari</h3>
                </div>
                <div class="modal-body">
                    <input type="hidden" value="{{$attivita->id}}" name="attivita_id">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::weText('nota', 'Nota *', $errors) !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-success btn-flat"><i class="fa fa-bell"></i> Sollecita</button>
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
    @if(!empty($errors) && $errors->has('nota'))
        <script>
            $(function() {
                $('#sollecitaAssegnatari').modal('show');
            });
        </script>
    @endif
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'b', route: "<?= route('admin.tasklist.attivita.index') ?>" }
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
