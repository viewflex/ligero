<?php

$common = include('common.php');
$override = [

    /*
    |--------------------------------------------------------------------------
    | Domain Localization
    |--------------------------------------------------------------------------
    */

    ## Labels for columns and values

    'id' => 'ID',
    'active' => 'Active',
    'name' => 'Name',
    'category' => 'Category',
    'subcategory' => 'Subcategory',
    'description' => 'Description',
    'price' => 'Price',
    'created_at' => 'Created At',
    'updated_at' => 'Updated At',

    ## Strings for values of boolean fields

    'active_true' => 'Active',
    'active_false' => 'Inactive',
    
];

return [
    'ui' => array_merge($common, $override)
];
