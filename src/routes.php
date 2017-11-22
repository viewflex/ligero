<?php

// These routes are endpoints for the Ligero demo.


// UI Controller Routes:

Route::group(['middleware' => 'web'], function () {

    Route::group(['prefix' => 'ligero/items'], function() {

        // Special actions...
        Route::get('json', array('as' => 'ligero.items.json', 'uses' => '\Viewflex\Ligero\Publish\Demo\Items\ItemsController@json'));
        Route::get('action', array('as' => 'ligero.items.action', 'uses' => '\Viewflex\Ligero\Publish\Demo\Items\ItemsController@action'));

        // Resourceful actions...
        Route::get('', array('as' => 'ligero.items.index', 'uses' => '\Viewflex\Ligero\Publish\Demo\Items\ItemsController@index'));
        Route::get('create', array('as' => 'ligero.items.create', 'uses' => '\Viewflex\Ligero\Publish\Demo\Items\ItemsController@create'));
        Route::get('{id}', array('as' => 'ligero.items.show', 'uses' => '\Viewflex\Ligero\Publish\Demo\Items\ItemsController@show'));
        Route::get('{id}/edit', array('as' => 'ligero.items.edit', 'uses' => '\Viewflex\Ligero\Publish\Demo\Items\ItemsController@edit'));
        Route::post('store', array('as' => 'ligero.items.store', 'uses' => '\Viewflex\Ligero\Publish\Demo\Items\ItemsController@store'));
        Route::put('{id}', array('as' => 'ligero.items.update', 'uses' => '\Viewflex\Ligero\Publish\Demo\Items\ItemsController@update'));
        Route::delete('{id}', array('as' => 'ligero.items.destroy', 'uses' => '\Viewflex\Ligero\Publish\Demo\Items\ItemsController@destroy'));

    });

});

// For resourceful actions like above, but with {items} automatically used as route parameter (instead of {id}), use this instead:
//    Route::resource('ligero/items', '\Viewflex\Ligero\Publish\Demo\Items\ItemsController', ['middleware' => 'web']);


// API Controller Routes:

Route::group(['middleware' => 'api'], function () {

    Route::group(['prefix' => 'api/ligero'], function() {

        // Standard CRUD domain actions...
        Route::post('{key}/find', array('as' => 'api.ligero.find', 'uses' => '\Viewflex\Ligero\Controllers\ContextApiController@find'));
        Route::post('{key}/findby', array('as' => 'api.ligero.findby', 'uses' => '\Viewflex\Ligero\Controllers\ContextApiController@findBy'));
        Route::post('{key}/store', array('as' => 'api.ligero.store', 'uses' => '\Viewflex\Ligero\Controllers\ContextApiController@store'));
        Route::post('{key}/update', array('as' => 'api.ligero.update', 'uses' => '\Viewflex\Ligero\Controllers\ContextApiController@update'));
        Route::post('{key}/delete', array('as' => 'api.ligero.destroy', 'uses' => '\Viewflex\Ligero\Controllers\ContextApiController@destroy'));

        // Custom context actions (defaulting to standard CRUD domain actions)...
        Route::post('{key}/context/find', array('as' => 'api.ligero.context.find', 'uses' => '\Viewflex\Ligero\Controllers\ContextApiController@findContext'));
        Route::post('{key}/context/findby', array('as' => 'api.ligero.context.findby', 'uses' => '\Viewflex\Ligero\Controllers\ContextApiController@findContextBy'));
        Route::post('{key}/context/store', array('as' => 'api.ligero.context.store', 'uses' => '\Viewflex\Ligero\Controllers\ContextApiController@storeContext'));
        Route::post('{key}/context/update', array('as' => 'api.ligero.context.update', 'uses' => '\Viewflex\Ligero\Controllers\ContextApiController@updateContext'));
        Route::post('{key}/context/delete', array('as' => 'api.ligero.context.destroy', 'uses' => '\Viewflex\Ligero\Controllers\ContextApiController@destroyContext'));

    });

});

