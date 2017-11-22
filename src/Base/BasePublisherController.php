<?php

namespace Viewflex\Ligero\Base;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Viewflex\Ligero\Publishers\HasPublisherSession;
use Viewflex\Ligero\Utility\BootstrapUiTrait;
use Viewflex\Ligero\Utility\RouteHelperTrait;

/**
 * Base publisher controller - extend and create publisher in constructor.
 * 
 * HasPublisherSession trait provides the methods for creating a publisher
 * and managing it's components, and includes methods that use session
 * to remember 'back_to' location and return there with status message
 * after operation, failed query, validation, etc.
 *
 * Any controller can have a primary publisher, saved as an attribute,
 * along with it's request, config, and query instances. Additional
 * secondary publishers can be created as needed, using newPublisher().
 *
 *
 * In this example, the primary publisher is created in the constructor:
 *
 * public function __construct(Config $config, Request $request, Query $query)
 * {
 *     $this->createPublisher($config, $request, $query);
 * }
 *
 *
 * In this example, a publisher is created in the local scope of a method:
 *
 * public function special(Config $config, Request $request, Query $query)
 * {
 *     $special = $this->newPublisher($config, $request, $query);
 *     ...
 * }
 * 
 */
abstract class BasePublisherController extends Controller
{
    use HasPublisherSession, RouteHelperTrait, BootstrapUiTrait;

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

        $validator = Validator::make($this->request->getQueryInputs(), $this->request->getRules());
        $validator->validate();
        
        // Return view if we have results, otherwise redirect back.
        if ($this->publisher->displayed()) {

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
                return $this->goToRoot('ui.msg_data_modified');

            // Go back to last good index() query, with failure message.
            return $this->goBack('ui.msg_search_failed');
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

        $validator = Validator::make($this->request->getQueryInputs(), $this->request->getRules());

        if ($validator->fails())
            return null;
        
        // Return full data if we have results, otherwise empty array.
        return ($this->publisher->displayed()) ? $this->publisher->presentData() : null;
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
            'info' => $this->config->ls('ui.viewing')
                .' '.$this->publisher->getPagination()['viewing']['first']
                .'~' . $this->publisher->getPagination()['viewing']['last']
                .' '.$this->config->ls('ui.of').' '.$this->publisher->found()
                .' '.$this->config->ls('ui.records', $this->publisher->found()),
            'page_menu' => $this->pageNav(),
            'query_dropdowns' => '',
            'keyword_search' => $this->keywordSearch(),
            'view_menu' => $this->viewMenu(),
            'items' => $this->publisher->presentItems(),
            'message' => $this->getMessage(),
            'path' => $this->currentRouteUrlDir(),
            'domain' => $this->config->getDomain(),
            'trans_prefix' => $this->config->getTranslationPrefix(),
            'namespace' => $this->config->getResourceNamespace(),
            'view_prefix' => $this->config->getDomainViewPrefix(),
            'title' => 'Search Results',
            'query_info' => $this->publisher->getQueryInfo()
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
            'domain' => $this->config->getDomain(),
            'trans_prefix' => $this->config->getTranslationPrefix(),
            'namespace' => $this->config->getResourceNamespace(),
            'view_prefix' => $this->config->getDomainViewPrefix(),
            'read_only' => false,
            'back_to' => $this->getBackTo(),
            'query_info' => null,
            'action_method' => $action_method
        ];

        switch ($action_method) {

            case 'show': // Route: (GET) {uri}/{$id} - query and show. No form action uri.
                $data['query'] = $this->publisher->getQueryInfo();
                $data['item'] = $this->publisher->getItems()[0];
                $data['form_action'] = 'show';
                $action_message = $this->config->ls('ui.action_view_domain_record', ['domain' => $this->config->getDomain()])
                    .' #'.$this->request->getQueryInput('id');
                $data['title'] = $action_message;
                $data['info'] = $action_message.': ';
                $data['read_only'] = true;
                break;

            case 'create': // Route: (GET) {uri}/create - show new item form, use inputs if provided.
                $data['item'] = $this->request->except('id');

                // Use {uri}/store as form action uri.
                $form_action = $this->currentRouteUrlDir().'/store';

                $data['form_action'] = $form_action;
                $action_message = $this->config->ls('ui.action_new_domain_record', ['domain' => $this->config->getDomain()]);
                $data['title'] = $action_message;
                $data['info'] = $action_message.': ';
                break;

            case 'edit': // Route: (GET) {uri}/{id}/edit - query and show.
                $data['query'] = $this->publisher->getQueryInfo();
                $data['item'] = $this->publisher->getItems()[0];

                // Use {uri}/{id} as form action uri.
                $form_action = $this->currentRouteUrlDir();

                $data['form_action'] = $form_action;
                $action_message = $this->config->ls('ui.action_update_domain_record', ['domain' => $this->config->getDomain()])
                    .' #'.$this->request->getQueryInput('id');
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

        $validator = Validator::make($this->request->getInputs(), $this->request->getPostRules());
        $validator->validate();


        if ($this->publisher->store())
            return $this->goBack('ui.msg_item_created');
        else
            return $this->goBack('ui.msg_item_not_created');

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
        $this->request->mergeInputs(['id' => $id]);
        
        if ($this->publisher->displayed())
            return $this->returnView('item', $this->composeItem());
        else
            return $this->goBack('ui.msg_search_failed');
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
        $this->request->mergeInputs(['id' => $id]);

        $validator = Validator::make($this->request->getInputs(), $this->request->getPostRules());
        $validator->validate();

        if ($this->publisher->update()) {
            $this->setDataModified();
            return $this->goBack('ui.msg_item_updated');
        } else
            return $this->goBack('ui.msg_item_not_updated');

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
        $this->request->setInputs(['id' => $id]);

        $validator = Validator::make(['id' => $id], $this->request->getPostRules());
        $validator->validate();

        if ($this->publisher->delete()) {
            $this->setDataModified();
            return $this->goBack('ui.msg_item_deleted');
        } else
            return $this->goBack('ui.msg_item_not_deleted');
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

        $validator = Validator::make($this->request->getQueryInputs(), $this->request->getRules());
        $validator->validate();

        if ($affected = $this->publisher->action()) {
            $this->setDataModified();
            return $this->goBack('ui.records_affected_by', ['action' => ("\"".$this->request->getAction()."\""), 'affected' => $affected]);
        } else
            return $this->goBack('ui.msg_no_data_affected');
    }
    
}
