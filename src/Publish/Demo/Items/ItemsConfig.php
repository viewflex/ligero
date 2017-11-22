<?php

namespace Viewflex\Ligero\Publish\Demo\Items;

use Viewflex\Ligero\Base\BasePublisherConfig;

class ItemsConfig extends BasePublisherConfig
{
    /*
    |--------------------------------------------------------------------------
    | Domain, Resource Namespaces, Translation
    |--------------------------------------------------------------------------
    */

    /**
     * @var string
     */
    protected $domain = 'Items';

    /**
     * @var string
     */
    protected $resource_namespace = 'ligero';

    /**
     * @var string
     */
    protected $translation_file = 'items';

    /*
    |--------------------------------------------------------------------------
    | Query Config - Table, Model
    |--------------------------------------------------------------------------
    */

    /**
     * @var string
     */
    protected $table_name = 'ligero_items';

    /**
     * @var string
     */
    protected $model_name = 'Viewflex\Ligero\Publish\Demo\Items\Item';
    
    /*
    |--------------------------------------------------------------------------
    | Query Config - Define Parameters, Results Columns, Wildcard Columns
    |--------------------------------------------------------------------------
    */

    /**
     * @var array
     */
    protected $query_defaults = [
        'id'            => '',
        'active'        => '',
        'name'          => '',
        'category'      => '',
        'subcategory'   => '',

        'keyword'       => '',
        'sort'          => '',
        'view'          => 'list',
        'limit'         => '',
        'start'         => '',
        'action'        => '',
        'items'         => '',
        'options'       => '',
        'page'          => ''
    ];

    /**
     * @var array
     */
    protected $results_columns = [
        'default'  => [
            'id',
            'active',
            'name',
            'category',
            'subcategory',
            'description',
            'price'
        ]
    ];

    /**
     * @var array
     */
    protected $wildcard_columns = [
        'category'
    ];

    /*
    |--------------------------------------------------------------------------
    | Toggle for UI Controls
    |--------------------------------------------------------------------------
    */

    /**
     * @var array
     */
    protected $controls = [
        'pagination'        => true,
        'keyword_search'    => true
    ];
    
    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    /**
     * @var array
     */
    protected $pagination_config = [
        'pager'             =>  [
            'make'              =>  true,
            'context'           =>  'logical',
        ],
        'page_menu'         =>  [
            'make'              =>  true,
            'context'           =>  'logical',
            'max_links'         =>  5
        ],
        'view_menu'         =>  [
            'make'              =>  true,
            'context'           =>  'logical',
        ],
        'use_page_number'   =>  true

    ];
    
    /*
    |--------------------------------------------------------------------------
    | Keyword Search
    |--------------------------------------------------------------------------
    */

    /**
     * @var array
     */
    protected $keyword_search_config = [
        'columns'               =>  [
            'name',
            'category',
            'subcategory',
            'description'
        ],
        'scope'                 =>  'query',
        'persist_sort'          =>  true,
        'persist_view'          =>  true,
        'persist_input'         =>  false,
        'on_change'             => 'this.form.submit()'
    ];

    /*
    |--------------------------------------------------------------------------
    |  Sorts and View/Limit
    |--------------------------------------------------------------------------
    */

    /**
     * @var array
     */
    protected $sorts = [
        'default'           => ['id' => 'asc'],
        'id'                => ['id' => 'asc', 'name' => 'asc'],
        'name'              => ['name' => 'asc', 'id' => 'asc'],
        'category'          => ['category' => 'asc', 'id' => 'asc'],
        'subcategory'       => ['subcategory' => 'asc', 'id' => 'asc'],
        'price'             => ['price' => 'asc', 'id' => 'asc']
    ];

    /**
     * @var array
     */
    protected $view_limits = [
        'default'   =>  10,
        'list'      =>  5,
        'grid'      =>  20,
        'item'      =>  1
    ];

    /*
    |--------------------------------------------------------------------------
    | Caching and Logging
    |--------------------------------------------------------------------------
    */

    /**
     * @var array
     */
    protected $caching = [
        'active'        =>  false,
        'minutes'       =>  10
    ];

    /**
     * @var array
     */
    protected $logging = [
        'active'        =>  true
    ];

    /*
    |--------------------------------------------------------------------------
    | General - Paths, Options
    |--------------------------------------------------------------------------
    */

    /**
     * @var bool
     */
    protected $absolute_urls = false;
    
    /**
     * @var array
     */
    protected $paths = [
        'home'              => '/',
        'images'            => 'images/content/',
        'graphics'          => 'images/graphics/',
        'css'               => 'css/',
    ];

    /**
     * @var array
     */
    protected $options = [];

    /*
    |--------------------------------------------------------------------------
    | Unit Formatting and Conversions
    |--------------------------------------------------------------------------
    */

    /**
     * @var bool
     */
    protected $unit_conversions = true;

    /**
     * @var array
     */
    protected $currencies = [
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
    ];
    
}
