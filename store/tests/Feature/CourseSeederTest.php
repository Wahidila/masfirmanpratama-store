<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\InstallmentScheme;
use Database\Seeders\CourseSeeder;
use Database\Seeders\InstallmentSchemeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_seeder_creates_three_courses(): void
    {
        $this->seed(CourseSeeder::class);

        $this->assertSame(3, Course::count());
    }

    public function test_seeded_course_has_correct_slug_and_title(): void
    {
        $this->seed(CourseSeeder::class);

        $course = Course::first();

        $this->assertSame('kelas-amc-reguler', $course->slug);
        $this->assertSame('Kelas Reguler Alpha Mind Control', $course->title);
    }

    public function test_seeded_course_has_syllabus_array(): void
    {
        $this->seed(CourseSeeder::class);

        $course = Course::first();

        $this->assertIsArray($course->syllabus);
        $this->assertCount(20, $course->syllabus);
    }

    public function test_seeded_course_has_price_and_status(): void
    {
        $this->seed(CourseSeeder::class);

        $course = Course::first();

        $this->assertSame('4500000.00', $course->price);
        $this->assertSame('active', $course->status);
    }

    public function test_seeded_course_has_description_schedule_benefits_testimonials(): void
    {
        $this->seed(CourseSeeder::class);

        $course = Course::first();

        $this->assertIsArray($course->description);
        $this->assertNotEmpty($course->description);
        $this->assertIsArray($course->schedule);
        $this->assertNotEmpty($course->schedule);
        $this->assertIsArray($course->benefits);
        $this->assertNotEmpty($course->benefits);
        $this->assertIsArray($course->testimonials);
        $this->assertNotEmpty($course->testimonials);
    }

    public function test_seeded_course_is_idempotent(): void
    {
        $this->seed(CourseSeeder::class);
        $this->seed(CourseSeeder::class);

        $this->assertSame(3, Course::count());
    }

    public function test_seeded_course_has_installment_available_true(): void
    {
        $this->seed(CourseSeeder::class);

        $course = Course::first();

        $this->assertTrue($course->installment_available);
    }

    public function test_seeded_course_has_related_array(): void
    {
        $this->seed(CourseSeeder::class);

        $course = Course::first();

        $this->assertIsArray($course->related);
        $this->assertNotEmpty($course->related);
    }

    public function test_course_model_has_active_scope(): void
    {
        $this->seed(CourseSeeder::class);

        $active = Course::active()->get();

        $this->assertCount(3, $active);
    }

    public function test_three_courses_have_correct_slugs_and_prices(): void
    {
        $this->seed(CourseSeeder::class);

        $reguler = Course::where('slug', 'kelas-amc-reguler')->first();
        $privat = Course::where('slug', 'kelas-amc-privat')->first();
        $platinum = Course::where('slug', 'kelas-amc-platinum')->first();

        $this->assertNotNull($reguler);
        $this->assertNotNull($privat);
        $this->assertNotNull($platinum);

        $this->assertSame('4500000.00', $reguler->price);
        $this->assertSame('7500000.00', $privat->price);
        $this->assertSame('22500000.00', $platinum->price);
    }

    public function test_courses_have_correct_sort_order_and_card_style(): void
    {
        $this->seed(CourseSeeder::class);

        $this->assertDatabaseHas('courses', ['slug' => 'kelas-amc-reguler', 'sort_order' => 1, 'card_style' => 'default', 'show_on_homepage' => true]);
        $this->assertDatabaseHas('courses', ['slug' => 'kelas-amc-privat', 'sort_order' => 2, 'card_style' => 'highlight', 'show_on_homepage' => true]);
        $this->assertDatabaseHas('courses', ['slug' => 'kelas-amc-platinum', 'sort_order' => 3, 'card_style' => 'dark', 'show_on_homepage' => true]);
    }

    public function test_courses_have_non_empty_card_features(): void
    {
        $this->seed(CourseSeeder::class);

        foreach (['kelas-amc-reguler', 'kelas-amc-privat', 'kelas-amc-platinum'] as $slug) {
            $course = Course::where('slug', $slug)->first();
            $this->assertIsArray($course->card_features);
            $this->assertNotEmpty($course->card_features, "card_features empty for {$slug}");
        }
    }

    public function test_courses_have_card_icon_and_cta_label(): void
    {
        $this->seed(CourseSeeder::class);

        $reguler = Course::where('slug', 'kelas-amc-reguler')->first();
        $this->assertSame('video', $reguler->card_icon);
        $this->assertSame('text-blue-600', $reguler->card_icon_color);
        $this->assertSame('Daftar Reguler', $reguler->cta_label);

        $privat = Course::where('slug', 'kelas-amc-privat')->first();
        $this->assertSame('mic', $privat->card_icon);
        $this->assertSame('text-accent-600', $privat->card_icon_color);
        $this->assertSame('Daftar Privat', $privat->cta_label);

        $platinum = Course::where('slug', 'kelas-amc-platinum')->first();
        $this->assertSame('gem', $platinum->card_icon);
        $this->assertSame('text-secondary-400', $platinum->card_icon_color);
        $this->assertSame('Pilih Platinum', $platinum->cta_label);
    }

    public function test_installment_schemes_for_privat_and_platinum(): void
    {
        $this->seed(CourseSeeder::class);
        $this->seed(InstallmentSchemeSeeder::class);

        $privat = Course::where('slug', 'kelas-amc-privat')->first();
        $platinum = Course::where('slug', 'kelas-amc-platinum')->first();

        // Privat: 12x, dp 15%
        $privatScheme = InstallmentScheme::where('course_id', $privat->id)
            ->where('name', '12x Cicilan (Kelas Privat)')
            ->first();
        $this->assertNotNull($privatScheme);
        $this->assertSame(15, (int) $privatScheme->dp_pct);
        $this->assertSame(12, $privatScheme->n_installments);
        $this->assertTrue($privatScheme->active);

        // Platinum: 12x, dp 20%
        $platinumScheme = InstallmentScheme::where('course_id', $platinum->id)
            ->where('name', '12x Cicilan (Kelas Platinum)')
            ->first();
        $this->assertNotNull($platinumScheme);
        $this->assertSame(20, (int) $platinumScheme->dp_pct);
        $this->assertSame(12, $platinumScheme->n_installments);
        $this->assertTrue($platinumScheme->active);
    }
}
