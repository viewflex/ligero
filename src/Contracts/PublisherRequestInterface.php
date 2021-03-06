<?php

namespace Viewflex\Ligero\Contracts;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

interface PublisherRequestInterface
{
    /*
    |--------------------------------------------------------------------------
    | Rules for GET and POST Validations
    |--------------------------------------------------------------------------
    */
    
    /**
     * Get query or request rules, based on http request method.
     * 
     * By returning a non-empty array here, we trigger
     * validation when using the Laravel FormRequest,
     * before the controller loads. Note: As of L5.4,
     * session is no longer accessible in boot phase.
     *
     * @return array
     */
    public function rules();

    /**
     * Get custom and reserved query rules.
     *
     * @return array
     */
    public function getQueryRules();

    /**
     * Set custom query rules, maintaining reserved rules.
     *
     * @param array $query_rules
     */
    public function setQueryRules($query_rules);

    /**
     * Set a specific query rule.
     *
     * @param string $name
     * @param string $value
     */
    public function setQueryRule($name, $value);

    /**
     * Get custom and reserved request rules.
     *
     * @return array
     */
    public function getRequestRules();

    /**
     * Set custom request rules, maintaining reserved rules.
     *
     * @param array $request_rules
     */
    public function setRequestRules($request_rules);

    /**
     * Set a specific request rule.
     *
     * @param string $name
     * @param string $value
     */
    public function setRequestRule($name, $value);

    /*
    |--------------------------------------------------------------------------
    | Inputs for GET Queries
    |--------------------------------------------------------------------------
    */
    
    /**
     * Get the cleaned, non-empty query parameters from input.
     *
     * @return array
     */
    public function getQueryInputs();

    /**
     * Returns an input filtered though getQueryInputs(), or '' if not found.
     *
     * @param string $key
     * @return mixed|string
     */
    public function getQueryInput($key = '');

    /*
    |--------------------------------------------------------------------------
    | Inputs for GET Queries or POST Requests
    |--------------------------------------------------------------------------
    */
    
    /**
     * Gets all the inputs for current method (GET|POST).
     *
     * @return array
     */
    public function getInputs();
        
    /**
     * Replace original inputs with new array provided.
     *
     * @param array $inputs
     */
    public function setInputs($inputs = []);

    /**
     * Merge new array provided with original inputs.
     *
     * @param array $inputs
     */
    public function mergeInputs($inputs = []);
    
    /**
     * Replace certain characters in string.
     *
     * @param string $param
     * @return mixed
     */
    public function cleanInput($param = '');

    /*
    |--------------------------------------------------------------------------
    | Inputs for Action, Items, and Options
    |--------------------------------------------------------------------------
    */

    /**
     * Get the 'action' parameter of form or list action request, if given.
     *
     * @return string
     */
    public function getAction();

    /**
     * Set the 'action' parameter of request.
     *
     * @param string $action
     * @return string
     */
    public function setAction($action = '');

    /**
     * Get the raw 'items' parameter of list action request, if given.
     *
     * @return array
     */
    public function getActionItems();

    /**
     * Set the 'items' parameter of request.
     *
     * @param array $items
     * @return string
     */
    public function setActionItems($items = []);

    /**
     * Get the raw 'options' parameter of list action request, if given.
     *
     * @return array
     */
    public function getActionOptions();

    /**
     * Set the 'options' parameter of request.
     *
     * @param array $options
     * @return string
     */
    public function setActionOptions($options = []);

    /*
    |--------------------------------------------------------------------------
    | Get Original Request Data
    |--------------------------------------------------------------------------
    */

    /**
     * Initialize the publisher request with data from current request,
     * similar to how it is done in FormRequestServiceProvider boot().
     * As of L5.4, the full request is not available in controller
     * constructor, so we should call this in each action method.
     *
     * When injecting publisher request into other class types, should
     * be resolved automatically via the IoC, so no need to use this.
     *
     * @param SymfonyRequest $current
     */
    public function initializeRequest(SymfonyRequest $current);
    
}
