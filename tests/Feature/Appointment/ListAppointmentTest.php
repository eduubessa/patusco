<?php

use App\Models\Animal;
use App\Models\Appointment;
use App\Models\User;
use function Pest\Laravel\actingAs;
use Inertia\Testing\AssertableInertia as Assert;

test("receptionist can access appointments list page", function () {
    $user = User::factory()->verified()->create(['role' => 'receptionist']);

    actingAs($user)
        ->get('/appointments')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Appointment/Calendar')
            ->has('events')
        )
        ->assertStatus(200)
        ->assertOk();
});


test("admin can access appointments list page", function () {
    $user = User::factory()->verified()->create(['role' => 'admin']);

    actingAs($user)
        ->get('/appointments')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Appointment/Calendar')
            ->has('events')
        )
        ->assertStatus(200)
        ->assertOk();
});

test("doctor cannot access appointments the full appointments list", function () {
    $user = User::factory()->verified()->create(['role' => 'doctor']);

    actingAs($user)
        ->get('/appointments')
        ->assertForbidden();
});

test("client cannot access appointments the full appointments list", function () {
    $user = User::factory()->verified()->create(['role' => 'customer']);

    actingAs($user)
        ->get('/appointments')
        ->assertForbidden();
});

test("doctor sees only their own appointments", function () {
    $doctor = User::factory()->verified()->create(['role' => 'doctor']);
    $customer = User::factory()->verified()->create(['role' => 'customer']);
    $animal = Animal::factory()->create();

    $own = Appointment::factory()->create(['doctor_id' => $doctor, 'author_id' => $customer, 'animal_id' => $animal]);
    Appointment::factory()->count(2)->create();

    actingAs($doctor)
        ->get('/appointments')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Appointment/Calendar')
            ->has('events')
            ->has('appointments', 1)
            ->where('appointments.0.id', $own->id)
        );
});

test("customer sees only their own appointments", function () {
    $doctor = User::factory()->verified()->create(['role' => 'doctor']);
    $customer = User::factory()->verified()->create(['role' => 'customer']);
    $animal = Animal::factory()->create();

    $own = Appointment::factory()->create(['author_id', $customer->id, 'animal_id' => $animal, 'doctor_id' => $doctor]);
    Appointment::factory()->count(2)->create();

    action($customer)
        ->get('/appointments')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Appointment/Calendar')
            ->has('events')
            ->where('appointments.0.id', $own->id)
        );
});
