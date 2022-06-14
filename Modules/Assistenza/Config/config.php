<?php

return [
    'name' => 'Assistenza',

    'ticket_intervento' => [
        'tipologie' => [
            -1 =>'',
            0=>'Formazione e aggiornamento personale Procedure URBi Smart',
            1=>'Consulenza'
        ],

        'settori' => [
            -1 =>'',
            0 => 'A Pagamento (Scala Giornate : No)',
            1 => 'Compreso nella Fornitura (Scala Giornate : Si)',
            2 => 'Extra Contratto (Scala Giornate : No)',
            3 => 'Service (Scala Giornate : Si)',
            4 => 'Gratuito (Scala Giornate : No)',
            5 => 'Controllo Conversioni (Scala Giornate : No)'
        ]
    ],

    'richieste_intervento' => [
      'livelli_urgenza' => [     
        0 => 'Bassa',
        1 => 'Media',
        2 => 'Alta',
        3 => 'Critica',
      ],

      'richieste_procedure_icon' =>[
        1 => '<i class="text-info fa fa-truck"></i> On site',
        2 => '<i class="text-info fa fa-television"></i> Da remoto'
      ],

      'richieste_procedure' =>[
        1 => 'On site',
        2 => 'Da remoto'
      ],

      'stati' => [
        8 => 'Normale',
        101 => 'Urgente',
        2 => 'Massima urgenza'
      ],

      'azioni' => [
        'tipo' => [
          1 => 'Aperta',
          2 => 'Azione',
          3 => 'Chiudi',
          4 => 'Sospendi',
          5 => 'Riprendi',
          6 => 'Assegna a',
          7 => 'Azione e notifica'
        ],
        'tipi' => [
          2 => '<i class="text-info fa fa-comment"></i> Nota Generica',
          3 => '<i class="text-success fa fa-close"></i> Chiusura',
          4 => '<i class="text-orange fa fa-pause"></i> Sospensione',
          5 => '<i class="text-warning fa fa-play"></i> Ripreso',
          6 => '<i class="text-primary fa fa-users"></i> Assegnazione',
          7 => '<i class="text-primary fa fa-phone"></i> Notifica al Cliente'
        ]
      ]
    ]
];
