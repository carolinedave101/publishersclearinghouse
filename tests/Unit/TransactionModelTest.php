<?php

namespace Tests\Unit;

use App\Models\Transaction;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_transaction(): void
    {
        $transaction = Transaction::factory()->create();

        $this->assertDatabaseHas('transactions', ['id' => $transaction->id]);
    }

    public function test_transaction_belongs_to_winner(): void
    {
        $winner = Winner::factory()->create();
        $transaction = Transaction::factory()->create(['winner_id' => $winner->id]);

        $this->assertInstanceOf(Winner::class, $transaction->winner);
        $this->assertEquals($winner->id, $transaction->winner->id);
    }

    public function test_transaction_types(): void
    {
        $deposit = Transaction::factory()->deposit()->create();
        $withdrawal = Transaction::factory()->withdrawal()->create();

        $this->assertEquals('deposit', $deposit->type);
        $this->assertEquals('withdrawal', $withdrawal->type);
        $this->assertGreaterThan(0, $deposit->net_amount);
        $this->assertLessThan(0, $withdrawal->net_amount);
    }

    public function test_amount_casts(): void
    {
        $transaction = Transaction::factory()->create(['amount' => 5000.25, 'fee' => 10.50]);

        $this->assertIsNumeric($transaction->amount);
        $this->assertIsNumeric($transaction->fee);
        $this->assertIsNumeric($transaction->net_amount);
        $this->assertEquals(5000.25, $transaction->amount);
        $this->assertEquals(10.50, $transaction->fee);
    }

    public function test_transaction_deletes_with_winner(): void
    {
        $winner = Winner::factory()->create();
        $transaction = Transaction::factory()->create(['winner_id' => $winner->id]);
        $txId = $transaction->id;

        $winner->delete();

        $this->assertDatabaseMissing('transactions', ['id' => $txId]);
    }

    public function test_transaction_has_morph_reference(): void
    {
        $transaction = Transaction::factory()->create([
            'reference_type' => 'deposit',
            'reference_id' => 999,
        ]);

        $this->assertEquals('deposit', $transaction->reference_type);
        $this->assertEquals(999, $transaction->reference_id);
    }
}
