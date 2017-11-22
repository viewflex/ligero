<?php

namespace Viewflex\Ligero\Publish\Demo\Items;

use Viewflex\Ligero\Base\BasePublisherRepository;

class ItemsRepository extends BasePublisherRepository
{
    /*
    |--------------------------------------------------------------------------
    | Column Mapping
    |--------------------------------------------------------------------------
    */

    /**
     * For mapping parameter names to different column names in data source.
     * Array of parameter names and their respective database column names,
     * where column name is different than parameter name. ie:
     *
     *    ['account' => 'account_id', 'company' => 'company_name']
     *
     * @var array
     */
    protected $column_map = [];


}
