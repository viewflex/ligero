<?php

namespace Viewflex\Ligero\Base;

use Carbon\Carbon;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Viewflex\Ligero\Contracts\PublisherRepositoryInterface;
use Viewflex\Ligero\Contracts\PublisherApiInterface as Api;
use Viewflex\Ligero\Contracts\PublisherConfigInterface as Config;
use Viewflex\Ligero\Contracts\PublisherRequestInterface as Request;
use Viewflex\Ligero\Exceptions\PublisherRepositoryException;
use Viewflex\Ligero\Utility\ArrayHelperTrait;
use Viewflex\Ligero\Utility\ModelLoaderTrait;

class BasePublisherRepository implements PublisherRepositoryInterface
{
    use ArrayHelperTrait, ModelLoaderTrait;

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
     * @var Model
     */
    protected $model;

    /*
    |--------------------------------------------------------------------------
    | Initialization
    |--------------------------------------------------------------------------
    */

    /**
     * @param Api $api
     */
    public function setApi(Api $api)
    {
        $this->api = $api;
        $this->config = $this->api->getConfig();
        $this->request = $this->api->getRequest();
        $this->loadModel();
    }

    /*
    |--------------------------------------------------------------------------
    | Database Read Operations
    |--------------------------------------------------------------------------
    |
    */

    /**
     * @return int
     * @throws PublisherRepositoryException
     */
    public function found()
    {
        $builder = $this->model->select($this->mapColumn('id'));
        $builder = $this->queryCriteria($builder);

        $found = function($builder) {
            return $builder->get()->count();
        };

        $rows_found = $this->cacheQuery($found, $builder);
        $this->logQuery('... found(): '.$rows_found.', query: '
            .$builder->getQuery()->toSql().' (bindings: '.serialize($builder->getQuery()->getBindings()).')');
        
        return $rows_found;
    }

    /**
     * @return int
     * @throws PublisherRepositoryException
     */
    public function displayed()
    {
        $results = $this->getResults();
        return $results ? $results->count() : 0;
    }
    
    /**
     * @return mixed
     * @throws PublisherRepositoryException
     */
    public function getResults()
    {
        $rows = null;

        if ($this->found()) {

            $builder = $this->publisherQuery();

            $results = function($builder) {
                return $builder->get();
            };

            $rows = $this->cacheQuery($results, $builder);
            $this->logQuery('... getResults(): rows returned: '.($rows ? strval(count($rows)) : '0').', query: '
                .$builder->getQuery()->toSql().' (bindings: '.serialize($builder->getQuery()->getBindings()));
        }

        return $rows;
    }

