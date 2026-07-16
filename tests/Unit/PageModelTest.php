<?php

namespace Tests\Unit;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_page(): void
    {
        $page = Page::factory()->create();

        $this->assertDatabaseHas('pages', ['id' => $page->id]);
    }

    public function test_published_scope(): void
    {
        Page::factory()->create(['is_published' => true]);
        Page::factory()->create(['is_published' => false]);

        $this->assertCount(1, Page::published()->get());
    }

    public function test_page_casts(): void
    {
        $page = Page::factory()->create([
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->assertTrue($page->is_published);
        $this->assertInstanceOf(\Carbon\Carbon::class, $page->published_at);
    }
}
