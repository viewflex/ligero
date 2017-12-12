<?php

namespace Viewflex\Ligero\Publishers;

use Viewflex\Ligero\Base\BasePublisherRepository;
use Viewflex\Ligero\Contracts\PublisherApiInterface;
use Viewflex\Ligero\Contracts\PublisherConfigInterface as Config;
use Viewflex\Ligero\Contracts\PublisherRequestInterface as Request;
use Viewflex\Ligero\Contracts\PublisherRepositoryInterface as Query;
use Viewflex\Ligero\Exceptions\PublisherException;
use Viewflex\Ligero\Utility\ModelLoaderTrait;
use Viewflex\Ligero\Utility\RouteHelperTrait;

class PublisherApi implements PublisherApiInterface
{
    use PublisherApiTrait, ModelLoaderTrait, RouteHelperTrait;

    /*
    |--------------------------------------------------------------------------
    | Component Objects
    |--------------------------------------------------------------------------
    */
    
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Query
     */
    protected $query;

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /*
    |--------------------------------------------------------------------------
    | Initialization
    |--------------------------------------------------------------------------
    */
    
    /**
     * @param Query $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
        $this->query->setApi($this);
    }

    /**
     * Use injected Config and Request, and injected or base Query.
     *
     * @param Config $config
     * @param Request $request
     * @param Query $query
     * @throws PublisherException
     */
    public function __construct(Config $config, Request $request, Query $query = null)
    {
        $this->config = $config;
        $this->request = $request;

        if (!$this->modelExists())
            throw new PublisherException('Specified model '.$this->modelName().' cannot be found.');

        if ($query)
            $this->setQuery($query);
        else
            $this->setQuery(new BasePublisherRepository());

    }
    
}
