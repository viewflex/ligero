<?php

namespace Viewflex\Ligero\Base;

use Viewflex\Ligero\Contracts\PublisherConfigInterface;

class BasePublisherConfig implements PublisherConfigInterface
{
    /*
    |--------------------------------------------------------------------------
    | Publisher Default Configuration
    |
    | Override these configuration properties in child classes as needed.
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Domain, Resource Namespaces, Translation
    |--------------------------------------------------------------------------
    */

    /**
     * The application or business domain name,
     * used to locate correct views, lang, etc.
     *
     * @var string
     */
    protected $domain = 'Ligero';

    /**
     * Modify this string to get translations from a specific namespace.
     * Namespace registered in the ServiceProvider's loadTranslationsFrom(),
     * or an alternate if the localization files require more segmentation.
     * This namespace will be used in the ls() function transparently,
     * saving the developer from specifying it explicitly everywhere.
     *
     * @var string
     */
    protected $resource_namespace = 'ligero';

    /**
     * Modify this string to get translations from specific file in namespace.
     *
     * @var string
     */
    protected $translation_file = '';

    /**
     * Get domain name.
     * 
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set domain name.
     * 
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * Get resource namespace.
     * 
     * @return string
     */
    public function getResourceNamespace()
    {
        return $this->resource_namespace;
    }

    /**
     * Set resource namespace.
     * 
     * @param string $resource_namespace
     */
    public function setResourceNamespace($resource_namespace)
    {
        $this->resource_namespace = $resource_namespace;
    }

    /**
     * Get translation (lang) file name.
     * 
     * @return string
     */
    public function getTranslationFile()
    {
        return $this->translation_file;
    }

    /**
     * Set translation (lang) file name.
     * 
     * @param string $translation_file
     */
    public function setTranslationFile($translation_file)
    {
        $this->translation_file = $translation_file;
    }

    /**
     * Concatenate the translation namespace and file,
     * for use in views where ls() is not possible.
     * 
     * @return string
     */
    public function getTranslationPrefix()
    {
        $ns = $this->getResourceNamespace() ? ($this->getResourceNamespace().'::') : '';
        $file = $this->getTranslationFile() ? ($this->getTranslationFile().'.') : '';
        return $ns.$file;
    }

    /**
     * Get a localized string, using namespace prefix if configured.
     * Alias for Laravel trans() or trans_choice() helper function,
     * depending on whether a count is supplied for inflection.
     *
     * @param string $key
     * @param null|array|int $option
     * @return string
     */
    public function ls($key, $option = null)
    {
        $prefix = $this->getTranslationPrefix();

        if (!$option)
            return trans($prefix.$key);

        if (is_array($option))
            return trans($prefix.$key, $option);

        if (is_integer($option))
            return trans_choice($prefix.$key, $option);

        return $key;
    }
    
    /**
     * Concatenate resource namespace and domain for view prefix.
     *
     * @return string
     */
    public function getDomainViewPrefix()
    {
        $ns = $this->getResourceNamespace() ? $this->getResourceNamespace().'::' : '';
        return ($ns.strtolower($this->getDomain()).'.');
    }

