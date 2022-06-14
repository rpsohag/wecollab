@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('commerciale::segnalazioniopportunita.title.edit segnalazioneopportunita') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ route('admin.commerciale.segnalazioneopportunita.index') }}">{{ trans('commerciale::segnalazioniopportunita.title.segnalazioniopportunita') }}</a></li>
        <li class="active">{{ trans('commerciale::segnalazioniopportunita.title.edit segnalazioneopportunita') }}</li>
    </ol>
@stop

@section('content')
    {!! Form::open(['route' => ['admin.commerciale.segnalazioneopportunita.update', $segnalazione->id], 'method' => 'put', 'files'=> true]) !!}
 
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                @include('partials.form-tab-headers')
                <div class="tab-content">
                    <?php $i = 0; ?>
                    @foreach (LaravelLocalization::getSupportedLocales() as $locale => $language)
                        <?php $i++; ?>
                        <div class="tab-pane {{ locale() == $locale ? 'active' : '' }}" id="tab_{{ $i }}">
                            @include('commerciale::admin.segnalazioniopportunita.partials.fields', ['lang' => $locale])
                        </div>
                    @endforeach
 					{{-- @include('wecore::admin.partials.activities', ['activities' => $activities]) --}}

                    <div class="box-footer"> 
                      <a class="btn btn-info btn-flat" href="{{ route('admin.commerciale.segnalazioneopportunita.read', $segnalazione->id) }}"><i class="fa fa-arrow-left"></i> Vai alla visualizzazione</a>
                      <button type="submit" class="btn btn-success btn-flat"><i class="fa fa-floppy-o"></i> {{ trans('core::core.button.save') }}</button>
                      @if(empty($segnalazione->analisi_vendita) && !empty($segnalazione->censimento()) && $segnalazione->stato_id == 1)
                        <a class="btn btn-warning btn-flat" href="{{ route('admin.commerciale.analisivendita.create', ['censimentocliente_id' => $segnalazione->censimento()->first()->id, 'segnalazioni_id' => $segnalazione->id,'commerciale_id' => $segnalazione->commerciale_id ])  }}"><i class="fa fa-floppy-o"></i> Crea un'Analisi di Vendita</a>
                      @endif
                      @if(Auth::user()->inRole('admin') || Auth::user()->inRole('direzione-commerciale'))            
                        <button type="button" class="btn btn-danger pull-right  btn-flat" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.commerciale.segnalazioneopportunita.destroy', [$segnalazione->id]) }}"><i class="fa fa-trash"></i> {{ trans('core::core.button.delete') }}</button>
                      @endif
                       <a class="btn btn-warning pull-right btn-flat" href="{{ route('admin.commerciale.segnalazioneopportunita.index')}}"><i class="fa fa-arrow-left"></i> Indietro</a>
                    </div>
                </div> 
            </div> {{-- end nav-tabs-custom --}}
        </div>
    </div>
    {!! Form::close() !!}
    <div class="modal fade" id="accettaSegnalazione" tabindex="-1" role="dialog" aria-hidden="true">
        {!! Form::open(['route' => ['admin.commerciale.segnalazioneopportunita.accept'], 'method' => 'post']) !!}
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Accetta Segnalazione</h3>
                </div>
                <div class="modal-body">
                    <input type="hidden" value="{{$segnalazione->id}}" name="id">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::weSelectSearch('commerciale_id', 'Commerciale', $errors, $commerciali) !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-success btn-flat"><i class="fa fa-check" aria-hidden="true"></i> Accetta</button>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    @include('core::partials.reject-modal')

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
                    { key: 'b', route: "<?= route('admin.commerciale.segnalazioneopportunita.index') ?>" }
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
