@php
    $cliente = (empty($cliente)) ? '' : $cliente;
    $si_no = [0=>'NO',1=>'SI'];
    $partner = json_decode(setting('clienti::partner'));
    $partner = (empty($partner)) ? [] : array_combine($partner, $partner);

    // $cliente_aree = (!empty($cliente->aree)) ? json_decode($cliente->aree) : [];
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
                       <a class="pull-right" href="{{ route('admin.commerciale.offerta.index', ['cliente' => $cliente->id]) }}">{{ $cliente->offerte->where('azienda', session('azienda'))->count() }}</a>
                    </li>
                    <li class="list-group-item">
                      <b>Offerte Non Fatturate</b> <a class="pull-right" href="{{ route('admin.commerciale.offerta.index', ['cliente' => $cliente->id, 'fatturata' => 0]) }}">{{ $cliente->offerte->where('azienda', session('azienda'))->where('fatturata', 0)->count() }}</a>
                    </li>
                    <li class="list-group-item">
                      <b>Offerte Fatturate</b> <a class="pull-right" href="{{ route('admin.commerciale.offerta.index', ['cliente' => $cliente->id, 'fatturata' => 1]) }}">{{ $cliente->offerte->where('azienda', session('azienda'))->where('fatturata', 1)->count() }}</a>
                    </li>
                  </ul>
                    @if(!empty($cliente->hash_link))
                        <a target="_blank" href="https://www.we-com.it/assistenza2/ticket/{{$cliente->hash_link}}/">[Link Assistenza]</a></h3>
                    @endif
                  {{-- <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a> --}}
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    @endif

    <div class="{{ empty($cliente) ? 'col-md-12' : 'col-md-9' }}">



            <div class="box-body no-padding col-md-4">
	            <ul class="nav nav-stacked">
	                <li class="padding"><strong>Tipo</strong>: <span class="pull-right">{{config('amministrazione.clienti.tipi') [get_if_exist($cliente, 'tipo')]  }}</span></li>
	                <li class="padding"><strong>Ragione Sociale</strong>: <span class="pull-right">{{ get_if_exist($cliente, 'ragione_sociale')}}</span></li>
	                <li class="padding"><strong>Partita IVA</strong>: <span class="pull-right">{{get_if_exist($cliente, 'p_iva')  }}</span></li>
	                <li class="padding"><strong>Tipologia</strong>: <span class="pull-right"> {{ config('amministrazione.clienti.tipologie')[strtolower(get_if_exist($cliente, 'tipologia'))]  }}</span></li>
	                <li class="padding"><strong>Codice Fiscale</strong>: <span class="pull-right">{{ get_if_exist($cliente, 'cod_fiscale') }}</span></li>
	                <li class="padding"><strong>E-Mail</strong>: <span class="pull-right"><a href="mailto:{{ get_if_exist($cliente, 'email') }}">{{ get_if_exist($cliente, 'email') }}</a></span></li>
	                <li class="padding"><strong>PEC</strong>: <span class="pull-right"><a href="mailto:{{ get_if_exist($cliente, 'pec') }}">{{ get_if_exist($cliente, 'pec') }}</a></span></li>
	                <li class="padding"><strong>Partner</strong>:
                    @if(get_if_exist($cliente, 'aree'))
                      <span class="pull-right"> {{ implode(', ',get_if_exist($cliente, 'aree')) }}</span>
                    @else
                      <span class="pull-right">nessuno</span>
                    @endif
                  </li>
	                <li class="padding"><strong>Codice Univoco</strong>: <span class="pull-right">{{ get_if_exist($cliente, 'codice_univoco') }}</span></li>
	            </ul>
	        </div>

         {{--
            <div class="col-md-12">
                 <b>Tipo</b> {{config('amministrazione.clienti.tipi') [get_if_exist($cliente, 'tipo')]  }}
            </div>
            <div class="col-md-12">
               <b>Ragione Sociale</b>  {{ get_if_exist($cliente, 'ragione_sociale')}}
            </div>
            <div class="col-md-12">
              <b>Partita IVA </b> {{get_if_exist($cliente, 'p_iva')  }}
            </div>


            <div class="col-md-12">
              <b>Tipologia</b>    {{ config('amministrazione.clienti.tipologie')[get_if_exist($cliente, 'tipologia')]  }}
            </div>
            <div class="col-md-12">
                <b> Codice Fiscale </b>  {{ get_if_exist($cliente, 'cod_fiscale') }}
            </div>
            <div class="col-md-12">
                <b>E-Mail</b>  {{ get_if_exist($cliente, 'email') }}
            </div>
            <div class="col-md-12">
               <b>PEC</b>    {{ get_if_exist($cliente, 'pec') }}
            </div>
         	<div class="col-md-12">
                <b>Partner</b>
            	 {{ implode(', ',get_if_exist($cliente, 'aree')  )}}
            </div>
            <div class="col-md-12">
            	<b>Codice Univoco</b>
            		{{ get_if_exist($cliente, 'codice_univoco') }}
            </div>
            --}}
        </div>
    </div>
</div>

@if(empty($cliente))
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

@if(!empty($giornate_cliente))
  <div class="box box-info box-shadow">
    <div class="box-header with-border">
      <h3 class="box-title" style="font-weight: bold;">Giornate</h3> 
    </div> 
    <div class="box-body">
      @foreach($giornate_cliente as $ordinativo => $giornate)
        @if($giornate->count() > 0)
          @if($giornate->where('tipo', 0)->sum('quantita_residue') > 0 || $giornate->where('tipo', 1)->sum('quantita_residue') > 0)
            <div>
              <div class="col-12">
                <h4>{{ $ordinativo }}</h4>
                <hr> 
              </div>
              <div class="row">
                <div class="col-md-4 col-sm-6 col-xs-12">
                  <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-exclamation-circle"></i></span>
            
                    <div class="info-box-content">
                      <span class="info-box-text">Quantita</span>
                      <span class="info-box-number">{{ $giornate->where('tipo', 0)->sum('quantita') }} <small>giornate &</small> {{ $giornate->where('tipo', 1)->sum('quantita') }} <small>ore</small></span>
                    </div>
            
                  </div>
                </div>
                <div class="col-md-4 col-sm-6 col-xs-12">
                  <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-exclamation-circle"></i></span>
            
                    <div class="info-box-content">
                      <span class="info-box-text">Quantita Residue</span>
                      <span class="info-box-number">{{ $giornate->where('tipo', 0)->sum('quantita_residue') }} <small>giornate &</small> {{ $giornate->where('tipo', 1)->sum('quantita_residue') }} <small>ore</small></span>
                    </div>
            
                  </div>
                </div>
                <div class="col-md-4 col-sm-6 col-xs-12">
                  <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-exclamation-circle"></i></span>
            
                    <div class="info-box-content">
                      <span class="info-box-text">Quantita Effettuate</span>
                      <span class="info-box-number">{{ $giornate->where('tipo', 0)->sum('quantita') - $giornate->where('tipo', 0)->sum('quantita_residue') }} <small>giornate &</small> {{ $giornate->where('tipo', 1)->sum('quantita') - $giornate->where('tipo', 1)->sum('quantita_residue') }} <small>ore</small></span>
                    </div>
            
                  </div>
                </div>
              </div>
            </div>
          @endif
        @endif
      @endforeach 
    </div>
  </div>
@endif
