<?php

namespace Viewflex\Ligero\Contracts;

interface PublisherConfigInterface
{
    /*
    |--------------------------------------------------------------------------
    | Domain, Resource Namespaces, Translation
    |--------------------------------------------------------------------------
    */

    /**
     * Get domain name.
     *
     * @return string
     */
    public function getDomain();

    /**
     * Set domain name.
     *
     * @param string $domain
     */
    public function setDomain($domain);

    /**
     * Get resource namespace.
     *
     * @return string
     */
    public function getResourceNamespace();

    /**
     * Set resource namespace.
     *
     * @param string $resource_namespace
     */
    public function setResourceNamespace($resource_namespace);

    /**
     * Get translation (lang) file name.
     *
     * @return string
     */
    public function getTranslationFile();

    /**
     * Set translation (lang) file name.
     *
     * @param string $translation_file
     */
    public function setTranslationFile($translation_file);

    /**
     * Concatenate the translation namespace and file,
     * for use in views where ls() is not possible.
     *
     * @return string
     */
    public function getTranslationPrefix();

    /**
     * Get a localized string, using namespace prefix if configured.
     * Alias for Laravel trans() or trans_choice() helper function,
     * depending on whether a count is supplied for inflection.
     *
     * @param string $key
     * @param null|array|int $option
     * @return string
     */
    public function ls($key, $option = null);

    /**
     * Concatenate resource namespace and domain for view prefix.
     *
     * @return string
     */
    public function getDomainViewPrefix();

    /**
     * Get the view name with namespace and domain prefix.
     *
     * @param string $view
     * @return string
     */
    public function getDomainViewName($view);

    /*
    |--------------------------------------------------------------------------
    | Query Config - Table, Model
    |--------------------------------------------------------------------------
    */

    /**
     * Get table name for this domain, from multi-domain tables array if existing,
     * keyed by $this->table_name, using the property itself as a fallback value.
     *
     * @return string
     */
    public function getTableName();

    /**
     * Set table name for this domain.
     *
     * @param string $table_name
     */
    public function setTableName($table_name);

    /**
     * Get model name for this domain, from multi-domain models array if existing,
     * keyed by $this->table_name, using $this->model_name as a fallback value.
     *
     * @return string
     */
    public function getModelName();

    /**
     * Set model name for this domain.
     *
     * @param string $model_name
     */
    public function setModelName($model_name);

    /*
    |--------------------------------------------------------------------------
    | Query Config - Define Parameters, Results Columns, Wildcard Columns
    |--------------------------------------------------------------------------
    */

    /**
     * Get valid query parameters and their defaults.
     *
     * @return array
     */
    public function getQueryDefaults();

    /**
     * Set valid query parameters and their defaults.
     *
     * @param array $query_defaults
     */
    public function setQueryDefaults($query_defaults);

    /**
     * Set a single key on the array of valid query parameters and their defaults.
     *
     * @param string $name
     * @param string $value
     */
    public function setQueryDefault($name, $value);

    /**
     * Get results columns for given view. If view is not specified,
     * or is but does not exist in this array, get 'default' columns.
     *
     * @param string $view
     * @return array
     */
    public function getResultsColumns($view = 'default');

    /**
     * Set results columns for given view, or if not specified,
     * set the 'default' array of results columns with input.
     *
     * @param array $results_columns
     * @param string $view
     */
    public function setResultsColumns($results_columns, $view = 'default');

    /**
     * Get list of string columns that get queried in wildcard style.
     *
     * @return array
     */
    public function getWildcardColumns();

    /**
     * Set the list of string columns that get queried in wildcard style.
     *
     * @param array $wildcard_columns
     */
    public function setWildcardColumns($wildcard_columns);

    /*
    |--------------------------------------------------------------------------
    | Toggle for UI Controls
    |--------------------------------------------------------------------------
    */

    /**
     * Get flags for various UI controls.
     *
     * @return array
     */
    public function getControls();

    /**
     * Set flags for various UI controls.
     *
     * @param array $controls
     */
    public function setControls($controls);

    /**
     * Get the flag for a single UI control.
     *
     * @param string $name
     * @return mixed
     */
    public function getControl($name);

    /**
     * Set the flag for a single UI control.
     *
     * @param string $name
     * @param bool $enabled
     */
    public function setControl($name, $enabled);

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    /**
     * Get pagination configuration.
     *
     * @return array
     */
    public function getPaginationConfig();

    /**
     * Set pagination configuration.
     *
     * @param array $pagination_config
     */
    public function setPaginationConfig($pagination_config);

    /*
    |--------------------------------------------------------------------------
    | Keyword Search
    |--------------------------------------------------------------------------
    */

    /**
     * Get keyword search configuration.
     *
     * @return array
     */
    public function getKeywordSearchConfig();

    /**
     * Set keyword search configuration.
     *
     * @param array $keyword_search_config
     */
    public function setKeywordSearchConfig($keyword_search_config);

