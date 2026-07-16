<?php

namespace Tests\Unit;

use App\Models\Deposit;
use App\Models\PaymentMethod;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepositModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_deposit(): void
    {
        $deposit = Deposit::factory()->create();

        $this->assertDatabaseHas('deposits', ['id' => $deposit->id]);
    }

    public function test_deposit_belongs_to_winner(): void
    {
        $winner = Winner::factory()->create();
        $deposit = Deposit::factory()->create(['winner_id' => $winner->id]);

        $this->assertInstanceOf(Winner::class, $deposit->winner);
        $this->assertEquals($winner->id, $deposit->winner->id);
    }

    public function test_deposit_belongs_to_payment_method(): void
    {
        $method = PaymentMethod::factory()->create();
        $deposit = Deposit::factory()->create(['payment_method_id' => $method->id]);

        $this->assertInstanceOf(PaymentMethod::class, $deposit->paymentMethod);
        $this->assertEquals($method->id, $deposit->paymentMethod->id);
    }

    public function test_pending_scope(): void
    {
        Deposit::factory()->count(3)->create(['status' => 'pending']);
        Deposit::factory()->create(['status' => 'approved']);

        $this->assertCount(3, Deposit::pending()->get());
    }

    public function test_approved_scope(): void
    {
        Deposit::factory()->count(2)->create(['status' => 'approved']);
        Deposit::factory()->create(['status' => 'pending']);

        $this->assertCount(2, Deposit::approved()->get());
    }

    public function test_rejected_scope(): void
    {
        Deposit::factory()->count(2)->create(['status' => 'rejected']);
        Deposit::factory()->create(['status' => 'pending']);

        $this->assertCount(2, Deposit::rejected()->get());
    }

    public function test_amount_is_cast_to_decimal(): void
    {
        $deposit = Deposit::factory()->create(['amount' => 1500.50]);

        $this->assertEquals(1500.50, $deposit->amount);
        $this->assertIsNumeric($deposit->amount);
    }

    public function test_deposit_approved_at_cast(): void
    {
        $deposit = Deposit::factory()->approved()->create();

        $this->assertNotNull($deposit->approved_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $deposit->approved_at);
    }

    public function test_deposit_rejected_at_cast(): void
    {
        $deposit = Deposit::factory()->rejected()->create();

        $this->assertNotNull($deposit->rejected_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $deposit->rejected_at);
    }

    public function test_deposit_deletes_with_winner(): void
    {
        $winner = Winner::factory()->create();
        $deposit = Deposit::factory()->create(['winner_id' => $winner->id]);
        $depositId = $deposit->id;

        $winner->delete();

        $this->assertDatabaseMissing('deposits', ['id' => $depositId]);
    }

    public function test_deposit_deletes_with_payment_method(): void
    {
        $method = PaymentMethod::factory()->create();
        $deposit = Deposit::factory()->create(['payment_method_id' => $method->id]);
        $depositId = $deposit->id;

        $method->delete();

        $this->assertDatabaseMissing('deposits', ['id' => $depositId]);
    }
}
