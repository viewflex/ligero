<?php

namespace Viewflex\Ligero\Base;

use Viewflex\Ligero\Contracts\AggregateContextInterface;

abstract class AggregateContext extends BaseContext implements AggregateContextInterface
{
    /*
    |--------------------------------------------------------------------------
    | Rich Context Query Actions
    |--------------------------------------------------------------------------
    */
    
    /**
     * Returns the results of context query on id in native or array format.
     *
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
     * Querying on the inputs provided, which may not have been validated,
     * returns the results of context query in native or array format.
     *
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
    
    
}