    /**
     * Set keyword search columns.
     *
     * @param array $keyword_search_columns
     */
    public function setKeywordSearchColumns($keyword_search_columns);

    /*
    |--------------------------------------------------------------------------
    |  Sorts and View/Limit
    |--------------------------------------------------------------------------
    */

    /**
     * Get named sorts.
     *
     * @return array
     */
    public function getSorts();

    /**
     * Add or replace named sorts.
     *
     * @param array $sorts
     */
    public function setSorts($sorts);

    /**
     * Return named or default orderBy instructions.
     *
     * @param string $name
     * @return array
     */
    public function getSort($name = 'default');

    /**
     * Get limits for named views.
     *
     * @return array
     */
    public function getViewLimits();

    /**
     * Add or replace limits for named views.
     *
     * @param array $view_limits
     */
    public function setViewLimits($view_limits);

    /**
     * Return named or default view limit.
     *
     * @param string $view
     * @return int
     */
    public function getViewLimit($view = 'default');

    /**
     * Add or replace limit for named view, or if
     * not specified, set the 'default' limit.
     *
     * @param int $view_limit
     * @param string $view
     */
    public function setViewLimit($view_limit, $view = 'default');

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
     * Get caching config.
     *
     * @return array
     */
    public function getCaching();

    /**
     * Set caching 'active' flag, or pass array including additional specs.
     *
     * @param array|bool $caching
     */
    public function setCaching($caching);

    /**
     * Get logging config.
     *
     * @return array
     */
    public function getLogging();

    /**
     * Set logging 'active' flag, or pass array including additional specs.
     *
     * @param array|bool $logging
     */
    public function setLogging($logging);

    /*
    |--------------------------------------------------------------------------
    | General - URL Format, Paths, Options
    |--------------------------------------------------------------------------
    */

    /**
     * Get absolute_urls config.
     *
     * @return bool
     */
    public function getAbsoluteUrls();

    /**
     * Set absolute_urls config.
     *
     * @param boolean $absolute_urls
     */
    public function setAbsoluteUrls($absolute_urls);

    /**
     * Get paths config.
     *
     * @return array
     */
    public function getPaths();

    /**
     * Set paths config.
     *
     * @param array $paths
     */
    public function setPaths($paths);

    /**
     * Get path config element by key.
     *
     * @param string $name
     * @return mixed
     */
    public function getPath($name);

    /**
     * Set path config element by key.
     *
     * @param string $name
     * @param string $path
     */
    public function setPath($name, $path);

    /**
     * Get options config.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Set options config.
     *
     * @param array $options
     */
    public function setOptions($options);

    /**
     * Get options config element by key.
     *
     * @param string $option
     * @return mixed
     */
    public function getOption($option);

    /**
     * Set options config element by key.
     *
     * @param string $name
     * @param string $option
     */
    public function setOption($name, $option);

    /*
    |--------------------------------------------------------------------------
    | Unit Formatting and Conversions
    |--------------------------------------------------------------------------
    */

    /**
     * Get the formatter class.
     *
     * @return string|null
     */
    public function getFormatter();

    /**
     * Set the formatter class.
     *
     * @param string $formatter
     */
    public function setFormatter($formatter);

    /**
     * Get unit_conversions config.
     *
     * @return bool
     */
    public function getUnitConversions();

    /**
     * Set unit_conversions config.
     *
     * @param boolean $unit_conversions
     */
    public function setUnitConversions($unit_conversions);
    
    /**
     * Get currencies config.
     *
     * @return array
     */
    public function getCurrencies();

    /**
     * Set currencies config.
     *
     * @param array $currencies
     */
    public function setCurrencies($currencies);

    /**
     * Get ruler_units config.
     *
     * @return array
     */
    public function getRulerUnits();

    /**
     * Set ruler_units config.
     *
     * @param array $ruler_units
     */
    public function setRulerUnits($ruler_units);

    /**
     * Get weight_units config.
     *
     * @return array
     */
    public function getWeightUnits();

    /**
     * Set weight_units config.
     *
     * @param array $weight_units
     */
    public function setWeightUnits($weight_units);

    /*
    |--------------------------------------------------------------------------
    | Tables, Models and Contexts for Custom Multi-Domain Implementations
    |
    | * Values from global config take precedence over the domain config.
    |--------------------------------------------------------------------------
    */

    /**
     * Get tables for multi-domain config.
     *
     * @return array
     */
    public function getTables();

    /**
     * Set tables for multi-domain config.
     *
     * @param array $tables
     */
    public function setTables($tables);

    /**
     * Get models for multi-domain config.
     *
     * @return array
     */
    public function getModels();

    /**
     * Set models for multi-domain config.
     *
     * @param array $models
     */
    public function setModels($models);

    /**
     * Get contexts for multi-domain config.
     *
     * @return array
     */
    public function getContexts();

    /**
     * Set contexts for multi-domain config.
     *
     * @param array $contexts
     */
    public function setContexts($contexts);
    
}
