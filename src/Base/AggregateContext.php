<?php

namespace Viewflex\Ligero\Base;

use Viewflex\Ligero\Contracts\AggregateContextInterface;

/**
 * Extend this class and configure its default Publisher,
 * implementing abstract methods supporting aggregates.
 */
abstract class AggregateContext extends BaseContext implements AggregateContextInterface
{

    /*
    |--------------------------------------------------------------------------
    | Rich Context Query Actions
    |--------------------------------------------------------------------------
    */

    /**
     * @param int $id
     * @param bool $native
     * @return mixed
     */
    public function findContext($id, $native = true)
    {
        // Get the aggregate root record.
        $response = $this->find($id, $native);

        // Return find() failure response if no root was found.
        if (! ($root = $this->responseData($response)) )
            return $response;

        // Return the context in response data.
        return $this->contextResponse(1, null, $this->getContext($root, $native));

    }

    /**
     * @param array $inputs
     * @param bool $native
     * @return mixed
     */
    public function findContextBy($inputs = [], $native = true)
    {
        $contexts = [];

        // Get the aggregate root records.
        $response = $this->findBy($inputs, $native);

        // Return findBy() failure response if no roots were found.
        if (! ($roots = $this->responseData($response)) )
            return $response;

        // Get the aggregate root records.
        foreach ($roots as $root)
            $contexts[] = $this->getContext($root, $native);

        // Return the contexts in response data.
        return $this->contextResponse(1, null, $contexts);
    }

    /*
    |--------------------------------------------------------------------------
    | Context Output Composition
    |--------------------------------------------------------------------------
    */

    /**
     * @param $root
     * @param bool $native
     * @return array
     */
    abstract public function getContext($root, $native = true);

    /*
    |--------------------------------------------------------------------------
    | Context Input Validation
    |--------------------------------------------------------------------------
    */

    /**
     * @param array $inputs
     * @return array|bool
     */
    abstract public function contextInputsAreValid($inputs);
}
