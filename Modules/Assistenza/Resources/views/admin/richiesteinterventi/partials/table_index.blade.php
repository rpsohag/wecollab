<div class="box-body">
    <div class="table-responsive">
        <table class="data-table table table-bordered table-hover">
            <thead>
              <tr>
                {!! order_th('numero', 'Codice') !!}
                {!! order_th('cliente', 'Cliente') !!}
                {!! order_th('area', 'Area di intervento') !!}
                {!! order_th('richiedente', 'Richiedente') !!}
                {!! order_th('numero_da_richiamare', 'Numero da richiamare') !!}
                {!! order_th('created_at', 'Data apertura') !!}
                {!! order_th('data_chiusura', 'Data chiusura') !!}
                {!! order_th('oggetto', 'Oggetto') !!}
                {!! order_th('descrizione_richiesta', 'Descrizione') !!}
                {!! order_th('destinatario', 'Destinatario') !!}
                <th>Ultima Azione</th>
                {{-- <th data-sortable="false">{{ trans('core::core.table.actions') }}</th> --}}
              </tr>
            </thead>
            <tbody>
                @if(isset($richiesteinterventi))
                    @foreach($richiesteinterventi as $richiestaintervento)
                     
                        @php

                        $richieste_azioni = $richiestaintervento->azioni->last();

                        $colore_testo = '';

                        if($richiestaintervento->livello_urgenza == 1)
                        {
                            //$colore = $stati_colori->livello_urgenza;
                            $colore_testo = 'bg-green text-white';
                        }

                        if($richiestaintervento->livello_urgenza == 2)
                        {
                            //$colore = $stati_colori->livello_urgenza;
                            $colore_testo = 'bg-red text-white';
                        }

                        if(!empty($richieste_azioni) && $richieste_azioni->created_user_id == Auth::id() && $richieste_azioni->tipo == 1){
                        $colore_testo = 'bg-green text-white';
                        }

                        if(!empty($richieste_azioni) && $richieste_azioni->tipo == 4){
                            $colore_testo = 'bg-gray text-black';
                        }

                        @endphp

                        <tr class="{{ (!empty($colore_testo)) ? $colore_testo : "" }}">
                            <td>
                                <a href="{{ route('admin.assistenza.richiesteintervento.read', [$richiestaintervento->id]) }}">
                                    {{ $richiestaintervento->codice }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.assistenza.richiesteintervento.read', [$richiestaintervento->id]) }}">
                                    {{ optional($richiestaintervento->cliente)->ragione_sociale }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.assistenza.richiesteintervento.read', [$richiestaintervento->id]) }}">
                                    {{ $richiestaintervento->area->titolo }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.assistenza.richiesteintervento.read', [$richiestaintervento->id]) }}">
                                    {{ $richiestaintervento->richiedente }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.assistenza.richiesteintervento.read', [$richiestaintervento->id]) }}">
                                    {{ $richiestaintervento->numero_da_richiamare }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.assistenza.richiesteintervento.read', [$richiestaintervento->id]) }}">
                                    {{ get_date_hour_ita($richiestaintervento->created_at) }}
                                </a>
                            </td>
                            <td>
                            @if(!empty($richieste_azioni) && $richieste_azioni->tipo == 3)
                                <a href="{{ route('admin.assistenza.richiesteintervento.read', [$richiestaintervento->id]) }}">
                                    {{ get_date_hour_ita($richieste_azioni->created_at) }}
                                </a>
                            @else
                                @if(!empty($richieste_azioni) && $richieste_azioni->tipo == 4)
                                    <a href="{{ route('admin.assistenza.richiesteintervento.read', [$richiestaintervento->id]) }}">Sospeso</a>
                                @else
                                    <a href="{{ route('admin.assistenza.richiesteintervento.read', [$richiestaintervento->id]) }}">Non Chiuso</a>
                                @endif
                            @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.assistenza.richiesteintervento.read', [$richiestaintervento->id]) }}">
                                    {{ $richiestaintervento->oggetto }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.assistenza.richiesteintervento.read', [$richiestaintervento->id]) }}">
                                    {{ Str::limit($richiestaintervento->descrizione_richiesta, 100, '...') }}
                                </a> 
                            </td>
                            <td>
                                <a href="{{ route('admin.assistenza.richiesteintervento.read', [$richiestaintervento->id]) }}">
                                    @php
                                    if(!empty($richieste_azioni)){
                                        if($richieste_azioni->tipo == 1){
                                            $full_name = $richieste_azioni->created_user->full_name;
                                        }
                                    }
                                    if(empty($full_name)){
                                    $full_name = "";
                                    } 
                                    @endphp
                                    @foreach($richiestaintervento->destinatari as $desti)
                                        @if($desti->full_name == $full_name)
                                            <strong style="color:#1D5683;">{{ $desti->full_name }}</strong>
                                        @else
                                            {{ $desti->full_name }}
                                        @endif
                                        @if(!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                    @php $full_name = ''; @endphp
                                </a>
                            </td>
                            <td>
                                @if(!empty($richieste_azioni))
                                    {{ $richieste_azioni->descrizione }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        <div class="text-right pagination-container">
          {{ $richiesteinterventi->links() }}
        </div>
    </div>
</div>