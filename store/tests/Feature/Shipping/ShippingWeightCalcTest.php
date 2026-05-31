<?php

namespace Tests\Feature\Shipping;

use App\Models\Product;
use App\Services\Shipping\ShippingRateService;
use Tests\TestCase;

class ShippingWeightCalcTest extends TestCase
{
    public function test_calculate_weight_with_two_books_different_qty(): void
    {
        Product::factory()->create([
            'slug' => 'buku-a',
            'type' => 'book',
            'weight_kg' => 0.5,
            'is_shippable' => true,
        ]);
        Product::factory()->create([
            'slug' => 'buku-b',
            'type' => 'book',
            'weight_kg' => 1.2,
            'is_shippable' => true,
        ]);

        $weight = app(ShippingRateService::class)->calculateWeight([
            ['slug' => 'buku-a', 'qty' => 2],
            ['slug' => 'buku-b', 'qty' => 3],
        ]);

        $this->assertEquals(4.6, $weight);
    }

    public function test_calculate_weight_skips_non_shippable(): void
    {
        Product::factory()->create([
            'slug' => 'course-a',
            'type' => 'course',
            'weight_kg' => null,
            'is_shippable' => false,
        ]);
        Product::factory()->create([
            'slug' => 'buku-a',
            'type' => 'book',
            'weight_kg' => 2.0,
            'is_shippable' => true,
        ]);

        $weight = app(ShippingRateService::class)->calculateWeight([
            ['slug' => 'course-a', 'qty' => 1],
            ['slug' => 'buku-a', 'qty' => 2],
        ]);

        $this->assertEquals(4.0, $weight);
    }

    public function test_calculate_weight_minimum_one_when_items_exist_but_weight_is_tiny(): void
    {
        Product::factory()->create([
            'slug' => 'tiny-book',
            'type' => 'book',
            'weight_kg' => 0.3,
            'is_shippable' => true,
        ]);

        $weight = app(ShippingRateService::class)->calculateWeight([
            ['slug' => 'tiny-book', 'qty' => 1],
        ]);

        $this->assertEquals(1.0, $weight);
    }

    public function test_calculate_weight_returns_zero_when_all_items_non_shippable(): void
    {
        Product::factory()->create([
            'slug' => 'course-a',
            'type' => 'course',
            'weight_kg' => null,
            'is_shippable' => false,
        ]);

        $weight = app(ShippingRateService::class)->calculateWeight([
            ['slug' => 'course-a', 'qty' => 1],
        ]);

        $this->assertEquals(0.0, $weight);
    }

    public function test_calculate_dimensions_length_max_width_max_height_sum(): void
    {
        Product::factory()->create([
            'slug' => 'buku-a',
            'type' => 'book',
            'weight_kg' => 0.5,
            'length_cm' => 20,
            'width_cm' => 15,
            'height_cm' => 3,
            'is_shippable' => true,
        ]);
        Product::factory()->create([
            'slug' => 'buku-b',
            'type' => 'book',
            'weight_kg' => 1.0,
            'length_cm' => 25,
            'width_cm' => 10,
            'height_cm' => 5,
            'is_shippable' => true,
        ]);

        $dims = app(ShippingRateService::class)->calculateDimensions([
            ['slug' => 'buku-a', 'qty' => 2],
            ['slug' => 'buku-b', 'qty' => 1],
        ]);

        $this->assertSame(25, $dims['length']);
        $this->assertSame(15, $dims['width']);
        $this->assertSame(11, $dims['height']);
    }

    public function test_calculate_dimensions_fallback_to_config_defaults_when_products_have_no_dimensions(): void
    {
        Product::factory()->create([
            'slug' => 'buku-no-dims',
            'type' => 'book',
            'weight_kg' => 0.5,
            'length_cm' => null,
            'width_cm' => null,
            'height_cm' => null,
            'is_shippable' => true,
        ]);

        $dims = app(ShippingRateService::class)->calculateDimensions([
            ['slug' => 'buku-no-dims', 'qty' => 1],
        ]);

        $this->assertSame(10, $dims['length']);
        $this->assertSame(10, $dims['width']);
        $this->assertSame(5, $dims['height']);
    }
}
