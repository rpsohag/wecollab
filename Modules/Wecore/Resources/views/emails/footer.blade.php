<table width="100%" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;">
    <tr style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;">
        <td class="content-block" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; width: 27%;" valign="top">
            <a href="{{ url('/') }}">
                Intranet
                <br>
                {{ session('azienda') }}
            </a>
        </td>
        <td class="content-block" style="width: 33%;">
            &nbsp;
        </td>
        <td class="content-block" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; width: 40%;" valign="top">
            <h4>
                {{ $azienda->ragione_sociale }}
                <br>
                <small style="font-weight: normal;">
                    {{ $azienda->indirizzo }} {{ $azienda->numero_civico }} - {{ $azienda->cap }}, {{ $azienda->citta }} ({{ $azienda->provincia }})
                </small>
            </h4>
            <span>
                Tel:&nbsp;&nbsp;&nbsp;&nbsp; <a href="tel:{{ $azienda->telefono }}">{{ $azienda->telefono }}</a>
                <br>
                Email: <a href="mailto:{{ $azienda->email }}">{{ $azienda->email }}</a>
            </span>
        </td>
    </tr>
</table>
