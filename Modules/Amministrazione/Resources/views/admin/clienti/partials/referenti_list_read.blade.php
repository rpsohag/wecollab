<div class="box box-warning box-shadow">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-user"> </i> Referenti</h3>

        <div class="box-tools pull-right">
            {{-- <button type="button" class="btn btn-box-tool btn-default" data-toggle="modal" data-target="#modal-default" data-title="Aggiungi referente" data-action="{{ route('admin.amministrazione.clienti.referenti.create', $cliente->id) }}">
                Nuovo referente
            </button>--}}
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
        </div>
        <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body">

        <section class="row">
            @foreach ($cliente->referenti as $key => $referente)
                <div class="col-md-4">
                    <div class="box box-default  box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ $referente->nome }} {{ $referente->cognome }}</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body no-padding">
                            <ul class="nav nav-stacked">
                                <li class="padding"><strong>Telefono</strong>: <span class="pull-right"><a href="tel:{{ $referente->telefono }}">{{ $referente->telefono }}</a></span></li>
                                <li class="padding"><strong>Email</strong>: <span class="pull-right"><a href="mailto:{{ $referente->email }}">{{ $referente->email }}</a></span></li>
                                <li class="padding"><strong>Mansione</strong>: <span class="pull-right">{{ $referente->mansione }}</span></li>
                            </ul>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                         {{--    <button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-default" data-title="Modifica indirizzo" data-action="{{ route('admin.amministrazione.clienti.referenti.edit', $referente->id) }}">
                                Modifica
                            </button>
                            <button class="pull-right btn btn-sm" type="button" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.amministrazione.clienti.referenti.destroy', [$referente->id]) }}"><i class="fa fa-trash"></i></button>
                        --}}
                        </div>
                    </div>
                </div>
            @endforeach
        </section>
    </div>
</div>
