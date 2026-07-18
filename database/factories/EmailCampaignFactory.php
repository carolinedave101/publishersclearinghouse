<?php

namespace Database\Factories;

use App\Models\EmailCampaign;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailCampaignFactory extends Factory
{
    protected $model = EmailCampaign::class;

    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'subject' => fake()->sentence(5),
            'body_variant_1' => '<p>Dear {name},</p><p>' . fake()->paragraph() . '</p>',
            'body_variant_2' => null,
            'body_variant_3' => null,
            'recipient_filter' => null,
            'total_recipients' => 0,
            'sent_count' => 0,
            'failed_count' => 0,
            'status' => 'draft',
            'rate_per_hour' => 50,
            'rate_per_day' => 1000,
            'scheduled_at' => null,
            'started_at' => null,
            'completed_at' => null,
        ];
    }
}
