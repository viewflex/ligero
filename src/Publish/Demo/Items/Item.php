<?php

namespace Viewflex\Ligero\Publish\Demo\Items;

use Viewflex\Ligero\Base\BaseModel;

class Item extends BaseModel {
    
    protected $table = 'ligero_items';

    /**
     * The Presenter class for generating formatted content from this model.
     *
     * @var string
     */
    protected $presenter = 'Viewflex\Ligero\Publish\Demo\Items\ItemPresenter';
    
}
