<?php

namespace Viewflex\Ligero\Publishers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Viewflex\Ligero\Contracts\PublisherInterface;
use Viewflex\Ligero\Contracts\PublisherApiInterface as Api;
use Viewflex\Ligero\Contracts\PublisherConfigInterface as Config;
use Viewflex\Ligero\Contracts\PublisherRequestInterface as Request;
use Viewflex\Ligero\Contracts\PublisherRepositoryInterface as Query;
use Viewflex\Ligero\Exceptions\PublisherException;

class Publisher implements PublisherInterface
{
    use PublisherTrait, ValidatesRequests;

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
     * @var Api
     */
    protected $api;

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
        return $this->api->getQuery();
    }

    /*
    |--------------------------------------------------------------------------
    | Initialization
    |--------------------------------------------------------------------------
    */
    
    /**
     * Take injected publisher api, extract config & request,
     * or, create publisher based on the injected config
     * and request (and optionally query object).
     *
     * @throws PublisherException
     */
    public function __construct()
    {
        $num_args = func_num_args();

        switch ($num_args) {
            case 1: {
                // Take given api instance and use it's config and request.
                $this->api = func_get_arg(0);
                $this->config = $this->api->getConfig();
                $this->request = $this->api->getRequest();
                break;
            }

            case 2: {
                // Make new api instance from given config and request.
                $this->config = func_get_arg(0);
                $this->request = func_get_arg(1);
                $this->api = new PublisherApi($this->config, $this->request);
                break;
            }

            case 3: {
                // Make new api instance from given config, request, and query.
                $this->config = func_get_arg(0);
                $this->request = func_get_arg(1);
                $query = func_get_arg(2);
                $this->api = new PublisherApi($this->config, $this->request, $query);
                break;
            }

            default: {
                throw new PublisherException('Wrong number of parameters.');
            }
        }

    }
    
}
