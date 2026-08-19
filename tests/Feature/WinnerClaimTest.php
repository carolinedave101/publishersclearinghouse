<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WinnerClaimTest extends TestCase
{
    use RefreshDatabase;

    public function test_winner_can_claim_prize_and_admins_get_database_notification(): void
    {
        $admin = User::factory()->create(['is_super_admin' => true, 'is_admin' => true]);
        $winner = Winner::factory()->create(['is_claimed' => false]);

        $response = $this->withSession(['winner_id' => $winner->id])
            ->post('/winner/claim');

        $response->assertRedirect(route('winner.dashboard'));

        $winner->refresh();
        $this->assertTrue($winner->is_claimed);
        $this->assertNotNull($winner->claimed_at);
        $this->assertDatabaseCount('notifications', 1);
        $this->assertDatabaseHas('notifications', [
            'type' => 'App\Notifications\WinnerClaimedNotification',
            'notifiable_id' => $admin->id,
        ]);
    }

    public function test_claim_without_winner_session_redirects_with_error(): void
    {
        $this->post('/winner/claim')->assertRedirect(route('home'));
    }
}