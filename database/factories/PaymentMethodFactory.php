<?php

namespace Database\Factories;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaymentMethodFactory extends Factory
{
    protected $model = PaymentMethod::class;

    public function definition(): array
    {
        $methods = [
            'Credit Card' => ['type' => 'card', 'slug' => 'credit-card'],
            'PayPal' => ['type' => 'paypal', 'slug' => 'paypal'],
            'Bank Transfer' => ['type' => 'bank', 'slug' => 'bank-transfer'],
            'Cash' => ['type' => 'offline', 'slug' => 'cash'],
        ];

        $name = $this->faker->unique()->randomElement(array_keys($methods));
        $meta = $methods[$name];

        return [
            'name' => $name,
            'slug' => $meta['slug'],
            'purpose' => 'deposit,withdrawal',
            'type' => $meta['type'],
            'description' => $this->faker->sentence(),
            'instructions' => $this->faker->optional()->paragraph(),
            'config' => $meta['type'] === 'paypal' ? ['client_id' => 'test', 'client_secret' => 'test'] : null,
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
