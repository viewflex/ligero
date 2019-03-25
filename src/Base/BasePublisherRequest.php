<?php

namespace Viewflex\Ligero\Base;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Viewflex\Ligero\Contracts\PublisherRequestInterface;

/**
 * The base Publisher Request class, used as the default.
 * Modify properties via setters, or extend and customize.
 */
class BasePublisherRequest extends Request implements PublisherRequestInterface
{
    /*
    |--------------------------------------------------------------------------
    | Rules for GET and POST Validations
    |--------------------------------------------------------------------------
    */

    /**
     * Standard query parameters and their rules.
     *
     * @var array
     */
    private $reserved_query_rules = [
        'id'            => 'numeric|min:1',
        'keyword'       => 'max:32',
        'sort'          => 'max:32',
        'view'          => 'in:list,grid,item',
        'limit'         => 'numeric|min:1',
        'start'         => 'numeric|min:0',
        'action'        => 'max:32',
        'items'         => 'array',
        'options'       => 'array',
        'page'          => 'numeric|min:1'
    ];

    /**
     * Standard request parameters and their rules.
     *
     * @var array
     */
    private $reserved_request_rules = [
        'id'            => 'numeric|min:1',
        'action'        => 'max:32',
        'items'         => 'array',
        'options'       => 'array'
    ];

    /**
     * Standard and custom query rules.
     *
     * @var array
     */
    protected $query_rules = [];

    /**
     * Standard and custom request rules.
     *
     * @var array
     */
    protected $request_rules = [];

    /**
     * Get query or request rules, based on http request method.
     * 
     * @return array
     */
    public function rules()
    {
        switch($this->method())
        {
            case 'GET':
            {
                return $this->getQueryRules();
                break;
            }
            case 'POST':
            {
                return $this->getRequestRules();
                break;
            }
            case 'PUT':
            {
                return $this->getRequestRules();
                break;
            }
            case 'PATCH':
            {
                return $this->getRequestRules();
                break;
            }
            case 'DELETE':
            {
                return $this->getRequestRules();
                break;
            }
            default:break;
        }
    }

    /**
     * Get custom and reserved query rules.
     * 
     * @return array
     */
    public function getQueryRules()
    {
        return $this->query_rules = array_merge($this->query_rules, $this->reserved_query_rules);;
    }

    /**
     * Set custom query rules, maintaining reserved rules.
     * 
     * @param array $query_rules
     */
    public function setQueryRules($query_rules)
    {
        $this->query_rules = array_merge($query_rules, $this->reserved_query_rules);
    }

    /**
     * Set a specific query rule.
     * 
     * @param string $name
     * @param string $value
     */
    public function setQueryRule($name, $value)
    {
        $this->query_rules[$name] = $value;
    }
    
    /**
     * Alias for getQueryRules().
     * 
     * @return array
     */
    public function getRules()
    {
        return $this->getQueryRules();
    }

    /**
     * Alias for setQueryRules().
     * 
     * @param array $query_rules
     */
    public function setRules($query_rules)
    {
        $this->setQueryRules($query_rules);
    }

    /**
     * Alias for setQueryRule().
     * 
     * @param string $name
     * @param string $value
     */
    public function setRule($name, $value)
    {
        $this->setQueryRule($name, $value);
    }

    /**
     * Get custom and reserved request rules.
     * 
     * @return array
     */
    public function getRequestRules()
    {
        return $this->request_rules = array_merge($this->request_rules, $this->reserved_request_rules);
    }

    /**
     * Set custom request rules, maintaining reserved rules.
     * 
     * @param array $request_rules
     */
    public function setRequestRules($request_rules)
    {
        $this->request_rules = array_merge($request_rules, $this->reserved_request_rules);
    }

    /**
     * Set a specific request rule.
     * 
     * @param string $name
     * @param string $value
     */
    public function setRequestRule($name, $value)
    {
        $this->request_rules[$name] = $value;
    }
    
