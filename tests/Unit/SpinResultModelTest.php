<?php

namespace Tests\Unit;

use App\Models\SpinAndWin;
use App\Models\SpinResult;
use App\Models\SpinWheelSegment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpinResultModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_result(): void
    {
        $result = SpinResult::factory()->create();

        $this->assertDatabaseHas('spin_results', ['id' => $result->id]);
    }

    public function test_belongs_to_spin_and_win(): void
    {
        $game = SpinAndWin::factory()->create();
        $result = SpinResult::factory()->create(['spin_and_win_id' => $game->id]);

        $this->assertTrue($result->spinAndWin->is($game));
    }

    public function test_belongs_to_segment(): void
    {
        $segment = SpinWheelSegment::factory()->create();
        $result = SpinResult::factory()->create(['spin_wheel_segment_id' => $segment->id]);

        $this->assertTrue($result->segment->is($segment));
    }

    public function test_claimed_scope(): void
    {
        SpinResult::factory()->count(2)->claimed()->create();
        SpinResult::factory()->create(['is_claimed' => false]);

        $this->assertCount(2, SpinResult::claimed()->get());
    }

    public function test_unclaimed_scope(): void
    {
        SpinResult::factory()->count(3)->create(['is_claimed' => false]);
        SpinResult::factory()->create(['is_claimed' => true]);

        $this->assertCount(3, SpinResult::unclaimed()->get());
    }

    public function test_result_casts(): void
    {
        $result = SpinResult::factory()->create([
            'prize_value' => 50.00,
            'is_claimed' => 1,
            'claimed_at' => now(),
        ]);

        $this->assertEquals(50, $result->prize_value);
        $this->assertTrue($result->is_claimed);
        $this->assertInstanceOf(\Carbon\Carbon::class, $result->claimed_at);
    }
}
