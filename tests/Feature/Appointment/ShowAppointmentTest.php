<?php

declare(strict_types=1);

use App\Models\Appointment;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('customer can view own appointments', function () {
    $user = User::factory()->verified()->create(['role' => 'customer']);
    $appointment = Appointment::factory()->create([
        'customer_id' => $user->id,
    ]);

    actingAs($user)
        ->get("/appointments/{$appointment->slug}")
        ->assertStatus(200)
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Appointment/Show')
            ->has('appointment')
            ->where('appointment.customer_id', $user->id)
        );
});

test('doctor can view own appointments', function () {
    $user = User::factory()->verified()->create(['role' => 'doctor']);
    $appointment = Appointment::factory()->create([

        'doctor_id' => $user->id,
    ]);

    actingAs($user)
        ->get("appointments/{$appointment->slug}")
        ->assertStatus(200)
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Appointment/Show')
            ->has('appointment')
            ->where('appointment.doctor_id', $user->id)
        );
});

test('receptionist can view all appointments', function () {
    $user = User::factory()->verified()->create(['role' => 'receptionist']);
    $appointment = Appointment::factory()->create([
        'author_id' => $user->id,
    ]);

    actingAs($user)
        ->get("/appointments/{$appointment->slug}")
        ->assertStatus(200)
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Appointment/Show')
            ->has('appointment')
        );
});

test('doctor cannot access appointment that does not belong to him', function () {
    $doctorWithPermission = User::factory()->verified()->create(['role' => 'doctor']);
    $doctorWithoutPermission = User::factory()->verified()->create(['role' => 'doctor']);

    $appointment = Appointment::factory()->create([
        'doctor_id' => $doctorWithPermission->id,
    ]);

    actingAs($doctorWithoutPermission)
        ->get("/appointments/{$appointment->slug}")
        ->assertStatus(403);
});

test('customer cannot access appointment that does not belong to him', function () {
    $customerWithPermission = User::factory()->verified()->create(['role' => 'customer']);
    $customerWithoutPermission = User::factory()->verified()->create(['role' => 'customer']);

    $appointment = Appointment::factory()->create([
        'customer_id' => $customerWithPermission,
    ]);

    actingAs($customerWithoutPermission)
        ->get("/appointments/{$appointment->slug}")
        ->assertStatus(403);
});

test('unverified user cannot access appointment single page', function () {
    $user = User::factory()->unverified()->create();
    $appointment = Appointment::factory()->create();

    actingAs($user)
        ->get("/appointments/{$appointment->slug}")
        ->assertStatus(302)
        ->assertRedirect('/email/verify');
});

test('guest cannon view single appointment', function () {
    $appointment = Appointment::factory()->create();

    get("/appointments/{$appointment->slug}")
        ->assertStatus(302)
        ->assertRedirect('/login');
});

test('non-existing appointment returns 404 error', function () {
    $user = User::factory()->verified()->create(['role' => 'admin']);

    $nonExistingAppointmentSlug = Str::slug('non-existing-'.Str::random(10));

    actingAs($user)
        ->get("/appointments/{$nonExistingAppointmentSlug}")
        ->assertStatus(404);
});
