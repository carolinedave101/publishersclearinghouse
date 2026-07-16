<?php

namespace Database\Factories;

use App\Models\ShopOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShopOrderFactory extends Factory
{
    protected $model = ShopOrder::class;

    public function definition(): array
    {
        return [
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'zip' => fake()->postcode(),
            'items' => [['id' => 1, 'name' => 'Test Product', 'quantity' => 1, 'price' => 29.99]],
            'total' => fake()->randomFloat(2, 10, 500),
            'status' => fake()->randomElement(['pending', 'processing', 'shipped', 'delivered']),
        ];
    }
}
