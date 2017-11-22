<?php

use Illuminate\Database\Migrations\Migration;
use Viewflex\Ligero\Database\Schema\LigeroSchema;

/**
 * Creates the database table for the Viewflex/Ligero package.
 */
class LigeroMigration extends Migration
{
    protected $listing_tables = [
        'ligero_items'               =>  'ligero_items'
    ];
    
    /**
     * Run the migrations.
     */
    public function up()
    {
        LigeroSchema::create($this->listing_tables);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        LigeroSchema::drop($this->listing_tables);
    }
}
