@extends('layouts.master')

@section('content-header')
    <h1>
        Visualizza analisi di vendita
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ route('admin.commerciale.analisivendita.index') }}">{{ trans('commerciale::analisivendite.title.analisivendite') }}</a></li>
        <li class="active">{{ trans('commerciale::analisivendite.title.edit analisivendita') }}</li>
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
                            @include('commerciale::admin.analisivendite.partials.fields_read', ['lang' => $locale])
                        </div>
                    @endforeach

                    <div class="box-footer">
                      {{-- @if(!empty($analisivendita->offerta) && empty($analisivendita->offerta->ordinativo)) --}}      
                        <a class="btn btn-primary btn-flat" href="{{ route('admin.commerciale.analisivendita.edit', [$analisivendita->id])}}"><i class="fa fa-edit"></i> {{ trans('core::core.button.update') }}</a>
                      @if(!get_if_exist($analisivendita , 'offerta_id'))
                        <a class="btn btn-success btn-flat" href="{{ route('admin.commerciale.offerta.create', ['analisi_id'=> $analisivendita->id, 'cliente_id' => $analisivendita->censimento_cliente->cliente_id])}}"><i class="fa fa-plus"></i> Crea Bozza Offerta</a>
                      @else
                        <a href="{{ route('admin.commerciale.offerta.read', [$analisivendita->offerta_id ])}}"  class="btn btn-info btn-flat">Vai a Offerta</a>
                      @endif

                      @if(auth_user()->hasAccess('commerciale.analisivendite.exportexcel'))
                        <a href="{{ route('admin.commerciale.analisivendita.exportexcel', ['analisivendita_id' => $analisivendita->id]) }}" class="btn bg-olive btn-flat">
                          <i class="fa fa-table"> </i> Esporta Excel
                        </a>
                      @endif

                      <a class="btn btn-info btn-flat" href="{{ route('admin.commerciale.segnalazioneopportunita.read', $analisivendita->segnalazioni->first->id)}}"><i class="fa fa-paper"></i>Vai a Segnalazione</a>
                      <a class="btn btn-warning btn-flat" href="{{ route('admin.commerciale.analisivendita.create', ['duplicate_id' => $analisivendita->id])}}"><i class="fa fa-clone"></i> Duplica</a>

                      <a class="btn btn-warning pull-right btn-flat" href="{{ route('admin.commerciale.analisivendita.index')}}"><i class="fa fa-arrow-left"></i> Indietro</a>
                    </div>
                </div>
            </div>
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
                    { key: 'b', route: "<?= route('admin.commerciale.analisivendita.index') ?>" }
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
    @if(!empty($read))
      <script>
        $(document).ready(function() {
          // Disable input if read
          $('input, select, textarea').attr('disabled', 'disabled')
                                      .attr('readonly', 'readonly')
                                      .addClass('disabled');

          $('#segnalazioni').selectize()[0].selectize.disable();

          // Remove row if read and empty
          $('.tr-area').each(function() {
            if($(this).find('input').length == 0)
              $(this).remove();
          });

          // Remove table if read and empty
          $('.table-procedura').each(function() {
            if($(this).find('input').length == 0)
              $(this).remove();
          });
        });
      </script>
    @endif
@endpush
