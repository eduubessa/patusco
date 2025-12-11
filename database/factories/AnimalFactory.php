<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Animal>
 */
final class AnimalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'id' => $this->faker->unique()->uuid,
            'name' => $this->faker->name(),
            'birthday' => $this->faker->date(),
            'species' => $this->faker->word(),
            'breed' => $this->faker->word(),
            'sex' => $this->faker->randomElement(['m', 'f']),
            'slug' => $this->faker->unique()->slug,
        ];
    }
}
