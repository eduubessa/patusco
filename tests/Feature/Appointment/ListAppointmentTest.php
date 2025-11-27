<?php

use App\Models\Animal;
use App\Models\Appointment;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('receptionist can access appointments list page', function () {
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

test('admin can access appointments list page', function () {
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

test('doctor sees only their own appointments', function () {
    $doctor = User::factory()->verified()->create(['role' => 'doctor']); // Create a doctor
    $customer = User::factory()->verified()->create(['role' => 'customer']); // create a customer (patient)
    $receptionist = User::factory()->verified()->create(['role' => 'receptionist']); // create a receptionist
    $animal = Animal::factory()->create(); // create a animal

    $appointment = Appointment::factory()->create([
        'author_id' => $receptionist->id,
        'customer_id' => $customer->id,
        'animal_id' => $animal->id,
        'doctor_id' => $doctor->id,
    ]);

    // Create random
    Appointment::factory()->count(2)->create();

    actingAs($customer)
        ->get('/appointments')
        ->assertStatus(200)
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Appointment/Calendar')
            ->has('events.data')
            ->where('events.data.0.doctor_id', $doctor->id)
        );
});

test('customer sees only their own appointments', function () {
    $doctor = User::factory()->verified()->create(['role' => 'doctor']); // Create a doctor
    $customer = User::factory()->verified()->create(['role' => 'customer']); // create a customer (patient)
    $receptionist = User::factory()->verified()->create(['role' => 'receptionist']); // create a receptionist
    $animal = Animal::factory()->create(); // create a animal

    $appointment = Appointment::factory()->create([
        'author_id' => $receptionist->id,
        'customer_id' => $customer->id,
        'animal_id' => $animal->id,
        'doctor_id' => $doctor->id,
    ]);

    // Create random
    Appointment::factory()->count(2)->create();

    actingAs($customer)
        ->get('/appointments')
        ->assertStatus(200)
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Appointment/Calendar')
            ->has('events.data')
            ->where('events.data.0.customer_id', $customer->id)
        );
});

test('doctor does not see appointments of other doctors', function () {
    $doctor = User::factory()->verified()->create(['role' => 'doctor']); // Create a doctor
    $customer = User::factory()->verified()->create(['role' => 'customer']); // create a customer (patient)
    $animal = Animal::factory()->create(); // create a animal

    $doctors = User::factory()->verified()->count(5)->create(['role' => 'doctor']);

    $appointment = Appointment::factory()->create([
        'author_id' => $customer->id,
        'doctor_id' => $doctor->id,
        'customer_id' => $customer->id,
        'animal_id' => $animal->id,
    ]);

    // Appointment without doctor logged
    $appointments = Appointment::factory()->count(5)->create();

    actingAs($doctor)
        ->get('/appointments')
        ->assertStatus(200)
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Appointment/Calendar')
            ->has('events.data', 1) // Check event has only one
            ->where('events.data.0.doctor_id', $doctor->id)
            ->where('events.data.0.customer_id', $customer->id)
        );
});

test('unverified user cannot access appointments list page', function () {
    $user = User::factory()->unverified()->create();

    actingAs($user)
        ->get('/appointments')
        ->assertStatus(302)
        ->assertRedirect('/email/verify');
});

test('guest cannot access appointments list page', function () {
    get('/appointments')
        ->assertStatus(302)
        ->assertRedirect('/login');
});
