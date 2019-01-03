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
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getResourceNamespace()
    {
        return $this->resource_namespace;
    }

    /**
     * @param string $resource_namespace
     */
    public function setResourceNamespace($resource_namespace)
    {
        $this->resource_namespace = $resource_namespace;
    }

    /**
     * @return string
     */
    public function getTranslationFile()
    {
        return $this->translation_file;
    }

    /**
     * @param string $translation_file
     */
    public function setTranslationFile($translation_file)
    {
        $this->translation_file = $translation_file;
    }

    /**
     * @return string
     */
    public function getTranslationPrefix()
    {
        $ns = $this->getResourceNamespace() ? ($this->getResourceNamespace().'::') : '';
        $file = $this->getTranslationFile() ? ($this->getTranslationFile().'.') : '';
        return $ns.$file;
    }

    /**
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
     * @return string
     */
    public function getDomainViewPrefix()
    {
        $ns = $this->getResourceNamespace() ? $this->getResourceNamespace().'::' : '';
        return ($ns.strtolower($this->getDomain()).'.');
    }

    /**
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
     * @return string
     */
    public function getTableName()
    {
        $tables = $this->getTables();
        return (array_key_exists($this->table_name, $tables)) ? $tables[$this->table_name] : $this->table_name;
    }

    /**
     * @param string $table_name
     */
    public function setTableName($table_name)
    {
        $this->table_name = $table_name;
    }

    /**
     * @return string
     */
    public function getModelName()
    {
        $models = $this->getModels();
        return (array_key_exists($this->table_name, $models)) ? $models[$this->table_name] : $this->model_name;
    }

    /**
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
        'start'         => '',
        'action'        => '',
        'items'         => '',
        'options'       => '',
        'page'          => ''
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
     * @return array
     */
    public function getQueryDefaults()
    {
        return $this->query_defaults;
    }

    /**
     * @param array $query_defaults
     */
    public function setQueryDefaults($query_defaults)
    {
        $this->query_defaults = $query_defaults;
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function setQueryDefault($name, $value)
    {
        $this->query_defaults[$name] = $value;
    }

    /**
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
     * @param array $results_columns
     * @param string $view
     */
    public function setResultsColumns($results_columns, $view = 'default')
    {
        $this->results_columns[$view] = $results_columns;
    }

    /**
     * @return array
     */
    public function getWildcardColumns()
    {
        return $this->wildcard_columns;
    }

    /**
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
     * @return array
     */
    public function getControls()
    {
        return $this->controls;
    }

    /**
     * @param array $controls
     */
    public function setControls($controls)
    {
        $this->controls = $controls;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getControl($name)
    {
        return $this->controls[$name];
    }

    /**
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

    /**
     * @return array
     */
    public function getPaginationConfig()
    {
        return $this->pagination_config;
    }

    /**
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
        'columns'               =>  [],
        'scope'                 =>  'query',
        'persist_sort'          =>  true,
        'persist_view'          =>  true,
        'persist_input'         =>  false,
        'on_change'             => 'this.form.submit()'
    ];

    /**
     * @return array
     */
    public function getKeywordSearchConfig()
    {
        return $this->keyword_search_config;
    }

    /**
     * @param array $keyword_search_config
     */
    public function setKeywordSearchConfig($keyword_search_config)
    {
        $this->keyword_search_config = $keyword_search_config;
    }

    /**
     * @param array $keyword_search_columns
     */
    public function setKeywordSearchColumns($keyword_search_columns)
    {
        $this->keyword_search_config['columns'] = $keyword_search_columns;
    }

    /*
    |--------------------------------------------------------------------------
    |  Sorts and View/Limit
    |--------------------------------------------------------------------------
    */

    /**
     * The various named sorts for use in publisher query.
     * There should always be at least the 'default'.
     *
     * @var array
     */
    protected $sorts = [
        'default'           => ['id' => 'asc']
    ];

    /**
     * The named views for publisher query, with their limits.
     * There should always be at least the 'default'.
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
     * @return array
     */
    public function getSorts()
    {
        return $this->sorts;
    }

    /**
     * @param array $sorts
     */
    public function setSorts($sorts)
    {
        foreach ($sorts as $name => $value) {
            $this->sorts[$name] = $value;
        }
    }

    /**
     * @param string $name
     * @return array
     */
    public function getSort($name = 'default')
    {
        return array_key_exists($name, $this->sorts) ? $this->sorts[$name] : $this->sorts['default'];
    }

    /**
     * @return array
     */
    public function getViewLimits()
    {
        return $this->view_limits;
    }

    /**
     * @param array $view_limits
     */
    public function setViewLimits($view_limits)
    {
        foreach ($view_limits as $name => $value) {
            $this->view_limits[$name] = $value;
        }
    }

    /**
     * @param string $view
     * @return int
     */
    public function getViewLimit($view = 'default')
    {
        return array_key_exists($view, $this->view_limits) ? $this->view_limits[$view] : $this->view_limits['default'];
    }

    /**
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
    protected $caching;

    /**
     * Global logging config.
     *
     * @var array
     */
    protected $logging;

    /**
     * @return array
     */
    public function getCaching()
    {
        if (! isset($this->caching)) {
            $this->setCaching(config('ligero.caching', [
                'active'        =>  false,
                'minutes'       =>  10
            ]));
        }

        return $this->caching;
    }

    /**
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
     * @return array
     */
    public function getLogging()
    {
        if (! isset($this->logging)) {
            $this->setLogging(config('ligero.logging', [
                'active'        =>  false
            ]));
        }

        return $this->logging;
    }

    /**
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
     * @var bool
     */
    protected $absolute_urls;
    
    /**
     * Paths to site's 'home' URL, content images, graphics, css, etc.
     *
     * @var array
     */
    protected $paths;

    /**
     * Switches for various built-in options.
     *
     * @var array
     */
    protected $options;

    /**
     * @return bool
     */
    public function getAbsoluteUrls()
    {
        if (! isset($this->absolute_urls)) {
            $this->setAbsoluteUrls(config('ligero.absolute_urls', false));
        }

        return $this->absolute_urls;
    }

    /**
     * Alias for getAbsoluteUrls().
     *
     * @return bool
     */
    public function absoluteUrls()
    {
        return $this->absoluteUrls();
    }

    /**
     * @param boolean $absolute_urls
     */
    public function setAbsoluteUrls($absolute_urls)
    {
        $this->absolute_urls = $absolute_urls;
    }
    
    /**
     * @return array
     */
    public function getPaths()
    {
        if (! isset($this->paths)) {
            $this->setPaths(config('ligero.paths', []));
        }

        return $this->paths;
    }

    /**
     * @param array $paths
     */
    public function setPaths($paths)
    {
        $this->paths = $paths;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getPath($name)
    {
        $this->getPaths();

        return $this->paths[$name];
    }

    /**
     * @param string $name
     * @param string $path
     */
    public function setPath($name, $path)
    {
        $this->paths[$name] = $path;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        if (! isset($this->options)) {
            $this->setOptions(config('ligero.options', []));
        }

        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @param string $option
     * @return mixed
     */
    public function getOption($option)
    {
        $this->getOptions();

        return $this->options[$option];
    }

    /**
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
    protected $formatter;

    /**
     * Convert primary currency and measurement units to secondary formats?
     * Does not modify data, only provides additional translated values.
     *
     * @var bool
     */
    protected $unit_conversions;
    
    /**
     * Primary and secondary currencies.
     *
     * @var array
     */
    protected $currencies;

    /**
     * Primary and secondary ruler units.
     *
     * @var array
     */
    protected $ruler_units;

    /**
     * Primary and secondary weight units.
     *
     * @var array
     */
    protected $weight_units;

    /**
     * @return string
     */
    public function getFormatter()
    {
        if (! isset($this->formatter)) {
            $this->setFormatter(config('ligero.formatter', ''));
        }
        
        return $this->formatter;
    }

    /**
     * @param string $formatter
     */
    public function setFormatter($formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * @return bool
     */
    public function getUnitConversions()
    {
        if (! isset($this->unit_conversions)) {
            $this->setUnitConversions(config('ligero.unit_conversions', false));
        }

        return $this->unit_conversions;
    }

    /**
     * Alias for getUnitConversions()
     *
     * @return bool
     */
    public function unitConversions()
    {
        return $this->getUnitConversions();
    }

    /**
     * @param boolean $unit_conversions
     */
    public function setUnitConversions($unit_conversions)
    {
        $this->unit_conversions = $unit_conversions;
    }
    
    /**
     * @return array
     */
    public function getCurrencies()
    {
        if (! isset($this->currencies)) {
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
                    'name'              =>  'EU Euros',
                    'ISO_code'          =>  'EUR',
                    'prefix'            =>  '',
                    'suffix'            =>  '€',
                    'thousands'         =>  '.',
                    'decimal'           =>  ',',
                    'precision'         =>  2
                ]
            ]));
        }

        return $this->currencies;
    }

    /**
     * @param array $currencies
     */
    public function setCurrencies($currencies)
    {
        $this->currencies = $currencies;
    }

    /**
     * @return array
     */
    public function getRulerUnits()
    {
        if (! isset($this->ruler_units)) {
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
     * @param array $ruler_units
     */
    public function setRulerUnits($ruler_units)
    {
        $this->ruler_units = $ruler_units;
    }

    /**
     * @return array
     */
    public function getWeightUnits()
    {
        if (! isset($this->weight_units)) {
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
    protected $tables;

    /**
     * The models representing the package domains.
     * The key representing a domain should be the
     * table name used in the domain config class.
     *
     * @var array
     */
    protected $models;

    /**
     * The contexts for package domains, corresponding to api/<package>/{key} route parameter.
     * This allows ContextApiController to load any context for CRUD and context operations,
     * as defined in ContextInterface (and AggregateContextInterface for rich contexts).
     *
     * @var array
     */
    protected $contexts;

    /**
     * @return array
     */
    public function getTables()
    {
        if (! isset($this->tables)) {
            $this->setTables(config('ligero.tables', []));
        }
        
        return $this->tables;
    }

    /**
     * @param array $tables
     */
    public function setTables($tables)
    {
        $this->tables = $tables;
    }

    /**
     * @return array
     */
    public function getModels()
    {
        if (! isset($this->models)) {
            $this->setModels(config('ligero.models', []));
        }
        
        return $this->models;
    }

    /**
     * @param array $models
     */
    public function setModels($models)
    {
        $this->models = $models;
    }

    /**
     * @return array
     */
    public function getContexts()
    {
        if (! isset($this->contexts)) {
            $this->setContexts(config('ligero.contexts', []));
        }

        return $this->contexts;
    }

    /**
     * @param array $contexts
     */
    public function setContexts($contexts)
    {
        $this->contexts = $contexts;
    }
    
}
