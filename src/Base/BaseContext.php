<?php

namespace Viewflex\Ligero\Base;

use Viewflex\Ligero\Contracts\ContextInterface;
use Viewflex\Ligero\Publishers\HasFluentConfiguration;
use Viewflex\Ligero\Publishers\HasPublisher;
use Viewflex\Ligero\Publishers\HasPublisherContext;
use Viewflex\Ligero\Utility\ArrayHelperTrait;

/**
 * Extend this class and configure its default Publisher,
 * to perform both simple and contextual CRUD actions.
 */
abstract class BaseContext implements ContextInterface
{
    
    use ArrayHelperTrait;
    use HasFluentConfiguration;
    use HasPublisher;
    use HasPublisherContext;
    
    /**
     * Create base context with default publisher components.
     */
    public function __construct()
    {
        $this->createPublisherWithDefaults();
    }

}
