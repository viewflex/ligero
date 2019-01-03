<?php

namespace Viewflex\Ligero\Publish\Demo\Items;

use Viewflex\Ligero\Base\BasePublisherController;

class ItemsController extends BasePublisherController
{

    public function __construct()
    {

        $this->createPublisherWithDefaults();

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
            ->setControls([
                'pagination'        => true,
                'keyword_search'    => true
            ])
            ->setKeywordSearchColumns([
                'name',
                'category',
                'subcategory',
                'description'
            ])
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

    }

}
