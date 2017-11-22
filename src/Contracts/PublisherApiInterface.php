<?php

namespace Viewflex\Ligero\Contracts;

use Viewflex\Ligero\Contracts\PublisherConfigInterface as Config;
use Viewflex\Ligero\Contracts\PublisherRequestInterface as Request;
use Viewflex\Ligero\Contracts\PublisherRepositoryInterface as Query;

interface PublisherApiInterface
{
    /*
    |--------------------------------------------------------------------------
    | Component Objects
    |--------------------------------------------------------------------------
    */
    
    /**
     * Returns the config used by API.
     *
     * @return Config
     */
    public function getConfig();

    /**
     * Returns the request used by API.
     *
     * @return Request
     */
    public function getRequest();

    /**
     * Returns the query used by API.
     *
     * @return Query
     */
    public function getQuery();

    /*
    |--------------------------------------------------------------------------
    | Initialization
    |--------------------------------------------------------------------------
    */
    
    /**
     * Sets the query used by API.
     *
     * @param Query $query
     */
    public function setQuery($query);

    /*
    |--------------------------------------------------------------------------
    | Output Bundles
    |--------------------------------------------------------------------------
    */
    
    /**
     * Returns info on API query and results.
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
     * Returns data for pagination UI controls and labels.
     * All URL parameters persist, only start changes,
     * unless of course we're switching view mode.
     *
     * Because user might conceivably input a start value that does not
     * correspond to a logical page start, we must offer two flavors
     * of pager links and page tally string - logical and relative.
     *
     * With relative navigation (relative to current start),
     * number of pages in page list can vary, but the first
     * page always starts at 0. Links are set to null if
     * they should be disabled, ie: we're already there.
     *
     * Switching view can be logical, relative or fresh.
     *
     * Page links with targets matching the current page and
     * context will be returned with null values for urls.
     *
     * @return mixed
     */
    public function getPagination();

    /**
     * Get API keyword query parameters and config. The persist_keyword
     * config determines whether input gets re-used as a prompt in
     * the composed UI control. All the query and display params
     * to be used in form are returned as parameters array.
     *
     * @return mixed
     */
    public function getKeywordSearch();
    
    /**
     * Returns all API data bundles together, along with query info.
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
     * Returns all publisher data bundles together, along with query info.
     *
     * @return array
     */
    public function presentData();
    
    /*
    |--------------------------------------------------------------------------
    | Query Input Parameters
    |--------------------------------------------------------------------------
    */

    /**
     * Returns current route as absolute or relative URL, without query parameters.
     *
     * @return string
     */
    public function getRoute();
    
    /**
     * Returns, in a fixed order, the validated user inputs, plus any existing
     * defaults applied where user inputs are empty. This array is used in db
     * queries, unlike $url_base_parameters, which is used to build URLs.
     *
     * @return array
     */
    public function getQueryParameters();

    /**
     * Returns the current query keyword.
     *
     * @return string
     */
    public function getQueryKeyword();

    /**
     * Returns name of current item view mode.
     *
     * @return string
     */
    public function getQueryView();
    
    /**
     * Returns sort as used in listing query.
     *
     * @return array
     */
    public function getQuerySort();

    /**
     * Returns limit as used in listing query.
     *
     * @return int
     */
    public function getQueryLimit();

    /**
     * Returns start as used in listing query.
     *
     * @return int
     */
    public function getQueryStart();

    /**
     * Returns, in a fixed order, the base query parameters for generating URL query
     * strings in breadcrumbs, query and sort menus, so we use no default parameters,
     * only the inputs (if not the same as defaults), including sort, view and limit.
     * Start is skipped, since we use these parameters to generate fresh queries.
     *
     * @return array
     */
    public function getUrlBaseParameters();

    /*
    |--------------------------------------------------------------------------
    | Utility Functions
    |--------------------------------------------------------------------------
    */

    /**
     * Returns, in a fixed order, the validated user inputs, plus any existing
     * defaults applied where user inputs are empty. This array is used in db
     * queries for store(), update(), and delete() methods, using POST rules.
     *
     * @return array
     */
    public function getRequestParameters();
    
    /**
     * Strips this API's particular query parameters that don't reference actual columns.
     *
     * @param array $parameters
     * @return array
     */
    public function dbQueryParameters($parameters = []);
        
    /**
     * Returns, in a fixed order, all query parameters for generating URL query strings.
     * The optional parameter $skip allows filtering of undesired parameters.
     *
     * @param array $skip
     * @return array
     */
    public function getUrlParametersExcept($skip = []);
    
    /**
     * Returns a URL query string from input array.
     *
     * @param array $parameters
     * @return string
     */
    public function urlQueryString($parameters = []);

    /**
     * Returns the current URL, with parameters properly ordered.
     *
     * @return string
     */
    public function urlSelf();

    /*
    |--------------------------------------------------------------------------
    | Query Actions
    |--------------------------------------------------------------------------
    */
    
    /**
     * Stores a new item using POST parameters.
     *
     * @return int
     */
    public function store();

    /**
     * Updates an existing item using POST parameters.
     *
     * @return int
     */
    public function update();

    /**
     * Deletes an existing item using POST parameters.
     *
     * @return int
     */
    public function delete();

    /**
     * Calls the appropriate db query, returning number of rows affected.
     * Also sets the session's 'message' attribute for visual feedback.
     * Actions supported by base repository are 'clone' and 'delete' -
     * extend the base class to support custom list actions required.
     * This method can be used to transparently perform action on
     * selected list items and then redirect back to listing.
     * 
     * The $options parameter can be used to pass through
     * any additional data required by the given action.
     *
     * @return int
     */
    public function action();
    
}
