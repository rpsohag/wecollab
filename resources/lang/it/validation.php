<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'Il campo deve essere accettato.',
    'active_url'           => 'Il campo non è un URL valido.',
    'after'                => 'Il campo deve essere una data successiva a :date.',
    'alpha'                => 'Il campo può contenere solo lettere.',
    'alpha_dash'           => 'Il campo può contenere solo lettere , numeri , caratteri speciali',
    'alpha_num'            => 'Il campo può contenere solo lettere e numeri.',
    'array'                => 'Il campo deve essere un Array.',
    'before'               => 'Il campo deve essere una data precedente a :date.',
    'between'              => [
        'numeric' => 'Il campo deve essere compreso da :min a :max.',
        'file'    => 'Il campo deve essere compreso da :min a :max kilobytes.',
        'string'  => 'Il campo deve essere compreso da :min a :max caratteri',
        'array'   => 'Il campo deve essere compreso da :min a :max elementi.',
    ],
    'boolean'              => 'Il campo deve essere si o no.',
    'confirmed'            => 'Il campo di conferma non corrisponde.',
    'date'                 => 'Il campo non corretta.',
    'date_format'          => 'Il campo Il formato non è valido , usare : :format.',
    'different'            => 'Il campo e :other non devono essere uguali.',
    'digits'               => 'Il campo deve essere di :digits cifre.',
    'digits_between'       => 'Il campo deve essere compreso da :min a :max cifre.',
    'distinct'             => 'Il campo non deve essere già presente.',
    'email'                => 'Il campo deve contenere un indirizzo e-mail valido.',
    'exists'               => 'La selezione non è valida.',
    'filled'               => 'Il campo è obbligatorio.',
    'image'                => 'Il campo deve essere un immagine.',
    'in'                   => 'La selezione non è valida.',
    'in_array'             => 'Il campo non esiste in :other.',
    'integer'              => 'Il campo deve essere un numero intero.',
    'ip'                   => 'Il campo deve essere un IP address.',
    'json'                 => 'Il campo deve essere una stringa JSON.',
    'max'                  => [
        'numeric' => 'Il campo non deve avere più di :max.',
        'file'    => 'Il campo non deve avere più di :max kilobytes.',
        'string'  => 'Il campo non deve avere più di :max caratteri.',
        'array'   => 'Il campo non deve avere più di :max elementi.',
    ],
    'mimes'                => 'Il campo deve essere un tipo di file: :values.',
    'min'                  => [
        'numeric' => 'Deve essere inserito almeno un valore.',
        'file'    => 'Il campo deve avere almeno :min kilobytes.',
        'string'  => 'Il campo deve avere almeno :min caratteri.',
        'array'   => 'Il campo deve avere almeno :min elementi.',
    ],
    'not_in'               => 'La selezione non è valida.',
    'numeric'              => 'Il campo deve essere numerico.',
    'present'              => 'Il campo è obbligatorio.',
    'regex'                => 'Il campo ha un formato non valido',
    'required'             => 'Il campo è obbligatorio.',
    'required_if'          => 'Il campo è obbligatorio quando :other è :value.',
    'required_unless'      => 'Il campo è obbligatorio tranne quando :other è in :values.',
    'required_with'        => 'Il campo è obbligatorio quando :values è presente.',
    'required_with_all'    => 'Il campo è obbligatorio quando :values è presente.',
    'required_without'     => 'Il campo è obbligatorio quando :values non è presente.',
    'required_without_all' => 'Il campo è obbligatorio quando nessun :values è presente.',
    'same'                 => 'Il campo e :other devono essere uguali.',
    'size'                 => [
        'numeric' => 'Il campo non deve superare la lunghezza di :size.',
        'file'    => 'Il file non deve superare :size kilobytes.',
        'string'  => 'Il campo può contenere fino a :size caratteri.',
        'array'   => 'Il campo può contenere fino a :size elementi.',
    ],
    'string'               => 'Il campo deve essere un testo.',
    'timezone'             => 'Il campo deve essere un timezone valido.',
    'unique'               => 'Il campo deve essere univoco.',
    'url'                  => 'Il campo non è un url.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
