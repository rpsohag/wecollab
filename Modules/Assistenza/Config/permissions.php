<?php

return [
    'assistenza.ticketinterventi' => [
        'index' => 'assistenza::ticketinterventi.list resource',
        'create' => 'assistenza::ticketinterventi.create resource',
        'edit' => 'assistenza::ticketinterventi.edit resource',
        'read' => 'assistenza::ticketinterventi.read resource',
        'exportexcel' => 'Export Excel',
        'destroy' => 'assistenza::ticketinterventi.destroy resource',
    ],
    'assistenza.ambienti' => [
        'index' => 'Visualizza tutti gli ambienti',
    ],
    'assistenza.ambienticonversioni' => [
        'index' => 'Visualizza tutti gli ambienti di conversione',
        'create' => 'Crea un nuovo ambiente di conversione',
        'edit' => 'Modifica un ambiente di conversione',
        'destroy' => 'Elimina un ambiente di conversione',
    ],
    'assistenza.richiesteinterventi' => [
        'index' => 'assistenza::richiesteinterventi.list resource',
        'create' => 'assistenza::richiesteinterventi.create resource',
        'edit' => 'assistenza::richiesteinterventi.edit resource',
        'read' => 'assistenza::richiesteinterventi.read resource',
        'destroy' => 'assistenza::richiesteinterventi.destroy resource',
        'admin' => 'Visualizza tutte le richieste di intervento',
        'urbismart' => 'Visualizza tutte le richieste di intervento di Urbi Smart',
        'areatecnica' => 'Visualizza tutte le richieste di intervento di Area Tecnica',
        'exportexcel' => 'Export Excel',
        'ticketweb' => 'api ticketweb',
    ],
// append


];
