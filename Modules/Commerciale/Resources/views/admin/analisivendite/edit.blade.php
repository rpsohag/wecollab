@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('commerciale::analisivendite.title.edit analisivendita') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ route('admin.commerciale.analisivendita.index') }}">{{ trans('commerciale::analisivendite.title.analisivendite') }}</a></li>
        <li class="active">{{ trans('commerciale::analisivendite.title.edit analisivendita') }}</li>
    </ol>
@stop

@section('content')
  @empty($read)
    {!! Form::open(['route' => ['admin.commerciale.analisivendita.update', $analisivendita->id], 'method' => 'put']) !!}
  @endempty
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                @include('partials.form-tab-headers')
                <div class="tab-content">
                    <?php $i = 0; ?>
                    @foreach (LaravelLocalization::getSupportedLocales() as $locale => $language)
                        <?php $i++; ?>
                        <div class="tab-pane {{ locale() == $locale ? 'active' : '' }}" id="tab_{{ $i }}">
                            @include('commerciale::admin.analisivendite.partials.fields', ['lang' => $locale])
                        </div>
                    @endforeach

                    <div class="box-footer">
                      @if(!empty($read))
                        <a class="btn btn-primary btn-flat" href="{{ route('admin.commerciale.analisivendita.edit', [$analisivendita->id])}}"><i class="fa fa-edit"></i> {{ trans('core::core.button.update') }}</a>
                      @else
                        <button type="submit" class="btn btn-success btn-flat"> <i class="fa fa-floppy-o"></i> {{ trans('core::core.button.save') }}</button>
                      @endif

                      @if(!get_if_exist($analisivendita , 'offerta_id'))
                        <a class="btn btn-success btn-flat" href="{{ route('admin.commerciale.offerta.create', ['analisi_id'=> $analisivendita->id, 'cliente_id' => $analisivendita->censimento_cliente->cliente_id])}}"><i class="fa fa-plus"></i> Crea Bozza Offerta</a>
                      @else
                        <a href="{{ route('admin.commerciale.offerta.edit', [$analisivendita->offerta_id ])}}"  class="btn btn-primary btn-flat">Vai a Offerta</a>
                      @endif
                        <a class="btn btn-info   btn-flat" href="{{ route('admin.commerciale.analisivendita.read', [$analisivendita->id])}} "><i class="fa fa-eye"></i> Vedi </a>

                      <a class="btn btn-warning pull-right btn-flat" href="{{ route('admin.commerciale.analisivendita.index')}}"><i class="fa fa-arrow-left"></i> Indietro</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty($read)
      {!! Form::close() !!}
    @endempty
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
