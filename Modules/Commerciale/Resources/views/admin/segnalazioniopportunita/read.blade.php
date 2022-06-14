@extends('layouts.master')

@section('content-header')
    <h1>
        Segnalazione Opportunità
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ route('admin.commerciale.segnalazioneopportunita.index') }}">{{ trans('commerciale::segnalazioniopportunita.title.segnalazioniopportunita') }}</a></li>
        <li class="active">{{ trans('commerciale::segnalazioniopportunita.title.edit segnalazione') }}</li>
    </ol>
@stop

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                @include('partials.form-tab-headers')
                <div class="tab-content">
                    <?php $i = 0; ?>
                    @foreach (LaravelLocalization::getSupportedLocales() as $locale => $language)
                        <?php $i++; ?>
                        <div class="tab-pane {{ locale() == $locale ? 'active' : '' }}" id="tab_{{ $i }}">
                            @include('commerciale::admin.segnalazioniopportunita.partials.fields_read', ['lang' => $locale])
                        </div>
                    @endforeach

                    <div class="box-footer">
                      @if(Auth::user()->inRole('admin') || Auth::user()->inRole('direzione-commerciale') || Auth::user()->hasAccess('commerciale.segnalazioniopportunita.edit'))
                        @if($segnalazione->stato_id == 0 || $segnalazione->stato_id == 2)
                            <a class="btn btn-info btn-flat" href="{{ route('admin.commerciale.segnalazioneopportunita.edit', [$segnalazione->id])  }}"><i class="fa fa-pencil"></i> Modifica</a>
                        @endif
                        @if(empty($segnalazione->analisi_vendita) && !empty(optional($segnalazione->censimento())->first()) && $segnalazione->stato_id == 1)
                            <a class="btn btn-warning btn-flat" href="{{ route('admin.commerciale.analisivendita.create', ['censimentocliente_id' => $segnalazione->censimento()->first()->id, 'segnalazioni_id' => $segnalazione->id,'commerciale_id' => $segnalazione->commerciale_id ])  }}"><i class="fa fa-floppy-o"></i> Crea un'Analisi di Vendita</a>
                        @endif
                      @endif
                      <a class="btn btn-warning pull-right btn-flat" href="{{ route('admin.commerciale.segnalazioneopportunita.index')}}"><i class="fa fa-arrow-left"></i> Indietro</a>
                    </div>
                </div>
            </div> {{-- end nav-tabs-custom --}}
        </div>
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
