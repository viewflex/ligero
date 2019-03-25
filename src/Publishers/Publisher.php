<?php

namespace Viewflex\Ligero\Publishers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Viewflex\Ligero\Base\BasePublisherConfig as DefaultConfig;
use Viewflex\Ligero\Base\BasePublisherRepository as DefaultQuery;
use Viewflex\Ligero\Base\BasePublisherRequest as DefaultRequest;
use Viewflex\Ligero\Contracts\PublisherApiInterface as Api;
use Viewflex\Ligero\Contracts\PublisherConfigInterface as Config;
use Viewflex\Ligero\Contracts\PublisherInterface;
use Viewflex\Ligero\Contracts\PublisherRepositoryInterface as Query;
use Viewflex\Ligero\Contracts\PublisherRequestInterface as Request;
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
     * @var Api
     */
    protected $api;

    /**
     * @return Api
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * @param Api $api
     */
    public function setApi($api)
    {
        $this->api = $api;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->getApi()->getConfig();
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->getApi()->getRequest();
    }

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->getApi()->getQuery();
    }

    /*
    |--------------------------------------------------------------------------
    | Initialization
    |--------------------------------------------------------------------------
    */
    
    /**
     * Create Publisher object with all default components, or
     * take injected publisher api, extract config & request,
     * or, create publisher based on the injected config
     * and request (and optionally query object),
     * or just create Publisher with all defaults.
     *
     * @throws PublisherException
     */
    public function __construct()
    {
        $num_args = func_num_args();

        switch ($num_args) {
            case 0: {
                // Create new API instance using default Config, Request, and Query.
                $this->api = new PublisherApi(new DefaultConfig(), new DefaultRequest(), new DefaultQuery());
                break;
            }
            
            case 1: {
                // Use given API instance.
                $this->api = func_get_arg(0);
                break;
            }

            case 2: {
                // Create new API instance using given config and request.
                $this->api = new PublisherApi(func_get_arg(0), func_get_arg(1));
                break;
            }

            case 3: {
                // Create new API instance using given config, request, and query.
                $this->api = new PublisherApi(func_get_arg(0), func_get_arg(1), func_get_arg(2));
                break;
            }

            default: {
                throw new PublisherException('Wrong number of parameters.');
            }
            
        }

    }
    
}
