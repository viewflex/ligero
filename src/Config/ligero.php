<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Ligero Global Configuration Defaults
    |
    | These settings provide the defaults for Ligero's global configuration,
    | and can be overridden by setter methods of PublisherConfigInterface.
    | See BasePublisherConfig, parent of all domain configurations, for an
    | explanation of how each configuration property affects operations.
    |--------------------------------------------------------------------------
    */
    
    /*
    |--------------------------------------------------------------------------
    | Caching and Logging
    |--------------------------------------------------------------------------
    */

    'caching'           => [
        'active'        =>  true,
        'minutes'       =>  10
    ],
    
    'logging'           => true,

    /*
    |--------------------------------------------------------------------------
    | General - URL Format, Paths, Options
    |--------------------------------------------------------------------------
    */

    'absolute_urls'     => false,
    
    'paths'             => [],
    
    'options'           => [],

    /*
    |--------------------------------------------------------------------------
    | Unit Formatting and Conversions
    |--------------------------------------------------------------------------
    */

    'formatter'         => '',
    
    'unit_conversions'  => false,
    
    'currencies'        => [
        'primary'           =>  [
            'name'              =>  'US Dollars',
            'ISO_code'          =>  'USD',
            'prefix'            =>  '$',
            'suffix'            =>  '',
            'thousands'         =>  ',',
            'decimal'           =>  '.',
            'precision'         =>  2
        ],
        'secondary'         =>  [
            'name'              =>  'EU Euros',
            'ISO_code'          =>  'EUR',
            'prefix'            =>  '',
            'suffix'            =>  '€',
            'thousands'         =>  '.',
            'decimal'           =>  ',',
            'precision'         =>  2
        ]
    ],
    
    'ruler_units'       => [
        'primary'           =>  [
            'name'              =>  'inches',
            'symbol'            =>  'in',
            'suffix'            =>  '&quot;',
            'precision'         =>  0
        ],
        'secondary'         =>  [
            'name'              =>  'centimeters',
            'symbol'            =>  'cm',
            'suffix'            =>  'cm',
            'precision'         =>  0
        ]
    ],
    
    'weight_units'      => [
        'primary'           =>  [
            'name'              =>  'pound',
            'symbol'            =>  'lb',
            'suffix'            =>  'lb',
            'precision'         =>  0
        ],
        'secondary'         =>  [
            'name'              =>  'kilogram',
            'symbol'            =>  'kg',
            'suffix'            =>  'kg',
            'precision'         =>  1
        ]
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Tables, Models and Contexts for Custom Multi-Domain Implementations
    |
    | These tables, models and contexts are keyed by a domain's $table_name.
    | If present here, they will override values set in individual domains.
    | See ContextApiController to understand usage of the contexts array.
    |--------------------------------------------------------------------------
    */

    'tables'            => [],

    'models'            => [],

    'contexts'          => []

];
