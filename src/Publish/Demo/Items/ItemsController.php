<?php

namespace Viewflex\Ligero\Publish\Demo\Items;

use Viewflex\Ligero\Base\BasePublisherController;
use Viewflex\Ligero\Publish\Demo\Items\ItemsConfig as Config;
use Viewflex\Ligero\Publish\Demo\Items\ItemsRequest as Request;
use Viewflex\Ligero\Publish\Demo\Items\ItemsRepository as Query;

class ItemsController extends BasePublisherController
{
    public function __construct(Config $config, Request $request, Query $query)
    {
        $this->createPublisher($config, $request, $query);
    }
    
}
