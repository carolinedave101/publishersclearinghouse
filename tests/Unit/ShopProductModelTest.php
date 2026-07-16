<?php

namespace Tests\Unit;

use App\Models\ShopProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopProductModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_product(): void
    {
        $product = ShopProduct::factory()->create();

        $this->assertDatabaseHas('shop_products', ['id' => $product->id]);
    }

    public function test_product_casts(): void
    {
        $product = ShopProduct::factory()->create(['price' => 29.99, 'is_active' => true]);

        $this->assertEquals(29.99, $product->price);
        $this->assertTrue($product->is_active);
    }

    public function test_active_scope(): void
    {
        ShopProduct::factory()->create(['is_active' => true]);
        ShopProduct::factory()->create(['is_active' => false]);

        $this->assertCount(1, ShopProduct::active()->get());
    }

    public function test_fillable_attributes(): void
    {
        $product = new ShopProduct;

        $this->assertEquals([
            'name', 'slug', 'description', 'price',
            'image', 'category', 'is_active', 'sort_order',
        ], $product->getFillable());
    }
}
