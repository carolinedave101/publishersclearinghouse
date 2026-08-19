<?php

namespace Tests\Feature;

use App\Models\RegistrationLink;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationLinkCsvExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_contains_header_and_link_winners(): void
    {
        $link = RegistrationLink::factory()->create(['name' => 'Facebook June', 'source' => 'facebook-june']);
        Winner::factory()->create([
            'registration_link_id' => $link->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane.doe@example.com',
            'unique_code' => 'ABC123XYZW',
            'status' => 'new',
            'prize_amount' => 5500000,
        ]);

        $csv = $link->exportWinnersCsv();
        $rows = array_map('str_getcsv', explode("\n", trim($csv)));

        $this->assertEquals('First Name', $rows[0][0]);
        $this->assertContains('jane.doe@example.com', array_column($rows, 2));
        $this->assertContains('ABC123XYZW', array_column($rows, 7));
        $this->assertContains('5500000.00', array_column($rows, 9));
    }

    public function test_export_excludes_winners_from_other_links(): void
    {
        $link = RegistrationLink::factory()->create(['source' => 'facebook-june']);
        $other = RegistrationLink::factory()->create(['source' => 'tiktok-july']);
        Winner::factory()->create(['registration_link_id' => $other->id, 'email' => 'other@example.com']);

        $csv = $link->exportWinnersCsv();

        $this->assertStringNotContainsString('other@example.com', $csv);
    }

    public function test_export_escapes_commas_in_fields(): void
    {
        $link = RegistrationLink::factory()->create(['source' => 'facebook-june']);
        Winner::factory()->create([
            'registration_link_id' => $link->id,
            'first_name' => 'Jane, Jr',
            'last_name' => 'Doe',
            'email' => 'jane.doe@example.com',
        ]);

        $csv = $link->exportWinnersCsv();

        $this->assertStringContainsString('"Jane, Jr"', $csv);
    }

    public function test_view_page_has_export_button(): void
    {
        $link = RegistrationLink::factory()->create(['source' => 'facebook-june']);
        $admin = \App\Models\User::factory()->create(['is_super_admin' => true, 'is_admin' => true]);

        $this->actingAs($admin)
            ->get(\App\Filament\Resources\RegistrationLinkResource::getUrl('view', ['record' => $link]))
            ->assertOk()
            ->assertSee('Export CSV');
    }
}
