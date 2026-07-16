<?php

namespace Tests\Unit;

use App\Models\PaymentMethod;
use App\Models\Withdrawal;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WithdrawalModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_withdrawal(): void
    {
        $withdrawal = Withdrawal::factory()->create();

        $this->assertDatabaseHas('withdrawals', ['id' => $withdrawal->id]);
    }

    public function test_withdrawal_belongs_to_winner(): void
    {
        $winner = Winner::factory()->create();
        $withdrawal = Withdrawal::factory()->create(['winner_id' => $winner->id]);

        $this->assertInstanceOf(Winner::class, $withdrawal->winner);
        $this->assertEquals($winner->id, $withdrawal->winner->id);
    }

    public function test_withdrawal_belongs_to_payment_method(): void
    {
        $method = PaymentMethod::factory()->create();
        $withdrawal = Withdrawal::factory()->create(['payment_method_id' => $method->id]);

        $this->assertInstanceOf(PaymentMethod::class, $withdrawal->paymentMethod);
        $this->assertEquals($method->id, $withdrawal->paymentMethod->id);
    }

    public function test_account_details_is_cast_to_array(): void
    {
        $details = ['bank_name' => 'Chase', 'account_number' => '123456', 'routing' => '021000021'];
        $withdrawal = Withdrawal::factory()->create(['account_details' => $details]);

        $this->assertIsArray($withdrawal->account_details);
        $this->assertEquals('Chase', $withdrawal->account_details['bank_name']);
    }

    public function test_pending_scope(): void
    {
        Withdrawal::factory()->count(3)->create(['status' => 'pending']);
        Withdrawal::factory()->create(['status' => 'approved']);

        $this->assertCount(3, Withdrawal::pending()->get());
    }

    public function test_approved_scope(): void
    {
        Withdrawal::factory()->count(2)->create(['status' => 'approved']);
        Withdrawal::factory()->create(['status' => 'pending']);

        $this->assertCount(2, Withdrawal::approved()->get());
    }

    public function test_completed_scope(): void
    {
        Withdrawal::factory()->count(2)->create(['status' => 'completed']);
        Withdrawal::factory()->create(['status' => 'pending']);

        $this->assertCount(2, Withdrawal::completed()->get());
    }

    public function test_rejected_scope(): void
    {
        Withdrawal::factory()->count(2)->create(['status' => 'rejected']);
        Withdrawal::factory()->create(['status' => 'pending']);

        $this->assertCount(2, Withdrawal::rejected()->get());
    }

    public function test_amount_is_cast_to_decimal(): void
    {
        $withdrawal = Withdrawal::factory()->create(['amount' => 2500.75]);

        $this->assertEquals(2500.75, $withdrawal->amount);
        $this->assertIsNumeric($withdrawal->amount);
    }

    public function test_withdrawal_status_timestamps(): void
    {
        $approved = Withdrawal::factory()->approved()->create();
        $completed = Withdrawal::factory()->completed()->create();
        $rejected = Withdrawal::factory()->rejected()->create();

        $this->assertNotNull($approved->approved_at);
        $this->assertNotNull($completed->approved_at);
        $this->assertNotNull($completed->completed_at);
        $this->assertNotNull($rejected->rejected_at);
    }

    public function test_withdrawal_deletes_with_winner(): void
    {
        $winner = Winner::factory()->create();
        $withdrawal = Withdrawal::factory()->create(['winner_id' => $winner->id]);
        $withdrawalId = $withdrawal->id;

        $winner->delete();

        $this->assertDatabaseMissing('withdrawals', ['id' => $withdrawalId]);
    }
}
