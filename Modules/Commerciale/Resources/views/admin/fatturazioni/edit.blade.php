@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('commerciale::fatturazioni.title.edit fatturazione') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ route('admin.commerciale.fatturazione.index') }}">{{ trans('commerciale::fatturazioni.title.fatturazioni') }}</a></li>
        <li class="active">{{ trans('commerciale::fatturazioni.title.edit fatturazione') }}</li>
    </ol>
@stop

@section('content')
    {!! Form::open(['route' => ['admin.commerciale.fatturazione.update', $fatturazione->id], 'method' => 'put']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                @include('partials.form-tab-headers')
                <div class="tab-content">
                    <?php $i = 0; ?>
                    @foreach (LaravelLocalization::getSupportedLocales() as $locale => $language)
                        <?php $i++; ?>
                        <div class="tab-pane {{ locale() == $locale ? 'active' : '' }}" id="tab_{{ $i }}">
                            @include('commerciale::admin.fatturazioni.partials.fields', ['lang' => $locale])
                        </div>
                    @endforeach

                    <div class="box-footer">
                        <button type="submit" class="btn btn-success btn-flat">  <i class="fa fa-floppy-o"></i> {{ trans('core::core.button.save') }}</button>
                        <a class="btn btn-info pull-left btn-flat" href="{{ route('admin.commerciale.fatturazione.read', [$fatturazione->id])}} "><i class="fa fa-arrow-left"></i> vai a visualizzazione </a>
                        <button  type="button"  class="btn btn-danger pull-right btn-flat" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.commerciale.fatturazione.destroy', [$fatturazione->id]) }}"><i class="fa fa-trash"></i> {{ trans('core::core.button.delete') }}</button>
                        <a class="btn btn-warning pull-right btn-flat" href="{{ route('admin.commerciale.fatturazione.index')}}"><i class="fa fa-arrow-left"></i> Indietro</a>
                    </div>
                </div>
            </div> {{-- end nav-tabs-custom --}}
        </div>
    </div>
    {!! Form::close() !!}
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
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'b', route: "<?= route('admin.commerciale.fatturazione.index') ?>" }
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
