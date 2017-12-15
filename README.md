# Ligero

[![GitHub license](https://img.shields.io/github/license/mashape/apistatus.svg)](LICENSE.md)

A lightweight yet versatile CRUD micro-framework for Laravel, with dynamic pagination and search controls, query caching, presenters, localization, support for APIs and DDD-style bounded contexts.

Quick-Links:

- [Installation](#installation)
- [Overview](#overview)
    - [Architecture](#architecture)
    - [Getting Started](#getting-started)
    - [Basic Steps Outlined](#basic-steps-outlined)
    - [Live Demo](#live-demo)
- [Class Types](#class-types)
    - [Config (PublisherConfigInterface)](#config-publisherconfiginterface)
    - [Request (PublisherRequestInterface)](#request-publisherrequestinterface)
    - [Publisher (PublisherInterface)](#publisher-publisherinterface)
    - [PublisherApi (PublisherApiInterface)](#publisherapi-publisherapiinterface)
    - [Repository (PublisherRepositoryInterface)](#repository-publisherrepositoryinterface)
    - [Model (PresentableInterface)](#model-presentableinterface)
    - [Presenter (PresenterInterface)](#presenter-presenterinterface)
    - [Context (ContextInterface)](#context-contextinterface)
- [Controllers and Contexts](#controllers-and-contexts)
    - [HasPublisher Trait](#haspublisher-trait)
    - [HasPublisherSession Trait](#haspublishersession-trait)
- [Basic Usage](#basic-usage)
    - [Creating a Publisher UI Controller](#creating-a-publisher-ui-controller)
    - [Thinking Beyond CRUD](#thinking-beyond-crud)
- [Advanced Usage](#advanced-usage)
    - [Configuration for Multiple Domains](#configuration-for-multiple-domains)
    - [Domain-Driven Design Using Contexts](#domain-driven-design-using-contexts)
    - [Creating a Domain Context](#creating-a-domain-context)
    - [Deploying an API for a Context](#deploying-an-api-for-a-context)
    - [Handling Relations](#handling-relations)
    - [Automatic Handling of Timestamps](#automatic-handling-of-timestamps)
- [Customization](#customization)
    - [Publishing the Package Files](#publishing-the-package-files)
    - [Extending or Decorating Base Classes](#extending-or-decorating-base-classes)
    - [Namespace for Custom Classes](#namespace-for-custom-classes)
- [Tests](#tests)
- [License](#license)
- [Changelog](#changelog)
- [Contributing](#contributing)

## Installation

Via Composer:

```bash
$ composer require viewflex/ligero
```

After installing, add the `LigeroServiceProvider` to the list of service providers in Laravel's `config/app.php` file.

```php
Viewflex\Ligero\LigeroServiceProvider::class,
```

## Overview

### Architecture

The goal is to provide a versatile, extensible CRUD micro-framework that can be easily understood and deployed, while integrating all the necessary component types, following SOLID design principles, and enabling rapid modelling of domains in a Domain-Driven Design (DDD) fashion.

The package architecture follows the Strategy Pattern, and is designed for simple or complex applications. A Publisher represents the assembly of components (configuration, request, presentation logic, domain logic, and database queries) that serve a particular business or application domain. The `PublisherInterface` provides the all the necessary methods for processing CRUD requests, returning raw or presented results, plus dynamic data for UI controls.

This is an illustration of the pattern employed in this package, which can be used to output views or raw data.

![publisher flow](https://raw.githubusercontent.com/viewflex/ligero-docs/master/img/publisher-flow.png)

This pattern is easy to replicate for each domain, enabling rapid scaffolding of an application with many domains. The included demo illustrates deployment of both UI and API controllers, using the example Items domain. The demo also includes an example of a Context (in the DDD sense of the word), which encapsulates a Publisher and it's components.

### Getting Started

This package provides several ways to create new domain Publishers, depending on the complexity of the requirements for a given domain. See the [Basic Usage](#basic-usage) and [Advanced Usage](#advanced-usage) sections below, for examples of creating and using Publishers.

A good place to start understanding any codebase is through the interfaces. In this package nearly every class implements an interface, providing a clear map of core functionality, and a decoupling of code that allows extension, decoration, or replacement of any class, without side-effects.

### Basic Steps Outlined

- Specify configuration, validation rules, and other attributes of the Config, Request, and Repository components. You can define all these via setters at runtime, or extend the base classes.
- Create a new Publisher instance with the configured components. This can be in a controller, or a Context - basically any class that uses the `HasPublisher` trait.
- Use methods on the Publisher to perform CRUD operations and generate dynamic navigation and query controls. Extend the Publisher if necessary to modify or add functionality.

There are multiple ways to implement domains - the demo illustrates implementing a Publisher in a UI controller. This and other implementations are explained in detail in the following sections.

### Live Demo

![demo screenshot](https://raw.githubusercontent.com/viewflex/ligero-docs/master/img/screenshots/results-list-view.png)

The Items domain is a working demo provided in this package; you can try it out using the [routes](#creating-a-publisher-ui-controller) listed below. The demo implements a very simple CRUD UI in plain HTML with Bootstrap.css, but of course the generated API data sent to the views could be presented using any front-end framework desired.

Ligero provides a complete separation of presentation and application logic, and outputs results with all necessary data elements for dynamic UI components, pre-packaged in a standardized format. Use the 'Items > Display as JSON' menu command to see what the raw data looks like.

#### Using the Demo as a Template

See the [Publishing the Package Files](#publishing-the-package-files) subsection below to learn how to quickly scaffold new domains by publishing (copying) the demo and customizing it to suit the requirements.

#### Demo API Endpoints

Besides the UI controller used in the demo, there is also a ready-made Context and API controller serving the API routes for the same Items domain. The package `ContextApiController` can be used to serve any number of domain Contexts via route parameter keyed to the configured `$contexts` array.


## Class Types

Understanding these class types, and the different tasks they perform, is the key to making productive use of this package. The way that they come together to get the job done provides great power and flexibility with minimal development effort.

This package includes several live examples, demonstrating a few of the ways in which a `Publisher` object can be created and used. The easiest way to get started using this package for your own projects would be to copy one of these implementations, changing the namespaces and class names.

The namespace `Viewflex\Ligero\Publish` corresponds to the `publish/viewflex/ligero` directory in your Laravel project root. When you use `php artisan vendor:publish`, [as detailed below](#publishing-the-package-files), this directory will be created if not already existing.


### Config `PublisherConfigInterface`

This class is where you configure the operation of the other class types. It provides getters, setters, and helper methods used throughout the `Publisher`, `PublisherApi` and `BasePublisherRepository` classes. These settings allow complete control over generation of search results and UI components. Extend the `BasePublisherConfig` class and override attributes to suit the domain.

This package's `Config/ligero.php` file provides defaults for the global settings in `BasePublisherConfig`, and can be published (via artisan command) to the Laravel `config` directory for customization (see the [Customization](#customization)  section below).

Config attributes can also be overridden using setter methods, so if you only need to override a few values, you can use the base Config class instead of creating an extended Config class. These are the methods of `PublisherConfigInterface`:

#### Domain Configuration

These configuration settings are specific to the given domain, getting their minimal defaults from the `BasePublisherConfig` class.

##### Domain, Resource Namespaces, Translation

Specify the domain name used in messages and labels ('Items' in the demo) - this is also used (lower-cased) as part of the the domain view prefix. For multiple-word domain names, name should be in `StudlyCaps` case (no spaces).

Specify the resource namespace used to locate published resources in the application's `resources/lang/vendor/[namespace]` and `resources/views/vendor/[namespace]` directories. The package default is 'ligero', but you can set a custom namespace for domain resources - useful if there are many, or if non-canonical names result in naming conflicts between domains.

Specify a translation filename ('items' in the demo) to get translations from a specific file in the configured resource namespace. The `ls()` method is a wrapper for Laravel's `trans()` helper function (or `trans_choice()`, if a count is supplied for inflection), enhancing flexibility of localization by allowing use of a custom namespace and file.

```php
getDomain()
setDomain($domain)
getResourceNamespace()
setResourceNamespace($resource_namespace)
getTranslationFile()
setTranslationFile($translation_file)
getTranslationPrefix()
ls($key, $option = null)
getDomainViewPrefix()
getDomainViewName($view)
```

##### Table and Model

Specify table and model name used in queries.

```php
getTableName()
setTableName($table_name)
getModelName()
setModelName($model_name)
```

##### Query Parameter Defaults, Results Columns, Wildcard Columns

Specify all valid query parameters for the given domain, along with their default values. Specify columns to return in query results (default, and optionally, per view), and columns to treat as wildcards ('LIKE' instead of '=') in queries.

```php
getQueryDefaults()
setQueryDefaults($query_defaults)
setQueryDefault($name, $value)
getResultsColumns($view = 'default')
setResultsColumns($results_columns, $view = 'default')
getWildcardColumns()
setWildcardColumns($wildcard_columns)
```

##### Toggle for UI Controls

Enable or disable generation of individual dynamic UI controls, such as pagination and keyword search..

```php
getControls()
setControls($controls)
getControl($name)
setControl($name, $enabled)
```

##### Pagination

Detailed configuration of pagination UI controls.

```php
getPaginationConfig()
setPaginationConfig($pagination_config)
```

##### Keyword Search

Detailed configuration of keyword search UI control.

```php
getKeywordSearchConfig()
setKeywordSearchConfig($keyword_search_config)
```

##### Sorts and View/Limit

Define named sorts available via the 'sort' query parameter, and specify limits for the default and named views (list, grid, item, and any custom views you create).

```php
getSorts()
setSorts($sorts)
getSort($name = 'default')
getViewLimits()
setViewLimits($view_limits)
getViewLimit($name = 'default')
setViewLimit($view_limit, $view = 'default')
```

#### Global Configuration

These configuration settings apply globally, and get their defaults from the package's `ligero.php` config file, which can be published to customize and override any values in it.

##### Caching and Logging

```php
getCaching()
setCaching($caching)
getLogging()
setLogging($logging)
```

##### URL Format, Paths, Options

Specify whether the URLs generated for search and navigation should be absolute or relative. Paths and options can be defined here for any purpose required.

```php
absoluteUrls()
setAbsoluteUrls($absolute_urls)
getPaths()
setPaths($paths)
getPath($name)
setPath($name, $path)
getOptions()
setOptions($options)
getOption($option)
setOption($name, $option)
```

##### Unit Formatting and Conversions

Enable unit conversions to take advantage of dual currencies and measurement units. Specify dual currencies, ruler units, and weight units, along with their desired formatting configurations. Currency conversions are performed based on live exchange rate. Optionally specify a custom formatter class to use in place of the default `Formatter` class.

```php
getFormatter()
setFormatter($formatter)
unitConversions()
setUnitConversions($unit_conversions)
getCurrencies()
setCurrencies($currencies)
getRulerUnits()
setRulerUnits($ruler_units)
getWeightUnits()
setWeightUnits($weight_units)
```

##### Tables, Models and Contexts for Multi-Domain Implementations

The getter methods below give precedence to table and model names listed in the `ligero.php` config file. See the [Advanced Usage](#advanced-usage) section for more information on custom multi-domain implementations.

```php
getTables()
setTables($tables)
getModels()
setModels($models)
getContexts()
setContexts($contexts)
```

### Request `PublisherRequestInterface`

This class represents the Laravel/Symfony Request class, which contains the inputs and validation rules. Extend the `BasePublisherRequest` class and override validation rules to suit the domain.

As with the Config class, setter methods exist to customize attributes of the Request class, allowing use of the base Request class directly, without creating an extended Request class. These are the methods of `PublisherRequestInterface`:

#### Rules for GET and POST Validations

```php
rules()
getRules()
setRules($rules)
setRule($name, $value)
getPostRules()
setPostRules($post_rules)
setPostRule($name, $value)
```

#### Raw and Filtered Inputs

```php
getQueryInputs()
getQueryInput($key = '')
getInputs()
setInputs($inputs = [])
mergeInputs($inputs = [])
cleanInput($param = '')
```

#### Inputs for Action, Items, and Options

```php
getAction()
setAction($action = '')
getActionItems()
setActionItems($items = [])
getActionOptions()
setActionOptions($options = [])
```

#### Get Original Request Data

```php
initializeRequest($current)
```

### Publisher `PublisherInterface`

The PublisherApi and it's encapsulating Publisher represent the the domain layer and the basic presentation layer, and allow customization via stacked (decorating) classes. Regarding the Decorator Pattern, as used to layer new or modified functionality onto a Publisher without touching that class: the base `Publisher` class in the stack must have a `PublisherApi` (domain layer), loaded with it's concrete Config, Request, and Repository implementations.

The `Publisher` and `PublisherApi` classes use the `PublisherTrait` and `PublisherApiTrait`, respectively, which allows their core methods to be used in custom Publisher and PublisherApi classes. Because these classes have constructors, to enhance or modify functionality (not generally necessary) we do not extend them, but rather create new ones that expect injected component objects implementing extended interfaces.

These are the methods of `PublisherInterface`:

#### Component Objects

```php
getConfig()
getRequest()
getQuery()
```

#### Raw Data

Get results, data for dynamic UI controls, and full info on query.

```php
getQueryInfo()
found()
displayed()
getResults()
getItems()
getPagination()
getKeywordSearch()
getData()
```

#### Dynamically Formatted Results

These methods output presented rather than raw results, as configured in the Presenter class.

```php
presentItems()
presentData()
```

#### Standard CRUD Actions

The `find()` and `findBy()` methods return a collection by default - to return an array instead, use `false` as the second parameter.

```php
find($id, $native = true)
findBy($inputs = [], $native = true)
store($inputs = null)
update($inputs = null)
delete($id = null)
```

#### Multi-Record List Actions

The 'action' and 'items' query parameters can be used to perform actions on a selection of records. The package supports the actions 'clone' and 'delete'. To add new actions, extend the `BasePublisherRepository`, adding new cases to the `action()` method.

```php
action($inputs = null)
```

Separate from this functionality, using 'select_all' as the 'action' parameter in a query will select all item checkboxes in the results display, via the `$form_item_checked` attribute for each row of results data passed to the views. This is useful when creating a pure HTML front end, where JavaScript can't be used to manipulate UI elements.


#### Utility Functions

```php
urlSelf()
inputsAreValid($inputs = [])
```


### PublisherApi `PublisherApiInterface`

The PublisherApi is the core of the package's functionality - many of the Publisher methods pass through to the class implementing this interface, which has additional methods providing more granular access to the encapsulated logic and individual properties. These are the methods of `PublisherApiInterface`:

#### Component Objects

```php
getConfig()
getRequest()
getQuery()
```

#### Initialization

```php
setQuery($query)
```

#### Raw Data

Get results, data for dynamic UI controls, and full info on query.

```php
getQueryInfo()
found()
displayed()
getResults()
getItems()
getPagination()
getKeywordSearch()
getData()
```

#### Dynamically Formatted Results

These methods output presented rather than raw results, as configured in the Presenter class.

```php
presentItems()
presentData()
```

#### Query Input Parameters

```php
getRoute()
getQueryParameters()
getQueryKeyword()
getQueryView()
getQuerySort()
getQueryLimit()
getQueryStart()
getQueryPage()
getUrlBaseParameters()
```

#### Utility Functions

```php
getRequestParameters()
dbQueryParameters($parameters = [])
getUrlParametersExcept($skip = [])
urlQueryString($parameters = [])
urlQueryWithStart($params, $start)
urlQueryWithPage($params, $page)
urlSelf()
```

#### Standard CRUD Actions

```php
store()
update()
delete()
```

#### Multi-Record List Actions

```php
action()
```


### Repository `PublisherRepositoryInterface`

The Repository Pattern specifies a separation of the domain layer from the storage layer. This doesn't mean a complete decoupling - while the `PublisherApi` (domain) has access to the Repository (storage), the Repository also has access to the public methods of the `PublisherApi` in order to do it's job better.

The `PublisherApi` provides the `Publisher` with access to the Repository; the `Publisher` then makes the results available to the wider application as specified by the `PublisherInterface`. The key relationship in the pattern implemented here is the one between the domain and the storage - this applies not only to the `PublisherApi` as used to provide CRUD functionality, but to the business domains that will implement this pattern to represent their own specialized functionality.

The base Repository uses Eloquent/Query Builder to take advantage of collections, but if needed to improve speed, it can also be decorated, in the same way as Publishers, to use raw SQL, or an entirely different storage endpoint. The `BasePublisherRepository` class may be used as a base for all application domains to use or extend.

These are the methods of `PublisherRepositoryInterface`:


#### Initialization

```php
loadModel()
setApi(PublisherApiInterface $api)
```

#### Database Read Operations

```php
found()
displayed()
getResults()
getItems()
```

#### Database Write Operations

```php
store()
update()
delete()
```

#### Multi-Record List Actions

```php
action()
```

#### Mapping Query Parameters to Database Columns

```php
getColumnMap()
setColumnMap($column_map)
mapColumn($key)
mapAttributes($attributes = [])
rmapColumn($value)
```

### Model `PresentableInterface`

The `BaseModel` class extends Laravel's standard Eloquent model, adding `PresentableTrait` to provide optional use of the `BasePresenter` or an extended custom Presenter, configurable via the model attribute `$presenter`.

```php
getPresenter()
setPresenter($presenter)
present()
```

### Presenter `PresenterInterface`

A Presenter is a commonly used strategy component that provides the ability to attach custom presentation methods to a model, corresponding to any of the model's data columns (plus any calculated values required), and using those methods instead of the column values themselves when outputting results.

This package's Presenter is more tightly integrated, providing the additional ability to inject a Publisher's Config, required for supported  advanced operations such as unit conversions and formatting. Of course, any Presenter can also be used on a model directly outside the scope of a Publisher.

A Presenter uses the `Formatter` utility class, which includes some typical formatting and conversion methods. To modify or add new methods, extend or clone this class and specify the custom formatter class in the Publisher's Config (or use `setFormatter()` to specify a new formatter on the Presenter).

```php
getEntity()
setEntity($entity)
getConfig()
setConfig($config)
getFormatter()
setFormatter($formatter)
requireConfig()
requireColumn($column)
__get($property)
```

### Context `ContextInterface`

A Context class encapsulates a domain Publisher and it's dependencies, and can be used to make a particular representation of the domain's internal functionality available to the wider application. This optional class facilitates creation of an application service layer, providing a path toward implementing complex multi-domain processes.

Similar to a controller, a Context includes methods for querying, storing, updating and deleting records, plus corresponding methods for doing the same not only for one domain, but possibly (if the domain is an aggregate root) for several at once, within a transaction (if saving data).

See the section on [Advanced Usage](#advanced-usage) below to learn more about Contexts. These are the methods of the `ContextInterface`:

#### Publisher Query Actions

```php
find($id, $native = true)
findBy($inputs = [], $native = true)
store($inputs)
update($inputs)
delete($id)
```

#### Context Query Actions

```php
findContext($id, $native = true)
findContextBy($inputs = [], $native = true)
storeContext($inputs)
updateContext($inputs)
deleteContext($id)
```

#### Validation

```php
inputsAreValid($inputs = [])
```

#### Utility

```php
onFailure($msg = '', $data = [])
failureMessage($operation = '', $inputs = [])
formatDomainContext($context, $name = null)
contextResponse($success, $msg, $data)
responseSuccess($response = [])
responseMessage($response = [])
responseData($response = [])
log($message = '')
```

#### Aggregate Context Methods

The `AggregateContextInterface` extends the `ContextInterface`, specifying additional methods to support rich Contexts.

```php
getContext($root, $native = true)
contextInputsAreValid($inputs)
```

## Controllers and Contexts

The controller or Context is typically where concrete classes are specified as components of a Publisher, corresponding to the Config, Request, and Repository interfaces.

The `BasePublisherController` and `BaseContext` classes use the `HasPublisher` trait, which provides `createPublisher()` and `createPublisherWithDefaults()`.

The `ItemsController` class in this package (for demo and testing) provides an example of extending the base UI controller, and using it's Publisher instance. See the [Advanced Usage](#advanced-usage) section below to learn about creating a Publisher instance within a Context, for greater encapsulation and flexibility (see the `ItemsContext` class).

The `BasePublisherController` class provides these methods - corresponding to the standard resourceful controller methods in Laravel:

```php
index()
create()
store()
show()
edit()
update()
destroy()
```

Also included in the base controller:

```php
json()
composeListing()
composeItem()
action()
```

The `index()` and `json()` methods return a multi-record listing corresponding to request parameters, along with all data for UI controls - `index()` displays the data in views, while `json()` would be used to provide this data to a front-end framework that does it's own composition and rendering.

The `action()` method processes the requested pre-defined action on a user-selected subset of displayed records. The base Publisher supports two list actions: 'clone' and 'delete' - extend or decorate a Publisher (and it's Repository, as needed) to support additional actions.

The `composeListing()` and `composeItem()` methods are for organizing and performing final decoration of the data for the views. Override these methods in your new controller to provide different or additional treatment to the data.

### `HasPublisher` Trait

This trait, typically employed in a controller or Context class, provides to any class using it the attributes and methods of a Publisher object, and the ability to create additional instances as needed. This is the full list of methods provided by the `HasPublisher` trait:

#### The Publisher and it's Components

```php
getConfig()
setConfig($config)
getRequest()
setRequest($request)
getQuery()
setQuery($query)
getPublisher()
setPublisher($publisher)
```

#### Create Publisher with Custom or Default (Base) Components

```php
initPublisher($publisher)
createPublisher($config, $request, $query = null)
createPublisherWithDefaults()
newPublisher($config, $request, $query = null)
```

#### Utility Methods

```php
returnView($view, $data)
setInputs($inputs = [])
initializeRequest($current)
```

### `HasPublisherSession` Trait

This trait uses the `HasPublisher` trait, adding methods for handling session-based state navigation.

```php
remember()
setBackTo()
getBackTo()
goBack($message = '', $translation_option = null)
getRoot()
setRoot()
goToRoot($message = '', $translation_option = null)
setDataModified()
getDataModified()
hasSession()
getMessage()
```

## Basic Usage

Because this package was designed for maximum extensibility, there are many different ways in which it can be used. Typically you will use either a UI controller or API controller with at least the basic CRUD methods as endpoints for the routes. Below are several examples illustrating how to create a stateful UI resource controller with a Publisher. See the section on [Advanced Usage](#advanced-usage) below for an example of an API controller using a Context wrapping the Publisher.

### Creating a Publisher UI Controller

Extending the base classes is a straightforward way to build on the base functionality, but if the domain's needs are very simple, it may be best to just extend the `BasePublisherController`, creating a Publisher instance using the base Config, Request, and Repository classes. Both ways are described here.

#### Routes

In Laravel, a resourceful controller's routes can be specified with one line, but for the demo controller, I've used explicit routes because I want the route parameter to be named 'id', not the singular name of the model, as Laravel would name it automatically.

Additional routes for the controller should always be listed first. These two routes provide some additional functionality to the CRUD layer:

```php
Route::get('ligero/items/json', array('as' => 'ligero.items.json', 'uses' => '\Viewflex\Ligero\Publish\Demo\Items\ItemsController@json', 'middleware' => 'web'));
Route::get('ligero/items/action', array('as' => 'ligero.items.action', 'uses' => '\Viewflex\Ligero\Publish\Demo\Items\ItemsController@action', 'middleware' => 'web'));
```

These are the resource controller routes:

```php
Route::get('ligero/items', array('as' => 'ligero.items.index', 'uses' => '\Viewflex\Ligero\Publish\Demo\Items\ItemsController@index', 'middleware' => 'web'));
Route::get('ligero/items/create', array('as' => 'ligero.items.create', 'uses' => '\Viewflex\Ligero\Publish\Demo\Items\ItemsController@create', 'middleware' => 'web'));
Route::get('ligero/items/{id}', array('as' => 'ligero.items.show', 'uses' => '\Viewflex\Ligero\Publish\Demo\Items\ItemsController@show', 'middleware' => 'web'));
Route::get('ligero/items/{id}/edit', array('as' => 'ligero.items.edit', 'uses' => '\Viewflex\Ligero\Publish\Demo\Items\ItemsController@edit', 'middleware' => 'web'));
Route::post('ligero/items/store', array('as' => 'ligero.items.store', 'uses' => '\Viewflex\Ligero\Publish\Demo\Items\ItemsController@store', 'middleware' => 'web'));
Route::put('ligero/items/{id}', array('as' => 'ligero.items.update', 'uses' => '\Viewflex\Ligero\Publish\Demo\Items\ItemsController@update', 'middleware' => 'web'));
Route::delete('ligero/items/{id}', array('as' => 'ligero.items.destroy', 'uses' => '\Viewflex\Ligero\Publish\Demo\Items\ItemsController@destroy', 'middleware' => 'web'));
```

#### Setup

##### Minimal Configuration

Use `createPublisherWithDefaults()`, which uses the package's `BasePublisherConfig`, `BasePublisherRequest` and `BasePublisherRepository` as the default components. Override their values as needed in the controller via setter methods.

Extend `BasePublisherController` and create a Publisher in the constructor of the new controller.

The model referenced in the base implementation has only a few columns, so you will need to customize some attributes of the base classes. This can all be done with setter methods, as illustrated below.

```php
use Viewflex\Ligero\Base\BasePublisherController;

class ProductsController extends BasePublisherController
{
    public function __construct()
    {
        // Instantiate a publisher with default components.
        $this->createPublisherWithDefaults();

        // Set the domain name used to locate routes and view templates.
        // Set the language file used to provide UI labels and messages.
        $this->config->setDomain('Products');
        $this->config->setTranslationFile('products');

        // Add input defaults and validation rules for query parameters.
        $this->config->setQueryDefault('category', '');
        $this->config->setQueryDefault('subcategory', '');
        $this->config->setQueryDefault('size', '');
        $this->config->setQueryDefault('color', '');
        $this->request->setRule('category', 'max:25');
        $this->request->setRule('subcategory', 'max:25');
        $this->request->setRule('size', 'max:25');
        $this->request->setRule('color', 'max:25');

        // Specify results columns returned by queries. Default columns are required,
        // can also add arrays for the standard views (list, grid, item) and others.
        $this->config->setResultsColumns(
            [
                'default'  => [
                    'id',
                    'name',
                    'category',
                    'subcategory',
                    'description',
                    'size',
                    'color'
                ]
            ]
        );

        // Where necessary, map query parameter names
        // to actual column names in data source.
        $this->query->setColumnMap([
            'category'      => 'product_category',
            'subcategory'   => 'product_subcategory',
            'description'   => 'product_description',
            'size'          => 'product_size',
            'color'         => 'product_color'
        ]);

    }
}
```

##### Moderate to Extensive Configuration

Create new Config, Request, and Repository classes, extending the package base classes.

Modify the Config and Request classes to suit the domain's required query parameters, column names, validation rules, and behavior of UI controls. Modify the Repository class as needed to map columns or add new functionality specific to the domain.

Import the Ligero `BasePublisherController`, and your extended Config, Request and Repository classes to instantiate a new Publisher.

```php
use My\Package\Products\ProductsPublisherConfig as Config;
use My\Package\Products\ProductsPublisherRequest as Request;
use My\Package\Products\ProductsPublisherRepository as Query;
use Viewflex\Ligero\Base\BasePublisherController;

class ProductsController extends BasePublisherController
{
    public function __construct(Config $config, Request $request, Query $query)
    {
        $this->createPublisher($config, $request, $query);
    }
}
```

##### Configuration With Custom Publisher

The base `Publisher` class has all the necessary CRUD capabilities plus a full suite of dynamically generated UI components, and is completely configurable in multiple ways. Still, for domains that must aggregate data and functionality across multiple domains, you can extend this class to incorporate more complex domain logic, queries, and composition.

In this example, custom Config, Request, and Repository instances are injected (via IoC resolution) into a custom Publisher class, to create the Publisher instance that will be used by this UI controller.

```php
use My\Package\Products\ProductsPublisherConfig as Config;
use My\Package\Products\ProductsPublisherRequest as Request;
use My\Package\Products\ProductsPublisherRepository as Query;
use My\Package\Products\ProductsPublisher as Publisher;
use Viewflex\Ligero\Base\BasePublisherController;

class ProductsController extends BasePublisherController
{
    public function __construct(Config $config, Request $request, Query $query)
    {
        $this->initPublisher(new Publisher($config, $request, $query));
    }
}
```

### Thinking Beyond CRUD

Now that you have seen several examples of custom Publishers implemented, you may be thinking of how Publishers can be used in the wider context of the application under development. If you are thinking along the lines of Domain-Driven Design (DDD) in designing your application, See the [Advanced Usage](#advanced-usage) section to understand the built-in support for bounded contexts.

## Advanced Usage

Beyond the dynamic CRUD and UI capabilities, this package provides interfaces for creating domain contexts following DDD patterns. Contexts can be useful for encapsulating both a domain Publisher and it's dependencies, and additional logic specific to a domain.

### Configuration for Multiple Domains

A domain's table and model, by default, are determined by the Config's `$table_name` and `$model_name` attributes. Supporting multi-domain implementations, `getTableName()` and `getModelName()` will return either the Config attributes themselves, or, if the `ligero.php` config file contains values for that domain (keyed by `$table_name`), those values be returned instead. The Config also has an array of Context class names, keyed by the context's route parameter, accessed by `getContexts()`.

The config file can be "published" for modification; this will copy it to Laravel's `config` directory, taking precedence over the original package file. See the section below on [Customization](#customization).

### Domain-Driven Design Using Contexts

The strategy pattern implemented by this package can be used to quickly scaffold domains with built-in CRUD functions and a nice contextual UI - a good foundation to build on. Of course, most applications or even micro-services require data and functions across several domains.

A Context provides a domain with an application service layer, allowing domains to use each other in predictable ways. Unlike a Publisher controller, a Context class instantiates a Publisher for all-purpose use (in a controller or elsewhere), already pre-configured with it's concrete dependencies.

When working in a DDD style, take advantage of this package's built-in support for contexts - each domain can be instantiated as a Context (or many different Contexts, as required). A Context that uses data and functions across multiple domains is referred to as an Aggregate Root (or Rich Domain), and will typically use database transactions to ensure data integrity in multi-domain operations.

The package's `BaseContext` and `AggregateContext` classes can be extended to create custom domain Contexts.

### Creating a Domain Context

This example from the demo illustrates creation of a Context:

```php
<?php

namespace Viewflex\Ligero\Publish\Demo\Items;

use Viewflex\Ligero\Base\BaseContext;

class ItemsContext extends BaseContext
{
    /*
    |--------------------------------------------------------------------------
    | Initialization
    |--------------------------------------------------------------------------
    */

    /**
     * Create basic context for this domain.
     **/
    public function __construct()
    {
        parent::__construct();

        /*
        |--------------------------------------------------------------------------
        | Config
        |--------------------------------------------------------------------------
        */

        // Set domain name and keys for locating domain views, translations, etc.
        $this->config->setDomain('Items');
        $this->config->setTranslationFile('items');

        // Set the key for locating table name in $tables array,
        // and for locating full model name in $models array.
        // If not found there, uses values in domain config.
        $this->config->setTableName('ligero_items');

        // Specify results columns to return. Array for 'default' columns required,
        // can also add arrays for standard views (list, grid, item) and others.
        $this->config->setResultsColumns([
            'id',
            'active',
            'name',
            'category',
            'subcategory',
            'description',
            'price'
        ]);

        // Define named sorts available via 'sort' query parameter.
        $this->config->setSorts([
            'default'           => ['id' => 'asc'],
            'id'                => ['id' => 'asc', 'name' => 'asc'],
            'name'              => ['name' => 'asc', 'id' => 'asc'],
            'category'          => ['category' => 'asc', 'id' => 'asc'],
            'subcategory'       => ['subcategory' => 'asc', 'id' => 'asc'],
            'price'             => ['price' => 'asc', 'id' => 'asc']
        ]);

        // Add custom GET parameters for queries, with default values.
        $this->config->setQueryDefault('id', '');
        $this->config->setQueryDefault('active', '');
        $this->config->setQueryDefault('name', '');
        $this->config->setQueryDefault('category', '');
        $this->config->setQueryDefault('subcategory', '');

        /*
        |--------------------------------------------------------------------------
        | Request
        |--------------------------------------------------------------------------
        */

        // Set validation rules for custom GET parameters.
        $this->request->setRule('id', 'numeric|min:1');
        $this->request->setRule('active', 'boolean');
        $this->request->setRule('name', 'max:60');
        $this->request->setRule('category', 'max:25');
        $this->request->setRule('subcategory', 'max:25');

        // Set validation rules for custom POST parameters.
        $this->request->setPostRule('id', 'numeric|min:1');
        $this->request->setPostRule('active', 'boolean');
        $this->request->setPostRule('name', 'max:60');
        $this->request->setPostRule('category', 'max:25');
        $this->request->setPostRule('subcategory', 'max:25');
        $this->request->setPostRule('description', 'max:250');

        /*
        |--------------------------------------------------------------------------
        | Query (Repository)
        |--------------------------------------------------------------------------
        */

        // Required - updates query $model to that specified by config.
        $this->query->loadModel();

        // Optional - mapping of input parameters to database column names.
        $this->query->setColumnMap([]);

    }

}
```

### Deploying an API for a Context

The demo included in this package uses the `ContextApiController`, which provides an API for Contexts:

```php
<?php

namespace Viewflex\Ligero\Controllers;

use App\Http\Controllers\Controller;
use Viewflex\Ligero\Contracts\ContextInterface as Context;

class ContextApiController extends Controller
{
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

    /*
    |--------------------------------------------------------------------------
    | JSON Publisher Query Actions
    |--------------------------------------------------------------------------
    */

    /**
     * Returns the results of json publisher query on id in native or array format.
     *
     * @param string $key
     * @return array
     */
    public function find($key)
    {
        $this->context = new $this->contexts[$key];
        return $this->context->find($this->inputs['id'], false);
    }
    ...

}
```

These are the built-in API routes for Context actions:

```php
// Standard CRUD domain actions...
Route::post('api/ligero/{key}/find', array('as' => 'api.ligero.find', 'uses' => '\Viewflex\Ligero\Controllers\ContextApiController@find', 'middleware' => 'api'));
Route::post('api/ligero/{key}/findby', array('as' => 'api.ligero.findby', 'uses' => '\Viewflex\Ligero\Controllers\ContextApiController@findBy', 'middleware' => 'api'));
Route::post('api/ligero/{key}/store', array('as' => 'api.ligero.store', 'uses' => '\Viewflex\Ligero\Controllers\ContextApiController@store', 'middleware' => 'api'));
Route::post('api/ligero/{key}/update', array('as' => 'api.ligero.update', 'uses' => '\Viewflex\Ligero\Controllers\ContextApiController@update', 'middleware' => 'api'));
Route::post('api/ligero/{key}/delete', array('as' => 'api.ligero.destroy', 'uses' => '\Viewflex\Ligero\Controllers\ContextApiController@destroy', 'middleware' => 'api'));

// Custom context actions (defaulting to standard CRUD domain actions)...
Route::post('api/ligero/{key}/context/find', array('as' => 'api.ligero.context.find', 'uses' => '\Viewflex\Ligero\Controllers\ContextApiController@findContext', 'middleware' => 'api'));
Route::post('api/ligero/{key}/context/findby', array('as' => 'api.ligero.context.findby', 'uses' => '\Viewflex\Ligero\Controllers\ContextApiController@findContextBy', 'middleware' => 'api'));
Route::post('api/ligero/{key}/context/store', array('as' => 'api.ligero.context.store', 'uses' => '\Viewflex\Ligero\Controllers\ContextApiController@storeContext', 'middleware' => 'api'));
Route::post('api/ligero/{key}/context/update', array('as' => 'api.ligero.context.update', 'uses' => '\Viewflex\Ligero\Controllers\ContextApiController@updateContext', 'middleware' => 'api'));
Route::post('api/ligero/{key}/context/delete', array('as' => 'api.ligero.context.destroy', 'uses' => '\Viewflex\Ligero\Controllers\ContextApiController@destroyContext', 'middleware' => 'api'));
```

### Handling Relations

There are several ways to approach related tables, depending on the complexity of your requirements, and whether you need to write related data in addition to reading it. Depending on the database engine and schema you deploy, there may also be actions taken at the database level, such as cascading updates and deletes, and various other data-integrity constraints to consider.

#### Relations using Eloquent and Presenters

Laravel's Eloquent ORM provides a rich set of tools for defining and using relations on a model; using a Presenter we can easily read data using these relations.

##### Reading Eloquent Data

If you define relational methods on your Eloquent model, they can be called via the model's `present()` method. Modify the `dynamicFields()` method in your Presenter to include the output of these relational methods in the results of `getItems()`.

##### Writing Eloquent Data

To use `store()` or `update()` to modify data in related tables, you would need to extend the Repository class to override those methods, adjusting your configuration and validation rules accordingly.

You can also extend the Publisher along with the repository, adding new methods to handle input with data for related tables.

#### Relations using Contexts

A "rich" Context can read and/or write data to other domains (tables), in addition to it's primary one. Each of the related domains (subcontexts) can be called using the standard Context methods, to manually maintain the arrangement of primary and related data.

### Automatic Handling of Timestamps

The `BasePublisherRepository` `store()`, `update()` and `delete()` methods transparently handle the columns `created_at`, `updated_at`, `deleted_at` (supporting soft deletes) and `creator_id`, if these exist as results columns. This means you don't have to handle them explicitly when using the package CRUD actions.

## Customization

This package comes with a demo domain, 'Items', that provides examples of publishing a domain with both a UI controller and an API controller. To install the demo 'Items' domain to play around with it, or to use it as boilerplate for a new domain, just run the `publish` command with the `ligero` tag, run the migration, and seed the demo database table, as described below.

Copy and rename the demo files you need and change the class names, to implement Publishers for custom domains. Copy and rename the resource files (views and lang), and customize as needed.


### Publishing the Package Files

The package service provider configures `artisan` to publish specific file groups with tags. There are several option available in this package.

#### Routes

Run this command to publish the `routes.php` file to the project's `publish/viewflex/listo` directory:

```bash
php artisan vendor:publish  --tag='ligero-routes'
```

#### Config

Run this command to publish the `ligero.php` config file to the project's `config` directory for customization:

```bash
php artisan vendor:publish  --tag='ligero-config'
```

#### Resources

Run this command to publish the blade templates for the demo UI, and lang files for package messages and UI strings:

```bash
php artisan vendor:publish  --tag='ligero-resources'
```

#### Routes, Demo Migration and Seeder

Run this command to install the migration and seeder for the 'Items' demo domain:

```bash
php artisan vendor:publish  --tag='ligero-data'
```

After publishing the demo migration and seeder, run the migration:

```bash
php artisan migrate
```

Then run the seeder:

```bash
php artisan db:seed --class="LigeroSeeder"
```

#### Routes, Config, Resources, Demo Migration and Seeder

Use this command to publish config, demo views, and lang files for modification. The demo migration and seeder are also copied to their proper directories:

```bash
php artisan vendor:publish  --tag='ligero'
```

### Extending or Decorating Base Classes

Ligero's architecture is based on a distinct pattern of class types, each defined by an interface; since classes relate to each other as abstract types, you can easily substitute your own custom classes, provided that they implement the same interfaces.

### Namespace for Custom Classes

The `Viewflex\Ligero\Publish` namespace, corresponding to the `publish/viewflex/ligero` directory, is recognized by the package, and is intended for organization of your custom classes. The Items demo classes will be published (copied) to this directory for customization, along with the demo routes file.

## Tests

The phpunit tests can be run in the usual way, as described in the [Test Documentation](https://github.com/viewflex/ligero-docs/blob/master/TESTS.md).

## License

This software is offered for use under the [MIT License](https://github.com/viewflex/ligero/blob/master/LICENSE.md).

## Changelog

Release versions are tracked in the [Changelog](https://github.com/viewflex/ligero-docs/blob/master/CHANGELOG.md).

## Contributing

Please see the [Contributing Guide](https://github.com/viewflex/ligero-docs/blob/master/CONTRIBUTING.md) to learn more about the project goals and how you can help.
