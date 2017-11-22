<?php

$common = include('common.php');
$override = [

    /*
    |--------------------------------------------------------------------------
    | Publisher Localization
    |--------------------------------------------------------------------------
    |
    | Override to customize for a given publisher domain.
    |
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

    /*
    |--------------------------------------------------------------------------
    | Dynamic Choices for Query Control Labels
    |--------------------------------------------------------------------------
    */

    ## Text for 'All' choice in query menus (doesn't alter option value)

    'submenu_all_category' => 'Categories',
    'submenu_all_subcategory' => 'Subcategories',
    'submenu_all_active' => 'All',

    ## Text for 'All' links corresponding to query menus

    'link_all_category' => 'Categories',
    'link_all_subcategory' => 'Subcategories',
    'link_all_active' => 'All',

    ## Text for sort menu option labels

    'label_sort_by' => 'Sort by',

    'sorts'       => [
        'default'               => 'Default',
        'id'                    => 'ID',
        'active'                => 'Active',
        'name'                  => 'Name',
        'category'              => 'Category',
        'subcategory'           => 'Subcategory',

        'default-desc'          => 'Default (R)',
        'id-desc'               => 'ID (R)',
        'active-desc'           => 'Active (R)',
        'name-desc'             => 'Name (R)',
        'category-desc'         => 'Category (R)',
        'subcategory-desc'      => 'Subcategory (R)',
    ],

];

return [
    'ui' => array_merge($common, $override)
];
