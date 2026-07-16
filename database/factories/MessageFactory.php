<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\Winner;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'winner_id' => Winner::factory(),
            'subject' => fake()->sentence(),
            'content' => fake()->paragraph(),
            'sent_by' => 'Admin',
            'sent_by_admin' => true,
            'read' => false,
        ];
    }
}
