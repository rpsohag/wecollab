<?php
return [
    'name' => 'Commerciale',

    'segnalazioneopportunita' => [
      'stati' => [
        0 => 'In Attesa Validazione',
        1 => 'Validata dalla Direzione Commerciale',
        2 => 'In Lavorazione',
        3 => 'Rifiutata',
        4 => 'Chiusa',
        5 => 'Offerta Rifiutata'
      ]
    ],

    'ordinativi' => [
      'documenti' => [
        0 => 'Contratti',
        1 => 'Check-list controllo conversioni',
        2 => 'Analisi di Dettaglio',
        3 => 'Collaudo Conversioni',
        4 => 'Programma formativo',
        5 => 'Comunicazioni'
      ],
      'voci' => [
        'categorie' => [
          0 => 'SELEZIONA',
          1 => 'CDM (Canoni di manutenzione)',
          2 => 'CANONI DA SERVIZI CONSULENZIALI',
          3 => 'CANONI DA SISTEMISTICA',
          4 => 'ATTIVAZIONI PARCO',
          5 => 'ATTIVAZIONI PROSPECT',
          6 => 'SERVIZI URBI',
          7 => 'SERVIZI CONSULENZIALI',
          8 => 'SERVIZI SISTEMISTICI',
          9 => 'FORNITURA HARDWARE E SOFTWARE TERZE PARTI',
          10 => 'ALTRO'
        ]
      ],
    ],

    'censimenticlienti' => [
      'fasce_abitanti' => [
        0 => '',
        1 => 'da 500 a 5.000',
        2 => 'da 5.001 a 10.000',
        3 => 'da 10.001 a 25.000',
        4 => 'da 25.001 e oltre'
      ],

      'pianta_organica' => [
        1 => 'Amministratori',
        2 => 'Responsabili',
        3 => 'Posizioni Organizzative',
        4 => 'Istruttori',
        5 => 'Altro'
      ],
        'altri_software' => [
            0 => '',
            1 => 'Halley',
            2 => 'Maggioli (sicra)',
            3 => 'Maggioli (sicraweb)',
            4 => 'Ascot',
            5 => 'StudioK / APK',
            6 => 'Civilia (deltadator)',
            7 => 'Sintecop',
            8 => 'AP System',
            9 => 'Altro (necessaria analisi)',
            10 => 'Urbi'
        ]
    ],

    'offerte' => [
        'stati' => [
            -1 => 'Bozza',
            0 => 'In Lavorazione',
            4 => 'Da Inviare',
            5 => 'Inviata',
            //6 => 'Da Posticipare',
            //7 => 'Da Modificare',
            //8 => 'Non Risposta',
            1 => 'Accettata',
            2 => 'Rifiutata',
            3 => 'Scaduta'
            ],
        'approvazioni' => [
            1 => 'amministrazione',
            2 => 'direttore_pa',
            3 => 'direttore_tecnico',
            4 => 'direttore_commerciale'
            ],
        'iva' => '22',
        'filter_allegati' => [
            '',
            'Senza Determina',
            'Senza ODA',
            'Senza Offerta Definitiva',
            'Con Determina',
            'Con ODA',
            'Con Offerta Definitiva'
        ]
    ],

    'fatturazioni' => [
        'We-COM' => [
          'iban' => [
              'IT06C0885172860000000032657' => 'Banca TEMA - IT06C0885172860000000032657',
              'IT37Y0605539040000000001705' => 'Banca Marche anticipi - IT37Y0605539040000000001705',
              'IT29S0605539040000000001611' => 'Banca Marche 2 - IT29S0605539040000000001611',
              'IT13U0885172860000000033633' => 'Banca TEMA Nuovo IBAN - IT13U0885172860000000033633',
              'IT07P0538714500000002571144' => 'BPER Banca spa - IT07P0538714500000002571144',
              'IT97Y0320502000000000000070' => 'Banca IFIS - IT97Y0320502000000000000070'
          ]
        ],
        'Digit Consulting' => [
          'iban' => [
              'IT84Z0873072860000000032657' => 'BCC - IT84Z0873072860000000032657',
              'IT37Y0605539040000000001705' => 'Banca Marche anticipi - IT37Y0605539040000000001705',
              'IT29S0605539040000000001611' => 'Banca Marche 2 - IT29S0605539040000000001611',
              'IT47D0885171920000000061725' => 'BCC Digit Consulting - IT47D0885171920000000061725'
          ],
        ],
        'macrocategorie' =>[
          1 => 'Consulenza',
          2 => 'Sistemistico',
          3 => 'Sviluppo',
          4 => 'Urbi',
          5 => 'Altro',
          6=> 'No',
        ],
        'tipologia_fornitura' =>[
            '',
            'Servizi',
            'Hardware'
        ],
        'n_giorni' =>[
            0,
            '30' => 30,
            '60' => 60,
            '90' => 90,
            '120' => 120,
            '150' => 150,
            '180' => 180,
            '240' => 240
        ],
        'tipologia_pagamento' =>[
            '',
            'Data Fattura',
            'Data Fatturazione Fine Mese',
            'Rimessa Diretta'
        ],
        'tipologia_pagamento_abbreviazione' =>[
            '',
            'DF',
            'DFFM',
            'RD'
        ],
        'anticipata' =>[
            '',
            'Anticipata BCC',
            'Anticipata'
        ],
        'stati' => [
            'scaduta' => 'Scaduta',
            'pagata' => 'Pagata',
            'consegnata' => 'Consegnata',
            'anticipata' => 'Anticipata',
            'default' => 'Emessa'
        ],
        'iva_tipi' => [
            '',
            '4%',
            '5%',
            '10%',
            '22%',
            'N1.1' => '0% - N1 - Escluso Art. 15 DPR 633/72',
            'N2.1' => '0% - N2.1 - Non soggette ad IVA ai sensi degli artt. Da 7 a 7-septies del DPR 633/72',
            'N2.2' => '0% - N2.2 - Non soggette - altri casi',
            'N3.1' => '0% - N3.1 - Non Imponibile assimilate alle esportazioni art. 8-bis DPR 633/72',
            'N3.1' => '0% - N3.1 - Non Imponibile esportazioni art. 8, c. 1, Let. A) DPR 633/72',
            'N3.2' => '0% - N3.2 - Non Imponibile cessioni intracomunitarie Art. 41 DL 331/93',
            'N3.3' => '0% - N3.3 - Non Imponibile cessioni verso San Marino e Citta del Vaticano Art. 71 Dpr 633/72',
            'N3.4' => '0% - N3.4 - Non imponibile operazioni assimilate alle cessioni all\'esportazione',
            'N3.5' => '0% - N3.5 - Non Imponibile a seguito dichiarazioni d\'intento Art. 8, c. 1, Let. C) DPR 633/72',
            'N3.6' => '0% - N3.6 - Non imponibile altre operazioni che non concorrono alla formazione del plafond',
            'N4.1' => '0% - N4 - Esente Art. 10 DPR 633/72',
            'N4.2' => '0% - N4 - Esente art.124 c.2 DL34/20 (operazioni contenimento Covid)',
            'N5.1' => '0% - N5 - Escluso Art. 74 DPR 633/72',
            'N5.2' => '0% - N5 - Regime del margine Art. 36 41/95',
            'N6.1' => '0% - N6.1 - Reverse charge Art. 74 vendita rottami e materiali di recupero DPR 633/72',
            'N6.2' => '0% - N6.2 - Reverse charge cessione di oro e argento puro Art. 17, c. 5  DPR 633/72',
            'N6.3' => '0% - N6.3 - Reverse charge subappalto nel settore edile Art. 17, c. 6 lett. a), DPR 633/72',
            'N6.4' => '0% - N6.4 - Reverse charge cessione di fabbricati Art. 17, c. 6 lett. a-bis) DPR 633/72',
            'N6.5' => '0% - N6.5 - Reverse charge cessione di telefoni cellulari Art. 17, c. 6 lett. b) DPR 633/72',
            'N6.6' => '0% - N6.6 - Reverse charge cessione di prodotti elettronici Art. 17, c. 6 lett. c) DPR 633/72',
            'N6.7' => '0% - N6.7 - Reverse charge prestazioni comparto edile e settori connessi Art. 17, c. 6 lett. a-ter) DPR 633/72',
            'N6.8' => '0% - N6.8 - Reverse charge op. settore energetico Art. 17, c. 6, lett. d-bis), d-ter), d-quater) DPR 633/72',
            'N6.9' => '0% - N6.9 - 0% - N6.9 - Reverse charge altri casi',
            'N7.1' => '0% - N7 - Vendite a distanza Art. 40/41 DL 331/93',

            'N1_1' => '0% - N1 - Escluso Art. 15 DPR 633/72 (OLD - NON USARE)',
            'N2_1' => '0% - N2 - Escluso Art. 13 5C DPR 633/72 (OLD - NON USARE)',
            'N2_2' => '0% - N2 - Fuori campo IVA Art. 2 DPR 633/72 (OLD - NON USARE)',
            'N2_3' => '0% - N2 - Fuori campo IVA Art. 3 DPR 633/72 (OLD - NON USARE)',
            'N2_4' => '0% - N2 - Fuori campo IVA Art. 4 DPR 633/72 (OLD - NON USARE)',
            'N2_5' => '0% - N2 - Fuori campo IVA Art. 5 DPR 633/72 (OLD - NON USARE)',
            'N2_6' => '0% - N2 - Fuori campo IVA Art. 7 DPR 633/72 (OLD - NON USARE)',
            'N2_7' => '0% - N2 - Regime dei minimi Art. 1 L. 244/2007 (OLD - NON USARE)',
            'N2_8' => '0% - N2 - Regime forfettario Art. 1 L. 190/2014 (OLD - NON USARE)',
            'N3_1' => '0% - N3 - Non Imponibile Art. 41 DL 331/93 (OLD - NON USARE)',
            'N3_2' => '0% - N3 - Non Imponibile Art. 74 DPR 633/72 (OLD - NON USARE)',
            'N3_3' => '0% - N3 - Non Imponibile Art. 8 DPR 633/72 (OLD - NON USARE)',
            'N3_4' => '0% - N3 - Non Imponibile Art. 9 DPR 633/72 (OLD - NON USARE)',
            'N3_5' => '0% - N3 - Non Imponibile a seguito di dichiarazioni d\'intento (OLD - NON USARE)',
            'N3_6' => '0% - N3 - Non Imponibile - altre operazioni che non concorrono alla formazione del plafond (OLD - NON USARE)',
            'N4_1' => '0% - N4 - Esente Art. 10 DPR 633/72 (OLD - NON USARE)',
            'N5_1' => '0% - N5 - Escluso Art. 74 DPR 633/72 (OLD - NON USARE)',
            'N5_2' => '0% - N5 - Regime del margine Art. 36 41/95 (OLD - NON USARE)',
            'N6_1' => '0% - N6 - Reverse charge Art. 17 DPR 633/72 (OLD - NON USARE)',
            'N6_2' => '0% - N6 - Reverse charge Art. 74 DPR 633/72 - cessione di oro e argento puro (OLD - NON USARE)',
            'N6_3' => '0% - N6 - Reverse charge Art. 74 DPR 633/72 - subappalto nel settore edile (OLD - NON USARE)',
            'N6_4' => '0% - N6 - Reverse charge Art. 74 DPR 633/72 - cessione di fabbricati (OLD - NON USARE)',
            'N6_5' => '0% - N6 - Reverse charge Art. 74 DPR 633/72 - cessione di telefoni cellulari (OLD - NON USARE)',
            'N6_6' => '0% - N6 - Reverse charge Art. 74 DPR 633/72 - cessione di prodotti elettronici (OLD - NON USARE)',
            'N6_7' => '0% - N6 - Reverse charge Art. 74 DPR 633/72 - prestazioni comparto edile e settori connessi (OLD - NON USARE)',
            'N6_8' => '0% - N6 - Reverse charge Art. 74 DPR 633/72 - operazioni settore energetico (OLD - NON USARE)',
            'N6_9' => '0% - N6 - Reverse charge Art. 74 DPR 633/72 - altri casi (OLD - NON USARE)',
            'N7_1' => '0% - N7 - Vendite a distanza Art. 40/41 DL 331/93 (OLD - NON USARE)'

        ],
        'attivita_svolte' => [
          '' => '',
          'C01' => 'C01 - Analisi, progettazione e sviluppo software',
          'C02' => 'C02 - Outsourcing informatico',
          'C03' => 'C03 - Consulenza informatica',
          'C04' => 'C04 - Progettazione e realizzazione di sistemi informatici "chiavi in mano"',
          'C05' => 'C05 - Installazione e configurazione hardware',
          'C06' => 'C06 - Manutenzione/assistenza/riparazione/helpdesk hardware',
          'C07' => 'C07 - Manutenzione/assistenza/helpdesk software',
          'C08' => 'C08 - Vendita di hardware e materiali di consumo',
          'C09' => 'C09 - Vendita di licenze relative a software di proprietà',
          'C10' => 'C10 - Vendita di licenze relative a software prodotto da terzi',
          'C11' => 'C11 - Acquisizione/elaborazione di dati contabili',
          'C12' => 'C12 - Acquisizione/elaborazione di altri dati',
          'C13' => 'C13 - Gestione di banche dati',
          'C14' => 'C14 - Internet Service Provider (I.S.P.), Application Service Provider (A.S.P.), fornitura di servizi di Software as a Service (SaaS) e servizi di Housing/Hosting/Storage',
          'C15' => 'C15 - Realizzazione e gestione di applicazioni web',
          'C16' => 'C16 - Elaborazioni grafiche',
          'C17' => 'C17 - Noleggio di hardware',
          'C18' => 'C18 - Servizi di e-marketing',
          'C19' => 'C19 - Realizzazione di prodotti multimediali',
          'C20' => 'C20 - Corsi di formazione/aggiornamento',
          'C21' => 'C21 - Altro'
        ]
    ],

    'contratti' => [
        'tipi' => [
				1 => 'Formazione e Aggiornamento del Personale',
                2 => 'Service',
                3 => 'Progetto Formazione del Personale',
                4 => 'Assistenza, configurazione e avvio software',
                5 => 'Consulenza',
                6 => 'Conversioni banca dati'
        ],

        'tipi_pagamento' => [
            0 => '',
            1 => 'Data Fattura',
            2 => 'Data Fatturazione Fine Mese',
            3 => 'Rimessa Diretta'
        ]
    ],

    'interventi' => [
        'tipi' => ['Giornate', 'Ore']
    ],

    'simaziendali' => [
        'tipi' => [
          0 => 'Normale',
          1 => 'E-Sim']
    ]
];
