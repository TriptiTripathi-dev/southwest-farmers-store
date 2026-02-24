<?php

namespace Tests\Feature;

use App\Models\StoreUser;
use App\Models\Product;
use App\Models\StoreStock;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use App\Models\ProductBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class Phase4InventoryTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = StoreUser::factory()->create([
            'store_id' => 1,
            'is_active' => true
        ]);
    }

    /** @test */
    public function it_can_convert_weight_to_units()
    {
        $product = Product::factory()->create([
            'unit_type' => 'weight',
            'unit' => 'kg'
        ]);

        $stock = StoreStock::create([
            'store_id' => 1,
            'product_id' => $product->id,
            'quantity' => 100
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('inventory.convert'), [
                'stock_id' => $stock->id,
                'source_qty' => 50,
                'target_unit' => 'Bags (50kg)',
                'resulting_qty' => 1
            ]);

        $response->assertStatus(302);
        
        $this->assertEquals(50, $stock->fresh()->quantity);
        
        // Check transaction log
        $this->assertDatabaseHas('stock_transactions', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 50,
            'reference_type' => 'weight_conversion'
        ]);
    }

    /** @test */
    public function it_captures_batch_and_expiry_on_receiving()
    {
        $po = StockRequest::factory()->create([
            'store_id' => 1,
            'status' => 'dispatched'
        ]);
        
        $item = StockRequestItem::factory()->create([
            'stock_request_id' => $po->id,
            'quantity' => 10,
            'dispatched_quantity' => 10
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('store.stock-control.received.confirm', $po->id), [
                'items' => [
                    [
                        'id' => $item->id,
                        'received_quantity' => 10,
                        'batch_number' => 'BATCH-TEST-001',
                        'expiry_date' => '2027-12-31'
                    ]
                ]
            ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('product_batches', [
            'product_id' => $item->product_id,
            'batch_number' => 'BATCH-TEST-001',
            'expiry_date' => '2027-12-31',
            'quantity' => 10
        ]);
    }

    /** @test */
    public function it_blocks_orders_if_stock_level_is_sufficient()
    {
        $product = Product::factory()->create([
            'min_stock' => 10,
            'max_stock' => 100
        ]);

        StoreStock::create([
            'store_id' => 1,
            'product_id' => $product->id,
            'quantity' => 101 // Already >= max_stock
        ]);

        // This is primarily a JS level block in the UI, but we can test the replenishment generation logic
        // if it uses the same check.
        
        // Actually, let's test the Replenishment Generation logic in the controller.
        $productLow = Product::factory()->create([
            'min_stock' => 10,
            'max_stock' => 100
        ]);
        
        StoreStock::create([
            'store_id' => 1,
            'product_id' => $productLow->id,
            'quantity' => 5 // Below min_stock
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('store.stock-control.replenish'));

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('stock_requests', [
            'store_id' => 1,
            'status' => 'pending'
        ]);
        
        // The created PO should have the low stock item
        $po = StockRequest::where('store_id', 1)->latest()->first();
        $this->assertDatabaseHas('stock_request_items', [
            'stock_request_id' => $po->id,
            'product_id' => $productLow->id,
            'quantity' => 95 // 100 - 5
        ]);
        
        // And NOT have the sufficient stock item
        $this->assertDatabaseMissing('stock_request_items', [
            'stock_request_id' => $po->id,
            'product_id' => $product->id
        ]);
    }
}
