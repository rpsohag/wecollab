@php
    $user = (empty($user)) ? (object) ['profile' => ''] : $user;

    $collaborazioni = json_decode(setting('profile::collaborazioni'));
    $collaborazioni = array_combine($collaborazioni, $collaborazioni);
    $collaborazioni = array_merge(['' => ''], $collaborazioni);

    $aree = json_decode(setting('profile::aree'));

    $aziende = json_decode(setting('profile::aziende'));
    $aziende = array_combine($aziende, $aziende);
    $aziende = array_merge(['' => ''], $aziende);
	$sedi = config('wecore.richieste.sedi');
@endphp

<h3>Profilo</h3>
<div class="row">
    <div class="col-sm-3">
        {!! Form::weSelect('profile[titolo]', 'Titolo', $errors, ['' => '', 'Sig.' => 'Sig.', 'Sig.ra' => 'Sig.ra', 'Dott.' => 'Dott.', 'Dott.sa' => 'Dott.sa', 'Ing.' => 'Ing.'], get_if_exist($user->profile, 'titolo')) !!}
    </div>
    <div class="col-sm-3">
        {!! Form::weText('profile[matricola]', 'Matricola', $errors, get_if_exist($user->profile, 'matricola')) !!}
    </div>
    <div class="col-sm-3">
        {!! Form::weText('profile[badge]', 'Badge', $errors, get_if_exist($user->profile, 'badge')) !!}
    </div>
    <div class="col-sm-3">
        {!! Form::weText('profile[username]', 'Username *', $errors, get_if_exist($user->profile, 'username')) !!}
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        {!! Form::weText('profile[tipologia_di_contratto]', 'Tipologia di contratto', $errors, get_if_exist($user->profile, 'tipologia_di_contratto')) !!}
    </div>
    <div class="col-sm-3">
        {!! Form::weText('profile[incarico]', 'Incarico', $errors, get_if_exist($user->profile, 'incarico')) !!}
    </div>
    <div class="col-sm-3">
        {!! Form::weDate('profile[data_assunzione]', 'Data Assunzione', $errors, get_if_exist($user->profile, 'data_assunzione')) !!}
    </div>
    <div class="col-sm-3">
        {!! Form::weDate('profile[fine_contratto]', 'Fine Contratto', $errors, get_if_exist($user->profile, 'fine_contratto')) !!}
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        {!! Form::weText('profile[titolo_di_studio]', 'Titolo di studio', $errors, get_if_exist($user->profile, 'titolo_di_studio')) !!}
    </div>
    <div class="col-sm-3">
        {!! Form::weText('profile[codice_fiscale]', 'Codice fiscale', $errors, get_if_exist($user->profile, 'codice_fiscale')) !!}
    </div>
    <div class="col-sm-3">
        {!! Form::weDate('profile[data_di_nascita]', 'Data di nascita', $errors, get_if_exist($user->profile, 'data_di_nascita')) !!}
    </div>
    <div class="col-sm-3">
        {!! Form::weText('profile[provincia_di_nascita]', 'Provincia di nascita', $errors, get_if_exist($user->profile, 'provincia_di_nascita')) !!}
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        {!! Form::weText('profile[cognome_cedolino]', 'Cognome cedolino', $errors, get_if_exist($user->profile, 'cognome_cedolino')) !!}
    </div>
    <div class="col-sm-2">
        {!! Form::weInt('profile[interno]', 'Interno', $errors, get_if_exist($user->profile, 'interno')) !!}
    </div>
    <div class="col-sm-3">
        {!! Form::weSelect('profile[tipo_collaborazione]', 'Tipo di collaborazione', $errors, $collaborazioni, get_if_exist($user->profile, 'tipo_collaborazione')) !!}
    </div>
	<div class="col-sm-4">
		{!! Form::weSelectSearch('profile[sede_partenza]', 'Sede Di Partenza(Rimborsi Km)', $errors, $sedi,  get_if_exist($user->profile, 'sede_partenza')) !!}
	</div>
</div>
<div class="row">
    <div class="col-sm-3">
        {!! Form::weText('profile[num_telefono_aziendale]', 'Numero telefono aziendale', $errors, get_if_exist($user->profile, 'num_telefono_aziendale')) !!}
    </div>
    <div class="col-sm-3">
        {!! Form::weText('profile[num_telefono_personale]', 'Numero telefono personale', $errors, get_if_exist($user->profile, 'num_telefono_personale')) !!}
    </div>
    <div class="col-sm-3">
        {{ Form::weTags('profile[partner]', 'Partner', $errors, $partner, $selected_partner) }}
    </div>
	<div class="col-sm-3">
        {!! Form::weSelect('profile[azienda]', 'Azienda', $errors, $aziende, get_if_exist($user->profile, 'azienda')) !!}
    </div>
</div>
<hr>
<div class="row">
    <h4 class="col-sm-12">Aree</h4>
    @if(!empty($aree))
        @foreach ($aree as $key => $area)
            <div class="col-sm-3">
                {!! Form::weCheckbox('profile[aree]['.$area.']', $area, $errors, checked(get_if_exist($user->profile, 'aree'), $area)) !!}
            </div>
        @endforeach
    @endif
</div>
