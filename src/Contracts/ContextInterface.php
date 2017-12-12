<?php

namespace Viewflex\Ligero\Contracts;

/**
 * A context encapsulates a custom domain publisher for general use,
 * without the session and IoC properties of a publisher controller,
 * but providing access to the domain's data via explicit calls.
 * 
 * Here we use the publisher CRUD methods, and also gather data
 * from other domains if this domain is an aggregate root.
 * 
 * A consistent response format is returned via contextResponse().
 */
interface ContextInterface
{
    /*
    |--------------------------------------------------------------------------
    | Basic CRUD Context Actions with Pre-Validation Using Domain Rules
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
     * Returns the results of publisher query in native or array format.
     *
     * @param array $inputs
     * @param bool $native
     * @return mixed
     */
    public function findBy($inputs = [], $native = true);

    /**
     * Store an item, using explicit request inputs.
     *
     * @param array $inputs
     * @return mixed
     */
    public function store($inputs);

    /**
     * Update an item, using explicit request inputs.
     *
     * @param array $inputs
     * @return mixed
     */
    public function update($inputs);

    /**
     * Delete an item, using explicit request input.
     *
     * @param int $id
     * @return mixed
     */
    public function delete($id);

    /*
    |--------------------------------------------------------------------------
    | Rich Context Query Actions - Use Basic CRUD Actions as Defaults
    |--------------------------------------------------------------------------
    */

    /**
     * Returns the results of context query on id in native or array format.
     *
     * @param int $id
     * @param bool $native
     * @return mixed
     */
    public function findContext($id, $native = true);

    /**
     * Find by attributes using explicit query inputs,
     * returns the results in native or array format.
     *
     * @param array $inputs
     * @param bool $native
     * @return mixed
     */
    public function findContextBy($inputs = [], $native = true);

    /**
     * Store a context using explicit inputs.
     *
     * @param array $inputs
     * @return mixed
     */
    public function storeContext($inputs);

    /**
     * Update a context using explicit inputs.
     *
     * @param array $inputs
     * @return mixed
     */
    public function updateContext($inputs);

    /**
     * Delete a context using explicit input.
     *
     * @param int $id
     * @return mixed
     */
    public function deleteContext($id);

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    /**
     * Validates inputs against the rules of this publisher instance.
     *
     * @param array $inputs
     * @return bool
     */
    public function inputsAreValid($inputs = []);

    /*
    |--------------------------------------------------------------------------
    | Utility
    |--------------------------------------------------------------------------
    */

    /**
     * The format for returning failure information for errors in transactions.
     *
     * @param string $msg
     * @param array $data
     * @return array
     */
    public function onFailure($msg = '', $data = []);

    /**
     * The format for returning operational failure information as a string.
     *
     * @param string $operation
     * @param array $inputs
     * @return string
     */
    public function failureMessage($operation = '', $inputs = []);

    /**
     * Returns array with domain name and context array as key => value pair.
     * The standard format for returning a context as an array element.
     *
     * @param array $context
     * @param null|string $name
     * @return array
     */
    public function formatDomainContext($context, $name = null);

    /**
     * Return response array according to context spec.
     *
     * @param int $success
     * @param mixed $msg
     * @param mixed $data
     * @return array
     */
    public function contextResponse($success, $msg, $data);

    /**
     * Return the 'success' element from context response or null.
     *
     * @param array $response
     * @return mixed|null
     */
    public function responseSuccess($response = []);

    /**
     * Return the 'msg' element from context response or null.
     *
     * @param array $response
     * @return mixed|null
     */
    public function responseMessage($response = []);

    /**
     * Return the 'data' element from context response or null.
     *
     * @param array $response
     * @return mixed|null
     */
    public function responseData($response = []);

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    */

    /**
     * Log context event if logging is enabled in config.
     * Return input fluently for reuse in either case.
     *
     * @param string $message
     * @return string
     */
    public function log($message = '');

}
