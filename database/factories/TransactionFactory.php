<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\Winner;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'winner_id' => Winner::factory(),
            'type' => $this->faker->randomElement(['deposit', 'withdrawal']),
            'amount' => $this->faker->randomFloat(2, 100, 50000),
            'fee' => 0,
            'net_amount' => fn (array $attrs) => $attrs['type'] === 'withdrawal' ? -$attrs['amount'] : $attrs['amount'],
            'payment_method' => $this->faker->randomElement(['Bank Transfer', 'PayPal', 'Credit Card']),
            'reference_type' => null,
            'reference_id' => null,
            'status' => 'completed',
            'description' => $this->faker->sentence(),
        ];
    }

    public function deposit(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'deposit',
            'net_amount' => $attributes['amount'] ?? 0,
        ]);
    }

    public function withdrawal(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'withdrawal',
            'net_amount' => -($attributes['amount'] ?? 0),
        ]);
    }
}
