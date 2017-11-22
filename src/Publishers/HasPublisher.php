<?php

namespace Viewflex\Ligero\Publishers;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Viewflex\Ligero\Base\BasePublisherConfig as DefaultConfig;
use Viewflex\Ligero\Base\BasePublisherRequest as DefaultRequest;
use Viewflex\Ligero\Base\BasePublisherRepository as DefaultQuery;
use Viewflex\Ligero\Contracts\PublisherConfigInterface as Config;
use Viewflex\Ligero\Contracts\PublisherRequestInterface as Request;
use Viewflex\Ligero\Contracts\PublisherRepositoryInterface as Query;
use Viewflex\Ligero\Contracts\PublisherInterface;

/**
 * This trait supports the creation and use of a Publisher object within
 * controllers, contexts, or other classes, adding new class attributes
 * for the Publisher, and it's Config, Request, and Query components.
 * Additional Publishers can be created as needed via newPublisher().
 */
trait HasPublisher
{
    /*
    |--------------------------------------------------------------------------
    | The Publisher and it's Components for This Controller
    |--------------------------------------------------------------------------
    */

    /**
     * The default config for this controller.
     *
     * @var Config
     */
    protected $config;

    /**
     * The default request for this controller.
     *
     * @var Request
     */
    protected $request;

    /**
     * The default repository of methods used internally to perform database queries.
     *
     * @var Query
     */
    protected $query;
    
    /**
     * The default publisher for this controller.
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
        return $this->config;
    }

    /**
     * Set the default config fluently.
     * 
     * @param Config $config
     * @return Config
     */
    public function setConfig($config)
    {
        return $this->config = $config;
    }

    /**
     * Get the default request.
     * 
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set the default request fluently.
     * 
     * @param Request $request
     * @return Request
     */
    public function setRequest($request)
    {
        return $this->request = $request;
    }

    /**
     * Get the default query.
     *
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set the default query fluently.
     *
     * @param Query $query
     * @return Query
     */
    public function setQuery($query)
    {
        return $this->query = $query;
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
     * Inject Publisher to initialize for this controller.
     *
     * @param PublisherInterface $publisher
     */
    public function initPublisher(PublisherInterface $publisher)
    {
        $this->setPublisher($publisher);
        $this->setConfig($publisher->getConfig());
        $this->setRequest($publisher->getRequest());
        $this->setQuery($publisher->getQuery());
    }
    
    /**
     * Inject config, request, and repository to create
     * new PublisherApi and it's decorating Publisher.
     *
     * Extend this method in controller to set values
     * specific to the domain we're publishing, ie:
     * $this->config->setTranslationNamespace('abc'),
     * before passing the config to Api constructor.
     *
     * @param Config $config
     * @param Request $request
     * @param Query $query
     * @return PublisherInterface
     */
    public function createPublisher(Config $config, Request $request, Query $query = null)
    {
        return $this->setPublisher(
            $this->newPublisher($this->setConfig($config), $this->setRequest($request), $this->setQuery($query))
        );
    }

    /**
     * Use package defaults to create a new publisher.
     * Can customize via setters afterward if needed.
     * 
     * @return PublisherInterface
     */
    public function createPublisherWithDefaults()
    {
        return $this->createPublisher(new DefaultConfig, new DefaultRequest, new DefaultQuery);
    }

    /**
     * @param Config $config
     * @param Request $request
     * @param Query|null $query
     * @return Publisher
     */
    public function newPublisher(Config $config, Request $request, Query $query = null)
    {
        return new Publisher($config, $request, $query);
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
        return view($this->config->getDomainViewName($view), $data);
    }

    /**
     * Replace original inputs with new array provided.
     *
     * @param array $inputs
     */
    public function setInputs($inputs = [])
    {
        $this->request->setInputs($inputs);
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
        $this->request->initializeRequest($current);
    }
    
}
