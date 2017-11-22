<?php

namespace Viewflex\Ligero\Database\Schema;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LigeroSchema
{
    /**
     * Run the migrations to create Ligero schema for testing or production.
     * 
     * @param array $tables
     */
    public static function create($tables = [])
    {
        // ----------------------------------------
        // Items
        // ----------------------------------------

        Schema::create($tables['ligero_items'], function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('active')->default(false);
            $table->string('name', 60)->nullable();
            $table->string('category', 60)->nullable();
            $table->string('subcategory', 60)->nullable();
            $table->text('description')->nullable();
            $table->double('price')->nullable()->default(0.00);
            $table->timestamps();
            $table->index('active');
            $table->index('name');
            $table->index('category');
            $table->index('subcategory');
            $table->index('price');
        });
    }
    
    /**
     * Reverse the migration.
     * 
     * @param array $tables
     */
    public static function drop($tables = [])
    {
        Schema::drop($tables['ligero_items']);
    }
    
    
}
