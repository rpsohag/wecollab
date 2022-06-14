@extends('layouts.master')

@section('content-header')
    <h1>
        Visualizza cliente
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ route('admin.amministrazione.clienti.index') }}">{{ trans('amministrazione::clienti.title.clienti') }}</a></li>
        <li class="active">{{ trans('amministrazione::clienti.title.edit clienti') }}</li>
    </ol>
@stop

@section('content')
    {!! Form::open(['route' => ['admin.amministrazione.clienti.update', $cliente->id], 'method' => 'put', 'files'=> true]) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                @include('partials.form-tab-headers')
                <div class="tab-content">
                    <?php $i = 0; ?>
                    @foreach (LaravelLocalization::getSupportedLocales() as $locale => $language)
                        <?php $i++; ?>
                        <div class="tab-pane {{ locale() == $locale ? 'active' : '' }}" id="tab_{{ $i }}">
                            @include('amministrazione::admin.clienti.partials.fields_read', ['lang' => $locale])
                            @include('amministrazione::admin.clienti.partials.indirizzi_list_read', ['lang' => $locale])
                            @include('amministrazione::admin.clienti.partials.referenti_list_read', ['lang' => $locale])
                            
                            @if($cliente->pa == 1 && !empty($cliente->ambiente()->first()) )
                                @include('amministrazione::admin.clienti.partials.ambienti_read', ['lang' => $locale])
                            @endif

                            @include('wecore::admin.partials.note', ['model' => $cliente])
                        </div>
                    @endforeach

                    <div class="box-footer">
                        <a class="btn btn-info btn-flat" href="{{ route('admin.amministrazione.clienti.edit',[$cliente->id])}}"><i class="fa fa-pencil"></i> Modifica</a>
                        @if(!$cliente->censimento()->first())
                            <a class="btn btn-warning pull-left btn-flat" href="{{ route('admin.amministrazione.clienti.creacensimento', ['cliente' => $cliente->id])}} "><i class="fa fa-sticky-note"></i> Crea Censimento </a>
                        @endif
                        <a class="btn btn-warning pull-right btn-flat mr-1" href="{{ route('admin.amministrazione.clienti.index')}}"><i class="fa fa-arrow-left"></i> Indietro</a>

                    
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
                    { key: 'b', route: "<?= route('admin.amministrazione.clienti.index') ?>" }
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
