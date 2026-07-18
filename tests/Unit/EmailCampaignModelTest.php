<?php

namespace Tests\Unit;

use App\Models\EmailCampaign;
use App\Models\EmailCampaignRecipient;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailCampaignModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_campaign(): void
    {
        $campaign = EmailCampaign::create([
            'name' => 'Test Campaign',
            'subject' => 'Hello {{name}}',
            'body_variant_1' => '<p>Dear {name},</p><p>You have won!</p>',
            'recipient_filter' => ['is_demo' => 'exclude'],
            'rate_per_hour' => 50,
            'rate_per_day' => 1000,
        ]);

        $this->assertDatabaseHas('email_campaigns', ['name' => 'Test Campaign', 'status' => 'draft']);
        $this->assertEquals(50, $campaign->rate_per_hour);
    }

    public function test_recipients_relation(): void
    {
        $campaign = EmailCampaign::factory()->create();
        $winner = Winner::factory()->create();

        $recipient = $campaign->recipients()->create([
            'winner_id' => $winner->id,
            'email' => $winner->email,
            'first_name' => $winner->first_name,
            'body_variant_used' => 1,
            'status' => 'pending',
        ]);

        $this->assertCount(1, $campaign->recipients);
        $this->assertTrue($campaign->recipients->contains($recipient));
    }

    public function test_sent_recipients_scope(): void
    {
        $campaign = EmailCampaign::factory()->create();
        $winner = Winner::factory()->create();

        $campaign->recipients()->create(['winner_id' => $winner->id, 'email' => $winner->email, 'first_name' => $winner->first_name, 'status' => 'sent', 'sent_at' => now()]);
        $campaign->recipients()->create(['winner_id' => $winner->id, 'email' => $winner->email, 'first_name' => $winner->first_name, 'status' => 'pending']);
        $campaign->recipients()->create(['winner_id' => $winner->id, 'email' => $winner->email, 'first_name' => $winner->first_name, 'status' => 'failed']);

        $this->assertEquals(1, $campaign->sentRecipients()->count());
        $this->assertEquals(1, $campaign->failedRecipients()->count());
        $this->assertEquals(1, $campaign->pendingRecipients()->count());
    }

    public function test_progress_percent(): void
    {
        $campaign = EmailCampaign::factory()->create(['total_recipients' => 100]);
        $campaign->sent_count = 30;
        $campaign->failed_count = 20;
        $campaign->save();

        $this->assertEquals(50.0, $campaign->progress_percent);
    }

    public function test_remaining_count(): void
    {
        $campaign = EmailCampaign::factory()->create(['total_recipients' => 100]);
        $campaign->sent_count = 30;
        $campaign->failed_count = 10;
        $campaign->save();

        $this->assertEquals(60, $campaign->remaining_count);
    }

    public function test_remaining_count_never_negative(): void
    {
        $campaign = EmailCampaign::factory()->create(['total_recipients' => 10]);
        $campaign->sent_count = 100;
        $campaign->failed_count = 0;
        $campaign->save();

        $this->assertEquals(0, $campaign->remaining_count);
    }

    public function test_estimated_hours(): void
    {
        $campaign = EmailCampaign::factory()->create([
            'total_recipients' => 200,
            'sent_count' => 0,
            'failed_count' => 0,
            'rate_per_hour' => 50,
        ]);

        $this->assertEquals(4, $campaign->estimated_hours);
    }

    public function test_estimated_hours_null_when_no_rate(): void
    {
        $campaign = EmailCampaign::factory()->create([
            'total_recipients' => 100,
            'sent_count' => 0,
            'rate_per_hour' => 0,
        ]);

        $this->assertNull($campaign->estimated_hours);
    }

    public function test_can_send_more_checks_rate_limits(): void
    {
        $campaign = EmailCampaign::factory()->create([
            'rate_per_hour' => 50,
            'rate_per_day' => 1000,
        ]);

        $this->assertTrue($campaign->canSendMore());
    }

    public function test_body_variants_count(): void
    {
        $campaign1 = EmailCampaign::factory()->create(['body_variant_1' => 'V1']);
        $this->assertEquals(1, $campaign1->body_variants_count);

        $campaign2 = EmailCampaign::factory()->create(['body_variant_1' => 'V1', 'body_variant_2' => 'V2']);
        $this->assertEquals(2, $campaign2->body_variants_count);

        $campaign3 = EmailCampaign::factory()->create(['body_variant_1' => 'V1', 'body_variant_2' => 'V2', 'body_variant_3' => 'V3']);
        $this->assertEquals(3, $campaign3->body_variants_count);
    }

    public function test_get_body_variant_falls_back_to_variant_1(): void
    {
        $campaign = EmailCampaign::factory()->create([
            'body_variant_1' => 'Primary body',
            'body_variant_2' => null,
            'body_variant_3' => null,
        ]);

        $this->assertEquals('Primary body', $campaign->getBodyVariant(2));
        $this->assertEquals('Primary body', $campaign->getBodyVariant(3));
        $this->assertEquals('Primary body', $campaign->getBodyVariant(1));
    }

    public function test_get_body_variant_returns_correct_variant(): void
    {
        $campaign = EmailCampaign::factory()->create([
            'body_variant_1' => 'V1 body',
            'body_variant_2' => 'V2 body',
            'body_variant_3' => 'V3 body',
        ]);

        $this->assertEquals('V1 body', $campaign->getBodyVariant(1));
        $this->assertEquals('V2 body', $campaign->getBodyVariant(2));
        $this->assertEquals('V3 body', $campaign->getBodyVariant(3));
    }

    public function test_recipient_filter_is_cast_to_array(): void
    {
        $campaign = EmailCampaign::factory()->create([
            'recipient_filter' => ['statuses' => ['approved'], 'is_demo' => 'exclude'],
        ]);

        $this->assertIsArray($campaign->recipient_filter);
        $this->assertEquals(['approved'], $campaign->recipient_filter['statuses']);
    }

    public function test_scheduled_at_is_cast_to_datetime(): void
    {
        $campaign = EmailCampaign::factory()->create([
            'scheduled_at' => '2026-07-20 10:00:00',
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $campaign->scheduled_at);
    }
}
