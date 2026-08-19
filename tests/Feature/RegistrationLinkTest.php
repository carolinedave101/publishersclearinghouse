<?php

namespace Tests\Feature;

use App\Models\RegistrationLink;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationLinkTest extends TestCase
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

    public function test_registration_via_active_link_attaches_link_to_winner(): void
    {
        $link = RegistrationLink::factory()->create([
            'source' => 'facebook-june',
            'is_active' => true,
        ]);

        $this->post('/register?source=facebook-june', $this->validPayload)
            ->assertRedirect(route('winner.dashboard'));

        $winner = Winner::where('email', 'jane.doe@example.com')->first();

        $this->assertEquals($link->id, $winner->registration_link_id);
        $this->assertEquals('facebook-june', $winner->source);
    }

    public function test_registration_with_unknown_source_creates_winner_without_link(): void
    {
        $this->post('/register?source=unknown-sauce', $this->validPayload)
            ->assertRedirect(route('winner.dashboard'));

        $winner = Winner::where('email', 'jane.doe@example.com')->first();

        $this->assertNull($winner->registration_link_id);
        $this->assertEquals('unknown-sauce', $winner->source);
    }

    public function test_registration_via_inactive_link_does_not_attach_link(): void
    {
        $link = RegistrationLink::factory()->create([
            'source' => 'tiktok-july',
            'is_active' => false,
        ]);

        $this->post('/register?source=tiktok-july', $this->validPayload)
            ->assertRedirect(route('winner.dashboard'));

        $winner = Winner::where('email', 'jane.doe@example.com')->first();

        $this->assertNull($winner->registration_link_id);
        $this->assertNotEquals($link->id, $winner->registration_link_id);
    }

    public function test_registration_without_source_creates_winner_without_link(): void
    {
        $this->post('/register', $this->validPayload)
            ->assertRedirect(route('winner.dashboard'));

        $winner = Winner::where('email', 'jane.doe@example.com')->first();

        $this->assertNull($winner->registration_link_id);
    }

    public function test_admin_can_access_registration_links_resource(): void
    {
        $admin = \App\Models\User::factory()->create(['is_super_admin' => true, 'is_admin' => true]);

        $this->actingAs($admin)
            ->get(\App\Filament\Resources\RegistrationLinkResource::getUrl('index'))
            ->assertOk()
            ->assertSee('Registration Links');
    }

    public function test_duplicate_link_source_is_rejected_at_database_level(): void
    {
        RegistrationLink::factory()->create(['source' => 'facebook-june']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        RegistrationLink::factory()->create(['source' => 'facebook-june']);
    }
}