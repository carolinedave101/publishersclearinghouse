<?php

namespace Tests\Unit;

use App\Models\MembershipSubscription;
use App\Models\MembershipTier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipTierModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_tier(): void
    {
        $tier = MembershipTier::factory()->create();

        $this->assertDatabaseHas('membership_tiers', ['id' => $tier->id]);
    }

    public function test_tier_has_subscriptions_relationship(): void
    {
        $tier = MembershipTier::factory()->create();
        MembershipSubscription::factory()->count(2)->create(['membership_tier_id' => $tier->id]);

        $this->assertCount(2, $tier->subscriptions);
    }

    public function test_active_scope(): void
    {
        MembershipTier::factory()->create(['is_active' => true]);
        MembershipTier::factory()->create(['is_active' => false]);

        $this->assertCount(1, MembershipTier::active()->get());
    }

    public function test_features_are_cast_to_array(): void
    {
        $features = ['Feature A', 'Feature B', 'Feature C'];
        $tier = MembershipTier::factory()->create(['features' => $features]);

        $this->assertIsArray($tier->features);
        $this->assertEquals($features, $tier->features);
    }
}
