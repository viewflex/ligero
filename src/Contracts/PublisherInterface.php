<?php

namespace Viewflex\Ligero\Contracts;

use Viewflex\Ligero\Contracts\PublisherApiInterface as Api;
use Viewflex\Ligero\Contracts\PublisherConfigInterface as Config;
use Viewflex\Ligero\Contracts\PublisherRepositoryInterface as Query;
use Viewflex\Ligero\Contracts\PublisherRequestInterface as Request;

interface PublisherInterface
{
    /*
    |--------------------------------------------------------------------------
    | Component Objects
    |--------------------------------------------------------------------------
    */

    /**
     * Returns the API used by publisher.
     * 
     * @return Api
     */
    public function getApi();

    /**
     * Sets the API used by publisher.
     * 
     * @param Api $api
     */
    public function setApi($api);
    
    /**
     * Returns the config used by publisher.
     *
     * @return Config
     */
    public function getConfig();

    /**
     * Returns the request used by publisher.
     *
     * @return Request
     */
    public function getRequest();

    /**
     * Returns the query used by publisher.
     *
     * @return Query
     */
    public function getQuery();
    
    /*
    |--------------------------------------------------------------------------
    | API Data
    |--------------------------------------------------------------------------
    */
    
    /**
     * Returns info on query and results.
     *
     * @return mixed
     */
    public function getQueryInfo();

    /**
     * Returns total number of records that would be found
     * if we were using publisher query without a limit.
     *
     * @return int
     */
    public function found();

    /**
     * Returns the number of records actually returned by publisher query.
     *
     * @return int
     */
    public function displayed();
    
    /**
     * Returns the results of listing query in native format.
     *
     * @return mixed
     */
    public function getResults();

    /**
     * Returns the results of listing query as array or null.
     *
     * @return mixed
     */
    public function getItems();

    /**
     * Returns API data for pagination UI controls and labels.
     *
     * @return mixed
     */
    public function getPagination();

    /**
     * Get API keyword query parameters and config. The persist_keyword
     * config determines whether input gets re-used as a prompt in
     * the generated UI control. All other params are returned.
     *
     * @return mixed
     */
    public function getKeywordSearch();

    /**
     * Returns all publisher API data bundles together, along with query info.
     *
     * @return array
     */
    public function getData();

    /*
    |--------------------------------------------------------------------------
    | Dynamically Formatted Results
    |--------------------------------------------------------------------------
    */

    /**
     * Returns processed results of listing query as array.
     *
     * @return array|null
     */
    public function presentItems();

    /**
     * Returns all publisher API data bundles together, along with query info.
     *
     * @return array
     */
    public function presentData();


    /*
    |--------------------------------------------------------------------------
    | CRUD Actions
    |--------------------------------------------------------------------------
    */

    /**
     * Returns the results of publisher query on id in native or array format.
     *
     * @param int $id
     * @param bool $native
     * @return mixed
     */
    public function find($id, $native = true);

    /**
     * Returns the results of publisher query in native or array format, or null.
     *
     * @param array $inputs
     * @param bool $native
     * @return mixed
     */
    public function findBy($inputs = [], $native = true);

    /**
     * Store an item, using explicit or pre-configured request inputs, returning new id.
     *
     * @param null|array $inputs
     * @return int
     */
    public function store($inputs = null);

    /**
     * Update an item, using explicit or pre-configured request inputs, returning number of rows affected.
     *
     * @param null|array $inputs
     * @return int
     */
    public function update($inputs = null);

    /**
     * Delete an item, using explicit or pre-configured request input, returning number of rows affected.
     *
     * @param null|int $id
     * @return int
     */
    public function delete($id = null);

    /*
    |--------------------------------------------------------------------------
    | Publisher Multi-Record List Actions
    |--------------------------------------------------------------------------
    */
    
    /**
     * Calls the appropriate db query, returning number of rows affected.
     * Also sets the session's 'message' attribute for visual feedback.
     * Actions supported by base repository are 'clone' and 'delete' -
     * extend the base class to support custom list actions required.
     * This method can be used to transparently perform action on
     * selected list items and then redirect back to listing.
     *
     * @param mixed $inputs
     * @return int
     */
    public function action($inputs = null);

    /*
    |--------------------------------------------------------------------------
    | Utility Functions
    |--------------------------------------------------------------------------
    */

    /**
     * Returns the current URL, with parameters properly ordered.
     * Non-query action and items (array) params are skipped.
     *
     * @return string
     */
    public function urlSelf();

    /**
     * Validates inputs against the rules of this publisher instance.
     *
     * @param array $inputs
     * @return bool
     */
    public function inputsAreValid($inputs = []);

    /**
     * Get localized string via trans() or trans_choice() based on domain configuration.
     *
     * @param string $key
     * @param null|array|int $option
     * @return string
     */
    public function ls($key, $option = null);
    
}
