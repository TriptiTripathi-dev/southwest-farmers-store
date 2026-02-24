<?php

namespace Database\Factories;

use App\Models\StockRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockRequestFactory extends Factory
{
    protected $model = StockRequest::class;

    public function definition(): array
    {
        return [
            'store_id' => 1,
            'request_number' => 'REQ-' . $this->faker->unique()->numberBetween(1000, 9999),
            'status' => 'pending',
            'total_items' => 0,
            'total_amount' => 0,
            'requested_by' => 1,
        ];
    }
}
