<?php

namespace Viewflex\Ligero\Contracts;

interface AggregateContextInterface extends ContextInterface
{
    /*
    |--------------------------------------------------------------------------
    | Context Output Composition
    |--------------------------------------------------------------------------
    */

    /**
     * Get the full bounded context for the given domain record.
     * Returns array of contexts, for this root and sub-domains.
     *
     * @param $root
     * @param bool $native
     * @return array
     */
    public function getContext($root, $native = true);

    /*
    |--------------------------------------------------------------------------
    | Context Input Validation
    |--------------------------------------------------------------------------
    */

    /**
     * Validate inputs for creating or updating a context.
     * Returns true or array with message and inputs.
     *
     * @param array $inputs
     * @return array|bool
     */
    public function contextInputsAreValid($inputs);

}
