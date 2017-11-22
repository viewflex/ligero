<?php

namespace Viewflex\Ligero\Publish\Demo\Items;

use Viewflex\Ligero\Base\BasePublisherRequest;

class ItemsRequest extends BasePublisherRequest
{
    /*
    |--------------------------------------------------------------------------
    | Rules for GET and POST Validations
    |--------------------------------------------------------------------------
    */
    
    /**
     * The complete list of valid query parameter names,
     * along with their respective validation rules.
     *
     * @var array
     */
    protected $rules = [
        'id'            => 'numeric|min:1',
        'active'        => 'boolean',
        'name'          => 'max:60',
        'category'      => 'max:25',
        'subcategory'   => 'max:25',
        
        'keyword'       => 'max:16',
        'sort'          => 'max:60',
        'view'          => 'in:list,grid,item',
        'limit'         => 'numeric|max:100',
        'start'         => 'numeric|min:0',
        'action'        => 'max:16',
        'items'         => 'array',
        'options'       => 'array',
        'page'          => 'numeric|min:1'
    ];

    /**
     * The complete list of valid POST parameter names,
     * along with their respective validation rules.
     *
     * @var array
     */
    protected $post_rules = [
        'id'            => 'nullable|numeric|min:1',
        'active'        => 'boolean',
        'name'          => 'max:60',
        'category'      => 'max:25',
        'subcategory'   => 'max:25',
        'description'   => 'max:250',
        'price'         => 'numeric|min:0',
        'action'        => 'max:16',
        'items'         => 'array',
        'options'       => 'array'
    ];
    
}
