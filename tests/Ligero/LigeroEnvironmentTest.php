<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Viewflex\Ligero\Database\Testing\LigeroTestData;
use Viewflex\Ligero\Publish\Demo\Items\Item;

class LigeroEnvironmentTest extends TestCase
{
    use DatabaseTransactions;
    
    /**
     * @test
     */
    public function data_source_can_be_created_and_queried()
    {
        LigeroTestData::create(['ligero_items' => 'ligero_items']);
        
        $items = Item::all();
        $this->assertEquals(10, count($items));


    }
    

}
