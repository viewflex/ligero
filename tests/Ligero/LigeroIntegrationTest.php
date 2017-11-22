<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Viewflex\Ligero\Database\Testing\LigeroTestData;
use Viewflex\Ligero\Publish\Demo\Items\ItemsConfig as Config;
use Viewflex\Ligero\Publish\Demo\Items\ItemsRequest as Request;
use Viewflex\Ligero\Publish\Demo\Items\ItemsRepository as Query;
use Viewflex\Ligero\Publishers\HasPublisher;

class LigeroIntegrationTest extends TestCase
{
    use HasPublisher;
    use DatabaseTransactions;

    protected function setUp()
    {
        parent::setUp();

        $this->createPublisher(new Config, new Request, new Query);
        LigeroTestData::create(['ligero_items' => 'ligero_items']);
    }

    public function test_query_finds_all_records()
    {
        $this->assertEquals(10, $this->publisher->found());
    }

    public function test_query_returns_all_records_with_default_limit()
    {
        $this->assertEquals(5, $this->publisher->displayed());
    }

    public function test_query_returns_all_records_with_view_limit()
    {
        $this->request->setInputs(['view' => 'item']);
        $this->assertEquals(1, $this->publisher->displayed());
    }

    public function test_query_returns_all_records_with_custom_limit()
    {
        $this->request->setInputs(['limit' => 100]);
        $this->assertEquals(10, $this->publisher->displayed());
    }

    public function test_query_finds_records_matching_single_input()
    {
        $this->request->setInputs(['category' => 'Footwear']);
        $this->assertEquals(4, $this->publisher->displayed());
    }

    public function test_query_finds_records_matching_multiple_inputs()
    {
        $this->request->setInputs(['category' => 'Footwear', 'subcategory' => 'Unisex']);
        $this->assertEquals(1, $this->publisher->displayed());
    }

    public function test_query_finds_records_using_wildcard_matching()
    {
        $this->config->setWildcardColumns(['subcategory']);
        $this->request->setInputs(['category' => 'Footwear', 'subcategory' => 'Men']);
        $this->assertEquals(3, $this->publisher->displayed());
    }

    public function test_query_finds_records_using_strict_matching()
    {
        $this->request->setInputs(['category' => 'Footwear', 'subcategory' => 'Men']);
        $this->assertEquals(2, $this->publisher->displayed());
    }

    public function test_query_ignores_non_matching_records()
    {
        $this->request->setInputs(['category' => 'nonsense']);
        $this->assertEquals(0, $this->publisher->found());
    }
    
    
}
