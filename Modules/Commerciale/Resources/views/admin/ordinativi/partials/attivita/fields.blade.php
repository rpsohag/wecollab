@php
    $attivita = (empty($attivita)) ? '' : $attivita;

    $categorie = json_decode(setting('tasklist::categorie'));
    $categorie = array_combine($categorie, $categorie);
    if(!empty($attivita->categoria)) {
        $categorie = array_merge([$attivita->categoria => $attivita->categoria], $categorie);
    } else {
        $categorie = array_merge(['' => ''], $categorie);
    }

    $utenti = $users;

    $clientis = [-1 => ''];
    $clientis = $clientis + $clienti;

    $priorita = config('tasklist.attivita.priorita');

    $durata_tipo =  config('tasklist.attivita.durata_tipo');

    $stati = config('tasklist.attivita.stati');

    $assegnatari = !empty($attivita) ? $attivita->users->pluck('id')->toArray() : [];

@endphp

<div id="form-attivita" name="form-attivita" class="box-body">
    <div class="row">
        <input type = "hidden" name="attivita[id]" id="attivita[id]" value="{{ (!empty($attivita->id)) ? $attivita->id : ''  }}" form ="form-ordinativo" >
        <div class="col-md-6">
            {{ Form::weSelectSearch('attivita[categoria]', 'Attivita *', $errors, $categorie, get_if_exist($attivita,'categoria'), ['form'=>"form-ordinativo" , 'required'=>'required']) }}
        </div>
        <div class="col-md-6">
            {{  Form::weSelectSearch('attivita[richiedente_id]', 'Richiedente *', $errors, $utenti, (get_if_exist($attivita,'richiedente_id')) ? get_if_exist($attivita,'richiedente_id') : Auth::id(), ['form'=>"form-ordinativo" , 'required'=>'required'])  }}
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {{  Form::weTags('attivita[assegnatari_id]', 'Assegnatari *', $errors, $utenti , ((!empty($assegnatari)) ? $assegnatari : Auth::id()), ['multiple' => 'multiple', 'form' => "form-ordinativo", 'id' => 'attivita-assegnatari' , 'required'=>'required']) }}

            @foreach ($gruppi as $key => $gruppo)
                <button class="btn btn-md get-gruppo-users" type="button" data-id="{{ $gruppo->id }}" data-select="#attivita-assegnatari">{{ $gruppo->nome }}</button>
            @endforeach
            <br><br>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {{ Form::weText('attivita[oggetto]', 'Oggetto', $errors, get_if_exist($attivita, 'oggetto'), ['form'=>"form-ordinativo"]) }}
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            {{  Form::weSelect('attivita[priorita]', 'Priorità *', $errors, $priorita , ((get_if_exist($attivita,'priorita')) ? get_if_exist($attivita,'priorita') : 5) , ['form'=>"form-ordinativo" , 'required'=>'required']) }}
        </div>
        <div class="col-md-3">
            {{  Form::weDate('attivita[data_inizio]', 'Data di Inizio', $errors , (get_if_exist($attivita,'data_inizio')) ? get_if_exist($attivita, 'data_inizio') : $ordinativo->data_inizio, ['form'=>"form-ordinativo"]) }}
        </div>
        <div class="col-md-3">
            {{  Form::weDate('attivita[data_fine]', 'Data Scadenza', $errors , (get_if_exist($attivita,'data_fine')) ? get_if_exist($attivita, 'data_fine') : $ordinativo->data_fine, ['form'=>"form-ordinativo"]) }}
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            {{  Form::weInt('attivita[durata_valore]', 'Durata *' , $errors, get_if_exist($attivita, 'durata_valore') ,['form'=>"form-ordinativo" , 'required'=>'required']) }}
        </div>
        <div class="col-md-2">
            {{  Form::weSelect('attivita[durata_tipo]', '&nbsp;', $errors, $durata_tipo , get_if_exist($attivita, 'durata_tipo') , ['form'=>"form-ordinativo" , 'required'=>'required']) }}
        </div>
        <div class="col-md-5">
            {{  Form::weSlider('attivita[percentuale_completamento]', 'Percentuale completamento', $errors, get_if_exist($attivita, 'percentuale_completamento') , ['form'=>"form-ordinativo"]) }}
        </div>
        <div class="col-md-3">
            {{  Form::weSelect('attivita[stato]', 'Stato *', $errors, $stati , get_if_exist($attivita, 'stato') , ['form'=>"form-ordinativo" , 'required'=>'required']) }}
        </div>
    </div>

    <div class="box-footer">
        <button type="submit" form="form-ordinativo" class="btn btn-success btn-flat">{{ trans('Salva') }}</button>
    </div>

</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#form-attivita").validate();

        var stato_val = $("select[name='attivita[stato]']").val();
        var stato = $("select[name='attivita[stato]']");
        var percentuale_slide = $('[name="attivita[percentuale_completamento]"]');

        if(stato_val != 0)
        {
             percentuale_slide.slider("disable");
        }

        //percentuale
        percentuale_slide.change(function() {
            if($(this).val() == 100)
            {
                stato.val(2);
            }
            else
            {
                stato.val(0);
            }
        });

        //stato
        stato.on('change', function(e) {
            if($(this).val() == 2) { // COMPLETATA
                percentuale_slide.slider("setAttribute", "value", 100);
                percentuale_slide.slider("refresh");
                percentuale_slide.slider("disable");
            }
            else if($(this).val() == 0 && stato_val == 2) { // IN LAVORAZIONE DA COMPLETATA
                percentuale_slide.slider("setAttribute", "value", 90);
                percentuale_slide.slider("refresh");
            }
            else if($(this).val() == 0) { // IN LAVORAZIONE
                percentuale_slide.slider("enable");
            }
            else if($(this).val() == 3 || $(this).val() == 1 ) { // ANNULLATTA E IN ATTESA
                percentuale_slide.slider("setAttribute", "value", 100);
                percentuale_slide.slider("refresh");
                percentuale_slide.slider("disable");
            }
        });

    });
</script>
