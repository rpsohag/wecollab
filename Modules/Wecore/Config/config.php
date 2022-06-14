<?php

return [
    'name' => 'WeCore',
    'sn'  => [0 => 'NO' , 1 => 'SI'],

    'aziende' => [
        'we-com' => 'We-COM',
        'digit-consulting' => 'Digit Consulting'
    ],

    'nazioni' => [
      'sigle' => [
        'ITALIA' => 'IT',
        'IT' => 'IT'
      ]
    ],

    'mesi' => [
        'Gennaio',
        'Febbraio',
        'Marzo',
        'Aprile',
        'Maggio',
        'Giugno',
        'Luglio',
        'Agosto',
        'Settembre',
        'Ottobre',
        'Novembre',
        'Dicembre'
    ],

	//ALTRO
	'richieste' => [
		'tipologie_richieste'=>[
			1 => 'Ferie',
			2=>'Permesso',
			3=>'Malattia',
			4=>'Rimborsi Trasferte / Generici',
			5=>'Rimborso Chilometrico'
		],
		'tipologie_permessi' => [1 => 'Normale',2=>'104',3=>'Donazione Sangue'],
		'mesi' => [1=>"Gennaio" , 2=>"Febbraio", 3=>"Marzo", 4=>"Aprile", 5=>"Maggio", 6=>"Giugno", 7=>"Luglio", 8=>"Agosto", 9=>"Settembre", 10=>"Ottobre", 11=>"Novembre", 12=>"Dicembre"],
		'sedi' => [
				"viale dei caduti nella guerra di liberazione, 446, Roma"=>"We-Com Sede di Roma",
				"via dei Prefetti , 46 , Roma"=>"We-Com Sede Prefetti",
				"via Papa Giovanni XXI, 23, Viterbo"=>"We-Com Sede di Viterbo",
				"strada N 18, 8 , Giarre"=>"We-Com Sede di Giarre"
			],
		'tipi_trasferte' =>	[1=>'Trasferte in giornata',2=>'Trasferte con pernottamento',3=>"Albergo",4=>"Biglietto Treno",5=>"Pasti",6=>"Autostrada",7=>"Carburante",8=>"Ricarica Telefonica",9=>"Acquisto Apparati",10=>"Varie"],
		],

    'log' => [
      'description' => [
        'created' => 'creato',
        'updated' => 'modificato',
        'destroyed' => 'eliminato',
        'rejected' => 'rifiutato'
      ]
    ],

    'pagination' => [
      'limit' => 20
    ]
];
