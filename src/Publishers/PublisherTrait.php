<?php

namespace Viewflex\Ligero\Publishers;

use Illuminate\Support\Facades\Validator;

trait PublisherTrait
{
    /*
    |--------------------------------------------------------------------------
    | API Data
    |--------------------------------------------------------------------------
    */

    /**
     * Get info on query and results.
     *
     * @return array
     */
    public function getQueryInfo()
    {
        return $this->api->getQueryInfo();
    }

    /**
     * Returns total number of records that would be found
     * if we were using publisher query without a limit.
     *
     * @return int
     */
    public function found()
    {
        return $this->api->found();
    }

    /**
     * Returns the number of records actually returned by publisher query.
     *
     * @return int
     */
    public function displayed()
    {
        return $this->api->displayed();
    }

    /**
     * Returns the results of publisher query in native format.
     *
     * @return mixed
     */
    public function getResults()
    {
        return $this->api->getResults();
    }

    /**
     * Returns the results of publisher query as array or null.
     *
     * @return mixed
     */
    public function getItems()
    {
        return $this->api->getItems();
    }

    /**
     * Returns API data for pagination UI controls and labels.
     *
     * @return mixed
     */
    public function getPagination()
    {
        return $this->api->getPagination();
    }
    
    /**
     * Get API keyword query parameters and config. The persist_keyword
     * config determines whether input gets re-used as a prompt in
     * the generated UI control. All query and display params
     * to be used in form are returned as parameters array.
     *
     * @return mixed
     */
    public function getKeywordSearch()
    {
        return $this->api->getKeywordSearch();
    }

    /**
     * Returns all publisher API data bundles together, along with query info.
     *
     * @return array
     */
    public function getData()
    {
        return $this->api->getData();
    }

    /*
    |--------------------------------------------------------------------------
    | Dynamically Formatted Results - Override to Customize
    |--------------------------------------------------------------------------
    */

    /**
     * Returns processed results of listing query as array.
     *
     * @return array|null
     */
    public function presentItems()
    {
        return $this->api->presentItems();
    }

    /**
     * Returns all publisher API data bundles together, along with query info.
     *
     * @return array
     */
    public function presentData()
    {
        return $this->api->presentData();
    }

    /*
    |--------------------------------------------------------------------------
    | CRUD Actions
    |--------------------------------------------------------------------------
    */

    /**
     * Returns the result of publisher query on id in native or array format, or null.
     *
     * @param int $id
     * @param bool $native
     * @return mixed
     */
    public function find($id, $native = true)
    {
        $this->request->setMethod('GET');
        $this->request->setInputs(['id' => $id]);
        $result = $native ? $this->getResults() : $this->getItems();
        return $result ? ($native ? $result->first() : $result[0]) : null;
    }

    /**
     * Returns the results of publisher query in native or array format, or null.
     *
     * @param array $inputs
     * @param bool $native
     * @return mixed
     */
    public function findBy($inputs = [], $native = true)
    {
        $this->request->setMethod('GET');
        $this->request->setInputs($inputs);
        return $native ? $this->getResults() : $this->getItems();
    }

    /**
     * Store an item, using explicit or pre-configured request inputs, returning new id.
     *
     * @param null|array $inputs
     * @return int
     */
    public function store($inputs = null)
    {
        if ($inputs) {
            $this->request->setMethod('POST');
            $this->request->setInputs($inputs);
        }

        return $this->api->store();
    }

    /**
     * Update an item, using explicit or pre-configured request inputs, returning number of rows affected.
     *
     * @param null|array $inputs
     * @return int
     */
    public function update($inputs = null)
    {
        if ($inputs) {
            $this->request->setMethod('POST');
            $this->request->setInputs($inputs);
        }

        return $this->api->update();
    }

    /**
     * Delete an item, using explicit or pre-configured request input, returning number of rows affected.
     *
     * @param null|int $id
     * @return int
     */
    public function delete($id = null)
    {
        if ($id) {
            $this->request->setMethod('POST');
            $this->request->setInputs(['id' => $id]);
        }

        return $this->api->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | Publisher Multi-Record List Actions
    |--------------------------------------------------------------------------
    */

    /**
     * Calls the appropriate db query, returning number of rows affected.
     * Also sets the session's 'message' attribute for visual feedback.
     * Actions supported by base repository are 'clone' and 'delete' -
     * extend the base class to support custom list actions required.
     * This method can be used to transparently perform action on
     * selected list items and then redirect back to listing.
     *
     * @param mixed $inputs
     * @return int
     */
    public function action($inputs = null)
    {
        $this->request->setMethod('GET');
        if ($inputs) {
            $this->request->setInputs($inputs);
        }

        return $this->api->action();
    }

    /*
    |--------------------------------------------------------------------------
    | Utility Functions
    |--------------------------------------------------------------------------
    */

    /**
     * Returns the current URL, with parameters properly ordered.
     * Non-query action and items (array) params are skipped.
     *
     * @return string
     */
    public function urlSelf()
    {
        return $this->api->urlSelf();
    }

    /**
     * Validates inputs against the rules of this publisher instance.
     *
     * @param array $inputs
     * @return bool
     */
    public function inputsAreValid($inputs = [])
    {
        $validator = Validator::make($inputs, $this->request->rules());
        return $validator->passes();
    }
    
}