    /**
     * @return mixed
     * @throws PublisherRepositoryException
     */
    public function getItems()
    {
        $results = $this->getResults();
        return ($results && !$results->isEmpty()) ? $results->toArray() : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Builder Functions
    |--------------------------------------------------------------------------
    */

    /**
     * Add search criteria to the builder instance.
     *
     * @param Builder $builder
     * @return Builder
     */
    protected function queryCriteria(Builder $builder)
    {
        $builder->where(function($query) {

            $params = $this->api->getQueryParameters();
            $params = $this->formatDateTimeInputs($params);
            
            if (array_key_exists('id', $params))
                $query->where($this->mapColumn('id'), '=', $params['id']);

            $others = array_except($this->api->dbQueryParameters($params), ['id']);
            $wildcards = $this->config->getWildcardColumns();

            foreach($others as $column => $value)
            {
                if (in_array($column, $wildcards))
                    $query->where($this->mapColumn($column), 'LIKE', ('%'.$value.'%'));
                else
                    $query->where($this->mapColumn($column), '=', $value);
            }

            // Keyword search is performed on array of columns.
            // Assumes table type offering full-text search.
            if (array_key_exists('keyword', $params)) {

                $query->where(function($keyword_query) {
                    $keyword = $this->api->getQueryParameters()['keyword'];
                    foreach ($this->config->getKeywordSearchConfig()['columns'] as $column) {
                        $keyword_query->orWhere($this->mapColumn($column), 'LIKE', ('%'.$keyword.'%'));
                    }
                });
            }

        });

        return $builder;
    }

    /**
     * Add order and pagination to the builder instance.
     *
     * @param Builder $builder
     * @return Builder
     */
    protected function queryPagination(Builder $builder)
    {
        $builder->getQuery()->offset($this->api->getQueryStart());
        $builder->getQuery()->limit($this->api->getQueryLimit());

        $sort = $this->api->getQuerySort();
        foreach ($sort as $field => $direction) {
            $builder->getQuery()->orderBy($this->mapColumn($field), $direction);
        }

        return $builder;
    }

    /**
     * Return a Builder instance for getting listing results.
     * Combines criteria and order, pagination.
     *
     * @return Builder
     */
    protected function publisherQuery()
    {
        $builder = $this->mapSelect($this->config->getResultsColumns($this->api->getQueryView()));
        $builder = $this->queryCriteria($builder);
        $builder = $this->queryPagination($builder);
        return $builder;
    }

    /**
     * Return passed Builder with an orWhere line for each item id.
     *
     * @param Builder $builder
     * @return Builder
     */
    protected function whereItems(Builder $builder)
    {
        $params = $this->api->getQueryParameters();
        if (array_key_exists('items', $params)) {

            foreach ($params['items'] as $id)
                $builder->orWhere($this->mapColumn('id'), '=', $id);
        }

        return $builder;
    }

    /**
     * Return a unique query identifier for use as a cache key.
     *
     * @param \Illuminate\Database\Query\Builder $builder
     * @return string
     */
    protected function cacheKey($builder)
    {
        return md5($this->api->getRoute().$builder->toSql().serialize($builder->getBindings()));
    }

    /**
     * Return result from given query and builder,
     * taking advantage of caching, if available.
     *
     * @param Closure $function
     * @param Builder $builder
     * @return mixed
     * @throws PublisherRepositoryException
     */
    protected function cacheQuery($function, $builder)
    {
        $cache = $this->config->getCaching();

        if($cache['active']) {
            $key = $this->cacheKey($builder->getQuery());

            if (Cache::has($key)) {
                $result = unserialize(Cache::get($key));
                $this->logQuery('Cached results...');
            } else {

                $result = $function($builder);
                $this->logQuery('Fresh results, writing to cache...');

                if (!Cache::add($key, serialize($result),
                    Carbon::now()->addMinutes($cache['minutes']))) {
                    throw new PublisherRepositoryException('Unable to add results to cache.');
                }
            }

        } else {
            $result = $function($builder);
            $this->logQuery('Fresh results, caching not enabled...');
        }

        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | Database Write  Operations
    |--------------------------------------------------------------------------
    */

    /**
     * @return int
     * @throws PublisherRepositoryException
     */
    public function store()
    {
        $params = $this->api->dbQueryParameters($this->api->getRequestParameters());
        
        if (!$params)
            throw new PublisherRepositoryException('No attributes specified for creating new item.');

        // Set created_at column if it exists (and if not already in params).
        if ((array_key_exists('created_at', $columns = $this->request->getPostRules())) && (! array_key_exists('created_at', $params) ))
            $params['created_at'] = Carbon::now()->toDateTimeString();

        // Set updated_at column if it exists (and if not already in params).
        if ((array_key_exists('updated_at', $columns)) && (! array_key_exists('updated_at', $params) ))
            $params['updated_at'] = Carbon::now()->toDateTimeString();

        // Set creator_id column if it exists (and if not already in params).
        if ((array_key_exists('creator_id', $columns)) && (! array_key_exists('creator_id', $params) ))
            $params['creator_id'] = Auth::id() ? : 0;

        $params = $this->formatDateTimeInputs($params);
        $this->logQuery('store(): '.$this->arrayToString($params));

        $id = DB::table($this->config->getTableName())->insertGetId($this->mapAttributes(array_except($params, 'id')));

        if (!$id)
            throw new PublisherRepositoryException('Item could not be created.');
        
        Cache::flush();

        return $id;
    }

    /**
     * @return int
     * @throws PublisherRepositoryException
     */
    public function update()
    {
        $params = $this->api->dbQueryParameters($this->api->getRequestParameters());

        if (!$params)
            throw new PublisherRepositoryException('No attributes specified for updating item.');

        if ((!array_key_exists('id', $params)) || (intval($params['id'] < 1)))
            throw new PublisherRepositoryException('No valid id was specified for updating item.');

        // Set updated_at column if it exists (and if not already in params).
        if ((array_key_exists('updated_at', $this->request->getPostRules())) && (! array_key_exists('updated_at', $params) ))
            $params['updated_at'] = Carbon::now()->toDateTimeString();

        $params = $this->formatDateTimeInputs($params);
        $this->logQuery('update(): '.$this->arrayToString($params));

        $affected_rows = DB::table($this->config->getTableName())->where($this->mapColumn('id'), $params['id'])->update($params);
        
        if ($affected_rows)
            Cache::flush();
            
//            throw new PublisherRepositoryException('Item update could not be performed.');
//        return $affected_rows;
        
        // Todo: compare inputs to old data to decide if update is needed - if data is the same it returns 0, so we'll just ignore that for now.
        
        return 1;
    }

    /**
     * @return int
     * @throws PublisherRepositoryException
     */
    public function delete()
    {
        $params = $this->api->dbQueryParameters($this->api->getRequestParameters());
        
        if ((!array_key_exists('id', $params)) || (intval($params['id'] < 1)))
            throw new PublisherRepositoryException('No valid id was specified for deleting item.');

        // See if we need to soft-delete.
        if (array_key_exists('deleted_at', $columns = $this->request->getPostRules())) {

            $now = Carbon::now()->toDateTimeString();
            $inputs['deleted_at'] = $now;

            // Set updated_at column if it exists (and if not already in params).
            if ((array_key_exists('updated_at', $this->request->getPostRules())) && (! array_key_exists('updated_at', $params) ))
                $params['updated_at'] = $now;

            $this->logQuery('delete(): '.$params['id'].' (soft-delete)');
            $affected_rows = DB::table($this->config->getTableName())->where($this->mapColumn('id'), $params['id'])->update($inputs);
        } else {
            $this->logQuery('delete(): '.$params['id']);
            $affected_rows = DB::table($this->config->getTableName())->where($this->mapColumn('id'), $params['id'])->delete();
        }

        if (!$affected_rows)
            throw new PublisherRepositoryException('Item could not be deleted.');

        Cache::flush();

        return $affected_rows;
    }

    /*
    |--------------------------------------------------------------------------
    | Publisher Multi-Record List Actions
    |--------------------------------------------------------------------------
    */
    
    /**
     * @return int
     * @throws PublisherRepositoryException
     */
    public function action()
    {
        $params = $this->api->getQueryParameters();
        
        if (!$params)
            throw new PublisherRepositoryException('No attributes specified for action.');

        if (!(array_key_exists('action', $params) && ($params['action'] !== '')))
            throw new PublisherRepositoryException('No action specified.');

        if (!(array_key_exists('items', $params) && ($params['items'] !== [])))
            throw new PublisherRepositoryException('No items specified for action.');

        $action = $params['action'];
        $action_items = $params['items'];

        if (array_key_exists('options', $params) && ($params['options'] !== []))
            $action_options = $params['options'];
        
        $affected_rows = 0;

        if (($action !== '') && ($action !== 'select_all') && count($action_items)) {

            $this->logQuery('action(): '.$action.', items: '.$this->arrayToString($action_items));

            // Create builder object with query criteria.
            $builder = $this->whereItems($this->mapSelect($this->config->getResultsColumns()));

            // Execute specified action on selected items.
            switch ($action) {

                case 'clone': {
                    foreach($action_items as $id) {
                        $this->model->create($this->model->find($id)->toArray());
                        $affected_rows++;
                    }
                    break;
                }

                case 'delete': {
                    $affected_rows = $builder->delete();
                    break;
                }
            }

            if (!$affected_rows) {
                throw new PublisherRepositoryException('List action could not be performed.');
            }

        }

        return $affected_rows;
    }

    /*
    |--------------------------------------------------------------------------
    | Column Mapping
    |--------------------------------------------------------------------------
    */

    /**
     * For mapping parameter names to different column names in data source.
     * Array of parameter names and their respective database column names,
     * where column name is different than parameter name. ie:
     *
     *    ['account' => 'account_id', 'company' => 'company_name']
     *
     * @var array
     */
    protected $column_map = [];

    /**
     * @return array
     */
    public function getColumnMap()
    {
        return $this->column_map;
    }

    /**
     * @param array $column_map
     */
    public function setColumnMap($column_map)
    {
        $this->column_map = $column_map;
    }

    /**
     * @param string $key
     * @return string
     */
    public function mapColumn($key)
    {
        return array_key_exists($key, $this->column_map) ? $this->column_map[$key] : $key;
    }

    /**
     * @param array $attributes
     * @return array
     */
    public function mapAttributes($attributes = [])
    {
        $mapped = [];
        foreach ($attributes as $key => $value) {
            $mapped = array_add($mapped, $this->mapColumn($key), $value);
        }

        return $mapped;
    }

    /**
     * @param string $value
     * @return string
     */
    public function rmapColumn($value)
    {
        $key = array_search($value, $this->column_map);
        return ($key !== false) ? $key : $value;
    }

    /**
     * @param array $columns
     * @return mixed
     */
    public function mapSelect($columns)
    {
        $raw = '';
        $i = 0;
        $num_columns = count($columns);

        foreach ($columns as $key) {
            $i++;
            $raw .= (array_key_exists($key, $this->column_map) ? ($this->column_map[$key].' as '.$key) : $key).
                (($i != $num_columns) ? ', ' : '');
        }

        return $this->model->select(DB::raw($raw));
    }

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    */

    /**
     * Log query if logging is enabled in config.
     *
     * @param string $message
     */
    protected function logQuery($message = '')
    {
        if($this->config->getLogging()['active']) {
            Log::info($this->config->getDomain().': '.$message);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Sanitize Validated Inputs Before Using in Query
    |--------------------------------------------------------------------------
    */

    /**
     * If param name contains one of the target keywords and it's value is null-like,
     * set value to proper null for the domain's db store/update/delete operations.
     * The formats expressed below are simply the defaults for this framework,
     * so be aware there are many formats not addressed by this treatment.
     *
     * @param array $inputs
     * @return array
     */
    protected function formatDateTimeInputs($inputs = [])
    {
        foreach ($inputs as $key => $value) {

            if ((strpos($key, 'datetime') !== false) || (strpos($key, 'timestamp') !== false)) {
                if (($value === '') || ($value === '0000-00-00 00:00:00')) {
                    $inputs[$key] = null;
                }
            }

            if (strpos($key, 'date') !== false) {
                if (($value === '') || ($value === '0000-00-00')) {
                    $inputs[$key] = null;
                }
            }

            if (strpos($key, 'time') !== false) {
                if (($value === '') || ($value === '00:00:00')) {
                    $inputs[$key] = null;
                }
            }
        }

        return $inputs;
    }

}