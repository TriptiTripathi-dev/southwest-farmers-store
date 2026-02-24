<?php

namespace Database\Factories;

use App\Models\StoreUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class StoreUserFactory extends Factory
{
    protected $model = StoreUser::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'store_id' => 1,
            'is_active' => true,
        ];
    }
}
