<?php

namespace Viewflex\Ligero\Controllers;

/**
 * Supports simple and contextual CRUD operations for a Context.
 */
trait HasContextApi
{
    /*
    |--------------------------------------------------------------------------
    | JSON Publisher Query Actions
    |--------------------------------------------------------------------------
    */

    /**
     * Returns the results of json publisher query on id.
     *
     * @param string $key
     * @return array
     */
    public function find($key)
    {
        $this->context = new $this->contexts[$key];
        return $this->context->find($this->inputs['id'], false);
    }

    /**
     * Returns the results of json publisher query.
     *
     * @param string $key
     * @return array
     */
    public function findBy($key)
    {
        $this->context = new $this->contexts[$key];
        return $this->context->findBy($this->inputs, false);
    }

    /**
     * Store a record using json inputs.
     *
     * @param string $key
     * @return array
     */
    public function store($key)
    {
        $this->context = new $this->contexts[$key];
        return $this->context->store($this->inputs);
    }

    /**
     * Update a record using json inputs.
     *
     * @param string $key
     * @return array
     */
    public function update($key)
    {
        $this->context = new $this->contexts[$key];
        $this->context->update($this->inputs);
    }

    /**
     * Delete a record using json input.
     *
     * @param string $key
     * @return array
     */
    public function destroy($key)
    {
        $this->context = new $this->contexts[$key];
        $this->context->delete($this->inputs['id']);
    }

    /*
    |--------------------------------------------------------------------------
    | JSON Rich Context Query Actions
    |--------------------------------------------------------------------------
    */

    /**
     * Returns the results of json context query on id.
     *
     * @return mixed
     */
    public function findContext($key)
    {
        $this->context = new $this->contexts[$key];
        return $this->context->findContext($this->inputs['id'], false);
    }

    /**
     * Returns the results of json context query.
     *
     * @return mixed
     */
    public function findContextBy($key)
    {
        $this->context = new $this->contexts[$key];
        return $this->context->findContextBy($this->inputs, false);
    }

    /**
     * Store a context using json inputs.
     *
     * @return int
     */
    public function storeContext($key)
    {
        $this->context = new $this->contexts[$key];
        return $this->context->storeContext($this->inputs);
    }

    /**
     * Update a context using json inputs.
     *
     * @return int
     */
    public function updateContext($key)
    {
        $this->context = new $this->contexts[$key];
        return $this->context->updateContext($this->inputs);
    }

    /**
     * Delete a context using json input.
     *
     * @return int
     */
    public function destroyContext($key)
    {
        $this->context = new $this->contexts[$key];
        return $this->context->deleteContext($this->inputs['id']);
    }
    
}
