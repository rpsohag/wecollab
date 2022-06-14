<div class="box box-warning box-shadow">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-map-marker"> </i> Indirizzi</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool btn-default" data-toggle="modal" data-target="#modal-default" data-title="Aggiungi indirizzo" data-action="{{ route('admin.amministrazione.clienti.indirizzi.create', $cliente->id) }}">
                Nuovo indirizzo
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
        </div>
        <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body">

        <section class="row">
            @foreach ($cliente->indirizzi as $key => $indirizzo)
                <div class="col-md-4">
                    <div class="box box-default collapsed-box box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ $indirizzo->denominazione }}</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                </button>
                            </div>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body no-padding">
                            <ul class="nav nav-stacked">
                                <li class="padding"><strong>Nazione</strong>: <span class="pull-right">{{ $indirizzo->nazione }}</span></li>
                                <li class="padding"><strong>CAP</strong>: <span class="pull-right">{{ $indirizzo->cap }}</span></li>
                                <li class="padding"><strong>Indirizzo</strong>: <span class="pull-right">{{ $indirizzo->indirizzo }}</span></li>
                                <li class="padding"><strong>Citta</strong>: <span class="pull-right">{{ $indirizzo->citta }}</span></li>
                                <li class="padding"><strong>Provincia</strong>: <span class="pull-right">{{ $indirizzo->provincia }}</span></li>
                                <li class="padding"><strong>Email</strong>: <span class="pull-right"><a href="mailto:{{ $indirizzo->email }}">{{ $indirizzo->email }}</a></span></li>
                                <li class="padding"><strong>Telefono</strong>: <span class="pull-right"><a href="tel:{{ $indirizzo->telefono }}">{{ $indirizzo->telefono }}</a></span></li>
                                <li class="padding"><strong>Fax</strong>: <span class="pull-right">{{ $indirizzo->fax }}</span></li>
                            </ul>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-default" data-title="Modifica indirizzo" data-action="{{ route('admin.amministrazione.clienti.indirizzi.edit', $indirizzo->id) }}">
                                Modifica
                            </button>
                            <button class="pull-right btn btn-sm" type="button" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.amministrazione.clienti.indirizzi.destroy', [$indirizzo->id]) }}"><i class="fa fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            @endforeach
        </section>
    </div>
</div>
