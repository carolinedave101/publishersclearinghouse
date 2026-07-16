<?php

namespace Tests\Unit;

use App\Models\Deposit;
use App\Models\Document;
use App\Models\Message;
use App\Models\Transaction;
use App\Models\Withdrawal;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WinnerModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_winner(): void
    {
        $winner = Winner::factory()->create();

        $this->assertDatabaseHas('winners', ['id' => $winner->id]);
    }

    public function test_winner_has_messages_relationship(): void
    {
        $winner = Winner::factory()->create();
        Message::factory()->count(3)->create(['winner_id' => $winner->id]);

        $this->assertCount(3, $winner->messages);
    }

    public function test_winner_has_documents_relationship(): void
    {
        $winner = Winner::factory()->create();
        Document::factory()->count(2)->create(['winner_id' => $winner->id]);

        $this->assertCount(2, $winner->documents);
    }

    public function test_prize_amount_formatted_accessor(): void
    {
        $winner = Winner::factory()->create(['prize_amount' => 50000]);

        $this->assertEquals('$50,000.00', $winner->prize_amount_formatted);
    }

    public function test_active_scope(): void
    {
        Winner::factory()->create(['is_active' => true]);
        Winner::factory()->create(['is_active' => false]);

        $this->assertCount(1, Winner::active()->get());
    }

    public function test_claimed_scope(): void
    {
        Winner::factory()->create(['is_claimed' => true]);
        Winner::factory()->create(['is_claimed' => false]);

        $this->assertCount(1, Winner::claimed()->get());
    }

    public function test_by_status_scope(): void
    {
        Winner::factory()->create(['status' => 'approved']);
        Winner::factory()->create(['status' => 'new']);
        Winner::factory()->create(['status' => 'new']);

        $this->assertCount(2, Winner::byStatus('new')->get());
    }

    public function test_winner_casts(): void
    {
        $winner = Winner::factory()->create([
            'is_claimed' => true,
            'is_active' => true,
            'prize_amount' => 10000.50,
            'claimed_at' => now(),
        ]);

        $this->assertTrue($winner->is_claimed);
        $this->assertTrue($winner->is_active);
        $this->assertInstanceOf(\Carbon\Carbon::class, $winner->claimed_at);
    }

    public function test_winner_has_deposits_relationship(): void
    {
        $winner = Winner::factory()->create();
        Deposit::factory()->count(2)->create(['winner_id' => $winner->id]);

        $this->assertCount(2, $winner->deposits);
    }

    public function test_winner_has_withdrawals_relationship(): void
    {
        $winner = Winner::factory()->create();
        Withdrawal::factory()->count(3)->create(['winner_id' => $winner->id]);

        $this->assertCount(3, $winner->withdrawals);
    }

    public function test_winner_has_transactions_relationship(): void
    {
        $winner = Winner::factory()->create();
        Transaction::factory()->count(2)->create(['winner_id' => $winner->id]);

        $this->assertCount(2, $winner->transactions);
    }

    public function test_available_balance_with_no_activity(): void
    {
        $winner = Winner::factory()->create(['prize_amount' => 50000]);

        $this->assertEquals(50000, $winner->available_balance);
    }

    public function test_available_balance_with_approved_deposits(): void
    {
        $winner = Winner::factory()->create(['prize_amount' => 50000]);
        Deposit::factory()->approved()->create(['winner_id' => $winner->id, 'amount' => 10000]);

        $this->assertEquals(60000, $winner->available_balance);
    }

    public function test_available_balance_with_completed_withdrawals(): void
    {
        $winner = Winner::factory()->create(['prize_amount' => 50000]);
        Withdrawal::factory()->completed()->create(['winner_id' => $winner->id, 'amount' => 5000]);

        $this->assertEquals(45000, $winner->available_balance);
    }

    public function test_available_balance_with_deposits_and_withdrawals(): void
    {
        $winner = Winner::factory()->create(['prize_amount' => 50000]);
        Deposit::factory()->approved()->create(['winner_id' => $winner->id, 'amount' => 20000]);
        Withdrawal::factory()->completed()->create(['winner_id' => $winner->id, 'amount' => 15000]);
        Withdrawal::factory()->approved()->create(['winner_id' => $winner->id, 'amount' => 5000]);

        $this->assertEquals(50000, $winner->available_balance);
    }

    public function test_available_balance_ignores_pending_deposits(): void
    {
        $winner = Winner::factory()->create(['prize_amount' => 50000]);
        Deposit::factory()->create(['winner_id' => $winner->id, 'amount' => 10000, 'status' => 'pending']);

        $this->assertEquals(50000, $winner->available_balance);
    }

    public function test_available_balance_ignores_pending_withdrawals(): void
    {
        $winner = Winner::factory()->create(['prize_amount' => 50000]);
        Withdrawal::factory()->create(['winner_id' => $winner->id, 'amount' => 5000, 'status' => 'pending']);

        $this->assertEquals(50000, $winner->available_balance);
    }

    public function test_available_balance_never_negative(): void
    {
        $winner = Winner::factory()->create(['prize_amount' => 100]);
        Withdrawal::factory()->completed()->create(['winner_id' => $winner->id, 'amount' => 1000]);

        $this->assertGreaterThanOrEqual(0, $winner->available_balance);
        $this->assertEquals(0, $winner->available_balance);
    }
}
