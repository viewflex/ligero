<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * For seeding Ligero test data via artisan command or static method seed().
 */
class LigeroSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * Note: These seeds are for testing.
     *
     * @return void
     */
    public function run()
    {
        $this::seed(['ligero_items' => 'ligero_items']);
    }
    
    /**
     * Seed the database with test data.
     *
     * @param array $tables
     */
    public static function seed($tables = [])
    {
        // ----------------------------------------
        // Items
        // ----------------------------------------

        DB::table($tables['ligero_items'])->insert(['id' => 1, 'active' => '1', 'name' => 'Patagonia Ski Jacket', 'category' => 'Outerwear', 'subcategory' => 'Unisex', 'description' => '', 'price' => 289.00]);
        DB::table($tables['ligero_items'])->insert(['id' => 2, 'active' => '1', 'name' => 'North Face Fleece Pullover', 'category' => 'Outerwear', 'subcategory' => 'Unisex', 'description' => '', 'price' => 49.90]);
        DB::table($tables['ligero_items'])->insert(['id' => 3, 'active' => '1', 'name' => 'Timberland Hiker Boots', 'category' => 'Footwear', 'subcategory' => 'Men', 'description' => '', 'price' => 150.00]);
        DB::table($tables['ligero_items'])->insert(['id' => 4, 'active' => '1', 'name' => 'Billabong Surfer Shorts', 'category' => 'Swimsuits', 'subcategory' => 'Men', 'description' => '', 'price' => 25.00]);
        DB::table($tables['ligero_items'])->insert(['id' => 5, 'active' => '1', 'name' => 'Naot Teva Sandals', 'category' => 'Footwear', 'subcategory' => 'Unisex', 'description' => '', 'price' => 34.99]);
        DB::table($tables['ligero_items'])->insert(['id' => 6, 'active' => '1', 'name' => 'Nike Air Jordan Sneakers', 'category' => 'Footwear', 'subcategory' => 'Men', 'description' => '', 'price' => 171.25]);
        DB::table($tables['ligero_items'])->insert(['id' => 7, 'active' => '1', 'name' => 'Reebok Princess Tennis Shoes', 'category' => 'Footwear', 'subcategory' => 'Women', 'description' => '', 'price' => 44.50]);
        DB::table($tables['ligero_items'])->insert(['id' => 8, 'active' => '1', 'name' => 'Woolrich Heavyweight Hiking Socks', 'category' => 'Socks', 'subcategory' => 'Unisex', 'description' => '', 'price' => 14.95]);
        DB::table($tables['ligero_items'])->insert(['id' => 9, 'active' => '1', 'name' => 'Hand-Knit Wool Leggins', 'category' => 'Socks', 'subcategory' => 'Women', 'description' => '', 'price' => 85.00]);
        DB::table($tables['ligero_items'])->insert(['id' => 10, 'active' => '1', 'name' => 'Hanes Thermal Crewneck Shirt', 'category' => 'Shirts', 'subcategory' => 'Unisex', 'description' => '', 'price' => 18.00]);
        
    }
    
}
