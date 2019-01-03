<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Viewflex\Ligero\Contracts\ContextInterface;
use Viewflex\Ligero\Database\Testing\LigeroTestData;
use Viewflex\Ligero\Publish\Demo\Items\ItemsContext;

class LigeroContextTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var ContextInterface
     */
    protected $context;
    
    protected function setUp()
    {
        parent::setUp();
        LigeroTestData::create(['ligero_items' => 'ligero_items']);
        $this->context = new ItemsContext;
    }


    public function test_context_find()
    {
        $expected = [
            "success" => 1,
            "msg" => null,
            "data" => [
                "id" => 2,
                "active" => "1",
                "name" => "North Face Fleece Pullover",
                "category" => "Outerwear",
                "subcategory" => "Unisex",
                "description" => "",
                "price" => "49.9"
            ]
        ];

        $this->assertEquals($expected, $this->context->find(2, false));
    }

    public function test_context_findBy()
    {
        $expected = [
            "id" => 2,
            "active" => "1",
            "name" => "North Face Fleece Pullover",
            "category" => "Outerwear",
            "subcategory" => "Unisex",
            "description" => "",
            "price" => "49.9"
        ];

        $this->assertEquals($expected, $this->context->findBy(['name' => 'North Face Fleece Pullover'], false)['data'][0]);
    }

    public function test_context_store()
    {
        $inputs = [
            'active' => '1',
            'name' => 'Alpaca Shawl',
            'category' => 'Outerwear',
            'subcategory' => 'Wraps',
            'description' => 'Gorgeous alpaca shawl will keep you toasty warm.',
            'price' => 128.50
        ];

        // Save the record, getting it's id.
        $id = $this->context->store($inputs)['data'];
        $this->assertGreaterThan(0, $id);

        // Search by id, should find it.
        $this->assertEquals(1, $this->context->find($id, false)['success']);

        // See if the new record conforms to input.
        $item = $this->context->find($id, false)['data'];
        $this->assertEquals($inputs, array_except($item, 'id'));

    }


    public function test_context_update()
    {
        $inputs = [
            'active' => '1',
            'name' => 'Suunto Weatherproof Compass',
            'category' => 'Sports Equipment',
            'subcategory' => 'Hiking',
            'description' => 'Matte black, rubberized with phosphorescent markings for night viewing. Lifetime warranty.',
            'price' => 415.98
        ];

        // Save the record, getting it's id.
        $id = $this->context->store($inputs)['data'];
        $this->assertGreaterThan(0, $id);

        // Search by id, should find it.
        $this->assertEquals(1, $this->context->find($id, false)['success']);

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
        $affected = $this->context->update($new_inputs)['data'];
        $this->assertEquals(1, $affected);

        // See if the modified record conforms to new input.
        $item = $this->context->find($id, false)['data'];
        $this->assertEquals($new_inputs, $item);
    }


    public function test_context_delete()
    {
        // Delete an existing record, getting number of rows affected (should be 1).
        $affected = $this->context->delete(2)['data'];
        $this->assertEquals(1, $affected);

        // See if the record can still be found.
        $this->assertEquals(0, $this->context->find(2)['success']);
    }

}
