@php
$ambiente = optional($cliente->ambiente())->first();
if($ambiente){
    $ambiente->ambiente = (strpos($ambiente->ambiente, 'http') !== false) ? $ambiente->ambiente : 'https://'.$ambiente->ambiente;

} else {
    $ambiente = null;
}
$si_no = [0=>'NO',1=>'SI'];
@endphp

@if(!empty($ambiente))
<div class="box box-info box-shadow">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-user"> </i> Informazioni PA Digitale </h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
        </div>
        <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body">

        <section class="row">
                <div class="col-md-4">
                    <div class="box box-default  box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">N° DB:
                                <span class="text-danger">{{ optional($ambiente)->n_db }}</span>
                                - API: <span class="text-info">{{ $si_no[(!empty(optional($ambiente)->api_sso) ? optional($ambiente)->api_sso : 0)]}}</span>
                            </h3>

                            @if(optional($ambiente)->api_sso == 1)
                                &nbsp;&nbsp;<button class="btn btn-sm" type="button" onclick="javascript:loginUrbi({{$cliente->id}});">Accedi</button>
                            @endif

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body no-padding">
                            <ul class="nav nav-stacked">
                                <li class="padding"><strong>Ambiente</strong>: <span class="pull-right">
                                    <a target="_blank" id="ambiente_link" href="{{optional($ambiente)->ambiente}}">{{optional($ambiente)->ambiente}}</a>
                                        &nbsp;&nbsp;<a class="btn bg-teal btn-sm" href="javascript:copia('ambiente_link')"
                                        data-toggle="tooltip" data-placement="right" title="" data-original-title="Copia">
                                        <i class="fa fa-tag"></i>
                                    </a>
                                </li>
                                <li class="padding"><strong>Admin</strong>:
                                    <span class="pull-right" id="ambiente_admin"> {{optional($ambiente)->admin}}
                                        <a class="btn bg-teal btn-sm" href="javascript:copia('ambiente_admin')"
                                        data-toggle="tooltip" data-placement="right" title="" data-original-title="Copia">
                                            <i class="fa fa-tag"></i>
                                        </a>
                                    </span>
                                </li>
                                <li class="padding"><strong>Admin Password</strong>:
                                    <span class="pull-right" id="ambiente_password_admin"> {{optional($ambiente)->password_admin}}
                                        <a class="btn bg-teal btn-sm" href="javascript:copia('ambiente_password_admin')"
                                        data-toggle="tooltip" data-placement="right" title="" data-original-title="Copia">
                                            <i class="fa fa-tag"></i>
                                        </a>
                                    </span>
                                </li>
                                <li class="padding"><strong>ADM</strong>:
                                    <span class="pull-right" id="ambiente_adm"> {{optional($ambiente)->adm}}
                                        <a class="btn bg-teal btn-sm" href="javascript:copia('ambiente_adm')"
                                        data-toggle="tooltip" data-placement="right" title="" data-original-title="Copia">
                                            <i class="fa fa-tag"></i>
                                        </a>
                                    </span>
                                </li>
                                <li class="padding"><strong>ADM Password</strong>:
                                    <span class="pull-right" id="ambiente_password_adm"> {{optional($ambiente)->password_adm}}
                                        <a class="btn bg-teal btn-sm" href="javascript:copia('ambiente_password_adm')"
                                        data-toggle="tooltip" data-placement="right" title="" data-original-title="Copia">
                                            <i class="fa fa-tag"></i>
                                        </a>
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-default" data-title="Modifica Informazioni PA" data-action="{{ route('admin.amministrazione.clienti.ambienti.edit', optional($ambiente)->id) }}">
                                Modifica
                            </button>
                        </div>
                    </div>
                </div>
        </section>
    </div>
</div>
@endif
