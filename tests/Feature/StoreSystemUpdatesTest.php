<?php

namespace Tests\Feature;

use App\Models\StoreDetail;
use App\Models\StorePurchaseOrder;
use App\Models\StorePurchaseOrderItem;
use App\Models\StoreUser;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class StoreSystemUpdatesTest extends TestCase
{
    use RefreshDatabase;

    protected $store;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('store_details')) {
            Schema::create('store_details', function (Blueprint $table) {
                $table->id();
                $table->string('store_name');
                $table->string('code')->nullable();
                $table->string('status')->default('active');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('store_users')) {
            Schema::create('store_users', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->nullable();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('store_roles')) {
            Schema::create('store_roles', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('guard_name')->default('web');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('store_model_has_roles')) {
            Schema::create('store_model_has_roles', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id');
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
            });
        }

        if (!Schema::hasTable('store_role_has_permissions')) {
            Schema::create('store_role_has_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('permission_id');
                $table->unsignedBigInteger('role_id');
            });
        }

        if (!Schema::hasTable('store_permissions')) {
            Schema::create('store_permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('guard_name')->default('web');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('product_name');
                $table->string('upc')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->unsignedBigInteger('store_id')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('store_stocks')) {
            Schema::create('store_stocks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id');
                $table->unsignedBigInteger('product_id');
                $table->integer('quantity')->default(0);
                $table->integer('min_stock')->default(0);
                $table->integer('max_stock')->default(100);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('stock_transactions')) {
            Schema::create('stock_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->nullable();
                $table->unsignedBigInteger('product_id');
                $table->string('type');
                $table->integer('quantity_change')->default(0);
                $table->integer('running_balance')->default(0);
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->text('remarks')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('store_purchase_orders')) {
            Schema::create('store_purchase_orders', function (Blueprint $table) {
                $table->id();
                $table->string('po_number');
                $table->unsignedBigInteger('store_id');
                $table->string('status')->default('pending');
                $table->integer('total_items')->default(0);
                $table->decimal('total_amount', 10, 2)->default(0);
                $table->text('warehouse_remarks')->nullable();
                $table->text('store_remarks')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('dispatched_at')->nullable();
                $table->timestamp('received_at')->nullable();
                $table->unsignedBigInteger('requested_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('store_purchase_order_items')) {
            Schema::create('store_purchase_order_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_purchase_order_id');
                $table->unsignedBigInteger('product_id');
                $table->integer('quantity');
                $table->integer('dispatched_quantity')->nullable();
                $table->integer('received_quantity')->nullable();
                $table->decimal('unit_cost', 10, 2)->default(0);
                $table->decimal('total_cost', 10, 2)->default(0);
                $table->timestamps();
            });
        }

        $this->store = StoreDetail::create([
            'store_name' => 'SWF - Bissonnet Store',
            'code' => 'BIS',
            'status' => 'active'
        ]);

        $this->user = StoreUser::create([
            'store_id' => $this->store->id,
            'name' => 'Store Tester',
            'email' => 'storetester@test.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_distinctive_store_order_id_generation()
    {
        $poNumber = StorePurchaseOrder::generatePONumber($this->store->id);
        $this->assertStringStartsWith('REQ-BISS-' . date('Y'), $poNumber);
        $this->assertStringEndsWith('-01', $poNumber);

        // Test Garland store prefix
        $garlandStore = StoreDetail::create(['store_name' => 'SWF - Garland Store']);
        $garlandPO = StorePurchaseOrder::generatePONumber($garlandStore->id);
        $this->assertStringStartsWith('REQ-GARL-' . date('Y'), $garlandPO);

        // Test Highway 6 store prefix
        $hwy6Store = StoreDetail::create(['store_name' => 'SWF - HWY6 Store']);
        $hwy6PO = StorePurchaseOrder::generatePONumber($hwy6Store->id);
        $this->assertStringStartsWith('REQ-HWY6-' . date('Y'), $hwy6PO);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_receiving_requires_driver_phone_and_staff_clocking_id()
    {
        $product = Product::create([
            'product_name' => 'Test Item',
            'upc' => '123456789',
            'price' => 10.00,
            'store_id' => $this->store->id,
        ]);

        $po = StorePurchaseOrder::create([
            'po_number' => 'REQ-BISS-2026-01',
            'store_id' => $this->store->id,
            'status' => 'in_transit',
            'total_items' => 1,
            'total_amount' => 100.00,
            'requested_by' => $this->user->id,
        ]);

        $item = StorePurchaseOrderItem::create([
            'store_purchase_order_id' => $po->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'dispatched_quantity' => 10,
            'unit_cost' => 10.00,
            'total_cost' => 100.00,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('store.orders.confirm-receive', $po->id), [
                'driver_phone' => '+15550199',
                'driver_code' => '998877',
                'staff_name' => 'John Receiver',
                'clocking_id' => 'EMP-104',
                'items' => [
                    [
                        'id' => $item->id,
                        'received_quantity' => 10,
                    ]
                ],
                'remarks' => 'Order received in great condition.'
            ]);

        $response->assertRedirect(route('store.orders.index'));
        $po->refresh();
        $this->assertEquals('completed', $po->status);
        $this->assertStringContainsString('Received By: John Receiver (Clocking ID: EMP-104)', $po->store_remarks);
        $this->assertStringContainsString('Driver Phone: +15550199', $po->store_remarks);
    }
}
