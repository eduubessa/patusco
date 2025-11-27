<?php

namespace Database\Factories;

use App\Helpers\Enums\AppointmentStatus;
use App\Models\Animal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $receptionist = User::factory()->create(['role' => 'receptionist']);
        $customer = User::factory()->verified()->create(['role' => 'customer']);
        $animal = Animal::inRandomOrder()->first() ?? Animal::factory()->create();
        $doctor = User::inRandomOrder()->first() ?? User::factory()->create(['role' => 'doctor']);

        return [
            //
            'author_id' => $this->faker->randomElement([$customer->id, $receptionist->id]),
            'customer_id' => $customer->id,
            'animal_id' => $animal->id,
            'doctor_id' => $doctor->id,
            'situation' => $this->faker->realText(30),
            'scheduled_at' => $this->faker->dateTimeBetween('now', '+30 years'),
            'status' => $this->faker->randomElement(AppointmentStatus::cases())->value,
            'slug' => Str::slug('P-'.Str::random(12)),
        ];
    }

    public function withExistingAnimal(): AppointmentFactory|Factory
    {
        return $this->state(function () {
            $animal = Animal::inRandomOrder()->first() ?? Animal::factory()->create();

            return [
                'animal_id' => $animal->id,
            ];
        });
    }

    public function withExistingDoctor(): AppointmentFactory|Factory
    {
        return $this->state(function () {
            $doctor = User::doctor()->inRandomOrder()->first() ?? User::factory()->create(['role' => 'doctor']);

            return [
                'user_id' => $doctor->id,
            ];
        });
    }
}
