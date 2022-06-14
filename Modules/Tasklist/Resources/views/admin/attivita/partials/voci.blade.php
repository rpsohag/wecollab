@php
    $attivita = empty($attivita) || ($attivita->voci->count() == 0) ? (object) ['voci' => [0]] : $attivita;
@endphp

@if(count($attivita->voci) > 1 && Route::currentRouteName() == "admin.tasklist.attivita.edit" || Route::currentRouteName() == "admin.tasklist.attivita.create")
<div class="box box-success box-shadow attivita-voci">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-code-fork"> </i>
            Lavori
            &nbsp;&nbsp;
            <small data-toggle="tooltip" title="" class="label bg-blue" data-original-title="Percentuale di completamento">{{ method_exists($attivita, 'percentuale_completamento') ? $attivita->percentuale_completamento() : '0%' }}</small>
        </h3>

        <div class="box-tools pull-right">
            <span data-toggle="tooltip" title="" class="badge bg-green" data-original-title="{{ count($attivita->voci) }} Lavori">{{ count($attivita->voci) }}</span>
            {{-- <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button> --}}
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body lista-voci">
        @foreach ($attivita->voci as $key => $voce)
            <div class="box box-warning box-shadow attivita-voce" data-key="{{ $key }}">
                <input type="hidden" name="voci[{{ $key }}][id]" value="{{ get_if_exist($voce, 'id') }}">
                <div class="box-header with-border">
                    <div class="col-md-12">
                        {{ Form::weText('voci[' . $key . '][descrizione]', 'Descrizione *', $errors, get_if_exist($voce, 'descrizione')) }}
                    </div>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool remove-voce" onclick="removeAttivitaVoce(this)"><i class="fa fa-trash text-danger"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="col-md-1">
                        {{ Form::weSelect('voci[' . $key . '][priorita]', 'Priorità', $errors, $priorita , (get_if_exist($voce,'priorita')) ? get_if_exist($voce,'priorita') : 5) }}
                    </div>
                    <div class="col-md-4">
                        {{ Form::weDate('voci[' . $key . '][data_inizio]', 'Data di Inizio', $errors, (get_if_exist($voce,'data_inizio')) ? get_if_exist($voce,'data_inizio') : date('d/m/Y')) }}
                    </div>
                    <div class="col-md-4">
                        {{ Form::weDate('voci[' . $key . '][data_fine]', 'Data Scadenza', $errors, get_if_exist($voce,'data_fine')) }}
                    </div>
                    <div class="col-md-1">
                        {{ Form::weInt('voci[' . $key . '][durata_valore]', 'Durata' , $errors, get_if_exist($voce, 'durata_valore')) }}
                    </div>
                    <div class="col-md-2">
                        {{ Form::weSelect('voci[' . $key . '][durata_tipo]', '&nbsp;', $errors, $durata_tipo , get_if_exist($voce, 'durata_tipo')) }}
                    </div>
                    <div class="col-md-6">
                        {{-- {{ dd($voce->users->pluck('id')->toArray()) }} --}}
                        {{ Form::weTags('voci[' . $key . '][users]', 'Assegnatari *', $errors, []) }}
                        <input type="hidden" name="voci[{{ $key }}][users_selected]" value="{{ (!empty($voce->users) ? $voce->users->pluck('id')->toJson() : '') }}">
                    </div>
                    <div class="col-md-4">
                        {{ Form::weSlider('voci[' . $key . '][percentuale_completamento]', 'Percentuale completamento', $errors, get_if_exist($voce, 'percentuale_completamento'), ['onchange' => 'vociPercentuale('.$key.')']) }}
                    </div>
                    <div class="col-md-2">
                        {{ Form::weSelect('voci[' . $key . '][stato]', 'Stato *', $errors, $stati , get_if_exist($voce, 'stato'), ['class' => 'voce-stato', 'onchange' => 'updateAttivitaStato('.$key.')']) }}
                    </div>
                </div>

                @include('wecore::admin.partials.files', ['model' => $voce, 'name' => 'voci[' . $key . ']'])
                @include('wecore::admin.partials.note', ['model' => $voce, 'name' => 'voci[' . $key . ']'])
            </div>
        @endforeach

        <div id="container-btn-add-voce" class="text-center">
            <button id="add-voce" class="btn btn-flat btn-default" type="button"><i class="fa fa-plus"> </i> Aggiungi lavoro</button>
        </div>
    </div>
    <!-- /.box-body -->
</div>
@endif
