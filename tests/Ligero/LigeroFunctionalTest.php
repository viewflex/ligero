<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Viewflex\Ligero\Database\Testing\LigeroTestData;
use Viewflex\Ligero\Publish\Demo\Items\ItemsConfig as Config;
use Viewflex\Ligero\Publish\Demo\Items\ItemsRequest as Request;
use Viewflex\Ligero\Publish\Demo\Items\ItemsRepository as Query;
use Viewflex\Ligero\Publishers\HasPublisher;

class LigeroFunctionalTest extends TestCase
{
    use HasPublisher;
    use DatabaseTransactions;

    protected function setUp()
    {
        parent::setUp();

        $this->createPublisher(new Config, new Request, new Query);
        LigeroTestData::create(['ligero_items' => 'ligero_items']);
    }


    public function test_publisher_find()
    {
        $this->createPublisher(new Config, new Request, new Query);
        $this->assertEquals('North Face Fleece Pullover', $this->publisher->find(2, false)['name']);
    }

    public function test_publisher_findBy()
    {
        $this->createPublisher(new Config, new Request, new Query);
        $this->assertEquals(2, $this->publisher->findBy(['name' => 'North Face Fleece Pullover'], false)[0]['id']);
    }

    public function test_publisher_store()
    {
        $this->createPublisher(new Config, new Request, new Query);
        $inputs = [
            'active' => '1',
            'name' => 'Alpaca Shawl',
            'category' => 'Outerwear',
            'subcategory' => 'Wraps',
            'description' => 'Gorgeous alpaca shawl will keep you toasty warm.',
            'price' => 128.50
        ];

        // Save the record, getting it's id.
        $this->request->setInputs($inputs);
        $id = $this->publisher->store();
        $this->assertGreaterThan(0, $id);

        // Search by id, should find exactly one record.
        $this->request->setInputs(['id' => $id]);
        $this->assertEquals(1, $this->publisher->found());

        // See if the new record conforms to input.
        $item = $this->publisher->getItems()[0];
        $this->assertEquals($inputs, array_except($item, 'id'));
    }


    public function test_publisher_update()
    {
        $this->createPublisher(new Config, new Request, new Query);
        $inputs = [
            'active' => '1',
            'name' => 'Suunto Weatherproof Compass',
            'category' => 'Sports Equipment',
            'subcategory' => 'Hiking',
            'description' => 'Matte black, rubberized with phosphorescent markings for night viewing. Lifetime warranty.',
            'price' => 415.98
        ];

        // Save the record, getting it's id.
        $this->request->setInputs($inputs);
        $id = $this->publisher->store();
        $this->assertGreaterThan(0, $id);

        // Search by id, should find exactly one record.
        $this->request->setInputs(['id' => $id]);
        $this->assertEquals(1, $this->publisher->found());

        $new_inputs = [
            'id' => $id,
            'active' => '1',
            'name' => 'Suunto Rubberized Mil-Spec Compass',
            'category' => 'Sports Equipment',
            'subcategory' => 'Hiking',
            'description' => 'Matte black, rubberized with phosphorescent markings for night viewing. Adjustable for all magnetic zones. Lifetime warranty.',
            'price' => 415.98
        ];

        // Modify the existing record, getting number of rows affected (should be 1).
        $this->request->setInputs($new_inputs);
        $affected = $this->publisher->update();
        $this->assertEquals(1, $affected);
        
        // See if the modified record conforms to new input.
        $this->request->setInputs(['id' => $id]);
        $item = $this->publisher->getItems()[0];
        $this->assertEquals($new_inputs, $item);
    }


    public function test_publisher_delete()
    {
        $this->createPublisher(new Config, new Request, new Query);

        // Delete an existing record, getting number of rows affected (should be 1).
        $this->request->setInputs(['id' => 2]);
        $affected = $this->publisher->delete();
        $this->assertEquals(1, $affected);

        // See if the record can still be found.
        $this->assertEquals(0, $this->publisher->find(2));
    }

}
