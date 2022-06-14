<?php

return [
    'name' => 'Amministrazione',

    'beni' => [
        'tipologie' => [
            1 => 'PDL',
            2 => 'Notebook',
            3 => 'Telefono',
            4 => 'Altro'
        ]
    ],

    'clienti' => [
        'name' => 'Clienti',

        'sidebar' => [
            'icon' => 'fa fa-address-book'
        ],

        'tipi' => [
            1 => 'Cliente',
            2 => 'Fornitore',
            3 => 'Cliente/Fornitore',
            4 => 'Solo Censimento Cliente'
        ],

        // 'aree' => [
        //     'pa digitale' => 'PA Digitale',
        //     'em consilia' => 'EM Consilia'
        // ],

        'tipologie' => [
            'pubblico' => 'Pubblico',
            'privato' => 'Privato'
        ],
    ],

    'ruoli' => [
        'direttore_tecnico' => 'Direttore tecnico',
        'direttore_pa' => 'Direttore PA',
        'direttore_commerciale' => 'Direttore commerciale',
        'amministrazione' => 'Amministrazione',
        'segreteria_commerciale' => 'Segreteria commerciale',
        'segreteria_amministrativa' => 'Segreteria amministrativa'
    ],

    'ruoli_approvazione' => [
        'direttore_tecnico' => 'Direttore tecnico',
        'direttore_pa' => 'Direttore PA',
        'direttore_commerciale' => 'Direttore commerciale',
        'amministrazione' => 'Amministrazione'
    ]
];
