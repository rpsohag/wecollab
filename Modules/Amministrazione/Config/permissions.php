<?php

return [
    'amministrazione' => [
        'sidebar'
    ],
    'amministrazione.clienti' => [
        'index' => 'amministrazione::clienti.list resource',
        'create' => 'amministrazione::clienti.create resource',
        'edit' => 'amministrazione::clienti.edit resource',
        'read' => 'amministrazione::clienti.read resource',
        'destroy' => 'amministrazione::clienti.destroy resource',
    ],
    'amministrazione.benistrumentali' => [
        'index' => 'Visualizza tutti i beni IT',
        'create' => 'Crea un nuovo bene IT',
        'edit' => 'Modifica un bene IT',
        'destroy' => 'Elimina un bene IT',
    ],
    // 'amministrazione.clientiindirizzis' => [
    //     'index' => 'amministrazione::clientiindirizzis.list resource',
    //     'create' => 'amministrazione::clientiindirizzis.create resource',
    //     'edit' => 'amministrazione::clientiindirizzis.edit resource',
    //     'destroy' => 'amministrazione::clientiindirizzis.destroy resource',
    // ],
    // 'amministrazione.clientereferentis' => [
    //     'index' => 'amministrazione::clientereferentis.list resource',
    //     'create' => 'amministrazione::clientereferentis.create resource',
    //     'edit' => 'amministrazione::clientereferentis.edit resource',
    //     'destroy' => 'amministrazione::clientereferentis.destroy resource',
    // ],
// append



];
