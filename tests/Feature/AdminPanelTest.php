<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_login_page_is_accessible(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('email');
        $response->assertSee('password');
    }

    public function test_admin_dashboard_requires_auth(): void
    {
        $response = $this->get('/admin');

        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_winners_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/winners');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_messages_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/messages');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_documents_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/documents');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_users_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/users');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_activity_logs_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/activity-logs');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_spin_games_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/spin-and-wins');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_spin_results_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/spin-results');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_giveaways_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/giveaways');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_shop_products_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/shop-products');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_shop_orders_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/shop-orders');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_membership_tiers_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/membership-tiers');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_membership_subscriptions_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/membership-subscriptions');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_pages_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/pages');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_settings_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/settings');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_user_messages_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/user-messages');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_shop_order_view_page(): void
    {
        $order = \App\Models\ShopOrder::factory()->create();
        $response = $this->actingAs($this->admin)->get("/admin/shop-orders/{$order->id}");

        $response->assertStatus(200);
    }

    public function test_admin_can_access_mail_settings_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/mail-settings');

        $response->assertStatus(200);
    }

    public function test_admin_dashboard_renders_with_widgets(): void
    {
        $response = $this->followingRedirects()
            ->actingAs($this->admin)
            ->get('/admin');

        // The dashboard (with the Stats / chart / activity widgets) must
        // render without error. Widgets hydrate client-side via
        // Livewire, so we assert the admin shell loads (200) and
        // the dashboard nav is present rather than brittle widget text.
        $response->assertStatus(200);
        $response->assertSee('PCH Winners Portal');
    }

    public function test_admin_can_access_winner_edit_with_relation_managers(): void
    {
        $winner = Winner::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get("/admin/winners/{$winner->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('Messages');
        $response->assertSee('Documents');
    }

    public function test_non_admin_cannot_access_admin(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get('/admin/winners');

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_admin(): void
    {
        $response = $this->get('/admin/winners');

        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');
    }
}
