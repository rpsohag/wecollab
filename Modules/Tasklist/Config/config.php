<?php

return [
    'name' => 'Tasklist',

    'timesheets' => [
        'tipologie' => [
            0 => 'Attività da Remoto',
            1 => 'Attività dal Cliente',
            2 => 'Configurazione',
            3 => 'Formazione da Remoto',
            4 => 'Formazione dal Cliente',
        ]
    ],

    'attivita' =>  [
        'priorita' =>   [0 => 'Bassa', 5 => 'Media', 10 => 'Alta'],
        'priorita_testi' =>   [
            0 => 'Bassa',
            1 => 'Bassa',
            2 => 'Bassa',
            3 => 'Bassa',
            4 => 'Media',
            5 => 'Media',
            6 => 'Media',
            7 => 'Media',
            8 => 'Alta',
            9 => 'Alta',
            10 => 'Alta'
        ],
        'priorita_icone' =>   [
            0 => ['icon' => 'fa fa-exclamation', 'class' => 'text-success'],
            1 => ['icon' => 'fa fa-exclamation', 'class' => 'text-success'],
            2 => ['icon' => 'fa fa-exclamation', 'class' => 'text-success'],
            3 => ['icon' => 'fa fa-exclamation', 'class' => 'text-success'],
            4 => ['icon' => 'fa fa-exclamation-triangle', 'class' => 'text-danger'],
            5 => ['icon' => 'fa fa-exclamation-triangle', 'class' => 'text-danger'],
            6 => ['icon' => 'fa fa-exclamation-triangle', 'class' => 'text-danger'],
            7 => ['icon' => 'fa fa-exclamation-triangle', 'class' => 'text-danger'],
            8 => ['icon' => 'fa fa-exclamation-circle', 'class' => 'text-red'],
            9 => ['icon' => 'fa fa-exclamation-circle', 'class' => 'text-red'],
            10 => ['icon' => 'fa fa-exclamation-circle', 'class' => 'text-red']
        ],
        'durata_tipo' => [
          0 => 'Ore',
          1 => 'Giorni'
        ],
        'stati' => ['In Lavorazione','In Attesa','Completata','Annullata'],
        'stati_icone' => ['fa fa-briefcase','fa fa-pause','fa fa-check','fa fa-times']
    ],

    'rinnovi' => [
        'tipi' => [
            0 => 'Annuale',
            1 => 'Mensile',
            // 2 => 'Settimanale'
        ],
        'types' => [
            0 => 'Y',
            1 => 'm',
            // 2 => 'd'
        ],
    ],

    'notifiche' => [
        'tipi' => [
            0 => 'Minuti prima',
            1 => 'Ore prima',
            2 => 'Giorni prima',
            3 => 'Mesi prima',
            4 => 'Anni prima'
        ],
        'types' => [
            0 => 'minutes',
            1 => 'hours',
            2 => 'days',
            3 => 'months',
            4 => 'years'
        ],
        'notifica' => [
            'email' => 'email',
            'portale' => 'portale'
        ]
    ]
];
