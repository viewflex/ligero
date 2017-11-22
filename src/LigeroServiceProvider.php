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
        | Publish Migration and Seeder to the Database Directory
        |--------------------------------------------------------------------------
        */

		$this->publishes([
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
        | Publish Config, Resources, Migration and Seeder
        |--------------------------------------------------------------------------
        */

		$this->publishes([
			__DIR__ . '/Config/ligero.php' => config_path('ligero.php'),
			__DIR__ . '/Database/Migrations' => base_path('database/migrations'),
			__DIR__ . '/Database/Seeds' => base_path('database/seeds'),
			__DIR__ . '/Publish' => base_path('publish/viewflex/ligero'),
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
    	| Include Package Routes File.
    	|--------------------------------------------------------------------------
    	*/

		require __DIR__ . '/routes.php';
		
    }

}
