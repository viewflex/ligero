<?php

namespace Viewflex\Ligero\Controllers;

use App\Http\Controllers\Controller;
use Viewflex\Ligero\Contracts\ContextInterface as Context;

class ContextApiController extends Controller
{
    use HasContextApi;

    /**
     * @var array
     */
    protected $inputs;
    
    /**
     * @var array
     */
    protected $contexts;

    /**
     * @var Context
     */
    protected $context;

    public function __construct()
    {
        $this->inputs = json_decode(request()->getContent(), true);
        $this->contexts = config('ligero.contexts', []);
    }
    
}
