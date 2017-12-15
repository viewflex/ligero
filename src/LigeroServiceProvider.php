<?php

namespace Viewflex\Ligero;

use Illuminate\Support\ServiceProvider;

class LigeroServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$resource_namespace = 'ligero';

		/*
        |--------------------------------------------------------------------------
        | Publish Routes File
        |--------------------------------------------------------------------------
        */

		$this->publishes([
			__DIR__.'/Publish/routes.php' => base_path('publish/viewflex/ligero/routes.php')
		], 'ligero-routes');
		
		/*
    	|--------------------------------------------------------------------------
    	| Set the Default Internal Namespace for Translations and Views
    	|--------------------------------------------------------------------------
    	*/

		$this->loadTranslationsFrom(__DIR__ . '/Resources/lang', $resource_namespace);
		$this->loadViewsFrom(__DIR__ . '/Resources/views', $resource_namespace);

		/*
    	|--------------------------------------------------------------------------
    	| Publish the Package Translations and Views to the Working Directory
    	|--------------------------------------------------------------------------
    	*/

		$this->publishes([
			__DIR__ . '/Resources/lang' => base_path('resources/lang/vendor/ligero'),
			__DIR__ . '/Resources/views' => base_path('resources/views/vendor/ligero')
		], 'ligero-resources');

		/*
        |--------------------------------------------------------------------------
        | Publish Routes, Migration and Seeder to the Database Directory
        |--------------------------------------------------------------------------
        */

		$this->publishes([
            __DIR__.'/Publish/routes.php' => base_path('publish/viewflex/ligero/routes.php'),
			__DIR__ . '/Database/Migrations' => base_path('database/migrations'),
			__DIR__ . '/Database/Seeds' => base_path('database/seeds')
		], 'ligero-data');

		/*
        |--------------------------------------------------------------------------
        | Publish Config File to Config Directory, Merge With App Globals
        |--------------------------------------------------------------------------
        */

		$this->publishes([
			__DIR__.'/Config/ligero.php' => config_path('ligero.php'),
		], 'ligero-config');

		$this->mergeConfigFrom(
			__DIR__.'/Config/ligero.php', 'ligero'
		);

		/*
        |--------------------------------------------------------------------------
        | Publish Routes, Config, Resources, Migration and Seeder
        |--------------------------------------------------------------------------
        */

		$this->publishes([
			__DIR__ . '/Config/ligero.php' => config_path('ligero.php'),
			__DIR__ . '/Database/Migrations' => base_path('database/migrations'),
			__DIR__ . '/Database/Seeds' => base_path('database/seeds'),
            __DIR__ . '/Publish/Demo' => base_path('publish/viewflex/ligero/Demo'),
            __DIR__ . '/Publish/routes.php' => base_path('publish/viewflex/ligero/routes.php'),
            __DIR__ . '/Resources/lang' => base_path('resources/lang/vendor/ligero'),
            __DIR__ . '/Resources/views' => base_path('resources/views/vendor/ligero')
		], 'ligero');

	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		/*
    	|--------------------------------------------------------------------------
    	| Include the Routes File Published for Customization, if it exists.
    	|--------------------------------------------------------------------------
    	*/

		$published_routes = base_path('publish/viewflex/ligero/routes.php');
		if (file_exists($published_routes))
			require $published_routes;
		
    }

}
