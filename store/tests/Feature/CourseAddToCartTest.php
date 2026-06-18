<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseAddToCartTest extends TestCase
{
    use RefreshDatabase;

    private function createCourse(array $overrides = []): Course
    {
        return Course::factory()->active()->create(array_merge([
            'slug' => 'kelas-amc-reguler',
            'title' => 'Kelas Reguler Alpha Mind Control',
            'price' => 4500000,
            'benefits' => [
                ['icon' => 'star', 'title' => 'Benefit A', 'desc' => 'Desc A'],
            ],
        ], $overrides));
    }

    public function test_course_cta_links_to_course_checkout(): void
    {
        $this->createCourse();

        $response = $this->get('/kelas/kelas-amc-reguler');
        $response->assertStatus(200);

        $content = $response->getContent();

        // CTA harus link ke /kelas/{slug}/checkout, bukan /checkout (book checkout)
        $this->assertStringContainsString(
            route('courses.checkout', 'kelas-amc-reguler'),
            $content,
            'CTA must link to course checkout, not book checkout'
        );

        // Tidak boleh ada addToCartAndCheckout (kelas tidak masuk cart)
        $this->assertStringNotContainsString(
            'addToCartAndCheckout',
            $content,
            'Course page must not have addToCartAndCheckout function'
        );
    }

    public function test_course_page_does_not_add_to_cart(): void
    {
        $this->createCourse();

        $response = $this->get('/kelas/kelas-amc-reguler');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Tidak boleh ada $store.cart.add di halaman kelas
        $this->assertStringNotContainsString(
            'store.cart.add',
            $content,
            'Course page must not add items to cart'
        );
    }

    public function test_course_checkout_page_renders(): void
    {
        $this->createCourse();

        $response = $this->get('/kelas/kelas-amc-reguler/checkout');
        $response->assertStatus(200);

        // Form pendaftaran harus ada
        $response->assertSee('Formulir Pendaftaran');
        $response->assertSee('customer_name', false);
        $response->assertSee('customer_email', false);
        $response->assertSee('customer_phone', false);
    }
}
