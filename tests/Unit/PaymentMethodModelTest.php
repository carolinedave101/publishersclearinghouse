<?php

namespace Tests\Unit;

use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_payment_method(): void
    {
        $method = PaymentMethod::factory()->create([
            'name' => 'PayPal',
            'slug' => 'paypal',
            'type' => 'paypal',
        ]);

        $this->assertModelExists($method);
        $this->assertEquals('PayPal', $method->name);
        $this->assertEquals('paypal', $method->slug);
        $this->assertEquals('paypal', $method->type);
    }

    public function test_config_is_cast_to_array(): void
    {
        $method = PaymentMethod::factory()->create([
            'config' => ['client_id' => 'abc123', 'mode' => 'sandbox'],
        ]);

        $this->assertIsArray($method->config);
        $this->assertEquals('abc123', $method->config['client_id']);
    }

    public function test_is_active_is_cast_to_boolean(): void
    {
        $method = PaymentMethod::factory()->create(['is_active' => true]);

        $this->assertTrue($method->is_active);
        $this->assertIsBool($method->is_active);
    }

    public function test_active_scope(): void
    {
        PaymentMethod::factory()->create(['name' => 'Active Method', 'is_active' => true]);
        PaymentMethod::factory()->inactive()->create(['name' => 'Inactive Method']);

        $active = PaymentMethod::active()->get();

        $this->assertCount(1, $active);
        $this->assertEquals('Active Method', $active->first()->name);
    }

    public function test_sort_order_defaults_to_zero(): void
    {
        $method = PaymentMethod::factory()->create(['sort_order' => 0]);

        $this->assertEquals(0, $method->sort_order);
    }

    public function test_fillable_attributes(): void
    {
        $method = PaymentMethod::factory()->create([
            'name' => 'Bank Transfer',
            'slug' => 'bank-transfer',
            'type' => 'bank',
            'description' => 'Pay via bank transfer',
            'instructions' => 'Send to account 123456',
            'config' => ['iban' => 'IBAN123'],
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $this->assertEquals('Bank Transfer', $method->name);
        $this->assertEquals('bank-transfer', $method->slug);
        $this->assertEquals('bank', $method->type);
        $this->assertEquals('Pay via bank transfer', $method->description);
        $this->assertEquals('Send to account 123456', $method->instructions);
        $this->assertEquals(['iban' => 'IBAN123'], $method->config);
        $this->assertTrue($method->is_active);
        $this->assertEquals(2, $method->sort_order);
    }

    public function test_payment_method_can_be_deleted(): void
    {
        $method = PaymentMethod::factory()->create();
        $method->delete();

        $this->assertModelMissing($method);
    }

    public function test_payment_method_can_be_updated(): void
    {
        $method = PaymentMethod::factory()->create(['name' => 'Old Name']);
        $method->update(['name' => 'Updated Name']);

        $this->assertEquals('Updated Name', $method->fresh()->name);
    }

    public function test_purpose_defaults_to_deposit_and_withdrawal(): void
    {
        $method = PaymentMethod::factory()->create();

        $this->assertStringContainsString('deposit', $method->purpose);
        $this->assertStringContainsString('withdrawal', $method->purpose);
    }

    public function test_purposes_accessor_returns_array(): void
    {
        $method = PaymentMethod::factory()->create(['purpose' => 'deposit,shop']);

        $this->assertEquals(['deposit', 'shop'], $method->purposes);
    }

    public function test_setting_purposes_stores_comma_separated(): void
    {
        $method = PaymentMethod::factory()->create();
        $method->purposes = ['deposit', 'withdrawal', 'shop'];
        $method->save();

        $this->assertEquals('deposit,withdrawal,shop', $method->fresh()->purpose);
    }

    public function test_has_purpose_returns_correctly(): void
    {
        $method = PaymentMethod::factory()->create(['purpose' => 'deposit,shop']);

        $this->assertTrue($method->hasPurpose('deposit'));
        $this->assertTrue($method->hasPurpose('shop'));
        $this->assertFalse($method->hasPurpose('withdrawal'));
    }

    public function test_for_deposits_scope(): void
    {
        PaymentMethod::factory()->create(['purpose' => 'deposit', 'name' => 'Deposit Only']);
        PaymentMethod::factory()->create(['purpose' => 'deposit,withdrawal', 'name' => 'Both']);
        PaymentMethod::factory()->create(['purpose' => 'withdrawal', 'name' => 'Withdrawal Only']);
        PaymentMethod::factory()->create(['purpose' => 'shop', 'name' => 'Shop Only']);

        $depositMethods = PaymentMethod::forDeposits()->get();

        $this->assertCount(2, $depositMethods);
        $this->assertTrue($depositMethods->pluck('name')->contains('Deposit Only'));
        $this->assertTrue($depositMethods->pluck('name')->contains('Both'));
    }

    public function test_for_withdrawals_scope(): void
    {
        PaymentMethod::factory()->create(['purpose' => 'withdrawal', 'name' => 'Withdrawal Only']);
        PaymentMethod::factory()->create(['purpose' => 'deposit,withdrawal', 'name' => 'Both']);
        PaymentMethod::factory()->create(['purpose' => 'deposit', 'name' => 'Deposit Only']);
        PaymentMethod::factory()->create(['purpose' => 'shop', 'name' => 'Shop Only']);

        $withdrawMethods = PaymentMethod::forWithdrawals()->get();

        $this->assertCount(2, $withdrawMethods);
        $this->assertTrue($withdrawMethods->pluck('name')->contains('Withdrawal Only'));
        $this->assertTrue($withdrawMethods->pluck('name')->contains('Both'));
    }

    public function test_for_shop_scope(): void
    {
        PaymentMethod::factory()->create(['purpose' => 'shop', 'name' => 'Shop Only']);
        PaymentMethod::factory()->create(['purpose' => 'deposit,withdrawal,shop', 'name' => 'All']);
        PaymentMethod::factory()->create(['purpose' => 'deposit', 'name' => 'Deposit Only']);

        $shopMethods = PaymentMethod::forShop()->get();

        $this->assertCount(2, $shopMethods);
        $this->assertTrue($shopMethods->pluck('name')->contains('Shop Only'));
        $this->assertTrue($shopMethods->pluck('name')->contains('All'));
    }

    public function test_get_purpose_options_returns_individual_options(): void
    {
        $options = PaymentMethod::getPurposeOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('deposit', $options);
        $this->assertArrayHasKey('withdrawal', $options);
        $this->assertArrayHasKey('shop', $options);
        $this->assertArrayNotHasKey('both', $options);
    }

    public function test_purpose_scopes_respect_is_active(): void
    {
        PaymentMethod::factory()->create(['purpose' => 'deposit', 'is_active' => true, 'name' => 'Active Deposit']);
        PaymentMethod::factory()->create(['purpose' => 'deposit', 'is_active' => false, 'name' => 'Inactive Deposit']);

        $activeDepositMethods = PaymentMethod::active()->forDeposits()->get();

        $this->assertCount(1, $activeDepositMethods);
        $this->assertEquals('Active Deposit', $activeDepositMethods->first()->name);
    }
}
