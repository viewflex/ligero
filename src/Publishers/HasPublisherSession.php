<?php

namespace Viewflex\Ligero\Publishers;

use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Methods for maintaining session-based state in a Publisher UI controller.
 */
trait HasPublisherSession
{

    /**
     * Set initial 'back_to' query and 'root' uri in session, flashing previously set
     * values or if null, creating new ones temporarily with generic site base path.
     * On successful index query, use setBackTo() to set both  'back_to' and 'root'.
     */
    public function remember()
    {
        // Load current request attributes (including session) into publisher's request.
        $this->initializeRequest(request());
        
        if ($this->getBackTo())
            Session::keep('back_to');
        else
            Session::put('back_to', '/');

        if ($this->getRoot())
            Session::keep('root');
        else
            Session::put('root', '/');
        
    }

    /**
     * Save the 'self' url of this publisher as the new 'back_to' session value,
     * for use in the next publisher request after operation or failed query.
     * Also save 'root' location for redirect after operation changed results.
     */
    public function setBackTo()
    {
        Session::put('back_to', $this->publisher->urlSelf());
        $this->setRoot();
    }
    
    /**
     * Retrieve stored 'back_to' url for redirect after operation or failed query.
     *
     * @return mixed
     */
    public function getBackTo()
    {
        return Session::get('back_to');
    }

    /**
     * Redirect to the stored 'back_to' url, with a session message.
     *
     * @param string $message
     * @param null|array|int $translation_option
     * @return \Illuminate\Http\RedirectResponse
     */
    public function goBack($message = '', $translation_option = null)
    {
        return redirect($this->getBackTo())->with('message', $this->getConfig()->ls($message, $translation_option));
    }

    /**
     * Retrieve stored 'root' uri for redirect.
     *
     * @return mixed
     */
    public function getRoot()
    {
        return Session::get('root');
    }

    /**
     * Save the  'root' session value.
     */
    public function setRoot()
    {
        Session::put('root', $this->currentRouteUrlRoot($this->getConfig()->getAbsoluteUrls()));
    }

    /**
     * Redirect to the root uri with a session message.
     *
     * @param string $message
     * @param null|array|int $translation_option
     * @return \Illuminate\Http\RedirectResponse
     */
    public function goToRoot($message = '', $translation_option = null)
    {
        return redirect($this->getRoot())->with('message', $this->getConfig()->ls($message, $translation_option));
    }

    /**
     * Set the temporary flag that can be checked
     * to reset failed query after update/delete.
     */
    public function setDataModified()
    {
        Session::flash('data_modified', true);
    }

    /**
     * Check if the last action modified data that
     * might affect previous query we redirect to.
     * 
     * @return mixed
     */
    public function getDataModified()
    {
        return Session::get('data_modified');
    }
    
    /**
     * Use in the controller to catch and work around
     * if you need to implement such flexibility.
     *
     * @return bool
     */
    public function hasSession()
    {
        return ((Session::all()) === []) ? false : true;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return Session::get('message');
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
