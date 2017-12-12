<?php

namespace Viewflex\Ligero\Base;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Viewflex\Ligero\Contracts\ContextInterface;
use Viewflex\Ligero\Publishers\HasPublisher;
use Viewflex\Ligero\Utility\ArrayHelperTrait;

/**
 * This is the base class of the Ligero context layer.
 * Extend this class and customize via setter methods.
 */
abstract class BaseContext implements ContextInterface
{
    use HasPublisher, ArrayHelperTrait;

    /**
     * Create base context with default publisher components, set custom defaults,
     * continue customizing config, request, and query as needed in child classes.
     */
    public function __construct()
    {
        $this->createPublisherWithDefaults();

        // Set namespace for package views, translations, etc.
        $this->config->setResourceNamespace('ligero');

        // Set table and model names for all domains.
        $this->config->setTables(config('ligero.tables', []));
        $this->config->setModels(config('ligero.models', []));

        // Set global caching and logging.
        $this->config->setCaching(config('ligero.caching', false));
        $this->config->setLogging(config('ligero.logging', false));

        // Use 'default' view configuration.
        $this->config->setQueryDefault('view', '');

        // Set limit for results returned by 'default' view.
        $this->config->setViewLimit(1000);

        // Set primary and secondary currencies.
        $this->config->setCurrencies([
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
                'name'              =>  'Israeli Shekels',
                'ISO_code'          =>  'ILS',
                'prefix'            =>  '₪',
                'suffix'            =>  'ILS',
                'thousands'         =>  ',',
                'decimal'           =>  '.',
                'precision'         =>  2
            ]
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Basic CRUD Actions with Pre-Validation Using Domain Rules
    |--------------------------------------------------------------------------
    */

    /**
     * @param int $id
     * @param bool $native
     * @return mixed
     */
    public function find($id, $native = true)
    {
        // Reject bad input.
        if (! intval($id) > 0 ) {
            $msg = 'find() failed - bad input for id.';
            return $this->contextResponse(0, $this->log($msg), null);
        }

        $validator = Validator::make(['id' => $id], $this->request->getRules());

        // Validate inputs before proceeding.
        if ($validator->fails())
            return $this->contextResponse(0, $validator->errors()->messages(), null);
        else {

            // Perform query, catching any internal errors.
            try {
                $found = $this->publisher->find($id, $native);
            } catch (\Exception $e) {
                return $this->contextResponse(0, 'Internal error: '.$e->getMessage(), null);
            }

            // Return expected response on query success or failure.
            if ($found) {
                return $this->contextResponse(1, null, $found);
            } else {
                return $this->contextResponse(0, 'No records found.', null);
            }

        }

    }

    /**
     * @param array $inputs
     * @param bool $native
     * @return mixed
     */
    public function findBy($inputs = [], $native = true)
    {
        // Reject bad input.
        if (! is_array($inputs) ) {
            $msg = 'findBy() failed - bad input type.';
            return $this->contextResponse(0, $this->log($msg), null);
        }

        $validator = Validator::make($inputs, $this->request->getRules());

        // Validate inputs before proceeding.
        if ($validator->fails())
            return $this->contextResponse(0, $validator->errors()->messages(), null);
        else {

            // Perform query, returning any internal errors.
            try {
                $found = $this->publisher->findBy($inputs, $native);
            } catch (\Exception $e) {
                return $this->contextResponse(0, 'Internal error: '.$e->getMessage(), null);
            }

            // Return expected response on query success or failure.
            if ($found) {
                return $this->contextResponse(1, null, $found);
            } else {
                return $this->contextResponse(0, 'No records found.', null);
            }

        }

    }

    /**
     * @param array $inputs
     * @return mixed
     */
    public function store($inputs)
    {
        // Reject bad input.
        if (! is_array($inputs) ) {
            $msg = 'store() failed - bad input type.';
            return $this->contextResponse(0, $this->log($msg), null);
        }

        $validator = Validator::make($inputs, $this->request->getPostRules());

        // Validate inputs before proceeding.
        if ($validator->fails())
            return $this->contextResponse(0, $validator->errors()->messages(), null);
        else {

            // Perform request, returning any internal errors.
            try {
                $response = $this->publisher->store($inputs);
            } catch (\Exception $e) {
                return $this->contextResponse(0, 'Internal error: '.$e->getMessage(), null);
            }

            // Return expected response on request success or failure.
            if ($response > 0) {
                return $this->contextResponse(1, null, $response);
            } else {
                return $this->contextResponse(0, 'Record could not be stored.', null);
            }

        }

    }

    /**
     * @param array $inputs
     * @return mixed
     */
    public function update($inputs)
    {
        // Reject bad input.
        if (! is_array($inputs) ) {
            $msg = 'update() failed - bad input type.';
            return $this->contextResponse(0, $this->log($msg), null);
        }

        $validator = Validator::make($inputs, $this->request->getPostRules());

        // Validate inputs before proceeding.
        if ($validator->fails())
            return $this->contextResponse(0, $validator->errors()->messages(), null);
        else {

            // Perform request, returning any internal errors.
            try {
                $response = $this->publisher->update($inputs);
            } catch (\Exception $e) {
                return $this->contextResponse(0, 'Internal error: '.$e->getMessage(), null);
            }

            // Return expected response on request success or failure.
            if ($response > 0) {
                return $this->contextResponse(1, null, $response);
            } else {
                return $this->contextResponse(0, 'Record could not be updated.', null);
            }

        }

    }

    /**
     * @param int $id
     * @return mixed
     */
    public function delete($id)
    {
        // Reject bad input.
        if (! intval($id) > 0 ) {
            $msg = 'delete() failed - bad input type.';
            return $this->contextResponse(0, $this->log($msg), null);
        }

        $validator = Validator::make(['id' => $id], $this->request->getPostRules());

        // Validate inputs before proceeding.
        if ($validator->fails())
            return $this->contextResponse(0, $validator->errors()->messages(), null);

        // Perform request, returning any internal errors.
        try {
            $response = $this->publisher->delete($id);
        } catch (\Exception $e) {
            return $this->contextResponse(0, 'Internal error: '.$e->getMessage(), null);
        }

        // Return expected response on request success or failure.
        if ($response > 0) {
            return $this->contextResponse(1, null, $response);
        } else {
            return $this->contextResponse(0, 'Record could not be deleted.', null);
        }

    }

    /*
    |--------------------------------------------------------------------------
    | Rich Context Query Actions - Use Basic CRUD Actions as Defaults
    |--------------------------------------------------------------------------
    */

    /**
     * @param int $id
     * @param bool $native
     * @return mixed
     */
    public function findContext($id, $native = true)
    {
        $response = $this->find($id, $native);
        return $this->contextResponse($response['success'], $response['msg'], $this->formatDomainContext($this->responseData($response)));
    }

    /**
     * @param array $inputs
     * @param bool $native
     * @return mixed
     */
    public function findContextBy($inputs = [], $native = true)
    {
        $response = $this->findBy($inputs, $native);
        return $this->contextResponse($response['success'], $response['msg'], $this->formatDomainContext($this->responseData($response)));
    }

    /**
     * @param array $inputs
     * @return mixed
     */
    public function storeContext($inputs)
    {
        return $this->store($inputs);
    }

    /**
     * @param array $inputs
     * @return mixed
     */
    public function updateContext($inputs)
    {
        return $this->update($inputs);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function deleteContext($id)
    {
        return $this->delete($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    /**
     * @param array $inputs
     * @return bool
     */
    public function inputsAreValid($inputs = [])
    {
        return $this->publisher->inputsAreValid($inputs);
    }

    /*
    |--------------------------------------------------------------------------
    | Utility
    |--------------------------------------------------------------------------
    */

    /**
     * @param string $msg
     * @param array $data
     * @return array
     */
    public function onFailure($msg = '', $data = [])
    {
        return ['msg' => $msg, 'data' => $this->arrayToString($data)];
    }

    /**
     * @param string $operation
     * @param array $inputs
     * @return string
     */
    public function failureMessage($operation = '', $inputs = [])
    {
        return $this->config->getDomain().'->'.$operation.' failed with inputs: '.$this->arrayToString($inputs);
    }

    /**
     * @param array $context
     * @param null|string $name
     * @return array
     */
    public function formatDomainContext($context, $name = null)
    {
        return [($name ? : snake_case( $this->config->getDomain() )) => $context];
    }

    /**
     * @param int $success
     * @param mixed $msg
     * @param mixed $data
     * @return array
     */
    public function contextResponse($success, $msg, $data)
    {
        return [
            'success' => $success,
            'msg' => $msg,
            'data' => $data
        ];
    }

    /**
     * @param array $response
     * @return mixed|null
     */
    public function responseSuccess($response = [])
    {
        if ((!$response) || (!array_key_exists('success', $response)))
            return null;

        return $response['success'];
    }

    /**
     * @param array $response
     * @return mixed|null
     */
    public function responseMessage($response = [])
    {
        if ((!$response) || (!array_key_exists('msg', $response)))
            return null;

        return $response['msg'];
    }

    /**
     * @param array $response
     * @return mixed|null
     */
    public function responseData($response = [])
    {
        if ((!$response) || (!array_key_exists('data', $response)))
            return null;

        return $response['data'];
    }

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    */

    /**
     * @param string $message
     * @return string
     */
    public function log($message = '')
    {
        if($this->config->getLogging()['active']) {
            Log::info($this->config->getDomain().' Context: '.$message);
        }

        return $message;
    }

}