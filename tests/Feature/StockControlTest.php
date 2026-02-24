<?php

namespace Tests\Feature;

use App\Models\StoreUser;
use App\Models\Product;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use App\Models\StoreStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockControlTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a store user
        $this->user = StoreUser::factory()->create([
            'store_id' => 1,
            'is_active' => true
        ]);
    }

    /** @test */
    public function it_can_create_a_purchase_order_with_multiple_items()
    {
        $products = Product::factory()->count(2)->create();

        $response = $this->actingAs($this->user)
            ->post(route('store.stock-control.requests.store'), [
                'products' => [
                    [
                        'product_id' => $products[0]->id,
                        'quantity' => 10,
                        'unit_cost' => 100
                    ],
                    [
                        'product_id' => $products[1]->id,
                        'quantity' => 5,
                        'unit_cost' => 200
                    ]
                ],
                'remarks' => 'Test PO'
            ]);

        $response->assertRedirect(route('store.stock-control.requests'));
        $this->assertDatabaseHas('stock_requests', [
            'store_id' => 1,
            'status' => 'pending',
            'total_items' => 2,
            'total_amount' => 2000 // (10*100) + (5*200)
        ]);
    }

    /** @test */
    public function it_can_view_po_details()
    {
        $po = StockRequest::factory()->create(['store_id' => 1]);
        $item = StockRequestItem::factory()->create(['stock_request_id' => $po->id]);

        $response = $this->actingAs($this->user)
            ->get(route('store.stock-control.requests.show', $po->id));

        $response->assertStatus(200)
            ->assertSee($po->request_number)
            ->assertSee($item->product->product_name);
    }

    /** @test */
    public function it_can_receive_dispatched_po_items()
    {
        $po = StockRequest::factory()->create([
            'store_id' => 1,
            'status' => 'dispatched'
        ]);
        
        $item1 = StockRequestItem::factory()->create([
            'stock_request_id' => $po->id,
            'quantity' => 10,
            'dispatched_quantity' => 10
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('store.stock-control.received.confirm', $po->id), [
                'items' => [
                    [
                        'id' => $item1->id,
                        'received_quantity' => 8
                    ]
                ],
                'remarks' => 'Partial receipt'
            ]);

        $response->assertRedirect(route('store.stock-control.requests'));
        
        // Check item update
        $this->assertDatabaseHas('stock_request_items', [
            'id' => $item1->id,
            'received_quantity' => 8
        ]);

        // Check stock update
        $this->assertDatabaseHas('store_stocks', [
            'store_id' => 1,
            'product_id' => $item1->product_id,
            'quantity' => 8
        ]);

        // Check status
        $this->assertDatabaseHas('stock_requests', [
            'id' => $po->id,
            'status' => 'completed'
        ]);
    }
}
