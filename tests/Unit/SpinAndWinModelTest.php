<?php

namespace Tests\Unit;

use App\Models\SpinAndWin;
use App\Models\SpinWheelSegment;
use App\Models\SpinResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpinAndWinModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_spin_and_win(): void
    {
        $game = SpinAndWin::factory()->create();

        $this->assertDatabaseHas('spin_and_wins', ['id' => $game->id]);
    }

    public function test_has_segments_relationship(): void
    {
        $game = SpinAndWin::factory()->create();
        SpinWheelSegment::factory()->count(5)->create(['spin_and_win_id' => $game->id]);

        $this->assertCount(5, $game->segments);
    }

    public function test_has_results_relationship(): void
    {
        $game = SpinAndWin::factory()->create();
        $segment = SpinWheelSegment::factory()->create(['spin_and_win_id' => $game->id]);
        SpinResult::factory()->count(3)->create([
            'spin_and_win_id' => $game->id,
            'spin_wheel_segment_id' => $segment->id,
        ]);

        $this->assertCount(3, $game->results);
    }

    public function test_active_segments_only_returns_active(): void
    {
        $game = SpinAndWin::factory()->create();
        SpinWheelSegment::factory()->create(['spin_and_win_id' => $game->id, 'is_active' => true]);
        SpinWheelSegment::factory()->create(['spin_and_win_id' => $game->id, 'is_active' => false]);

        $this->assertCount(1, $game->activeSegments);
    }

    public function test_active_scope(): void
    {
        SpinAndWin::factory()->create(['is_active' => true]);
        SpinAndWin::factory()->create(['is_active' => false]);

        $this->assertCount(1, SpinAndWin::active()->get());
    }

    public function test_casts(): void
    {
        $game = SpinAndWin::factory()->create([
            'is_active' => 1,
            'requires_login' => 1,
            'max_spins_per_day' => 5,
        ]);

        $this->assertTrue($game->is_active);
        $this->assertTrue($game->requires_login);
        $this->assertIsInt($game->max_spins_per_day);
    }
}
