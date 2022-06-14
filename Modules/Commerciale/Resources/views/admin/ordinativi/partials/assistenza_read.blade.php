<div class="box-body">
    <div class="row">
        @if(!empty($ordinativo->assistenza_per))
            <div class="col-md-5">
                <p><strong>Assistenza Per</strong></p>
                {{ $ordinativo->assistenza_per }}
            </div>
        @endif
        @if(!empty($ordinativo->hash_link))
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong> Link Assistenza </strong></p>
                        <a class="hash_value" style="margin-top:5px;"></a>
                            &nbsp;&nbsp;<a class="btn bg-teal btn-sm" href="javascript:clipboard_copy('https://www.we-com.it/assistenza/ticket_adm/{{$ordinativo->hash_link}}/')"
                            data-toggle="tooltip" data-placement="right" title="" data-original-title="Copia">
                            <i class="fa fa-tag"></i></a>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <p><strong> Link Apertura Ticket </strong></p>
                        <a class="hash_value" style="margin-top:5px;"></a>
                            &nbsp;&nbsp;<a class="btn bg-teal btn-sm" href="javascript:clipboard_copy('https://www.we-com.it/assistenza/ticket/{{$ordinativo->hash_link}}/')"
                            data-toggle="tooltip" data-placement="right" title="" data-original-title="Copia">
                            <i class="fa fa-tag"></i></a>
                        </a>
                    </div>
                </div>
            </div>
        @endif
        @if(!empty($ordinativo->api_password))
            <div class="col-md-3">
                <p><strong> Password </strong></p>
                <a class="hash_value" style="margin-top:5px;">{{$ordinativo->api_password}}</a>
                    &nbsp;&nbsp;<a class="btn bg-teal btn-sm" href="javascript:clipboard_copy('{{$ordinativo->api_password}}')"
                    data-toggle="tooltip" data-placement="right" title="" data-original-title="Copia">
                    <i class="fa fa-tag"></i></a>
                </a>
            </div>
        @endif
    </div> 
    <br>    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header">
                    <h4><strong>Attività</strong></h4>
                </div>
                <div class="box-body">
                    <div id="table-attivita">
                        @if(!empty($ordinativo->assistenza))
                            @php $i = 1; @endphp
                            @foreach($ordinativo->assistenza as $key => $attivita)
                                <h4 class="text-info"><strong># {{$i}}</strong></h4>
                                <div class="row" data-id="{{ $key }}">
                                    <br>
                                    <div class="col-md-2">
                                        <strong>PROCEDURA:</strong> {{ $procedure_list[$attivita->procedura_id] }}
                                    </div>
                                    <div class="col-md-2">
                                        <strong>AREA:</strong> {{ $aree_list[$attivita->area_id] }}
                                    </div>
                                    <div class="col-md-2">
                                        <strong>ATTIVITA':</strong> {{ $gruppi_list[$attivita->gruppo_id] }}
                                    </div>
                                    <div class="col-md-2">
                                        <strong>DESTINATARI:</strong>
                                        @if(!empty($attivita->destinatari_ids))
                                            @foreach((array)$attivita->destinatari_ids as $destinatario_id)
                                                {{ $utenti_list[$destinatario_id] }}@if(!$loop->last), @endif
                                            @endforeach 
                                        @endif
                                    </div>
                                    @if(!empty($attivita->descrizione))
                                        <div class="col-md-4">
                                            <strong>DESCRIZIONE:</strong> {{ $attivita->descrizione }}
                                        </div>
                                    @endif
                                </div>
                                @php $i++; @endphp
                                @if(!$loop->last)
                                    <hr>
                                @else
                                    <br>     
                                @endif
                            @endforeach
                        @endif   
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>