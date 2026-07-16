<?php

namespace Database\Factories;

use App\Models\Deposit;
use App\Models\PaymentMethod;
use App\Models\Winner;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepositFactory extends Factory
{
    protected $model = Deposit::class;

    public function definition(): array
    {
        return [
            'winner_id' => Winner::factory(),
            'payment_method_id' => PaymentMethod::factory(),
            'amount' => $this->faker->randomFloat(2, 100, 50000),
            'fee' => 0,
            'net_amount' => fn (array $attrs) => $attrs['amount'],
            'proof_file' => 'deposits/test/proof.pdf',
            'proof_file_name' => 'proof.pdf',
            'notes' => $this->faker->optional()->sentence(),
            'status' => 'pending',
            'admin_notes' => null,
            'approved_at' => null,
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

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);
    }
}
