<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Viewflex\Ligero\Database\Testing\LigeroTestData;
use Viewflex\Ligero\Publish\Demo\Items\ItemsConfig as Config;
use Viewflex\Ligero\Publish\Demo\Items\ItemsRequest as Request;
use Viewflex\Ligero\Publish\Demo\Items\ItemsRepository as Query;
use Viewflex\Ligero\Publishers\HasPublisher;

class LigeroUnitTest extends TestCase
{
    use HasPublisher;
    use DatabaseTransactions;

    protected function setUp()
    {
        parent::setUp();

        $this->createPublisher(new Config, new Request, new Query);
        LigeroTestData::create(['ligero_items' => 'ligero_items']);
    }
    
    public function test_mapColumn()
    {
        $this->query->setColumnMap([
            'name'          => 'full_name',
            'dob'           => 'date_of_birth'
        ]);
        $this->assertEquals('full_name', $this->query->mapColumn('name'));
        $this->assertEquals('name', $this->query->rmapColumn('full_name'));
    }

    public function test_getQueryInputs()
    {
        $this->request->setInputs([
            'id'            => '5',
            'active'        => '1',
            'name'          => 'Naot Teva Sandals',
            'category'      => 'Footwear',
            'subcategory'   => 'Unisex ', // trailing space should be trimmed
            'keyword'       => ' ', // empty string should be filtered
            'sort'          => 'name',
            'view'          => 'list',
            'limit'         => '',
            'start'         => '',
            'action'        => '',
            'items'         => '',
            'options'       => '',
            'page'          => '2'
        ]);

        $this->assertEquals(8, count($this->request->getQueryInputs()));
        $this->assertEquals('Unisex', $this->request->getQueryInputs()['subcategory']);
        $this->assertFalse(array_key_exists('keyword', $this->request->getQueryInputs()));
    }
    
}
