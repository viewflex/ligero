<?php

namespace Viewflex\Ligero\Base;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Viewflex\Ligero\Contracts\PublisherRequestInterface;

class BasePublisherRequest extends Request implements PublisherRequestInterface
{
    /*
    |--------------------------------------------------------------------------
    | Rules for GET and POST Validations
    |--------------------------------------------------------------------------
    */

    /**
     * The complete list of valid query parameter names,
     * along with their respective validation rules.
     *
     * @var array
     */
    protected $rules = [
        'id'            => 'numeric|min:1',
        'keyword'       => 'max:32',
        'sort'          => 'max:32',
        'view'          => 'in:list,grid,item',
        'limit'         => 'numeric|max:100',
        'start'         => 'numeric|min:0',
        'action'        => 'max:32',
        'items'         => 'array',
        'options'       => 'array',
        'page'          => 'numeric|min:1'
    ];

    /**
     * The complete list of valid POST parameter names,
     * along with their respective validation rules.
     *
     * @var array
     */
    protected $post_rules = [
        'id'            => 'numeric|min:1'
    ];

    /**
     * @return array
     */
    public function rules()
    {
        switch($this->method())
        {
            case 'GET':
            {
                return $this->rules;
                break;
            }
            case 'POST':
            {
                return $this->post_rules;
                break;
            }
            case 'PUT':
            {
                return $this->post_rules;
                break;
            }
            case 'PATCH':
            {
                return $this->post_rules;
                break;
            }
            case 'DELETE':
            {
                return $this->post_rules;
                break;
            }
            default:break;
        }
    }

    /**
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param array $rules
     */
    public function setRules($rules)
    {
        $this->rules = $rules;
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function setRule($name, $value)
    {
        $this->rules[$name] = $value;
    }
    
    /**
     * @return array
     */
    public function getPostRules()
    {
        return $this->post_rules;
    }

    /**
     * @param array $post_rules
     */
    public function setPostRules($post_rules)
    {
        $this->post_rules = $post_rules;
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function setPostRule($name, $value)
    {
        $this->post_rules[$name] = $value;
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
        $names = array_keys($this->rules);
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
