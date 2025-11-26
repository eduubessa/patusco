<?php

namespace Database\Factories;

use App\Helpers\Enums\AppointmentStatus;
use App\Models\Animal;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        $customer = User::factory()->verified()->create(['role' => 'customer']);
        $animal = Animal::inRandomOrder()->first() ?? Animal::factory()->create();
        $doctor = User::doctor()->inRandomOrder()->first() ?? User::factory()->create(['role' => 'doctor']);

        return [
            //
            'customer_id' => $customer->id,
            'animal_id' => $animal->id,
            'doctor_id' => $doctor->id,
            'situation' => $this->faker->realText(30),
            'scheduled_at' => $this->faker->dateTimeBetween('now', '+30 years'),
            'status' => $this->faker->randomElement(AppointmentStatus::cases()),
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
                'user_id' => $doctor->id
            ];
        });
    }
}
