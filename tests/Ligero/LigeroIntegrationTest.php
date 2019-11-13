<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Viewflex\Ligero\Database\Testing\LigeroTestData;
use Viewflex\Ligero\Publishers\HasFluentConfiguration;
use Viewflex\Ligero\Publishers\HasPublisher;

class LigeroIntegrationTest extends TestCase
{
    use HasFluentConfiguration;
    use HasPublisher;
    use DatabaseTransactions;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->createPublisherWithDefaults();
        include('ConfiguresItems.php');
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
        $this->setInputs(['view' => 'item']);
        $this->assertEquals(1, $this->publisher->displayed());
    }

    public function test_query_returns_all_records_with_custom_limit()
    {
        $this->setInputs(['limit' => 100]);
        $this->assertEquals(10, $this->publisher->displayed());
    }

    public function test_query_finds_records_matching_single_input()
    {
        $this->setInputs(['category' => 'Footwear']);
        $this->assertEquals(4, $this->publisher->displayed());
    }

    public function test_query_finds_records_matching_multiple_inputs()
    {
        $this->setInputs(['category' => 'Footwear', 'subcategory' => 'Unisex']);
        $this->assertEquals(1, $this->publisher->displayed());
    }

    public function test_query_finds_records_using_wildcard_matching()
    {
        $this->setWildcardColumns(['subcategory']);
        $this->setInputs(['category' => 'Footwear', 'subcategory' => 'Men']);
        $this->assertEquals(3, $this->publisher->displayed());
    }

    public function test_query_finds_records_using_strict_matching()
    {
        $this->setWildcardColumns([]);
        $this->setInputs(['category' => 'Footwear', 'subcategory' => 'Men']);
        $this->assertEquals(2, $this->publisher->displayed());
    }

    public function test_query_ignores_non_matching_records()
    {
        $this->setInputs(['category' => 'nonsense']);
        $this->assertEquals(0, $this->publisher->found());
    }
    
    
}
