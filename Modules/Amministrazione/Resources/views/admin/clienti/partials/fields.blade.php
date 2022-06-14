@php
    $cliente = (empty($cliente)) ? '' : $cliente;
    $si_no = [0=>'NO',1=>'SI'];
    $partner = json_decode(setting('clienti::partner'));
    $partner = (empty($partner)) ? [] : array_combine($partner, $partner);
@endphp


<div class="row">
    @if (!empty($cliente))
        <div class="col-md-3">
            <div class="box box-primary">
                <div class="box-body box-profile box-shadow box-profilo-cliente">
                  <img id="immagine-logo" class="profile-user-img img-responsive img-square" src="{{ (empty(get_if_exist($cliente, 'logo'))) ? set_via_placeholder(100) : get_if_exist($cliente, 'logo') }}" alt="Logo {{ get_if_exist($cliente, 'ragione_sociale') }}">

                  <h3 class="profile-username text-center">{{ get_if_exist($cliente, 'ragione_sociale') }}</h3>

                  <p class="text-muted text-center">{{ (empty(get_if_exist($cliente, 'p_iva'))) ? 'Codice Fiscale : '.(get_if_exist($cliente, 'cod_fiscale')) : 'Partita IVA : '.(get_if_exist($cliente, 'p_iva')) }}  </p>

                  <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                      <b>Offerte totali</b>
                      <a class="btn btn-xs btn-flat btn-default" href="{{ route('admin.commerciale.offerta.create') }}"><i class="fa fa-plus"> </i></a>
                      <a class="pull-right" href="{{ route('admin.commerciale.offerta.index', ['cliente' => $cliente->id]) }}">{{ $cliente->offerte->where('azienda', session('azienda'))->count() }}</a>
                    </li>
                    <li class="list-group-item">
                      <b>Offerte Non Fatturate</b> <a class="pull-right" href="{{ route('admin.commerciale.offerta.index', ['cliente' => $cliente->id, 'fatturata' => 0]) }}">{{ $cliente->offerte->where('azienda', session('azienda'))->where('fatturata', 0)->count() }}</a>
                    </li>
                    <li class="list-group-item">
                      <b>Offerte Fatturate</b> <a class="pull-right" href="{{ route('admin.commerciale.offerta.index', ['cliente' => $cliente->id, 'fatturata' => 1]) }}">{{ $cliente->offerte->where('azienda', session('azienda'))->where('fatturata', 1)->count() }}</a>
                    </li>
                    @if(!empty($cliente->hash_link))
                        <a target="_blank" href="https://www.we-com.it/assistenza2/ticket/{{$cliente->hash_link}}/">[Link Assistenza]</a></h3>
                    @endif
                  </ul>
 
                  {{-- <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a> --}}
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    @endif

    <div class="{{ empty($cliente) ? 'col-md-12' : 'col-md-9' }}">
        <div class="row">
            @if(!empty($cliente->segnalazione_opportunita))
              <input type="hidden" name="segnalazione_opportunita" value="{{ $cliente->segnalazione_opportunita }}" />
            @endif
            <div class="col-md-3">
                {{ Form::weSelect('tipo', 'Tipo *', $errors, config('amministrazione.clienti.tipi'), get_if_exist($cliente, 'tipo')) }}
            </div>
            <div class="col-md-5">
                {{ Form::weText('ragione_sociale', 'Ragione Sociale *', $errors, get_if_exist($cliente, 'ragione_sociale')) }}
            </div>
            <div class="col-md-4">
                {{ Form::weText('p_iva', 'Partita IVA *', $errors, get_if_exist($cliente, 'p_iva')) }}
            </div>
        </div>

        {{-- <div class="row">
          <div class="col-md-3">
              {{ Form::weText('indirizzo', 'Indirizzo *', $errors, get_if_exist($cliente, 'indirizzo')) }}
          </div>
          <div class="col-md-3">
              {{ Form::weText('citta', 'Città *', $errors, get_if_exist($cliente, 'citta')) }}
          </div>
          <div class="col-md-3">
              {{ Form::weText('provincia', 'Provincia *', $errors, get_if_exist($cliente, 'provincia')) }}
          </div>
          <div class="col-md-3">
              {{ Form::weText('cap', 'CAP *', $errors, get_if_exist($cliente, 'cap')) }}
          </div>
        </div> --}}

        <div class="row">
            {{-- <div class="col-md-3">
                {{ Form::weText('nazione', 'Nazione *', $errors, (!empty(get_if_exist($cliente, 'nazione'))) ? get_if_exist($cliente, 'nazione') : 'Italia' ) }}
            </div> --}}
            <div class="col-md-3">
                {{ Form::weSelect('tipologia', 'Tipologia *', $errors, config('amministrazione.clienti.tipologie'), get_if_exist($cliente, 'tipologia')) }}
            </div>
            <div class="col-md-3">
                {{ Form::weText('cod_fiscale', 'Codice Fiscale', $errors, get_if_exist($cliente, 'cod_fiscale')) }}
            </div>
            <div class="col-md-3">
              {{ Form::weText('email', 'E-Mail', $errors, get_if_exist($cliente, 'email')) }}
            </div>
            <div class="col-md-3">
              {{ Form::weText('pec', 'PEC', $errors, get_if_exist($cliente, 'pec')) }}
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                {{ Form::weFile('logo', 'Logo', $errors, get_if_exist($cliente, 'logo'), [], (!empty($cliente) && !empty(get_if_exist($cliente, 'logo'))) ? 'delete-logo' : null) }}
            </div>
            {{-- @foreach (config('amministrazione.clienti.aree') as $key => $area)
                <div class="col-md-3">
                    {{ Form::weCheckbox("aree[$key]", "Cliente $area", $errors, get_if_exist($cliente_aree, $key)) }}
                </div>
            @endforeach --}}
            <div class="col-md-3">
                {{ Form::weTags('aree', 'Partner', $errors, $partner, get_if_exist($cliente, 'aree')) }}
            </div>
            <div class="col-md-3">
              {{ Form::weText('codice_univoco', 'Codice Univoco', $errors, get_if_exist($cliente, 'codice_univoco')) }}
            </div>
        </div>
        <div class="row">
           <div class="col-md-3">
                {{ Form::weSelect('pa', 'PA Digitale', $errors, $si_no, get_if_exist($cliente, 'pa')) }}
                @if(!empty($cliente) && $cliente->pa == 1 && empty($cliente->ambiente()->first()) ) 
                  <button type="button" class="btn btn-box-tool btn-default" data-toggle="modal" data-target="#modal-default" 
                      data-title="Aggiungi" data-action="{{ route('admin.amministrazione.clienti.ambienti.create', $cliente->id) }}">
                      Informazioni PA
                  </button>
                @endif
            </div>
        </div>
        <div class="row">
           <div class="col-md-4">
                {{ Form::weSelect('default_ordinativo', 'Ordinativo Predefinito', $errors, $ordinativi, get_if_exist($cliente, 'default_ordinativo')) }}
            </div>
            <div class="col-md-4">
              {!! Form::weSelectSearch('commerciale_id', 'Commerciale Di Riferimento', $errors, $commerciali, get_if_exist($cliente, 'commerciale_id')) !!}
            </div>
        </div>
    </div>
