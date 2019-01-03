<?php

$this
    ->setDomain('Items')
    ->setTranslationFile('items')
    ->setTableName('ligero_items')
    ->setModelName('Viewflex\Ligero\Publish\Demo\Items\Item')
    ->setResultsColumns([
        'id',
        'active',
        'name',
        'category',
        'subcategory',
        'description',
        'price'
    ])
    ->setWildcardColumns([
        'category'
    ])
    ->setKeywordSearchColumns([
        'name',
        'category',
        'subcategory',
        'description'
    ])
    ->setSorts([
        'default'           => ['id' => 'asc'],
        'id'                => ['id' => 'asc', 'name' => 'asc'],
        'name'              => ['name' => 'asc', 'id' => 'asc'],
        'category'          => ['category' => 'asc', 'id' => 'asc'],
        'subcategory'       => ['subcategory' => 'asc', 'id' => 'asc'],
        'price'             => ['price' => 'asc', 'id' => 'asc']
    ])
    ->setControls([
        'pagination'        => true,
        'keyword_search'    => true
    ])
    ->setCaching(false)
    ->setLogging(true)
    ->setQueryRules([
        'id'                => 'numeric|min:1',
        'active'            => 'boolean',
        'name'              => 'max:60',
        'category'          => 'max:25',
        'subcategory'       => 'max:25'
    ])
    ->setRequestRules([
        'id'                => 'numeric|min:1',
        'active'            => 'boolean',
        'name'              => 'max:60',
        'category'          => 'max:25',
        'subcategory'       => 'max:25',
        'description'       => 'max:250',
        'price'             => 'numeric'
    ]);
