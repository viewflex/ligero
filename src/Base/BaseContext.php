<?php

namespace Viewflex\Ligero\Base;

use Viewflex\Ligero\Contracts\ContextInterface;
use Viewflex\Ligero\Publishers\HasPublisher;
use Viewflex\Ligero\Utility\ArrayHelperTrait;

abstract class BaseContext implements ContextInterface
{
    use HasPublisher, HasContext, ArrayHelperTrait;

    /**
     * Create base context with default publisher components, set custom defaults,
     * continue customizing config, request, and query as needed in child classes.
     */
    public function __construct()
    {
        $this->createPublisherWithDefaults();

        // Set namespace for package views, translations, etc.
        $this->config->setResourceNamespace('ligero');

        // Set table and model names for all domains.
        $this->config->setTables(config('ligero.tables', []));
        $this->config->setModels(config('ligero.models', []));

        // Set global caching and logging.
        $this->config->setCaching(config('ligero.caching', false));
        $this->config->setLogging(config('ligero.logging', false));

        // Use 'default' view configuration.
        $this->config->setQueryDefault('view', '');

        // Set limit for results returned by 'default' view.
        $this->config->setViewLimit(1000);

        // Set primary and secondary currencies.
        $this->config->setCurrencies([
            'primary'           =>  [
                'name'              =>  'US Dollars',
                'ISO_code'          =>  'USD',
                'prefix'            =>  '$',
                'suffix'            =>  '',
                'thousands'         =>  ',',
                'decimal'           =>  '.',
                'precision'         =>  2
            ],
            'secondary'         =>  [
                'name'              =>  'Israeli Shekels',
                'ISO_code'          =>  'ILS',
                'prefix'            =>  '₪',
                'suffix'            =>  'ILS',
                'thousands'         =>  ',',
                'decimal'           =>  '.',
                'precision'         =>  2
            ]
        ]);
    }

}
