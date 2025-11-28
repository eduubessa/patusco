<?php

use App\Helpers\Enums\AppointmentStatus;
use App\Helpers\Enums\UserRoles;
use App\Models\Animal;
use App\Models\Appointment;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('admin can access to create appointment screen', function () {
    $user = User::factory()->verified()->create(['role' => 'admin']);

    actingAs($user)
        ->get('appointments/new')
        ->assertStatus(200)
        ->assertOk();
});

test('receptionist can access to create appointment screen', function () {
    $user = User::factory()->verified()->create(['role' => 'receptionist']);

    actingAs($user)
        ->get('appointments/new')
        ->assertStatus(200)
        ->assertOk();
});

test('customer can access to create appointment screen', function () {
    $user = User::factory()->verified()->create(['role' => 'customer']);

    actingAs($user)
        ->get('appointments/new')
        ->assertStatus(200)
        ->assertOk();
});

test('doctor cannot access to create appointment screen', function () {
    $user = User::factory()->verified()->create(['role' => 'doctor']);

    actingAs($user)
        ->get('appointments/new')
        ->assertStatus(403)
        ->assertForbidden();
});

test('unverified user cannot access to create appointment screen', function () {
    $user = User::factory()->unverified()->create(['role' => 'admin']);

    actingAs($user)
        ->get('appointments/new')
        ->assertStatus(302)
        ->assertRedirect('/email/verify');
});

test('guest cannot access to create appointment screen', function () {
    get('appointments/new')
        ->assertStatus(302)
        ->assertRedirect('login');
});

test('admin can create new appointment', function () {
    $user = User::factory()->verified()->create(['role' => 'admin']);
    $customer = User::factory()->verified()->create(['role' => 'customer']);
    $doctor = User::factory()->verified()->create(['role' => 'doctor']);
    $animal = Animal::factory()->create();

    actingAs($user)
        ->post('/appointments/', [
            'customer' => $customer->id,
            'doctor' => $doctor->id,
            'animal' => $animal->id,
            'situation' => fake()->realText(50),
            'scheduled_at' => fake()->dateTimeBetween('now', '+1 year')->format('Y-m-d H:i:s'),
            'status' => fake()->randomElement(array_map(fn ($case) => $case->value, AppointmentStatus::cases())),
        ])
        ->assertStatus(302);

    expect(Appointment::count())->toBe(1);
});

test('receptionist can create new appointment', function () {
    $user = User::factory()->verified()->create(['role' => 'receptionist']);
    $customer = User::factory()->verified()->create(['role' => 'customer']);
    $doctor = User::factory()->verified()->create(['role' => 'doctor']);
    $animal = Animal::factory()->create();

    actingAs($user)
        ->post('/appointments/', [
            'customer' => $customer->id,
            'doctor' => $doctor->id,
            'animal' => $animal->id,
            'situation' => fake()->realText(50),
            'scheduled_at' => fake()->dateTimeBetween('now', '+1 year')->format('Y-m-d H:i:s'),
            'status' => fake()->randomElement(array_map(fn ($case) => $case->value, AppointmentStatus::cases())),
        ])
        ->assertStatus(302)
        ->assertSessionHas('success');

    expect(Appointment::count())->toBe(1);
});

test('customer can create new appointment', function () {
    $user = User::factory()->verified()->create(['role' => 'customer']);
    $customer = User::factory()->verified()->create(['role' => 'customer']);
    $doctor = User::factory()->verified()->create(['role' => 'doctor']);
    $animal = Animal::factory()->create();

    actingAs($user)
        ->post('/appointments/', [
            'customer' => $customer->id,
            'doctor' => $doctor->id,
            'animal' => $animal->id,
            'situation' => fake()->realText(50),
            'scheduled_at' => fake()->dateTimeBetween('now', '+1 year')->format('Y-m-d H:i:s'),
            'status' => fake()->randomElement(array_map(fn ($case) => $case->value, AppointmentStatus::cases())),
        ])
        ->assertStatus(302)
        ->assertSessionHas('success');

    expect(Appointment::count())->toBe(1);
});

test('unverified user cannot create appointment', function () {
    $user = User::factory()->unverified()->create(['role' => 'admin']);
    $customer = User::factory()->verified()->create(['role' => 'customer']);
    $doctor = User::factory()->verified()->create(['role' => 'doctor']);
    $animal = Animal::factory()->create();

    actingAs($user)
        ->post('/appointments/', [
            'customer' => $customer->id,
            'doctor' => $doctor->id,
            'animal' => $animal->id,
            'situation' => fake()->realText(50),
            'scheduled_at' => fake()->dateTimeBetween('now', '+1 year')->format('Y-m-d H:i:s'),
            'status' => fake()->randomElement(array_map(fn ($case) => $case->value, AppointmentStatus::cases())),
        ])
        ->assertStatus(302)
        ->assertRedirect('/email/verify');
});

test('guest cannot create appointment', function () {
    $customer = User::factory()->verified()->create(['role' => 'customer']);
    $doctor = User::factory()->verified()->create(['role' => 'doctor']);
    $animal = Animal::factory()->create();

    post('/appointments/', [
        'customer' => $customer->id,
        'doctor' => $doctor->id,
        'animal' => $animal->id,
        'situation' => fake()->realText(50),
        'scheduled_at' => fake()->dateTimeBetween('now', '+1 year')->format('Y-m-d H:i:s'),
        'status' => fake()->randomElement(array_map(fn ($case) => $case->value, AppointmentStatus::cases())),
    ])
        ->assertStatus(302)
        ->assertRedirect('/login');
});

test('cannot create appointment with invalid data', function () {
    $user = User::factory()->verified()->create(['role' => 'admin']);

    actingAs($user)
        ->post('/appointments/', [
            'customer' => 'invalid',
            'doctor' => 'invalid',
            'animal' => 'invalid',
            'situation' => fake()->realText(50),
            'scheduled_at' => fake()->dateTimeBetween('now', '+1 year')->format('Y-m-d H:i:s'),
        ])
        ->assertStatus(302)
        ->assertSessionHasErrors([
            'customer',
            'doctor',
            'animal',
        ]);
});

test('store fails when doctor reaches max appointments per slot', function () {
    $customer = User::factory()->verified()->create(['role' => 'customer']);
    $doctor = User::factory()->verified()->create(['role' => 'doctor']);
    $animal = Animal::factory()->create();

    $slot = now()->addDay()->format('Y-m-d H:i:s');

    Appointment::factory()->count(5)->create([
        'doctor_id' => $doctor->id,
        'scheduled_at' => $slot,
        'status' => AppointmentStatus::Scheduled->value
    ]);

    actingAs($customer)
        ->post('/appointments/', [
            'customer' => $customer->id,
            'doctor' => $doctor->id,
            'animal' => $animal->id,
            'situation' => "Initial situation from test!",
            'scheduled_at' => $slot,
            'status' => AppointmentStatus::Scheduled->value
        ])
        ->assertStatus(302)
        ->assertSessionHasErrors('doctor', "O veternário selecionado já não tem disponibilidade para essa hora!");
});
