<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserMessageFactory extends Factory
{
    protected $model = UserMessage::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'admin_id' => User::factory(),
            'subject' => fake()->sentence(),
            'message' => fake()->paragraph(),
            'direction' => fake()->randomElement(['admin_to_user', 'user_to_admin']),
            'is_read' => false,
            'read_at' => null,
        ];
    }

    public function fromAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'direction' => 'admin_to_user',
        ]);
    }

    public function fromUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'direction' => 'user_to_admin',
            'admin_id' => null,
        ]);
    }
}
