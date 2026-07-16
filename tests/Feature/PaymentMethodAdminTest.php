<?php

namespace Tests\Feature;

use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_payment_methods_page(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get('/admin/payment-methods');

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_payment_methods_page(): void
    {
        $response = $this->get('/admin/payment-methods');

        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_payment_methods_page(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/payment-methods');

        $response->assertStatus(200);
    }

    public function test_admin_can_view_payment_methods_list(): void
    {
        $admin = User::factory()->admin()->create();
        PaymentMethod::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get('/admin/payment-methods');

        $response->assertStatus(200);
        $response->assertSee('Payment Methods');
    }
}
