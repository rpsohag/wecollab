@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('commerciale::offerte.title.create offerta') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ route('admin.commerciale.offerta.index') }}">{{ trans('commerciale::offerte.title.offerte') }}</a></li>
        <li class="active">{{ trans('commerciale::offerte.title.create offerta') }}</li>
    </ol>
@stop

@section('content')
    {!! Form::open(['route' => ['admin.commerciale.offerta.store'], 'method' => 'post', 'files'=> true]) !!}
    <div class="row">
        <input type="hidden" name="ctrl_numero" value="{{ Modules\Commerciale\Entities\Offerta::get_numero_offerta_new() }}">

        <div class="col-md-12">
            <div class="nav-tabs-custom">
                @include('partials.form-tab-headers')
                <div class="tab-content">
                    <?php $i = 0; ?>
                    @foreach (LaravelLocalization::getSupportedLocales() as $locale => $language)
                        <?php $i++; ?>
                        <div class="tab-pane {{ locale() == $locale ? 'active' : '' }}" id="tab_{{ $i }}">
                            @include('commerciale::admin.offerte.partials.fields', ['lang' => $locale])
                        </div>
                    @endforeach

                    <div class="box-footer">
                        <a class="btn btn-warning pull-right btn-flat" href="{{ route('admin.commerciale.offerta.index')}}"><i class="fa fa-arrow-left"></i> Indietro</a>
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
                    { key: 'b', route: "<?= route('admin.commerciale.offerta.index') ?>" }
                ]
            });
            $('input[type="checkbox"].flat-blue, input[type="radio"].flat-blue').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                radioClass: 'iradio_flat-blue'
            });
        });
    </script>
@endpush