    /**
     * Get the view name with namespace and domain prefix.
     *
     * @param string $view
     * @return string
     */
    public function getDomainViewName($view)
    {
        return ($this->getDomainViewPrefix().$view);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Config - Table, Model
    |--------------------------------------------------------------------------
    */

    /**
     * The database table we'll use for queries.
     *
     * @var string
     */
    protected $table_name = 'ligero_items';

    /**
     * The model we'll designate to represent an item.
     * If none given, loadModel() uses default.
     *
     * @var string
     */
    protected $model_name = 'Viewflex\Ligero\Base\BaseModel';

    /**
     * Get table name for this domain, from multi-domain tables array if existing,
     * keyed by $this->table_name, using the property itself as a fallback value.
     * 
     * @return string
     */
    public function getTableName()
    {
        $tables = $this->getTables();
        return (array_key_exists($this->table_name, $tables)) ? $tables[$this->table_name] : $this->table_name;
    }

    /**
     * Set table name for this domain.
     * 
     * @param string $table_name
     */
    public function setTableName($table_name)
    {
        $this->table_name = $table_name;
    }

    /**
     * Get model name for this domain, from multi-domain models array if existing,
     * keyed by $this->table_name, using $this->model_name as a fallback value.
     * 
     * @return string
     */
    public function getModelName()
    {
        return (array_key_exists($this->table_name, $this->models)) ? $this->models[$this->table_name] : $this->model_name;
    }

    /**
     * Set model name for this domain.
     * 
     * @param string $model_name
     */
    public function setModelName($model_name)
    {
        $this->model_name = $model_name;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Config - Define Parameters, Results Columns, Wildcard Columns
    |--------------------------------------------------------------------------
    */
    
    /**
     * The list of valid query parameter names, along with respective default values.
     * Override in child class according to inputs expected by request validator.
     *
     * @var array
     */
    protected $query_defaults = [
        'id'            => '',
        'keyword'       => '',
        'sort'          => '',
        'view'          => 'list',
        'limit'         => '',
        'start'         => '0',
        'action'        => '',
        'items'         => '',
        'options'       => '',
        'page'          => '1'
    ];

    /**
     * The lists of columns from which to retrieve data, corresponding to views.
     * The 'default' element is the full list, used if no view is specified.
     * The 'id' column should always be included in the each list.
     *
     * @var array
     */
    protected $results_columns = [
        'default'  => [
            'id'
        ],
        'list'  => [
            'id'
        ],
        'grid'  => [
            'id'
        ],
        'item'  => [
            'id'
        ]
    ];

    /**
     * Perform wildcard search on any of these string columns,
     * when they are among the publisher query parameters.
     * This finds partial rather than exact matches.
     *
     * Note: This array should not include the query parameters
     * 'id' or 'keyword', as they are processed separately.
     *
     * @var array
     */
    protected $wildcard_columns = [];

    /**
     * Get valid query parameters and their defaults.
     * 
     * @return array
     */
    public function getQueryDefaults()
    {
        return $this->query_defaults;
    }

    /**
     * Set valid query parameters and their defaults.
     * 
     * @param array $query_defaults
     */
    public function setQueryDefaults($query_defaults)
    {
        $this->query_defaults = $query_defaults;
    }

    /**
     * Set a single key on the array of valid query parameters and their defaults.
     * 
     * @param string $name
     * @param string $value
     */
    public function setQueryDefault($name, $value)
    {
        $this->query_defaults[$name] = $value;
    }

    /**
     * Get results columns for given view. If view is not specified,
     * or is but does not exist in this array, get 'default' columns.
     *
     * @param string $view
     * @return array
     */
    public function getResultsColumns($view = 'default')
    {
        if (! array_key_exists($view, $this->results_columns))
            $view = 'default';

        return $this->results_columns[$view];
    }

    /**
     * Set results columns for given view, or if not specified,
     * set the 'default' array of results columns with input.
     *
     * @param array $results_columns
     * @param string $view
     */
    public function setResultsColumns($results_columns, $view = 'default')
    {
        $this->results_columns[$view] = $results_columns;
    }

    /**
     * Get list of string columns that get queried in wildcard style.
     *
     * @return array
     */
    public function getWildcardColumns()
    {
        return $this->wildcard_columns;
    }

    /**
     * Set the list of string columns that get queried in wildcard style.
     * 
     * @param array $wildcard_columns
     */
    public function setWildcardColumns($wildcard_columns)
    {
        $this->wildcard_columns = $wildcard_columns;
    }

    /*
    |--------------------------------------------------------------------------
    | Toggle for UI Controls
    |--------------------------------------------------------------------------
    */

    /**
     * Boolean flags for UI controls to be generated.
     *
     * @var array
     */
    protected $controls = [
        'pagination'        => false,
        'keyword_search'    => false
    ];

    /**
     * Get flags for various UI controls.
     * 
     * @return array
     */
    public function getControls()
    {
        return $this->controls;
    }

    /**
     * Set flags for various UI controls.
     * 
     * @param array $controls
     */
    public function setControls($controls)
    {
        $this->controls = $controls;
    }

    /**
     * Get the flag for a single UI control.
     * 
     * @param string $name
     * @return mixed
     */
    public function getControl($name)
    {
        return $this->controls[$name];
    }

    /**
     * Set the flag for a single UI control.
     * 
     * @param string $name
     * @param bool $enabled
     */
    public function setControl($name, $enabled)
    {
        $this->controls[$name] = $enabled;
    }

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    /**
     * Specify which results navigation elements to make, and their
     * respective contexts: 'logical' (pages are aligned with start
     * of results, or 'relative' (pages are aligned with current
     * start row), or (for view menu) 'fresh' (non-contextual).
     *
     * Also specify the maximum number of page links to display,
     * and whether or not to add page number as a URL param.
     *
     * Using the page number in the links (in logical context)
     * can provide extra markers for users and search engines.
     * This makes sense only in logical context, and if page
     * number is provided in the inputs, forcing start to
     * represents the natural starting row of the page.
     *
     * Note: The 'page' input determines 'start' row only
     * if start is not specified. In nav control urls the
     * 'page' param is based on the actual 'start' row.
     *
     * When an element is contextual, it becomes disabled (null)
     * when the current state matches the target of that link,
     * as an indication that the state is already displayed.
     *
     * @var array
     */
    protected $pagination_config = [
        'pager'             =>  [
            'make'              =>  true,
            'context'           =>  'relative',
        ],
        'page_menu'         =>  [
            'make'              =>  true,
            'context'           =>  'relative',
            'max_links'         =>  5
        ],
        'view_menu'         =>  [
            'make'              =>  true,
            'context'           =>  'relative',
        ],
        'use_page_number'   =>  false

    ];

    /**
     * Get pagination configuration.
     *
     * @return array
     */
    public function getPaginationConfig()
    {
        return $this->pagination_config;
    }

    /**
     * Set pagination configuration.
     * 
     * @param array $pagination_config
     */
    public function setPaginationConfig($pagination_config)
    {
        $this->pagination_config = $pagination_config;
    }

    /*
    |--------------------------------------------------------------------------
    | Keyword Search
    |--------------------------------------------------------------------------
    */

    /**
     * Lists string columns to be included in keyword search. Specifies
     * the scope (global|query) and the onchange action of UI menu
     * (null|this.form.submit()|etc), and persistence of sort and
     * view/limit, for the default generated UI keyword search.
     * This parameter targets partial matches, not exact.
     *
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
        'persist_input'         =>  true,
        'on_change'             => 'this.form.submit()'
    ];

    /**
     * Get keyword search configuration.
     *
     * @return array
     */
    public function getKeywordSearchConfig()
    {
        return $this->keyword_search_config;
    }

    /**
     * Set keyword search configuration.
     * 
     * @param array $keyword_search_config
     */
    public function setKeywordSearchConfig($keyword_search_config)
    {
        $this->keyword_search_config = $keyword_search_config;
    }

    /*
    |--------------------------------------------------------------------------
    |  Sorts and View/Limit
    |--------------------------------------------------------------------------
    */

    /**
     * The various named sorts for use in listing query.
     * There should always be at least the 'default'.
     *
     * @var array
     */
    protected $sorts = [
        'default'           => ['id' => 'desc'],
        'id'                => ['id' => 'asc']
    ];

    /**
     * The named views for listing query, with their limits.
     * There should always be a value for the 'default'.
     *
     * @var array
     */
    protected $view_limits = [
        'default'   =>  10,
        'list'      =>  5,
        'grid'      =>  20,
        'item'      =>  1
    ];

    /**
     * Get named sorts.
     * 
     * @return array
     */
    public function getSorts()
    {
        return $this->sorts;
    }

    /**
     * Set named sorts.
     * 
     * @param array $sorts
     */
    public function setSorts($sorts)
    {
        $this->sorts = $sorts;
    }

    /**
     * Return named or default orderBy instructions.
     *
     * @param string $name
     * @return array
     */
    public function getSort($name = 'default')
    {
        return array_key_exists($name, $this->sorts) ? $this->sorts[$name] : $this->sorts['default'];
    }

    /**
     * Get limits for named views.
     * 
     * @return array
     */
    public function getViewLimits()
    {
        return $this->view_limits;
    }

    /**
     * Set limits for named views.
     * 
     * @param array $view_limits
     */
    public function setViewLimits($view_limits)
    {
        $this->view_limits = $view_limits;
    }

    /**
     * Return named or default view limit.
     *
     * @param string $view
     * @return int
     */
    public function getViewLimit($view = 'default')
    {
        return array_key_exists($view, $this->view_limits) ? $this->view_limits[$view] : $this->view_limits['default'];
    }

    /**
     * Set limit for given view, or if not specified,
     * set the 'default' view limit with input.
     *
     * @param int $view_limit
     * @param string $view
     */
    public function setViewLimit($view_limit, $view = 'default')
    {
        $this->view_limits[$view] = $view_limit;
    }

    /*
    |--------------------------------------------------------------------------
    | Global Configuration
    |
    | These properties apply to all child configurations, but of course can be
    | overridden in child classes, or by using their setter methods (below).
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Caching and Logging
    |--------------------------------------------------------------------------
    */

    /**
     * Global caching config.
     *
     * @var array
     */
    protected $caching = [];

    /**
     * Global logging config.
     *
     * @var array
     */
    protected $logging = [];

    /**
     * Get caching config.
     *
     * @return array
     */
    public function getCaching()
    {
        if (! $this->caching) {
            $this->setCaching(config('ligero.caching', [
                'active'        =>  false,
                'minutes'       =>  10
            ]));
        }

        return $this->caching;
    }

    /**
     * Set caching 'active' flag, or pass array including additional specs.
     *
     * @param array|bool $caching
     */
    public function setCaching($caching)
    {
        if (is_array($caching))
            $this->caching = $caching;
        else
            $this->caching['active'] = $caching;
    }

    /**
     * Get logging config.
     *
     * @return array
     */
    public function getLogging()
    {
        if (! $this->logging) {
            $this->setLogging(config('ligero.logging', [
                'active'        =>  false
            ]));
        }

        return $this->logging;
    }

    /**
     * Set logging 'active' flag, or pass array including additional specs.
     *
     * @param array|bool $logging
     */
    public function setLogging($logging)
    {
        if (is_array($logging))
            $this->logging = $logging;
        else
            $this->logging['active'] = $logging;
    }

    /*
    |--------------------------------------------------------------------------
    | General - URL Format, Paths, Options
    |--------------------------------------------------------------------------
    */

    /**
     * Use absolute route in URLs or not?
     *
     * @var bool|null
     */
    protected $absolute_urls = null;
    
    /**
     * Paths to site's 'home' URL, content images, graphics, css, etc.
     *
     * @var array
     */
    protected $paths = [];

    /**
     * Switches for various built-in options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Get absolute_urls config.
     *
     * @return bool
     */
    public function absoluteUrls()
    {
        if (! $this->absolute_urls) {
            $this->setAbsoluteUrls(config('ligero.absolute_urls', false));
        }

        return $this->absolute_urls;
    }

    /**
     * Set absolute_urls config.
     *
     * @param boolean $absolute_urls
     */
    public function setAbsoluteUrls($absolute_urls)
    {
        $this->absolute_urls = $absolute_urls;
    }
    
    /**
     * Get paths config.
     *
     * @return array
     */
    public function getPaths()
    {
        if (! $this->paths) {
            $this->setPaths(config('ligero.paths', []));
        }

        return $this->paths;
    }

    /**
     * Set paths config.
     *
     * @param array $paths
     */
    public function setPaths($paths)
    {
        $this->paths = $paths;
    }

    /**
     * Get path config element by key.
     *
     * @param string $name
     * @return mixed
     */
    public function getPath($name)
    {
        $this->getPaths();

        return $this->paths[$name];
    }

    /**
     * Set path config element by key.
     *
     * @param string $name
     * @param string $path
     */
    public function setPath($name, $path)
    {
        $this->paths[$name] = $path;
    }

    /**
     * Get options config.
     *
     * @return array
     */
    public function getOptions()
    {
        if (! $this->options) {
            $this->setOptions(config('ligero.options', [
                'unit_conversions'  => false
            ]));
        }

        return $this->options;
    }

    /**
     * Set options config.
     *
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * Get options config element by key.
     *
     * @param string $option
     * @return mixed
     */
    public function getOption($option)
    {
        $this->getOptions();

        return $this->options[$option];
    }

    /**
     * Set options config element by key.
     *
     * @param string $name
     * @param string $option
     */
    public function setOption($name, $option)
    {
        $this->options[$name] = $option;
    }

    /*
    |--------------------------------------------------------------------------
    | Unit Formatting and Conversions
    |--------------------------------------------------------------------------
    */

    /**
     * The formatter class to use.
     *
     * @var string
     */
    protected $formatter = null;

    /**
     * Convert primary currency and measurement units to secondary formats?
     * Does not modify data, only provides additional translated values.
     *
     * @var bool|null
     */
    protected $unit_conversions = null;
    
    /**
     * Primary and secondary currencies.
     *
     * @var array
     */
    protected $currencies = [];

    /**
     * Primary and secondary ruler units.
     *
     * @var array
     */
    protected $ruler_units = [];

    /**
     * Primary and secondary weight units.
     *
     * @var array
     */
    protected $weight_units = [];

    /**
     * Get the formatter class.
     *
     * @return string|null
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * Set the formatter class.
     *
     * @param string $formatter
     */
    public function setFormatter($formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * Get unit_conversions config.
     *
     * @return bool
     */
    public function unitConversions()
    {
        if (! $this->unit_conversions) {
            $this->setUnitConversions(config('ligero.unit_conversions', false));
        }

        return $this->unit_conversions;
    }

    /**
     * Set unit_conversions config.
     *
     * @param boolean $unit_conversions
     */
    public function setUnitConversions($unit_conversions)
    {
        $this->unit_conversions = $unit_conversions;
    }
    
    /**
     * Get currencies config.
     *
     * @return array
     */
    public function getCurrencies()
    {
        if (! $this->currencies) {
            $this->setCurrencies(config('ligero.currencies', [
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
                    'name'              =>  'Canadian Dollars',
                    'ISO_code'          =>  'CAD',
                    'prefix'            =>  '$',
                    'suffix'            =>  'CAD',
                    'thousands'         =>  ',',
                    'decimal'           =>  '.',
                    'precision'         =>  2
                ]
            ]));
        }

        return $this->currencies;
    }

    /**
     * Set currencies config.
     *
     * @param array $currencies
     */
    public function setCurrencies($currencies)
    {
        $this->currencies = $currencies;
    }

    /**
     * Get ruler_units config.
     *
     * @return array
     */
    public function getRulerUnits()
    {
        if (! $this->ruler_units) {
            $this->setRulerUnits(config('ligero.ruler_units', [
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
            ]));
        }

        return $this->ruler_units;
    }

    /**
     * Set ruler_units config.
     *
     * @param array $ruler_units
     */
    public function setRulerUnits($ruler_units)
    {
        $this->ruler_units = $ruler_units;
    }

    /**
     * Get weight_units config.
     *
     * @return array
     */
    public function getWeightUnits()
    {
        if (! $this->weight_units) {
            $this->setWeightUnits(config('ligero.weight_units', [
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
            ]));
        }

        return $this->weight_units;
    }

    /**
     * Set weight_units config.
     *
     * @param array $weight_units
     */
    public function setWeightUnits($weight_units)
    {
        $this->weight_units = $weight_units;
    }

    /*
    |--------------------------------------------------------------------------
    | Tables, Models and Contexts for Custom Multi-Domain Implementations
    |
    | * Values from global config take precedence over the domain config.
    |--------------------------------------------------------------------------
    */

    /**
     * The table names used by migrations and all domain database operations.
     * Note: package model classes have same table names as keys, hardcoded,
     * in case we want to use them outside of the Ligero publisher codebase.
     *
     * @var array
     */
    protected $tables = [];

    /**
     * The models representing the package domains.
     * The key representing a domain should be the
     * table name used in the domain config class.
     *
     * @var array
     */
    protected $models = [];

    /**
     * The contexts for package domains, corresponding to api/<package>/{key} route parameter.
     * This allows ContextApiController to load any context for CRUD and context operations,
     * as defined in ContextInterface (and AggregateContextInterface for rich contexts).
     *
     * @var array
     */
    protected $contexts = [];

    /**
     * Get tables for multi-domain config.
     * 
     * @return array
     */
    public function getTables()
    {
        if (! $this->tables) {
            $this->setTables(config('ligero.tables', []));
        }
        
        return $this->tables;
    }

    /**
     * Set tables for multi-domain config.
     * 
     * @param array $tables
     */
    public function setTables($tables)
    {
        $this->tables = $tables;
    }

    /**
     * Get models for multi-domain config.
     * 
     * @return array
     */
    public function getModels()
    {
        if (! $this->models) {
            $this->setModels(config('ligero.models', []));
        }
        
        return $this->models;
    }

    /**
     * Set models for multi-domain config.
     * 
     * @param array $models
     */
    public function setModels($models)
    {
        $this->models = $models;
    }

    /**
     * Get contexts for multi-domain config.
     *
     * @return array
     */
    public function getContexts()
    {
        if (! $this->contexts) {
            $this->setContexts(config('ligero.contexts', []));
        }

        return $this->contexts;
    }

    /**
     * Set contexts for multi-domain config.
     *
     * @param array $contexts
     */
    public function setContexts($contexts)
    {
        $this->contexts = $contexts;
    }
    
}
