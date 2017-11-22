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
    use ModelLoaderTrait, RouteHelperTrait;

    /*
    |--------------------------------------------------------------------------
    | Component Objects
    |--------------------------------------------------------------------------
    */
    
    /**
     * The configuration to use for this publisher implementation.
     *
     * @var Config
     */
    protected $config;

    /**
     * The current request, with it's validated user input.
     *
     * @var Request
     */
    protected $request;

    /**
     * Repository of methods for querying the data source.
     *
     * @var Query
     */
    protected $query;

    /**
     * Returns the config used by publisher implementation.
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Returns the request used by publisher implementation.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Returns the query used by publisher implementation.
     *
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
     * Sets the query used by listing implementation.
     *
     * @param Query $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
        $this->query->setApi($this);
    }

    /**
     * Save injected Config and Request, use injected Search if
     * if given, or create one, to use it's implementation of
     * getItems(), getDistinctColumn(), found(), displayed().
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

    /*
    |--------------------------------------------------------------------------
    | Output Bundles
    |--------------------------------------------------------------------------
    */

    /**
     * Returns info on query and results.
     *
     * @return array
     */
    public function getQueryInfo()
    {
        return [
            'self'                      =>  $this->urlSelf(),
            'query'                     =>  $this->urlSelfQueryString(),
            'route'                     =>  $this->getRoute(),
            'parameters'                =>  [
                'inputs'                    =>  $this->getUrlParametersExcept(),
                'defaults'                  =>  array_filter($this->config->getQueryDefaults(), 'strlen'),
                'all'                       =>  $this->getQueryParameters()
            ],
            'view'                      =>  $this->getQueryView(),
            'limit'                     =>  $this->getQueryLimit(),
            'start'                     =>  $this->getQueryStart(),
            'found'                     =>  $this->query->found(),
            'displayed'                 =>  $this->query->displayed()
        ];
    }

    /**
     * Returns total number of records that would be found
     * if we were using publisher query without a limit.
     *
     * @return int
     */
    public function found()
    {
        return $this->query->found();
    }

    /**
     * Returns the number of records actually returned by publisher query.
     *
     * @return int
     */
    public function displayed()
    {
        return $this->query->displayed();
    }
    
    /**
     * Returns the results of listing query in native format.
     *
     * @return mixed
     */
    public function getResults()
    {
        return $this->query->getResults();
    }

    /**
     * Returns the results of listing query as array or null.
     *
     * @return mixed
     */
    public function getItems()
    {
        return $this->query->getItems();
    }

    /**
     * Returns data for pagination UI controls and labels.
     * All URL parameters persist, only start changes,
     * unless of course we're switching view mode.
     *
     * Because user might conceivably input a start value that does not
     * correspond to a logical page start, we must offer two flavors
     * of pager links and page tally string - logical and relative.
     *
     * With relative navigation (relative to current start),
     * number of pages in page list can vary, but the first
     * page always starts at 0. Links are set to null if
     * they should be disabled, ie: we're already there.
     *
     * Switching view can be logical, relative or fresh.
     *
     * Page links with targets matching the current page and
     * context will be returned with null values for urls.
     *
     * @return mixed
     */
    public function getPagination()
    {
        if ($this->config->getControl('pagination')) {

            $found = $this->query->found();
            $view = $this->getQueryView();
            $start = $this->getQueryStart();
            $displayed = $this->query->displayed();

            $str_first = strval($start + 1);
            $str_last = $displayed > 1 ? strval($start+$displayed) : strval($start + $displayed);
            $str_range = $displayed > 1 ? $str_first.$this->config->ls('ui.through').$str_last : $str_first;
            $route = $this->getRoute();

            $config = $this->config->getPaginationConfig();
            $pager = $page_menu = $view_menu = null;

            if ($config['pager']['make'])
                $pager = $this->pager($config['pager']['context']);

            if ($config['page_menu']['make'])
                $page_menu = $this->pageMenu($config['page_menu']['context']);

            if ($config['view_menu']['make'])
                $view_menu = $this->viewMenu($config['view_menu']['context']);


            ## -------------------- Package the results -------------------- ##
            return [
                'config'            =>  $config,
                'route'             =>  $route,
                'viewing'           =>  [
                    'view'              =>  $view,
                    'first'             =>  $str_first,
                    'last'              =>  $str_last,
                    'range'             =>  $str_range,
                    'found'             =>  $found,
                    'displayed'         =>  $displayed,
                ],
                'pager'             =>  $pager,
                'page_menu'         =>  $page_menu,
                'view_menu'         =>  $view_menu
            ];
        }

        return null;
    }

    /**
     * Get API keyword query parameters and config. The persist_keyword
     * config determines whether input gets re-used as a prompt in
     * the composed UI control. All the query and display params
     * to be used in form are returned as parameters array.
     *
     * @return mixed
     */
    public function getKeywordSearch()
    {
        if ($this->config->getControl('keyword_search')) {

            $route = $this->getRoute();
            $config = $this->config->getKeywordSearchConfig();
            $base_params = $this->getUrlBaseParameters();
            $display_params = $this->getUrlDisplayParameters($config, $base_params);
            $keyword = $this->getQueryKeyword();

            // What params will we use as hidden values in form?
            switch ($config['scope']) {
                case 'global':
                    $params = $display_params;
                    break;

                default: // query scope
                    $params = array_merge(array_except($base_params, 'keyword'), $display_params);
                    break;
            }

            $clear = $route.$this->urlQueryString($params);

            return [
                'config'            =>  [
                    'scope'             =>  $config['scope'],
                    'persist_sort'      =>  $config['persist_sort'],
                    'persist_view'      =>  $config['persist_view'],
                    'persist_input'     =>  $config['persist_input']
                ],
                'route'             =>  $route,
                'base_parameters'   =>  $params,
                'keyword'           =>  $keyword,
                'clear'             =>  $clear,
                'label_search'      =>  $this->config->ls('ui.label_search')
            ];

        }

        return null;
    }

    /**
     * Returns all publisher API data bundles together, along with query info.
     *
     * @return array
     */
    public function getData()
    {
        return [
            'query'                     =>  $this->getQueryInfo(),
            'items'                     =>  $this->getItems(),
            'pagination'                =>  $this->getPagination(),
            'keyword_search'            =>  $this->getKeywordSearch()
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Dynamically Formatted Results
    |--------------------------------------------------------------------------
    */

    /**
     * Returns processed results of listing query as array.
     *
     * @return array|null
     */
    public function presentItems()
    {
        $items = [];
        $results = $this->getResults();

        if ($results && !$results->isEmpty()) {
            $columns = $this->config->getResultsColumns($this->getQueryView());

            $has_presenter = ($results->first()->getPresenter());

            // If model has a presenter, set it's config reference.
            if ($has_presenter)
                $results->first()->present($this->getConfig());

            $display_position = 0;
            $query_position = $this->getQueryStart() - 1;

            // For select_all action, set form checkboxes for displayed items.
            $inputs = $this->request->getQueryInputs();
            $form_item_checked = (array_key_exists('action', $inputs) && $inputs['action'] == 'select_all') ? ' CHECKED' : '';

            // Loop through collection.
            foreach ($results as $results_item) {
                $item = [];
                $display_position++;
                $query_position++;

                // Process this item's columns.
                foreach ($columns as $column) {

                    // If model has a presenter, run this column name through it.
                    if ($has_presenter)
                        $item[$column] = $results_item->present()->$column;
                    else
                        $item[$column] = $results_item->$column;
                }

                // Generate additional dynamic values
                $dynamic_fields = $results_item->present()->dynamicFields;
                $dynamic_urls = $this->itemViewChangUrls($query_position);

                // Merge arrays and add item to array of items.
                $items[] = array_merge($item, $dynamic_fields, $dynamic_urls,
                    ['display_position' => $display_position, 'query_position' => $query_position, 'form_item_checked' => $form_item_checked]);
            }

        }

        return $items;
    }

    /**
     * Get item view-change urls - going to the new view
     * will have that item as the first one displayed.
     *
     * When we're already in a given view, that url is '#'.
     * The 'default_view' url removes the view parameter
     * and is active even when in that view already.
     *
     * @param int $query_position
     * @return array
     */
    public function itemViewChangUrls($query_position = 0)
    {
        $route = $this->getRoute();
        $view = $this->getQueryView();

        $skip = [
            'view',
            'start',
            'action',
            'items',
            'options',
            'page'
        ];

        // Views corresponding to current relative start
        $rel_view_base_params = array_add($this->getUrlParametersExcept($skip), 'start', $query_position);
        $urls = [
            'list_view' => (($view != 'list') ? $route.$this->urlQueryString(array_add($rel_view_base_params, 'view', 'list')) : '#'),
            'grid_view' => (($view != 'grid') ? $route.$this->urlQueryString(array_add($rel_view_base_params, 'view', 'grid')) : '#'),
            'item_view' => (($view != 'item') ? $route.$this->urlQueryString(array_add($rel_view_base_params, 'view', 'item')) : '#'),
            'default_view' => ($route.$this->urlQueryString($rel_view_base_params))
        ];

        return $urls;
    }

    /**
     * Returns all publisher data bundles together, along with query info.
     *
     * @return array
     */
    public function presentData()
    {
        return [
            'query'                     =>  $this->getQueryInfo(),
            'items'                     =>  $this->presentItems(),
            'pagination'                =>  $this->getPagination(),
            'keyword_search'            =>  $this->getKeywordSearch()
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Query Input Parameters
    |--------------------------------------------------------------------------
    */

    /**
     * Returns current route as absolute or relative URL, without query parameters.
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->currentRouteUrlRoot($this->config->absoluteUrls());
    }
    
    /**
     * Returns, in a fixed order, the validated user inputs, plus any existing
     * defaults applied where user inputs are empty. This array is used in db
     * queries, unlike $url_base_parameters, which is used to build URLs.
     *
     * @return array
     */
    public function getQueryParameters()
    {
        $query_defaults = array_filter($this->config->getQueryDefaults(), 'strlen');
        $query_params = $this->request->getQueryInputs();
        
        foreach ($query_defaults as $key => $default)
            $query_params = array_add($query_params, $key, $default);

        $keys = array_keys($this->request->rules());

        // Arrange the params in the proper order.
        $params = [];
        foreach($keys as $key) {
            if (array_key_exists($key, $query_params)) {
                $params = array_add($params, $key, $query_params[$key]);
            }
        }

        return $params;
    }
    
    /**
     * Returns the current query keyword.
     *
     * @return string
     */
    public function getQueryKeyword()
    {
        $params = $this->getQueryParameters();
        return array_key_exists('keyword', $params) ? $params['keyword'] : '';
    }

    /**
     * Returns name of current item view mode.
     *
     * @return string
     */
    public function getQueryView()
    {
        $params = $this->getQueryParameters();
        return array_key_exists('view', $params) ? $params['view'] : 'default';
    }
    
    /**
     * Returns sort as used in listing query.
     *
     * @return array
     */
    public function getQuerySort()
    {
        $params = $this->getQueryParameters();
        return $this->config->getSort(array_key_exists('sort', $params) ? $params['sort'] : 'default');
    }

    /**
     * Returns limit as used in listing query.
     *
     * @return int
     */
    public function getQueryLimit()
    {
        $params = $this->getQueryParameters();

        if (array_key_exists('view', $params))
            $limit = $this->config->getViewLimit($params['view']);
        else
            $limit = $this->config->getViewLimit();

        if (array_key_exists('limit', $params))
            $limit = intval($params['limit']);

        return $limit;
    }

    /**
     * Returns start as used in listing query. If page
     * is specified explicitly, use to calculate start,
     * but only if there's no start specified as well.
     *
     * @return int
     */
    public function getQueryStart()
    {
        $params = $this->getQueryParameters();

        $start_given = array_key_exists('start', $params);

        if ((!$start_given) && ($page_number = $this->getQueryPage())) {
            $query_limit = $this->getQueryLimit();
            return (($page_number * $query_limit) - $query_limit);
        }


        return $start_given ? intval($params['start']) : 0;
    }

    /**
     * Returns page as used to calculate start, or null if not present in inputs.
     *
     * @return int|null
     */
    public function getQueryPage()
    {
        $params = $this->getQueryParameters();
        return array_key_exists('page', $params) ? intval($params['page']) : null;
    }

    /**
     * Returns, in a fixed order, the base query parameters for generating URL query
     * strings in breadcrumbs, query and sort menus, so we use no default parameters,
     * only the inputs (if not the same as defaults), including sort, view and limit.
     * Start is skipped, as are qid, qname, and iref, since we generate new queries.
     *
     * @return array
     */
    public function getUrlBaseParameters()
    {
        return $this->getUrlParametersExcept([
            'start',
            'action',
            'items',
            'options',
            'page'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Utility Functions
    |--------------------------------------------------------------------------
    */

    /**
     * Returns, in a fixed order, the validated user inputs, plus any existing
     * defaults applied where user inputs are empty. This array is used in db
     * queries for store(), update(), and delete() methods, using POST rules.
     *
     * @return array
     */
    public function getRequestParameters()
    {
        $request_params = $this->request->getInputs();
        $keys = array_keys($this->request->getPostRules());

        // Arrange the params in the proper order.
        $params = [];
        foreach($keys as $key) {
            if (array_key_exists($key, $request_params)) {
                $params = array_add($params, $key, $request_params[$key]);
            }
        }

        return $params;
    }
    
    /**
     * Strips this API's particular query parameters that don't reference actual columns.
     *
     * @param array $parameters
     * @return array
     */
    public function dbQueryParameters($parameters = [])
    {
        return array_except($parameters, ['keyword', 'sort', 'view', 'limit', 'start', 'action', 'items', 'options', 'page']);
    }
    
    /**
     * Returns, in a fixed order, all query parameters for generating URL query strings.
     * The optional parameter $skip allows filtering of undesired parameters.
     *
     * @param array $skip
     * @return array
     */
    public function getUrlParametersExcept($skip = [])
    {
        $query_params = [];
        $defaults = array_filter($this->config->getQueryDefaults(), 'strlen');
        $inputs = $this->request->getQueryInputs();
        $keys = array_keys($this->request->rules());

        foreach($keys as $key) {
            if (array_key_exists($key, $inputs) && !in_array($key, $skip)) {
                if (array_key_exists($key, $defaults)) {
                    if ($inputs[$key] !== $defaults[$key])
                        $query_params = array_add($query_params, $key, $inputs[$key]);
                }
                else
                    $query_params = array_add($query_params, $key, $inputs[$key]);
            }
        }

        return $query_params;
    }

    /**
     * Returns a URL query string from input array.
     *
     * @param array $parameters
     * @return string
     */
    public function urlQueryString($parameters = [])
    {
        $keys = array_keys($this->request->rules());
        // Arrange the params in the proper order.
        $query_parameters = [];
        foreach($keys as $key) {
            if (array_key_exists($key, $parameters)) {
                $query_parameters = array_add($query_parameters, $key, $parameters[$key]);
            }
        }

        $query_string = '';
        $pos = 0;
        foreach($query_parameters as $key => $value) {
            // Skip array parameters
            if (!is_array($value)) {
                $query_string .= (($pos ? '&' : '?').($key.'='.rawurlencode($value)));
                $pos++;
            }
        }

        return $query_string;
    }

    /**
     * Returns a URL query string from input array plus starting row.
     *
     * @param array $params
     * @param int $start
     * @return string
     */
    public function urlQueryWithStart($params, $start)
    {
        return $this->urlQueryString(($start > 0) ? array_add($params, 'start', $start) : $params);
    }

    /**
     * Returns a URL query string from input array plus page.
     *
     * @param array $params
     * @param int $page
     * @return string
     */
    public function urlQueryWithPage($params, $page)
    {
        return $this->urlQueryString(($page > 1) ? array_add($params, 'page', $page) : $params);
    }

    /**
     * Returns the current URL, with parameters properly ordered.
     * Non-query action and items (array) params are skipped.
     *
     * @return string
     */
    public function urlSelf()
    {
        return $this->currentRouteUrlRoot().$this->urlSelfQueryString();
    }

    /**
     * Returns the current URL's parameters properly ordered.
     * Non-query action and items (array) params are skipped.
     *
     * @return string
     */
    public function urlSelfQueryString()
    {
        $skip = [
            'action',
            'items',
            'options'
        ];
        return $this->urlQueryString($this->getUrlParametersExcept($skip));
    }
    
    /**
     * Returns array of params for sort, view and limit, as determined by the given config.
     * Used by getBreadcrumbs(), getQueryMenus(), getKeywordSearch(), itemCrossQueryUrls().
     *
     * @param array $config
     * @param array $base_params
     * @return array
     */
    public function getUrlDisplayParameters($config = [], $base_params = [])
    {
        $display_params = [];

        // If configured to persist sort...
        if ($config['persist_sort'] === true && array_key_exists('sort', $base_params))
            $display_params = array_add($display_params, 'sort', $base_params['sort']);

        // If configured to persist view/limit...
        if ($config['persist_view'] === true) {
            if (array_key_exists('view', $base_params))
                $display_params = array_add($display_params, 'view', $base_params['view']);

            if (array_key_exists('limit', $base_params))
                $display_params = array_add($display_params, 'limit', $base_params['limit']);
        }

        return $display_params;
    }

    /**
     * Returns, in a fixed order, all base query params for navigation links.
     *
     * @return array
     */
    public function getNavUrlBaseParameters() {

        $skip = [
            'start',
            'action',
            'items',
            'options',
            'page'
        ];

        return $this->getUrlParametersExcept($skip);

    }

    /**
     * Returns number of pages required to display items,
     * given items count, limit, and the starting row.
     *
     * @param int $found
     * @param int $limit
     * @param int $start
     * @return int
     */
    public function numPages($found = 0, $limit = 1, $start = 0)
    {
        $start = $start < 0 ? 0 : $start;

        if ($found) {
            if ($start == 0)
                $num_pages = intval(ceil($found / $limit));
            else {
                $prior = $start;
                $forward = $found - $prior;
                $num_pages = intval(ceil($prior / $limit)) + intval(ceil($forward / $limit));
            }
        }
        else
            $num_pages = 0;

        return $num_pages;
    }

    /**
     * Returns logical page of given start, ie:
     * if pages were ordered from true start.
     *
     * @param int $start
     * @return int
     */
    public function logicalPage($start = 0) {

        $limit = $this->getQueryLimit();
        $logical_offset = $start % $limit;
        $current_logical_start = $start - $logical_offset;
        return intval(floor(($current_logical_start + 1) / $limit) + ($limit > 1 ? 1 : 0));
    }

    /**
     * Returns relative page of given start.
     *
     * @param int $start
     * @return int
     */
    public function relativePage($start = 0) {

        $limit = $this->getQueryLimit();
        $logical_offset = $start % $limit;
        return $logical_offset === 0 ? $this->logicalPage($start) : intval(ceil(($start + 1) / $limit) + 1);
    }

    /**
     * Returns a pager in logical or relative context.
     *
     * @param string $context
     * @return array
     */
    public function pager($context = 'relative') {

        $found = $this->query->found();
        $limit = $this->getQueryLimit();
        $start = $this->getQueryStart();
        $route = $this->getRoute();

        $logical_offset = $start % $limit;
        $current_logical_start = $start - $logical_offset;

        $current_logical_page = $this->logicalPage($start);
        $num_logical_pages = $this->numPages($found, $limit);

        $current_relative_page = $this->relativePage($start);
        $num_relative_pages = $logical_offset == 0 ? $this->numPages($found, $limit) : $this->numPages($found, $limit, $start);

        $nav_base_params = $this->getNavUrlBaseParameters();

        if ($context == 'logical') {

            $page_num = $current_logical_page;
            $num_pages = $num_logical_pages;
            $use_page_number = $this->config->getPaginationConfig()['use_page_number'];

            if ($current_logical_start < $limit) {
                $prev_page = $first_page = null; // We're already on first page
            } else {

                if ($use_page_number) {
                    $first_page = $route.$this->urlQueryWithPage($nav_base_params, strval(1));
                    $prev_page = $route.$this->urlQueryWithPage($nav_base_params, strval($current_logical_page - 1));
                } else {
                    $first_page = $route.$this->urlQueryWithStart($nav_base_params, strval(0));
                    $prev_page = $route.$this->urlQueryWithStart($nav_base_params, strval($current_logical_start - $limit));
                }
            }

            if (($current_logical_start + $limit) >= $found)
                $next_page = $last_page = null; // We're already on last page
            else {
                if ($use_page_number) {
                    $next_page = $route.$this->urlQueryWithPage($nav_base_params, strval($current_logical_page + 1));
                    $last_page = $route.$this->urlQueryWithPage($nav_base_params, strval( ceil($found / $limit) ));
                } else {
                    $next_page = $route.$this->urlQueryWithStart($nav_base_params, strval($current_logical_start + $limit));
                    $last_page = $route.$this->urlQueryWithStart($nav_base_params, strval((ceil($found / $limit) - 1) * $limit));
                }
            }
        }
        else { // relative

            $page_num = $current_relative_page;
            $num_pages = $num_relative_pages;

            if ($start < $limit) {
                if ($logical_offset == 0)
                    $prev_page = $first_page = null; // We're already on first page
                else {
                    $first_page = $route.$this->urlQueryWithStart($nav_base_params, strval(0));
                    $prev_page = $route.$this->urlQueryWithStart($nav_base_params, strval($current_logical_start));
                }
            }
            else {
                $first_page = $route.$this->urlQueryWithStart($nav_base_params, strval(0));
                $prev_page = $route.$this->urlQueryWithStart($nav_base_params, strval($start - $limit));
            }

            if (($start + $limit) >= $found)
                $next_page = $last_page = null; // We're already on last page
            else {
                $next_page = $route.$this->urlQueryWithStart($nav_base_params, strval($start + $limit));
                $last_page = $route.$this->urlQueryWithStart($nav_base_params, strval($start + ((ceil(($found - $start) / $limit) - 1) * $limit)));
            }
        }

        return [
            'context'           =>  $context,
            'pages'             =>  [
                'first'             =>  $first_page,
                'prev'              =>  $prev_page,
                'next'              =>  $next_page,
                'last'              =>  $last_page
            ],
            'page_num'          =>  $page_num,
            'num_pages'         =>  $num_pages
        ];
    }

    /**
     * Returns an array of page links, indexed by
     * page number with start and url for each.
     *
     * Context determines logical or relative
     * (ie: actual) page start positions.
     *
     * @param string $context
     * @return array
     * @internal param int $start
     */
    public function pageMenu($context = 'relative')
    {
        $pages = [];
        $found = $this->query->found();
        $limit = $this->getQueryLimit();
        $start = $this->getQueryStart();
        $route = $this->getRoute();

        $logical_offset = $start % $limit;
        $current_logical_start = $start - $logical_offset;

        $nav_base_params = $this->getNavUrlBaseParameters();
        $max_links = $this->config->getPaginationConfig()['page_menu']['max_links'];

        if($context == 'relative') {
            // Allow pagination relative to current start row.
            $using_start = $start;
            $use_page_number = false;
        } else {
            // Make links with logical pagination (ie: page 1 = start 0),
            // and if enabled use page number instead of start row.
            $using_start = $current_logical_start;
            $use_page_number = $this->config->getPaginationConfig()['use_page_number'];
        }

        // How many pages are in the results using given start?
        $num_pages = $this->numPages($found, $limit, $using_start);
        $num_page_links = $num_pages <= $max_links ? $num_pages : $max_links;

        // Derive relative (or logical, depending on start) page number of current start.
        $current_page = $this->relativePage($using_start);

        // Current page (disabled)
        $pg = $current_page;
        $pages = array_add($pages, $pg, ['start' => $start, 'url' => null]);

        // Previous pages...
        $num_prev_links = intval(floor($num_page_links / 2));

        $prev = 0;
        if ($using_start > $limit) {

            $i = 1;
            $proceed = true;
            while (($i <= $num_prev_links) && ($proceed)) {

                $pg = $current_page - $i;
                $start_row = $using_start - ($i * $limit);
                if ($start_row < 0) {
                    $start_row = 0;
                    $proceed = false;
                }

                if($use_page_number) {
                    $url = $route.$this->urlQueryWithPage($nav_base_params, strval($pg));
                } else {
                    $url = $route.$this->urlQueryWithStart($nav_base_params, strval($start_row));
                }

                $pages = array_add($pages, $pg, ['start' => $start_row, 'url' => $url]);

                $prev++;
                $i++;
            }
        }

        // Next pages...
        $num_next_links = $num_page_links - count($pages);

        $next = 0;
        $i = 1;
        $proceed = true;
        while (($i <= $num_next_links) && ($proceed)) {

            $pg = $current_page + $i;
            $start_row = $using_start + ($i * $limit);
            if ($start_row + 1 > $found) {
                $proceed = false;
            }
            else {

                if($use_page_number) {
                    $url = $route.$this->urlQueryWithPage($nav_base_params, strval($pg));
                } else {
                    $url = $route.$this->urlQueryWithStart($nav_base_params, strval($start_row));
                }

                $pages = array_add($pages, $pg, ['start' => $start_row, 'url' => $url]);
            }

            $next++;
            $i++;
        }

        // More previous pages?...
        $num_extra_links = $num_page_links - count($pages);

        $i = 1;
        $proceed = true;
        while (($i <= $num_extra_links) && ($proceed)) {

            $pg = $current_page - ($i + $prev);
            $start_row = $using_start - (($i + $prev) * $limit);
            if ($start_row < 0) {
                $start_row = 0;
                $proceed = false;
            }

            if($use_page_number) {
                $url = $route.$this->urlQueryWithPage($nav_base_params, strval($pg));
            } else {
                $url = $route.$this->urlQueryWithStart($nav_base_params, strval($start_row));
            }

            $pages = array_add($pages, $pg, ['start' => $start_row, 'url' => $url]);

            $i++;
        }

        ksort($pages);

        return [
            'context'           =>  $context,
            'pages'             =>  $pages,
            'page_num'          =>  $current_page,
            'num_pages'         =>  $num_pages
        ];
    }

    /**
     * Returns array of view mode links, in one of three contexts:
     * logical (zero-start), relative, or fresh (non-contextual).
     * Omits view param matching default from generated urls.
     *
     * @param string $context
     * @return array
     */
    public function viewMenu($context = 'fresh')
    {
        $view = $this->getQueryView();
        $limit = $this->getQueryLimit();
        $start = $this->getQueryStart();
        $route = $this->getRoute();
        $logical_offset = $start % $limit;
        $current_logical_start = $start - $logical_offset;

        $skip = [
            'view',
            'limit',
            'start',
            'action',
            'items',
            'options',
            'page'
        ];
        $view_base_params = $this->getUrlParametersExcept($skip);
        $query_defaults = array_filter($this->config->getQueryDefaults(), 'strlen');
        $view_default = array_key_exists('view', $query_defaults) ? $query_defaults['view'] : '';

        switch ($context) {

            case 'logical':
            {
                // Views changes corresponding to current logical start,
                // use start row, or calculated page number, as configured,
                // to keep the starting record displayed after view change.

                if($this->config->getPaginationConfig()['use_page_number']) {
                    $limits = $this->config->getViewLimits();

                    // list
                    $limit = $limits['list'];
                    $page_number = intval(ceil(($start + 1) / $limit));
                    $base_params = ($page_number > 1) ? array_add($view_base_params, 'page', $page_number) : $view_base_params;
                    $list_view = ($view != 'list') ? $route.$this->urlQueryString($view_default == 'list' ? $base_params : array_add($base_params, 'view', 'list')) : null;

                    // grid
                    $limit = $limits['grid'];
                    $page_number = intval(ceil(($start + 1) / $limit));
                    $base_params = ($page_number > 1) ? array_add($view_base_params, 'page', $page_number) : $view_base_params;
                    $grid_view = ($view != 'grid') ? $route.$this->urlQueryString($view_default == 'grid' ? $base_params : array_add($base_params, 'view', 'grid')) : null;

                    // item
                    $limit = $limits['item'];
                    $page_number = intval(ceil(($start + 1) / $limit));
                    $base_params = ($page_number > 1) ? array_add($view_base_params, 'page', $page_number) : $view_base_params;
                    $item_view = ($view != 'item')? $route.$this->urlQueryString($view_default == 'item' ? $base_params : array_add($base_params, 'view', 'item')) : null;

                } else {
                    if($current_logical_start) {
                        $view_base_params = array_add($view_base_params, 'start', $current_logical_start);
                    }

                    $list_view = (($view != 'list') || ($logical_offset)) ? $route.$this->urlQueryString($view_default == 'list' ? $view_base_params : array_add($view_base_params, 'view', 'list')) : null;
                    $grid_view = (($view != 'grid') || ($logical_offset)) ? $route.$this->urlQueryString($view_default == 'grid' ? $view_base_params : array_add($view_base_params, 'view', 'grid')) : null;
                    $item_view = (($view != 'item') || ($logical_offset)) ? $route.$this->urlQueryString($view_default == 'item' ? $view_base_params : array_add($view_base_params, 'view', 'item')) : null;
                }
                break;
            }

            case 'relative':
            {
                // View changes corresponding to current relative start,
                // keep the starting record displayed after view change.

                if ($start)
                    $rel_view_base_params = array_add($view_base_params, 'start', $start);
                else
                    $rel_view_base_params = $view_base_params;
                $list_view = ($view != 'list') ? $route.$this->urlQueryString($view_default == 'list' ? $rel_view_base_params : array_add($rel_view_base_params, 'view', 'list')) : null;
                $grid_view = ($view != 'grid') ? $route.$this->urlQueryString($view_default == 'grid' ? $rel_view_base_params : array_add($rel_view_base_params, 'view', 'grid')) : null;
                $item_view = ($view != 'item') ? $route.$this->urlQueryString($view_default == 'item' ? $rel_view_base_params : array_add($rel_view_base_params, 'view', 'item')) : null;
                break;
            }

            default: // or fresh
            {
                // Views corresponding to zero logical start, without regard to current starting position.
                $list_view = !(($view == 'list') && ($start == 0)) ? $route.$this->urlQueryString($view_default == 'list' ? $view_base_params : array_add($view_base_params, 'view', 'list')) : null;
                $grid_view = !(($view == 'grid') && ($start == 0)) ? $route.$this->urlQueryString($view_default == 'grid' ? $view_base_params : array_add($view_base_params, 'view', 'grid')) : null;
                $item_view = !(($view == 'item') && ($start == 0)) ? $route.$this->urlQueryString($view_default == 'item' ? $view_base_params : array_add($view_base_params, 'view', 'item')) : null;
                break;
            }
        };

        return [
            'context'   =>  $context,
            'views'     =>  [
                'list'          =>  [
                    'display'   =>  $this->config->ls('ui.link_results_list'),
                    'limit'     =>  $this->config->getViewLimit('list'),
                    'url'       =>  $list_view
                ],
                'grid'          =>  [
                    'display'   =>  $this->config->ls('ui.link_results_grid'),
                    'limit'     =>  $this->config->getViewLimit('grid'),
                    'url'       =>  $grid_view
                ],
                'item'          =>  [
                    'display'   =>  $this->config->ls('ui.link_results_item'),
                    'limit'     =>  $this->config->getViewLimit('item'),
                    'url'       =>  $item_view
                ]
            ],
            'selected'          =>  $view,
            'label_view_as'     =>  $this->config->ls('ui.label_view_as')
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Query Actions
    |--------------------------------------------------------------------------
    */
    
    /**
     * Stores a new item using POST parameters.
     *
     * @return int
     */
    public function store()
    {
        return $this->query->store();
    }

    /**
     * Updates an existing item using POST parameters.
     *
     * @return int
     */
    public function update()
    {
        return $this->query->update();
    }

    /**
     * Deletes an existing item using POST parameters.
     *
     * @return int
     */
    public function delete()
    {
        return $this->query->delete();
    }

    /**
     * Calls the appropriate db query, returning number of rows affected.
     * Also sets the session's 'message' attribute for visual feedback.
     * Actions supported by base repository are 'clone' and 'delete' -
     * extend the base class to support custom list actions required.
     * This method can be used to transparently perform action on
     * selected list items and then redirect back to listing.
     *
     * The $options parameter can be used to pass through
     * any additional data required by the given action.
     *
     * @return int
     */
    public function action()
    {
        $affected_rows = 0;
        $action = $this->request->getAction();

        if (($action !== '') && ($action !== 'select_all') && count($this->request->getActionItems())) {
            $affected_rows = $this->query->action();
        }

        return $affected_rows;
    }
    
}
