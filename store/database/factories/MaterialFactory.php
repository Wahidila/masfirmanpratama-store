<?php

namespace Database\Factories;

use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Material>
 */
class MaterialFactory extends Factory
{
    protected $model = Material::class;

    public function definition(): array
    {
        $types = ['banner', 'brosur', 'video', 'template_wa'];

        return [
            'title' => fake('id_ID')->sentence(4),
            'description' => fake('id_ID')->paragraph(2),
            'file_path' => 'materials/'.fake()->uuid().'.pdf',
            'type' => fake()->randomElement($types),
        ];
    }
}
