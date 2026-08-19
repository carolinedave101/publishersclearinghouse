<?php
namespace Tests\Feature;

use App\Filament\Resources\RegistrationLinkResource\Pages\ViewRegistrationLink;
use App\Models\RegistrationLink;
use App\Models\User;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ViewLinkLivewireProbeTest extends TestCase
{
    use RefreshDatabase;

    public function test_view_page_renders_full(): void
    {
        $link = RegistrationLink::factory()->create(['source' => 'facebook-june']);
        Winner::factory()->create(['registration_link_id' => $link->id, 'source' => 'facebook-june']);
        $admin = User::factory()->create(['is_super_admin' => true, 'is_admin' => true]);

        Livewire::actingAs($admin)
            ->test(ViewRegistrationLink::class, ['record' => $link->getRouteKey()])
            ->assertOk();
    }
}
