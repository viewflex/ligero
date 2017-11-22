<?php

namespace Viewflex\Ligero\Database\Testing;

use Viewflex\Ligero\Database\Schema\LigeroSchema;

class LigeroTestData
{
    /**
     * Create the test database and tables.
     *
     * @param array $tables
     * @return void
     */
    public static function create($tables)
    {
        // Migrate
        LigeroSchema::create($tables);

        // Seed:
        LigeroTestSeeder::seed($tables);
    }

    /**
     * Drop the test database and tables.
     *
     * @param array $tables
     * @return void
     */
    public static function drop($tables)
    {
        // Drop the tables that we created.
        LigeroSchema::drop($tables);
        
    }
    
}
