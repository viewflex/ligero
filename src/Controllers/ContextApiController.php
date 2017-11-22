<?php

namespace Viewflex\Ligero\Controllers;

use App\Http\Controllers\Controller;
use Viewflex\Ligero\Contracts\ContextInterface as Context;

class ContextApiController extends Controller
{
    /**
     * @var array
     */
    protected $inputs;
    
    /**
     * @var array
     */
    protected $contexts;

    /**
     * @var Context
     */
    protected $context;

    public function __construct()
    {
        $this->inputs = json_decode(request()->getContent(), true);
        $this->contexts = config('ligero.contexts', []);
    }

    /*
    |--------------------------------------------------------------------------
    | JSON Publisher Query Actions
    |--------------------------------------------------------------------------
    */

    /**
     * Returns the results of json publisher query on id in native or array format.
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
     * Querying on json inputs provided, which may not have been validated,
     * returns the results of publisher query in native or array format.
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
     * Returns the results of json context query on id in native or array format.
     *
     * @return mixed
     */
    public function findContext($key)
    {
        $this->context = new $this->contexts[$key];
        return $this->context->findContext($this->inputs['id'], false);
    }

    /**
     * Find by attributes using json query inputs,
     * returns results in native or array format.
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
