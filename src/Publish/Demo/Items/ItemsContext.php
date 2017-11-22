<?php

namespace Viewflex\Ligero\Publish\Demo\Items;

use Viewflex\Ligero\Base\BaseContext;

class ItemsContext extends BaseContext
{
    /*
    |--------------------------------------------------------------------------
    | Initialization
    |--------------------------------------------------------------------------
    */

    /**
     * Create basic context for this domain.
     **/
    public function __construct()
    {
        parent::__construct();

        /*
        |--------------------------------------------------------------------------
        | Config
        |--------------------------------------------------------------------------
        */

        // Set domain name and keys for locating domain views, translations, etc.
        $this->config->setDomain('Items');
        $this->config->setTranslationFile('items');

        // Set the key for locating table name in $tables array,
        // and for locating full model name in $models array.
        // If not found there, uses values in domain config.
        $this->config->setTableName('ligero_items');

        // Specify results columns to return. Array for 'default' columns required,
        // can also add arrays for standard views (list, grid, item) and others.
        $this->config->setResultsColumns([
            'id',
            'active',
            'name',
            'category',
            'subcategory',
            'description',
            'price'
        ]);

        // Define named sorts available via 'sort' query parameter.
        $this->config->setSorts([
            'default'           => ['id' => 'asc'],
            'id'                => ['id' => 'asc', 'name' => 'asc'],
            'name'              => ['name' => 'asc', 'id' => 'asc'],
            'category'          => ['category' => 'asc', 'id' => 'asc'],
            'subcategory'       => ['subcategory' => 'asc', 'id' => 'asc'],
            'price'             => ['price' => 'asc', 'id' => 'asc']
        ]);

        // Add custom GET parameters for queries, with default values.
        $this->config->setQueryDefault('id', '');
        $this->config->setQueryDefault('active', '');
        $this->config->setQueryDefault('name', '');
        $this->config->setQueryDefault('category', '');
        $this->config->setQueryDefault('subcategory', '');

        /*
        |--------------------------------------------------------------------------
        | Request
        |--------------------------------------------------------------------------
        */

        // Set validation rules for custom GET parameters.
        $this->request->setRule('id', 'numeric|min:1');
        $this->request->setRule('active', 'boolean');
        $this->request->setRule('name', 'max:60');
        $this->request->setRule('category', 'max:25');
        $this->request->setRule('subcategory', 'max:25');

        // Set validation rules for custom POST parameters.
        $this->request->setPostRule('id', 'numeric|min:1');
        $this->request->setPostRule('active', 'boolean');
        $this->request->setPostRule('name', 'max:60');
        $this->request->setPostRule('category', 'max:25');
        $this->request->setPostRule('subcategory', 'max:25');
        $this->request->setPostRule('description', 'max:250');

        /*
        |--------------------------------------------------------------------------
        | Query (Repository)
        |--------------------------------------------------------------------------
        */

        // Required - updates query $model to that specified by config.
        $this->query->loadModel();

        // Optional - mapping of input parameters to database column names.
        $this->query->setColumnMap([]);

    }

}
