<?php

namespace Tests\Feature;

use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WinnerPasswordLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_stores_hashed_password(): void
    {
        $this->post('/register', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane.doe@example.com',
            'phone' => '555-123-4567',
            'address' => '123 Main St',
            'city' => 'Springfield',
            'state' => 'IL',
            'zip' => '62701',
            'password' => 'secret-password-123',
            'password_confirmation' => 'secret-password-123',
        ]);

        $winner = Winner::where('email', 'jane.doe@example.com')->first();

        $this->assertNotNull($winner);
        $this->assertNotEquals('secret-password-123', $winner->password);
        $this->assertTrue(Hash::check('secret-password-123', $winner->password));
    }

    public function test_registration_requires_confirmed_password(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane.doe@example.com',
            'phone' => '555-123-4567',
            'address' => '123 Main St',
            'city' => 'Springfield',
            'state' => 'IL',
            'zip' => '62701',
            'password' => 'secret-password-123',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertEquals(0, Winner::count());
    }

    public function test_winner_can_login_with_email_and_password(): void
    {
        $winner = Winner::factory()->create([
            'email' => 'jane.doe@example.com',
            'password' => Hash::make('secret-password-123'),
            'is_active' => true,
        ]);

        $response = $this->post('/winner/login', [
            'email' => 'jane.doe@example.com',
            'password' => 'secret-password-123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('winner.dashboard'));
        $this->assertEquals($winner->id, session('winner_id'));
    }

    public function test_winner_login_rejects_wrong_password(): void
    {
        Winner::factory()->create([
            'email' => 'jane.doe@example.com',
            'password' => Hash::make('secret-password-123'),
            'is_active' => true,
        ]);

        $response = $this->post('/winner/login', [
            'email' => 'jane.doe@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertNull(session('winner_id'));
    }

    public function test_winner_without_password_cannot_use_email_login(): void
    {
        Winner::factory()->create([
            'email' => 'jane.doe@example.com',
            'password' => null,
            'is_active' => true,
        ]);

        $response = $this->post('/winner/login', [
            'email' => 'jane.doe@example.com',
            'password' => 'anything',
        ]);

        $response->assertSessionHas('error');
        $this->assertNull(session('winner_id'));
    }

    public function test_winner_code_login_still_works_after_password_set(): void
    {
        $winner = Winner::factory()->create([
            'password' => Hash::make('secret-password-123'),
            'is_active' => true,
        ]);

        $response = $this->post('/winner/lookup', ['code' => $winner->unique_code]);

        $response->assertRedirect(route('winner.dashboard'));
        $this->assertEquals($winner->id, session('winner_id'));
    }
}