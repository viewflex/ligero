<?php

namespace Viewflex\Ligero\Publishers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

trait HasPublisherContext
{
    
    use HasPublisher;
    
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

        $validator = Validator::make(['id' => $id], $this->getRequest()->getQueryRules());

        // Validate inputs before proceeding.
        if ($validator->fails())
            return $this->contextResponse(0, $validator->errors()->messages(), null);
        else {

            // Perform query, catching any internal errors.
            try {
                $found = $this->getPublisher()->find($id, $native);
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

        $validator = Validator::make($inputs, $this->getRequest()->getQueryRules());

        // Validate inputs before proceeding.
        if ($validator->fails())
            return $this->contextResponse(0, $validator->errors()->messages(), null);
        else {

            // Perform query, returning any internal errors.
            try {
                $found = $this->getPublisher()->findBy($inputs, $native);
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

        $validator = Validator::make($inputs, $this->getRequest()->getRequestRules());

        // Validate inputs before proceeding.
        if ($validator->fails())
            return $this->contextResponse(0, $validator->errors()->messages(), null);
        else {

            // Perform request, returning any internal errors.
            try {
                $response = $this->getPublisher()->store($inputs);
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

        $validator = Validator::make($inputs, $this->getRequest()->getRequestRules());

        // Validate inputs before proceeding.
        if ($validator->fails())
            return $this->contextResponse(0, $validator->errors()->messages(), null);
        else {

            // Perform request, returning any internal errors.
            try {
                $response = $this->getPublisher()->update($inputs);
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

        $validator = Validator::make(['id' => $id], $this->getRequest()->getRequestRules());

        // Validate inputs before proceeding.
        if ($validator->fails())
            return $this->contextResponse(0, $validator->errors()->messages(), null);

        // Perform request, returning any internal errors.
        try {
            $response = $this->getPublisher()->delete($id);
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
        return $this->getPublisher()->inputsAreValid($inputs);
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
        return $this->getConfig()->getDomain().'->'.$operation.' failed with inputs: '.$this->arrayToString($inputs);
    }

    /**
     * @param array $context
     * @param null|string $name
     * @return array
     */
    public function formatDomainContext($context, $name = null)
    {
        return [($name ? : snake_case( $this->getConfig()->getDomain() )) => $context];
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
        if($this->getConfig()->getLogging()['active']) {
            Log::info($this->getConfig()->getDomain().' Context: '.$message);
        }

        return $message;
    }
}
