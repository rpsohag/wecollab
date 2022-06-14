@php
    $azienda = get_azienda_dati();
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>{{ get_if_exist($titolo) }} - {{ session('azienda') }}</title>

    {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
    integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
    crossorigin="anonymous"> --}}
    <link href="{{ asset('themes/admin/vendor/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Titillium+Web" rel="stylesheet">
    <link href="{{ asset('themes/admin/css/style_pdf.css') }}" rel="stylesheet">
    @stack('css-stack')
</head>

<body>
    <header class="row">
        <div class="col-xs-7">
            <h2>{{ get_if_exist($titolo) }}</h2>
        </div>
        <div class="col-xs-4 text-right">
            @if(!empty(get_if_exist($azienda, 'logo')))
                <img src="{{ asset(get_if_exist($azienda, 'logo')) }}" style="height: 35px;">
            @endif
        </div>
        <br><br><br>
        <hr>
    </header>

    <footer class="row">
        @if(get_azienda() == 'we-com')
            <div class="bg-footer">
                <img src="{{ asset('img/w-c/pallini.jpg') }}">
            </div>
        @endif
        <hr style="margin: 0 0 10px; z-index: 999;">
        <div class="col-xs-5">
            <small>
                <strong>Sede Legale</strong>
                <br>
                {{ get_if_exist($azienda, 'indirizzo') }}, {{ get_if_exist($azienda, 'numero_civico') }}
                <br>
                {{ get_if_exist($azienda, 'cap') }} {{ get_if_exist($azienda, 'citta') }} ({{ get_if_exist($azienda, 'provincia') }})
                <br>
                Tel. <a href="tel:{{ get_if_exist($azienda, 'telefono') }}" target="_blank">{{ get_if_exist($azienda, 'telefono') }}</a>
            </small>
        </div>
        <div class="col-xs-3">
            <small>
                <strong>Contatti</strong>
                <br>
                Fax. {{ get_if_exist($azienda, 'telefono') }}
                <br>
                E-mail. <a href="mailto:{{ get_if_exist($azienda, 'email') }}" target="_blank">{{ get_if_exist($azienda, 'email') }}</a>
                <br>
                Web. <a href="{{ get_if_exist($azienda, 'sito_web') }}" target="_blank">{{ get_if_exist($azienda, 'sito_web') }}</a>
            </small>
        </div>
        <div class="col-xs-3 text-center">
            <small>
                <strong>{!! get_if_exist($azienda, 'iso') !!}</strong>
                <br><br>
                @if(!empty(get_if_exist($azienda, 'iso_img')))
                    <img src="{{ asset(get_if_exist($azienda, 'iso_img')) }}" style="height: 40px">
                @endif
            </small>
        </div>
    </footer>

    @yield('content')

</body>
</html>
