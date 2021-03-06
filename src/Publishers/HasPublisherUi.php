<?php

namespace Viewflex\Ligero\Publishers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

/**
 * Methods for a stateful Publisher UI controller.
 */
trait HasPublisherUi
{
    
    /*
    |--------------------------------------------------------------------------
    | Listing Output
    |--------------------------------------------------------------------------
    */

    /**
     * Show the requested results or redirect.
     *
     * Route: (GET) {uri}
     *
     * @return Response
     */
    public function index()
    {
        // Load current request attributes into our custom request.
        // Keep last index() query 'back_to' url and 'root' uri.
        $this->remember();

        $validator = Validator::make($this->getRequest()->getQueryInputs(), $this->getRequest()->getQueryRules());
        $validator->validate();

        // Return view if we have results, otherwise redirect back.
        if ($this->getPublisher()->displayed()) {

            // Compose data elements as needed for view.
            $data = $this->composeListing();

            // We have new results, so set the successful query as back_to
            // location to return to in case the next user query fails, or
            // after any other failed CRUD operation initiated from the UI.
            $this->setBackTo();

            return $this->returnView('results', $data);

        } else {

            // If we just updated/deleted record(s), then obviously
            // the previous query is now invalid, back to root URI.
            if ($this->getDataModified())
                return $this->goToRoot('ui.msg.data_modified');

            // Go back to last good index() query, with failure message.
            return $this->goBack('ui.msg.search_failed');
        }

    }

    /*
    |--------------------------------------------------------------------------
    | Listing JSON Output
    |--------------------------------------------------------------------------
    */