    /**
     * Alias for getRequestRules().
     * 
     * @return array
     */
    public function getPostRules()
    {
        return $this->getRequestRules();
    }

    /**
     * Alias for setRequestRules().
     * 
     * @param array $request_rules
     */
    public function setPostRules($request_rules)
    {
        $this->setRequestRules($request_rules);
    }

    /**
     * Alias for setRequestRule().
     * 
     * @param string $name
     * @param string $value
     */
    public function setPostRule($name, $value)
    {
        $this->setRequestRule($name, $value);
    }

    /*
    |--------------------------------------------------------------------------
    | Inputs for GET Queries
    |--------------------------------------------------------------------------
    */

    /**
     * @return array
     */
    public function getQueryInputs()
    {
        $clean_params = array();
        $names = array_keys($this->getQueryRules());
        $inputs = array_only($this->all(), $names);

        foreach($inputs as $key => $value) {
            // Just pass through array or null value
            if (is_array($value) || ($value === null)) {
                $clean_params = array_add($clean_params, $key, $value);
            } else {
                // Safety-check value as a string.
                $clean_value = $this->cleanInput($value);
                if (!(strlen($clean_value) == 0))
                    $clean_params = array_add($clean_params, $key, $clean_value);
            }
        }

        return $clean_params;
    }

    /**
     * @param string $key
     * @return mixed|string
     */
    public function getQueryInput($key = '')
    {
        $inputs = $this->getQueryInputs();
        return array_key_exists($key, $inputs) ? $inputs[$key] : '';
    }

    /*
    |--------------------------------------------------------------------------
    | Inputs for GET Queries or POST Requests
    |--------------------------------------------------------------------------
    */
    
    /**
     * @return array
     */
    public function getInputs()
    {
        return $this->all();
    }
    
    /**
     * @param array $inputs
     */
    public function setInputs($inputs = [])
    {
        $this->replace($inputs);
    }

    /**
     * @param array $inputs
     */
    public function mergeInputs($inputs = [])
    {
        $this->merge($inputs);
    }

    /**
     * @param string $param
     * @return mixed
     */
    public function cleanInput($param = '')
    {
        $clean_param = trim($param);
        return $clean_param;
    }
    
    /*
    |--------------------------------------------------------------------------
    | Inputs for Action, Items, and Options
    |--------------------------------------------------------------------------
    */
    
    /**
     * @return string
     */
    public function getAction()
    {
        return strtolower($this->input('action'));
    }

    /**
     * @param string $action
     * @return string
     */
    public function setAction($action = '')
    {
        $this->mergeInputs(['action' => $action]);
    }

    /**
     * @return array
     */
    public function getActionItems()
    {
        return $this->input('items');
    }

    /**
     * @param array $items
     * @return string
     */
    public function setActionItems($items = [])
    {
        $this->mergeInputs(['items' => $items]);
    }

    /**
     * @return array
     */
    public function getActionOptions()
    {
        return $this->input('options');
    }

    /**
     * @param array $options
     * @return string
     */
    public function setActionOptions($options = [])
    {
        $this->mergeInputs(['options' => $options]);
    }

    /*
    |--------------------------------------------------------------------------
    | Get Original Request Data
    |--------------------------------------------------------------------------
    */
    
    /**
     * @param SymfonyRequest $current
     */
    public function initializeRequest(SymfonyRequest $current)
    {
        $files = $current->files->all();
        $files = is_array($files) ? array_filter($files) : $files;

        $this->initialize(
            $current->query->all(), $current->request->all(), $current->attributes->all(),
            $current->cookies->all(), $files, $current->server->all(), $current->getContent()
        );

        $this->setJson($current->json());

        if ($session = $current->getSession())
            $this->setLaravelSession($session);

        $this->setUserResolver($current->getUserResolver());
        $this->setRouteResolver($current->getRouteResolver());
    }
    
}
