<?php

namespace Viewflex\Ligero\Publishers;

/**
 * Convenient fluent configuration of Publisher components.
 */
trait HasFluentConfiguration
{

    ## ----- Domain, Resource Namespaces, Translation
    
    /**
     * @param string $domain
     * @return $this
     */
    public function setDomain($domain)
    {
        $this->getConfig()->setDomain($domain);
        return $this;
    }

    /**
     * @param string $resource_namespace
     * @return $this
     */
    public function setResourceNamespace($resource_namespace)
    {
        $this->getConfig()->setResourceNamespace($resource_namespace);
        return $this;
    }

    /**
     * @param string $translation_file
     * @return $this
     */
    public function setTranslationFile($translation_file)
    {
        $this->getConfig()->setTranslationFile($translation_file);
        return $this;
    }
    
    
    ## ----- Table, Model

    /**
     * @param string $table_name
     * @return $this
     */
    public function setTableName($table_name)
    {
        $this->getConfig()->setTableName($table_name);
        $this->getQuery()->loadModel();
        return $this;
    }

    /**
     * @param string $model_name
     * @return $this
     */
    public function setModelName($model_name)
    {
        $this->getConfig()->setModelName($model_name);
        $this->getQuery()->loadModel();
        return $this;
    }


    ## ----- Query Parameters, Results Columns, Wildcard Columns

