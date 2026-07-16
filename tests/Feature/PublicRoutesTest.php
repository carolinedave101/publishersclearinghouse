<?php

namespace Tests\Feature;

use App\Models\Giveaway;
use App\Models\ShopOrder;
use App\Models\User;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_returns_200(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_home_page_has_required_content(): void
    {
        $response = $this->get('/');

        $response->assertSee('Publishers Clearing House');
        $response->assertSee('Enter your unique winner code');
    }

    public function test_giveaways_page_returns_200(): void
    {
        Giveaway::factory()->create(['status' => 'active']);

        $response = $this->get('/giveaways');

        $response->assertStatus(200);
    }

    public function test_giveaways_page_shows_giveaways(): void
    {
        $giveaway = Giveaway::factory()->create(['status' => 'active']);

        $response = $this->get('/giveaways');

        $response->assertSee($giveaway->title);
    }

    public function test_games_page_returns_200(): void
    {
        $game = \App\Models\SpinAndWin::factory()->create();
        \App\Models\SpinWheelSegment::factory()->count(3)->create(['spin_and_win_id' => $game->id]);

        $response = $this->get('/games');

        $response->assertStatus(200);
        $response->assertSee('Spin & Win');
    }

    public function test_shop_page_returns_200(): void
    {
        $response = $this->get('/shop');

        $response->assertStatus(200);
    }

    public function test_memberships_page_returns_200(): void
    {
        $response = $this->get('/memberships');

        $response->assertStatus(200);
    }

    public function test_recent_winners_json_endpoint(): void
    {
        Winner::factory()->count(3)->claimed()->create();

        $response = $this->getJson('/winners/recent');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function test_stats_endpoint(): void
    {
        Winner::factory()->count(5)->claimed()->create(['prize_amount' => 1000]);

        $response = $this->getJson('/winners/stats');

        $response->assertStatus(200);
        $response->assertJsonStructure(['total_prizes', 'total_winners', 'recent_count']);
    }

    public function test_winner_lookup_with_valid_code(): void
    {
        $winner = Winner::factory()->create(['is_active' => true]);

        $response = $this->post('/winner/lookup', [
            'code' => $winner->unique_code,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/winner/dashboard');
    }

    public function test_winner_lookup_with_invalid_code(): void
    {
        $response = $this->post('/winner/lookup', [
            'code' => 'INVALID-CODE',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error');
    }

    public function test_setup_route_without_token_returns_403(): void
    {
        $response = $this->get('/setup');

        $response->assertStatus(403);
    }

    public function test_dashboard_requires_auth(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_orders_page_requires_auth(): void
    {
        $response = $this->get('/orders');

        $response->assertRedirect('/login');
    }

    public function test_dashboard_displays_user_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Welcome');
    }

    public function test_orders_page_displays_orders(): void
    {
        $user = User::factory()->create();
        ShopOrder::factory()->create([
            'customer_email' => $user->email,
            'status' => 'completed',
            'total' => 99.99,
        ]);

        $response = $this->actingAs($user)->get('/orders');

        $response->assertStatus(200);
        $response->assertSee('Completed');
    }
}
