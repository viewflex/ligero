<?php

namespace Viewflex\Ligero\Publishers;

use Viewflex\Ligero\Contracts\PublisherConfigInterface as Config;
use Viewflex\Ligero\Contracts\PublisherRepositoryInterface as Query;
use Viewflex\Ligero\Contracts\PublisherRequestInterface as Request;
use Viewflex\Ligero\Contracts\PublisherInterface;

/**
 * Supports implementation of a Publisher in controllers, contexts, or other classes.
 */
trait HasPublisher
{
    
    /*
    |--------------------------------------------------------------------------
    | The Publisher and it's Components
    |--------------------------------------------------------------------------
    */
    
    /**
     * The default publisher for this class.
     *
     * @var PublisherInterface
     */
    protected $publisher = null;
    
    /**
     * Get the default config.
     * 
     * @return Config
     */
    public function getConfig()
    {
        return $this->publisher->getConfig();
    }

    /**
     * Get the default request.
     * 
     * @return Request
     */
    public function getRequest()
    {
        return $this->publisher->getRequest();
    }

    /**
     * Get the default query.
     *
     * @return Query
     */
    public function getQuery()
    {
        return $this->publisher->getQuery();
    }
    
    /**
     * Get the default publisher.
     * 
     * @return PublisherInterface
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * Set the default publisher.
     * 
     * @param PublisherInterface $publisher
     * @return PublisherInterface
     */
    public function setPublisher($publisher)
    {
        return $this->publisher = $publisher;
    }

    /*
    |--------------------------------------------------------------------------
    | Create Publisher with Custom Components or Default (Base) Components.
    |--------------------------------------------------------------------------
    */

    /**
     * Initialize and return a new publisher with config, request, and (optionally) repository .
     *
     * @param Config $config
     * @param Request $request
     * @param Query $query
     * @return PublisherInterface
     */
    public function createPublisher(Config $config, Request $request, Query $query = null)
    {
        return $this->setPublisher(new Publisher($config, $request, $query));
    }

    /**
     * Initialize and return a new publisher with default components.
     * 
     * @return PublisherInterface
     */
    public function createPublisherWithDefaults()
    {
        return $this->setPublisher(new Publisher());
    }
    
}