</div>

@if(empty($cliente->tipologia))
    <div class="box box-warning box-shadow">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-map-marker"> </i> Indirizzo</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                  {{ Form::weText('indirizzo_base[denominazione]', 'Denominazione *', $errors, 'Sede legale') }}
                </div>
                <div class="col-md-6">
                  {{ Form::weText('indirizzo_base[indirizzo]', 'Indirizzo *', $errors) }}
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                  {{ Form::weText('indirizzo_base[citta]', 'Città *', $errors) }}
                </div>
                <div class="col-md-2">
                  {{ Form::weText('indirizzo_base[provincia]', 'Provincia *', $errors) }}
                </div>
                <div class="col-md-3">
                  {{ Form::weText('indirizzo_base[cap]', 'CAP *', $errors) }}
                </div>
                <div class="col-md-3">
                    {{ Form::weText('indirizzo_base[nazione]', 'Nazione *', $errors, 'Italia') }}
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                  {{ Form::weEmail('indirizzo_base[email]', 'Email', $errors) }}
                </div>
                <div class="col-md-4">
                  {{ Form::weText('indirizzo_base[telefono]', 'Telefono', $errors) }}
                </div>
                <div class="col-md-4">
                  {{ Form::weText('indirizzo_base[fax]', 'Fax', $errors) }}
                </div>
            </div>
        </div>
    </div>
@endif
