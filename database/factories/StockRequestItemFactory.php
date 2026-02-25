<?php

namespace Database\Factories;

use App\Models\StockRequestItem;
use App\Models\StockRequest;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockRequestItemFactory extends Factory
{
    protected $model = StockRequestItem::class;

    public function definition(): array
    {
        return [
            'stock_request_id' => StockRequest::factory(),
            'product_id' => Product::factory(),
            'quantity' => $this->faker->numberBetween(1, 100),
            'dispatched_quantity' => 0,
            'received_quantity' => 0,
            'unit_cost' => $this->faker->randomFloat(2, 5, 200),
            'total_cost' => 0,
        ];
    }
}
