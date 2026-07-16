<?php

namespace Tests\Unit;

use App\Models\Giveaway;
use App\Models\GiveawayEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GiveawayModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_giveaway(): void
    {
        $giveaway = Giveaway::factory()->create();

        $this->assertDatabaseHas('giveaways', ['id' => $giveaway->id]);
    }

    public function test_giveaway_has_entries_relationship(): void
    {
        $giveaway = Giveaway::factory()->create();
        GiveawayEntry::factory()->count(3)->create(['giveaway_id' => $giveaway->id]);

        $this->assertCount(3, $giveaway->entries()->get());
    }

    public function test_entry_count_attribute(): void
    {
        $giveaway = Giveaway::factory()->create();
        GiveawayEntry::factory()->count(5)->create(['giveaway_id' => $giveaway->id]);

        $this->assertEquals(5, $giveaway->entry_count);
    }

    public function test_active_scope(): void
    {
        Giveaway::factory()->create(['status' => 'active']);
        Giveaway::factory()->create(['status' => 'upcoming']);
        Giveaway::factory()->create(['status' => 'ended']);

        $this->assertCount(1, Giveaway::active()->get());
    }

    public function test_giveaway_casts(): void
    {
        $giveaway = Giveaway::factory()->create([
            'prize_value' => 50000.00,
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
        ]);

        $this->assertEquals(50000.00, $giveaway->prize_value);
        $this->assertInstanceOf(\Carbon\Carbon::class, $giveaway->starts_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $giveaway->ends_at);
    }
}
