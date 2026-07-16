<?php

namespace Tests\Unit;

use App\Models\SpinAndWin;
use App\Models\SpinWheelSegment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpinWheelSegmentModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_segment(): void
    {
        $segment = SpinWheelSegment::factory()->create();

        $this->assertDatabaseHas('spin_wheel_segments', ['id' => $segment->id]);
    }

    public function test_belongs_to_spin_and_win(): void
    {
        $game = SpinAndWin::factory()->create();
        $segment = SpinWheelSegment::factory()->create(['spin_and_win_id' => $game->id]);

        $this->assertTrue($segment->spinAndWin->is($game));
    }

    public function test_active_scope(): void
    {
        SpinWheelSegment::factory()->create(['is_active' => true]);
        SpinWheelSegment::factory()->create(['is_active' => false]);

        $this->assertCount(1, SpinWheelSegment::active()->get());
    }

    public function test_jackpot_scope(): void
    {
        SpinWheelSegment::factory()->create(['is_jackpot' => true]);
        SpinWheelSegment::factory()->create(['is_jackpot' => false]);

        $this->assertCount(1, SpinWheelSegment::jackpots()->get());
    }

    public function test_segment_casts(): void
    {
        $segment = SpinWheelSegment::factory()->create([
            'prize_value' => 100,
            'is_jackpot' => 1,
            'is_active' => 1,
        ]);

        $this->assertEquals(100, $segment->prize_value);
        $this->assertTrue($segment->is_jackpot);
        $this->assertTrue($segment->is_active);
    }
}
