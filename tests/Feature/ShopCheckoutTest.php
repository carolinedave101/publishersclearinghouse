<?php

namespace Tests\Feature;

use App\Models\PaymentMethod;
use App\Models\ShopProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShopCheckoutTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private ShopProduct $product;
    private PaymentMethod $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();

        $this->product = ShopProduct::factory()->create([
            'name' => 'Test Product',
            'price' => 29.99,
            'is_active' => true,
        ]);

        $this->paymentMethod = PaymentMethod::factory()->create([
            'name' => 'Credit Card',
            'slug' => 'credit-card',
            'type' => 'card',
            'purpose' => 'deposit,withdrawal,shop',
            'is_active' => true,
        ]);
    }

    public function test_shop_page_returns_200(): void
    {
        $response = $this->get('/shop');

        $response->assertStatus(200);
        $response->assertSee('Test Product');
        $response->assertSee('Credit Card');
    }

    public function test_place_order_with_valid_items(): void
    {
        $cartItems = json_encode([
            [
                'product_id' => $this->product->id,
                'name' => $this->product->name,
                'price' => (float) $this->product->price,
                'quantity' => 2,
            ],
        ]);

        $response = $this->postJson('/shop/order', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'zip' => '10001',
            'items' => $cartItems,
            'payment_method' => 'credit-card',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('shop_orders', [
            'customer_email' => 'john@example.com',
            'total' => 59.98,
            'status' => 'pending',
            'payment_method' => 'credit-card',
        ]);
    }

    public function test_place_order_calculates_total_server_side(): void
    {
        $cartItems = json_encode([
            [
                'product_id' => $this->product->id,
                'name' => $this->product->name,
                'price' => 1.00,
                'quantity' => 3,
            ],
        ]);

        $response = $this->postJson('/shop/order', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'address' => '456 Oak Ave',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'zip' => '90001',
            'items' => $cartItems,
            'payment_method' => 'credit-card',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('shop_orders', [
            'customer_email' => 'jane@example.com',
            'total' => 89.97,
        ]);
    }

    public function test_place_order_rejects_empty_cart(): void
    {
        $response = $this->postJson('/shop/order', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'zip' => '10001',
            'items' => json_encode([]),
            'payment_method' => 'credit-card',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_place_order_rejects_invalid_product(): void
    {
        $cartItems = json_encode([
            [
                'product_id' => 9999,
                'name' => 'Nonexistent',
                'price' => 10.00,
                'quantity' => 1,
            ],
        ]);

        $response = $this->postJson('/shop/order', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'zip' => '10001',
            'items' => $cartItems,
            'payment_method' => 'credit-card',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_place_order_rejects_inactive_product(): void
    {
        $inactiveProduct = ShopProduct::factory()->create([
            'name' => 'Inactive Item',
            'price' => 15.00,
            'is_active' => false,
        ]);

        $cartItems = json_encode([
            [
                'product_id' => $inactiveProduct->id,
                'name' => $inactiveProduct->name,
                'price' => (float) $inactiveProduct->price,
                'quantity' => 1,
            ],
        ]);

        $response = $this->postJson('/shop/order', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'zip' => '10001',
            'items' => $cartItems,
            'payment_method' => 'credit-card',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_shop_page_lists_active_payment_methods(): void
    {
        PaymentMethod::factory()->inactive()->create(['name' => 'Old Method', 'slug' => 'old']);

        $response = $this->get('/shop');

        $response->assertStatus(200);
        $response->assertSee('Credit Card');
        $response->assertDontSee('Old Method');
    }

    public function test_place_order_requires_payment_method(): void
    {
        $cartItems = json_encode([
            [
                'product_id' => $this->product->id,
                'name' => $this->product->name,
                'price' => (float) $this->product->price,
                'quantity' => 1,
            ],
        ]);

        $response = $this->postJson('/shop/order', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'zip' => '10001',
            'items' => $cartItems,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['payment_method']);
    }
}
