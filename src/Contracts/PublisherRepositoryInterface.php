<?php

namespace Viewflex\Ligero\Contracts;

use Viewflex\Ligero\Contracts\PublisherApiInterface as Api;
use Viewflex\Ligero\Exceptions\PublisherRepositoryException;

interface PublisherRepositoryInterface
{
    /*
    |--------------------------------------------------------------------------
    | Initialization
    |--------------------------------------------------------------------------
    */

    /**
     * Used in publisher queries to initialize model and (optionally) set it's table name.
     * If table name is not configured, default is lowercase plural of model class name.
     */
    public function loadModel();
    
    /**
     * Set publisher api, extract config and request, load model from config.
     *
     * @param Api $api
     */
    public function setApi(Api $api);

    /*
    |--------------------------------------------------------------------------
    | Database Read Operations
    |--------------------------------------------------------------------------
    */

    /**
     * Returns total number of records that would be found
     * if we were using publisher query without a limit.
     *
     * @return int
     * @throws PublisherRepositoryException
     */
    public function found();

    /**
     * Returns the number of records actually returned by publisher query.
     *
     * @return int
     * @throws PublisherRepositoryException
     */
    public function displayed();
    
    /**
     * Returns the results of publisher query as a collection, or null.
     *
     * @return mixed
     * @throws PublisherRepositoryException
     */
    public function getResults();

    /**
     * Returns the results of publisher query as an array, or null.
     *
     * @return mixed
     * @throws PublisherRepositoryException
     */
    public function getItems();
    
    /*
    |--------------------------------------------------------------------------
    |  Database Write Operations
    |--------------------------------------------------------------------------
    */

    /**
     * Stores a new item.
     *
     * @return int
     * @throws PublisherRepositoryException
     */
    public function store();

    /**
     * Updates an existing item.
     *
     * @return int
     * @throws PublisherRepositoryException
     */
    public function update();

    /**
     * Deletes an existing item, 'soft-deleting' if table is so configured.
     *
     * @return int
     * @throws PublisherRepositoryException
     */
    public function delete();

    /*
    |--------------------------------------------------------------------------
    | Publisher Multi-Record List Actions
    |--------------------------------------------------------------------------
    */
    
    /**
     * Calls the appropriate db query, returning number of rows affected.
     * This is a method you will want to override in extended classes
     * to provide additional list actions using request parameters
     * set and validated in request or explicitly in publisher.
     *
     * @return int
     * @throws PublisherRepositoryException
     */
    public function action();
    
    /*
    |--------------------------------------------------------------------------
    | Mapping Query Parameters to Database Columns
    |--------------------------------------------------------------------------
    */

    /**
     * Gets column mapping array.
     * 
     * @return array
     */
    public function getColumnMap();

    /**
     * Sets column mapping array.
     * 
     * @param array $column_map
     */
    public function setColumnMap($column_map);

    /**
     * Returns input or mapped column name if configured.
     *
     * @param string $key
     * @return string
     */
    public function mapColumn($key);

    /**
     * Returns array of attributes with keys mapped.
     *
     * @param array $attributes
     * @return array
     */
    public function mapAttributes($attributes = []);

    /**
     * Returns input or reverse mapped column alias if configured.
     *
     * @param string $value
     * @return string
     */
    public function rmapColumn($value);
    
}
