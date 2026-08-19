<?php

namespace Tests\Feature;

use App\Mail\WinnerNotification;
use App\Models\User;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WinnerRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private array $validPayload = [
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
    ];

    public function test_register_page_returns_200_with_registration_form(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Register');
        $response->assertSee('first_name');
    }

    public function test_registration_creates_winner_with_fixed_prize_and_code(): void
    {
        $response = $this->post('/register', $this->validPayload);

        $response->assertStatus(302);
        $response->assertRedirect(route('winner.dashboard'));

        $this->assertDatabaseHas('winners', [
            'email' => 'jane.doe@example.com',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'prize_amount' => 5500000,
            'is_active' => true,
        ]);

        $winner = Winner::where('email', 'jane.doe@example.com')->first();
        $this->assertNotNull($winner->unique_code);
        $this->assertEquals(10, strlen($winner->unique_code));
        $this->assertEquals($winner->id, session('winner_id'));
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        Winner::factory()->create(['email' => 'jane.doe@example.com']);

        $response = $this->post('/register', $this->validPayload);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
        $this->assertEquals(1, Winner::count());
    }

    public function test_registration_requires_all_profile_fields(): void
    {
        $response = $this->post('/register', []);

        $response->assertSessionHasErrors([
            'first_name',
            'last_name',
            'email',
            'phone',
            'address',
            'city',
            'state',
            'zip',
            'password',
        ]);
        $this->assertEquals(0, Winner::count());
    }

    public function test_registration_stores_source_from_query_param(): void
    {
        $this->post('/register?source=tiktok', $this->validPayload);

        $this->assertDatabaseHas('winners', [
            'email' => 'jane.doe@example.com',
            'source' => 'tiktok',
        ]);
    }

    public function test_registration_sends_email_with_winner_code(): void
    {
        Mail::fake();

        $this->post('/register', $this->validPayload);

        $winner = Winner::where('email', 'jane.doe@example.com')->first();

        Mail::assertSent(WinnerNotification::class, function ($mail) use ($winner) {
            return $mail->hasTo($winner->email)
                && str_contains($mail->messageBody, $winner->unique_code);
        });
    }

    public function test_registration_no_longer_creates_admin_user(): void
    {
        $this->post('/register', $this->validPayload);

        $this->assertEquals(0, User::count());
        $this->assertEquals(1, Winner::count());
    }

    public function test_registered_winner_can_login_with_code_later(): void
    {
        $this->post('/register', $this->validPayload);

        $winner = Winner::where('email', 'jane.doe@example.com')->first();

        $response = $this->post('/winner/lookup', ['code' => $winner->unique_code]);

        $response->assertRedirect(route('winner.dashboard'));
        $this->assertEquals($winner->id, session('winner_id'));
    }
}