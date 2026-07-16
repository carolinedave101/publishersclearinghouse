<?php

namespace Database\Factories;

use App\Models\PaymentMethod;
use App\Models\Withdrawal;
use App\Models\Winner;
use Illuminate\Database\Eloquent\Factories\Factory;

class WithdrawalFactory extends Factory
{
    protected $model = Withdrawal::class;

    public function definition(): array
    {
        return [
            'winner_id' => Winner::factory(),
            'payment_method_id' => PaymentMethod::factory(),
            'amount' => $this->faker->randomFloat(2, 50, 10000),
            'fee' => 0,
            'net_amount' => fn (array $attrs) => $attrs['amount'],
            'account_details' => ['bank_name' => 'Test Bank', 'account_number' => '1234567890'],
            'notes' => $this->faker->optional()->sentence(),
            'status' => 'pending',
            'admin_notes' => null,
            'approved_at' => null,
            'completed_at' => null,
            'rejected_at' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'approved_at' => now(),
            'completed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);
    }
}
