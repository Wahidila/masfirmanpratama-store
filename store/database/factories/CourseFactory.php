<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(3);
        $slug = Str::slug($title).'-'.Str::lower(Str::random(4));

        return [
            'title' => $title,
            'slug' => $slug,
            'subtitle' => $this->faker->optional()->sentence(),
            'price' => $this->faker->numberBetween(50000, 500000),
            'original_price' => $this->faker->boolean(30) ? $this->faker->numberBetween(600000, 1500000) : null,
            'status' => $this->faker->randomElement(['draft', 'active', 'archived']),
            'image_path' => null,
            'badge' => $this->faker->optional()->word(),
            'badge_icon' => $this->faker->optional()->word(),
            'category_label' => $this->faker->optional()->word(),
            'rating' => $this->faker->boolean(40) ? (string) $this->faker->randomFloat(1, 4.0, 5.0) : null,
            'student_count' => $this->faker->boolean(40) ? $this->faker->numberBetween(50, 500).'+' : null,
            'tagline' => $this->faker->optional()->sentence(),
            'installment_available' => $this->faker->boolean(),
            'description' => $this->faker->paragraphs(3),
            'syllabus' => $this->faker->sentences(4),
            'schedule' => [],
            'benefits' => [],
            'testimonials' => [],
            'related' => [],
            'meta_seo' => null,
            'sort_order' => 0,
            'show_on_homepage' => true,
            'card_features' => [],
            'card_icon' => null,
            'card_icon_color' => null,
            'card_style' => 'default',
            'cta_label' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => 'active']);
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => 'draft']);
    }
}