    /**
     * @param array $query_defaults
     * @return $this
     */
    public function setQueryDefaults($query_defaults)
    {
        $this->getConfig()->setQueryDefaults($query_defaults);
        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function setQueryDefault($name, $value)
    {
        $this->getConfig()->setQueryDefault($name, $value);
        return $this;
    }

    /**
     * @param array $results_columns
     * @param string $view
     * @return $this
     */
    public function setResultsColumns($results_columns, $view = 'default')
    {
        $this->getConfig()->setResultsColumns($results_columns, $view);
        return $this;
    }

    /**
     * @param array $wildcard_columns
     * @return $this
     */
    public function setWildcardColumns($wildcard_columns)
    {
        $this->getConfig()->setWildcardColumns($wildcard_columns);
        return $this;
    }
    
    
    ## ----- Toggle for UI Controls

    /**
     * @param array $controls
     * @return $this
     */
    public function setControls($controls)
    {
        $this->getConfig()->setControls($controls);
        return $this;
    }

    /**
     * @param string $name
     * @param bool $enabled
     * @return $this
     */
    public function setControl($name, $enabled)
    {
        $this->getConfig()->setControl($name, $enabled);
        return $this;
    }
    
    
    ## ----- Pagination

    /**
     * @param array $pagination_config
     * @return $this
     */
    public function setPaginationConfig($pagination_config)
    {
        $this->getConfig()->setPaginationConfig($pagination_config);
        return $this;
    }
    
    
    ## ----- Keyword Search

    /**
     * @param array $keyword_search_config
     * @return $this
     */
    public function setKeywordSearchConfig($keyword_search_config)
    {
        $this->getConfig()->setKeywordSearchConfig($keyword_search_config);
        return $this;
    }

    /**
     * @param array $keyword_search_columns
     * @return $this
     */
    public function setKeywordSearchColumns($keyword_search_columns)
    {
        $this->getConfig()->setKeywordSearchColumns($keyword_search_columns);
        return $this;
    }

    
    ## ----- Sorts and View/Limit
    
    /**
     * @param array $sorts
     * @return $this
     */
    public function setSorts($sorts)
    {
        $this->getConfig()->setSorts($sorts);
        return $this;
    }

    /**
     * @param array $view_limits
     * @return $this
     */
    public function setViewLimits($view_limits)
    {
        $this->getConfig()->setViewLimits($view_limits);
        return $this;
    }

    /**
     * @param int $view_limit
     * @param string $view
     * @return $this
     */
    public function setViewLimit($view_limit, $view = 'default')
    {
        $this->getConfig()->setViewLimit($view_limit, $view);
        return $this;
    }
    

    ## ----- Caching and Logging

    /**
     * @param array|bool $caching
     * @return $this
     */
    public function setCaching($caching)
    {
        $this->getConfig()->setCaching($caching);
        return $this;
    }

    /**
     * @param array|bool $logging
     * @return $this
     */
    public function setLogging($logging)
    {
        $this->getConfig()->setLogging($logging);
        return $this;
    }
    
    
    ## ----- General - URL Format, Paths, Options
    
    /**
     * @param boolean $absolute_urls
     * @return $this
     */
    public function setAbsoluteUrls($absolute_urls)
    {
        $this->getConfig()->setAbsoluteUrls($absolute_urls);
        return $this;
    }

    /**
     * @param array $paths
     * @return $this
     */
    public function setPaths($paths)
    {
        $this->getConfig()->setPaths($paths);
        return $this;
    }

    /**
     * @param string $name
     * @param string $path
     * @return $this
     */
    public function setPath($name, $path)
    {
        $this->getConfig()->setPath($name, $path);
        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->getConfig()->setOptions($options);
        return $this;
    }

    /**
     * @param string $name
     * @param string $option
     * @return $this
     */
    public function setOption($name, $option)
    {
        $this->getConfig()->setOption($name, $option);
        return $this;
    }
    
    
    ## ----- Unit Formatting and Conversions

    /**
     * @param string $formatter
     * @return $this
     */
    public function setFormatter($formatter)
    {
        $this->getConfig()->setFormatter($formatter);
        return $this;
    }

    /**
     * @param boolean $unit_conversions
     * @return $this
     */
    public function setUnitConversions($unit_conversions)
    {
        $this->getConfig()->setUnitConversions($unit_conversions);
        return $this;
    }

    /**
     * @param array $currencies
     * @return $this
     */
    public function setCurrencies($currencies)
    {
        $this->getConfig()->setCurrencies($currencies);
        return $this;
    }

    /**
     * @param array $ruler_units
     * @return $this
     */
    public function setRulerUnits($ruler_units)
    {
        $this->getConfig()->setRulerUnits($ruler_units);
        return $this;
    }

    /**
     * @param array $weight_units
     * @return $this
     */
    public function setWeightUnits($weight_units)
    {
        $this->getConfig()->setWeightUnits($weight_units);
        return $this;
    }
    
    
    ## ----- Tables, Models and Contexts (supporting Multi-Domain Implementations)

    /**
     * @param array $tables
     * @return $this
     */
    public function setTables($tables)
    {
        $this->getConfig()->setTables($tables);
        return $this;
    }

    /**
     * @param array $models
     * @return $this
     */
    public function setModels($models)
    {
        $this->getConfig()->setModels($models);
        return $this;
    }

    /**
     * @param array $contexts
     * @return $this
     */
    public function setContexts($contexts)
    {
        $this->getConfig()->setContexts($contexts);
        return $this;
    }

    
    ## ----- Rules for GET and POST Validations

    /**
     * @param array $query_rules
     * @return $this
     */
    public function setQueryRules($query_rules)
    {
        $this->getRequest()->setQueryRules($query_rules);
        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function setQueryRule($name, $value)
    {
        $this->getRequest()->setQueryRule($name, $value);
        return $this;
    }

    /**
     * @param array $request_rules
     * @return $this
     */
    public function setRequestRules($request_rules)
    {
        $this->getRequest()->setRequestRules($request_rules);
        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function setRequestRule($name, $value)
    {
        $this->getRequest()->setRequestRule($name, $value);
        return $this;
    }

    /**
     * @param array $inputs
     * @return $this
     */
    public function setInputs($inputs = [])
    {
        $this->getRequest()->setInputs($inputs);
        return $this;
    }

    /**
     * @param array $inputs
     * @return $this
     */
    public function mergeInputs($inputs = [])
    {
        $this->getRequest()->mergeInputs($inputs);
        return $this;
    }
    
    
    ## ----- Inputs for Action, Items, and Options

    /**
     * @param string $action
     * @return string
     * @return $this
     */
    public function setAction($action = '')
    {
        $this->getRequest()->setAction($action);
        return $this;
    }

    /**
     * @param array $items
     * @return string
     * @return $this
     */
    public function setActionItems($items = [])
    {
        $this->getRequest()->setActionItems($items);
        return $this;
    }

    /**
     * @param array $options
     * @return string
     * @return $this
     */
    public function setActionOptions($options = [])
    {
        $this->getRequest()->setActionOptions($options);
        return $this;
    }


    ## ----- Column Mapping

    /**
     * @param array $column_map
     * @return $this
     */
    public function setColumnMap($column_map)
    {
        $this->getQuery()->setColumnMap($column_map);
        return $this;
    }
    
}
