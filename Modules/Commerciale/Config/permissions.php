<?php
return [
    'commerciale.filtri' => [
        'cliente' => 'Filtro Clienti'
    ],
    'commerciale.offerte' => [
        'index' => 'commerciale::offerte.list resource',
        'create' => 'commerciale::offerte.create resource',
        'edit' => 'commerciale::offerte.edit resource',
        'read' => 'commerciale::offerte.read resource',
        'destroy' => 'commerciale::offerte.destroy resource',
    ],
    'commerciale.ordinativi' => [
        'index' => 'commerciale::ordinativi.list resource',
        'create' => 'commerciale::ordinativi.create resource',
        'edit' => 'commerciale::ordinativi.edit resource',
        'edit.home' => 'commerciale::ordinativi.edit home',
        'edit.vocieconomiche' => 'Modifica voci economiche ordinativo',
        'edit.attivita' => 'commerciale::ordinativi.edit attivita',
        'edit.assistenza' => 'Modifica assistenza ordinativo',
        'edit.rinnovi' => 'commerciale::ordinativi.edit rinnovi',
        'edit.interventi' => 'commerciale::ordinativi.edit interventi',
        'edit.scadenzefatturazioni' => 'commerciale::ordinativi.edit scadenze_fatturazioni',
        'edit.documenti' => 'commerciale::ordinativi.edit documenti',
        'read' => 'commerciale::ordinativi.read resource',
        'read.home' => 'commerciale::ordinativi.read home',
        'read.vocieconomiche' => 'Visualizza voci economiche ordinativo',
        'read.attivita' => 'commerciale::ordinativi.read attivita',
        'read.assistenza' => 'Visualizza assistenza ordinativo',
        'read.rinnovi' => 'commerciale::ordinativi.read rinnovi',
        'read.interventi' => 'commerciale::ordinativi.read interventi',
        'read.richiesteassistenza' => 'commerciale::ordinativi.read richieste_assistenza',
        'read.scadenzefatturazioni' => 'commerciale::ordinativi.read scadenze_fatturazioni',
        'read.riepilogocommessa' => 'commerciale::ordinativi.read riepilogo_commessa',
        'read.documenti' => 'commerciale::ordinativi.read documenti',
        'read.quadroavanzamento' => 'commerciale::ordinativi.read quadro avanzamento',
        'export.sal' => 'Export SAL Ordinativi',
        'destroy' => 'commerciale::ordinativi.destroy resource',
    ],
    'commerciale.fatturazioni' => [
        'index' => 'commerciale::fatturazioni.list resource',
        'create' => 'commerciale::fatturazioni.create resource',
        'edit' => 'commerciale::fatturazioni.edit resource',
        'read' => 'commerciale::fatturazioni.read resource',
        'destroy' => 'commerciale::fatturazioni.destroy resource',
    ],
    'commerciale.analisivendite' => [
        'index' => 'commerciale::analisivendite.list resource',
        'create' => 'commerciale::analisivendite.create resource',
        'read' => 'commerciale::analisivendite.read resource',
        'edit' => 'commerciale::analisivendite.edit resource',
        'exportexcel' => 'commerciale::analisivendite.exportexcel resource',
        'destroy' => 'commerciale::analisivendite.destroy resource',
    ],
    'commerciale.censimenticlienti' => [
        'index' => 'commerciale::censimenticlienti.list resource',
        'create' => 'commerciale::censimenticlienti.create resource',
        'read' => 'commerciale::censimenticlienti.read resource',
        'edit' => 'commerciale::censimenticlienti.edit resource',
        'destroy' => 'commerciale::censimenticlienti.destroy resource',
    ],
    'commerciale.segnalazioniopportunita' => [
        'index' => 'commerciale::segnalazioniopportunita.list resource',
        'create' => 'commerciale::segnalazioniopportunita.create resource',
        'read' => 'commerciale::segnalazioniopportunita.read resource',
        'edit' => 'commerciale::segnalazioniopportunita.edit resource',
        'destroy' => 'commerciale::segnalazioniopportunita.destroy resource',
        'restore' => 'commerciale::segnalazioniopportunita.restore resource',
        'notify' => 'commerciale::segnalazioniopportunita.notify resource',
        'disconnect_censimento' => 'commerciale::segnalazioniopportunita.disconnect_censimento resource',
    ],
    'commerciale.simaziendalis' => [
        'index' => 'commerciale::simaziendalis.list resource',
        'read' => 'commerciale::simaziendalis.read resource',
        'create' => 'commerciale::simaziendalis.create resource',
        'edit' => 'commerciale::simaziendalis.edit resource',
        'destroy' => 'commerciale::simaziendalis.destroy resource',
    ],
// append








];
