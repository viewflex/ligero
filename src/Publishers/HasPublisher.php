<?php

namespace Viewflex\Ligero\Publishers;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Viewflex\Ligero\Contracts\PublisherConfigInterface as Config;
use Viewflex\Ligero\Contracts\PublisherRequestInterface as Request;
use Viewflex\Ligero\Contracts\PublisherRepositoryInterface as Query;
use Viewflex\Ligero\Contracts\PublisherInterface;

/**
 * Supports instantiation, fluent configuration, and use of
 * a Publisher in controllers, contexts, or other classes.
 */
trait HasPublisher
{
    
    use HasFluentConfiguration;
    
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
    
    /*
    |--------------------------------------------------------------------------
    | Utility Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Return named view with data according to configured domain resource namespace.
     * 
     * @param $view
     * @param $data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function returnView($view, $data)
    {
        return view($this->getConfig()->getDomainViewName($view), $data);
    }

    /**
     * Get localized string via trans() or trans_choice() based on domain configuration.
     *
     * @param string $key
     * @param null|array|int $option
     * @return string
     */
    public function ls($key, $option = null)
    {
        return $this->getConfig()->ls($key, $option);
    }

    /**
     * Initialize the publisher request with data from current request,
     * similar to how it is done in FormRequestServiceProvider boot().
     * As of L5.4, the full request is not available in controller
     * constructor, so we should call this in each action method.
     *
     * @param SymfonyRequest $current
     */
    protected function initializeRequest(SymfonyRequest $current)
    {
        $this->getRequest()->initializeRequest($current);
    }
    
}
