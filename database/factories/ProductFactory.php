<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'product_name' => $this->faker->words(3, true),
            'sku' => $this->faker->unique()->bothify('SKU-####'),
            'sku' => $this->faker->unique()->ean13(),
            'cost_price' => $this->faker->randomFloat(2, 10, 500),
            'is_active' => true,
        ];
    }
}
