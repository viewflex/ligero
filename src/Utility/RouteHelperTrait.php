<?php

namespace Viewflex\Ligero\Utility;

use Illuminate\Support\Facades\Route;

/**
 * This trait provides framework-agnostic methods
 * to wrap the Laravel Route facade methods.
 */
trait RouteHelperTrait
{
    /**
     * Get the absolute or relative URL to the current named route.
     * Includes all route and query parameters as a complete URL.
     *
     * @param bool $absolute
     * @return string
     */
    public function currentRouteUrl($absolute = true)
    {
        $uri = request()->server()['REQUEST_URI'];
        return $absolute ? url($uri) : ($uri);
    }

    /**
     * Get the URL path of the current named route, minus query string.
     *
     * @param bool $absolute
     * @return string
     */
    public function currentRouteUrlRoot($absolute = true)
    {
        $url = $this->currentRouteUrl($absolute);
        $pos = strpos($url, '?');
        $root = $pos ? substr($url, 0, $pos) : substr($url, 0);
        
        return $root;
    }
    
    /**
     * Get the URL directory of the current named route, minus trailing '/'.
     *
     * @param bool $absolute
     * @return string
     */
    public function currentRouteUrlDir($absolute = true)
    {
        return pathinfo($this->currentRouteUrl($absolute))['dirname'];
    }

    /**
     * Get the URL basename (last segment) of the current named route, minus query string.
     *
     * @param bool $absolute
     * @return string
     */
    public function currentRouteUrlBasename($absolute = true)
    {
        return basename($this->currentRouteUrlRoot($absolute));
    }

    /**
     * Get the current route name.
     *
     * @return string|null
     */
    public function currentRouteName()
    {
        return Route::currentRouteName();
    }

    /**
     * Get the current route action.
     *
     * @return string|null
     */
    public function currentRouteAction()
    {
        return Route::current()->getActionName();
    }

    /**
     * Get the current route action method.
     *
     * @return string|null
     */
    public function currentRouteActionMethod()
    {
        return Route::current()->getActionMethod();
    }

}
