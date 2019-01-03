<?php

namespace Viewflex\Ligero\Base;

use Viewflex\Ligero\Contracts\ContextInterface;
use Viewflex\Ligero\Publishers\HasPublisherContext;

abstract class BaseContext implements ContextInterface
{
    use HasPublisherContext;

    /**
     * Create base context with default publisher components.
     */
    public function __construct()
    {
        $this->createPublisherWithDefaults();
    }

}