    /**
     * Return decorated listing data or null.
     *
     * Route: (GET) {uri}/json
     *
     * @return array|null
     */
    public function json()
    {
        $this->remember();

        $validator = Validator::make($this->getRequest()->getQueryInputs(), $this->getRequest()->getQueryRules());

        if ($validator->fails())
            return null;

        // Return full data if we have results, otherwise empty array.
        return ($this->getPublisher()->displayed()) ? $this->getPublisher()->presentData() : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Composer Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Use the bootstrap trait to generate some fancy UI elements from data.
     *
     * @return array|null
     */
    public function composeListing()
    {
        return [
            'info' => $this->ls('ui.results.viewing')
                .' '.$this->getPublisher()->getPagination()['viewing']['first']
                .$this->ls('ui.symbol.range').$this->getPublisher()->getPagination()['viewing']['last']
                .' '.$this->ls('ui.results.of').' '.$this->getPublisher()->found()
                .' '.$this->ls('ui.results.records', $this->getPublisher()->found()),
            'page_menu' => $this->pageNav(),
            'query_dropdowns' => '',
            'keyword_search' => $this->keywordSearch(),
            'view_menu' => $this->viewMenu(),
            'items' => $this->getPublisher()->presentItems(),
            'message' => $this->getMessage(),
            'path' => $this->currentRouteUrlDir(),
            'domain' => $this->getConfig()->getDomain(),
            'trans_prefix' => $this->getConfig()->getTranslationPrefix(),
            'namespace' => $this->getConfig()->getResourceNamespace(),
            'view_prefix' => $this->getConfig()->getDomainViewPrefix(),
            'title' => 'Search Results',
            'query_info' => $this->getPublisher()->getQueryInfo()
        ];
    }

    /**
     * Return array of view data, determined by the current controller method.
     * Actions 'store', 'update', and 'delete' are silent with redirects back.
     * Actions 'edit', 'show', and 'index' query db, while 'create' does not.
     *
     * @return array|null
     */
    public function composeItem()
    {
        $action_method = $this->currentRouteActionMethod();

        $data = [
            'query' => null,
            'item' => null,
            'message' => $this->getMessage(),
            'path' => $this->currentRouteUrlDir(),
            'domain' => $this->getConfig()->getDomain(),
            'trans_prefix' => $this->getConfig()->getTranslationPrefix(),
            'namespace' => $this->getConfig()->getResourceNamespace(),
            'view_prefix' => $this->getConfig()->getDomainViewPrefix(),
            'read_only' => false,
            'back_to' => $this->getBackTo(),
            'query_info' => null,
            'action_method' => $action_method
        ];

        switch ($action_method) {

            case 'show': // Route: (GET) {uri}/{$id} - query and show. No form action uri.
                $data['query'] = $this->getPublisher()->getQueryInfo();
                $data['item'] = $this->getPublisher()->getItems()[0];
                $data['form_action'] = 'show';
                $action_message = $this->ls('ui.title.view_domain_record', ['domain' => $this->getConfig()->getDomain()])
                    .' #'.$this->getRequest()->getQueryInput('id');
                $data['title'] = $action_message;
                $data['info'] = $action_message.': ';
                $data['read_only'] = true;
                break;

            case 'create': // Route: (GET) {uri}/create - show new item form, use inputs if provided.
                $data['item'] = $this->getRequest()->except('id');

                // Use {uri}/store as form action uri.
                $form_action = $this->currentRouteUrlDir().'/store';

                $data['form_action'] = $form_action;
                $action_message = $this->ls('ui.title.new_domain_record', ['domain' => $this->getConfig()->getDomain()]);
                $data['title'] = $action_message;
                $data['info'] = $action_message.': ';
                break;

            case 'edit': // Route: (GET) {uri}/{id}/edit - query and show.
                $data['query'] = $this->getPublisher()->getQueryInfo();
                $data['item'] = $this->getPublisher()->getItems()[0];

                // Use {uri}/{id} as form action uri.
                $form_action = $this->currentRouteUrlDir();

                $data['form_action'] = $form_action;
                $action_message = $this->ls('ui.title.update_domain_record', ['domain' => $this->getConfig()->getDomain()])
                    .' #'.$this->getRequest()->getQueryInput('id');
                $data['title'] = $action_message;
                $data['info'] = $action_message.': ';
                break;

            case 'store': // Route: (POST) {uri}/store - no composition
                break;

            case 'update': // Route: (PUT) {uri}/{id} - no composition
                break;

            case 'destroy': // Fall through to delete

            case 'delete': // Route: (DELETE) {uri}/{id} - no composition
                break;
        }

        return $data;

    }

    /*
    |--------------------------------------------------------------------------
    | Publisher Single-Record CRUD Operations
    |--------------------------------------------------------------------------
    */

    /**
     * Show the form for new item, with empty 'id' parameter.
     * The form should be submitted to the 'store' route.
     *
     * Route: (GET) {uri}/create
     *
     * @return Response
     */
    public function create()
    {
        $this->remember();

        return $this->returnView('item', $this->composeItem());
    }

    /**
     * Stores a new item and redirects back.
     *
     * Route: (POST) {uri}/store
     *
     * @return Response
     */
    public function store()
    {
        $this->remember();

        $validator = Validator::make($this->getRequest()->getInputs(), $this->getRequest()->getRequestRules());
        $validator->validate();


        if ($this->getPublisher()->store())
            return $this->goBack('ui.msg.item_created');
        else
            return $this->goBack('ui.msg.item_not_created');

    }

    /**
     * Display the specified resource.
     *
     * Route: (GET) {uri}/{$id}
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return $this->edit($id);
    }

    /**
     * Show the form for editing an existing item, specified by id.
     * The form should be submitted to the 'update' route.
     *
     * Route: (GET) {uri}/{id}/edit
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $this->remember();
        $this->getRequest()->mergeInputs(['id' => $id]);

        if ($this->getPublisher()->displayed())
            return $this->returnView('item', $this->composeItem());
        else
            return $this->goBack('ui.msg.search_failed');
    }

    /**
     * Updates the specified record using POST inputs, redirects back.
     *
     * Route: (PUT) {uri}/{id}
     *
     * @param int $id
     * @return Response
     */
    public function update($id)
    {
        $this->remember();
        $this->getRequest()->mergeInputs(['id' => $id]);

        $validator = Validator::make($this->getRequest()->getInputs(), $this->getRequest()->getRequestRules());
        $validator->validate();

        if ($this->getPublisher()->update()) {
            $this->setDataModified();
            return $this->goBack('ui.msg.item_updated');
        } else
            return $this->goBack('ui.msg.item_not_updated');

    }

    /**
     * Remove the specified resource from storage.
     *
     * Route: (DELETE) {uri}/{id}
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $this->remember();
        $this->getRequest()->setInputs(['id' => $id]);

        $validator = Validator::make(['id' => $id], $this->getRequest()->getRequestRules());
        $validator->validate();

        if ($this->getPublisher()->delete()) {
            $this->setDataModified();
            return $this->goBack('ui.msg.item_deleted');
        } else
            return $this->goBack('ui.msg.item_not_deleted');
    }

    /*
    |--------------------------------------------------------------------------
    | Publisher Multi-Record List Actions
    |--------------------------------------------------------------------------
    */

    /**
     * Responds to 'action' of listing on selected items, and redirects back.
     *
     * Route: (GET) {uri}/action
     *
     * @return Response
     */
    public function action()
    {
        $this->remember();

        $validator = Validator::make($this->getRequest()->getQueryInputs(), $this->getRequest()->getQueryRules());
        $validator->validate();

        if ($affected = $this->getPublisher()->action()) {
            $this->setDataModified();
            return $this->goBack('ui.msg.records_affected_by', ['action' => ("\"".$this->getRequest()->getAction()."\""), 'affected' => $affected]);
        } else
            return $this->goBack('ui.msg.no_data_affected');
    }

    /*
    |--------------------------------------------------------------------------
    | Utility Functions
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
        return $this->getPublisher()->ls($key, $option);
    }
    
}
