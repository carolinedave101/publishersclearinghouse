<?php

namespace Database\Factories;

use App\Models\Winner;
use App\Services\CodeGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

class WinnerFactory extends Factory
{
    protected $model = Winner::class;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'zip' => fake()->postcode(),
            'prize_amount' => fake()->randomFloat(2, 500, 100000),
            'prize_description' => fake()->sentence(),
            'email' => fake()->safeEmail(),
            'unique_code' => app(CodeGenerator::class)->generateUniqueCode(),
            'is_claimed' => false,
            'claimed_at' => null,
            'is_active' => true,
            'status' => 'new',
            'next_steps' => null,
            'admin_notes' => null,
        ];
    }

    public function claimed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_claimed' => true,
            'claimed_at' => now(),
            'status' => 'approved',
        ]);
    }
}
