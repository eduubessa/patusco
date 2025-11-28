<?php

use App\Helpers\Enums\AppointmentStatus;
use App\Helpers\Enums\UserRoles;
use App\Models\Animal;
use App\Models\Appointment;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('admin can access to update appointment screen', function () {
    $user = User::factory()->verified()->create(['role' =>  UserRoles::Admin->value]);
    $appointment = Appointment::factory()->create();

    actingAs($user)
        ->get("/appointments/{$appointment->slug}/edit")
        ->assertStatus(200)
        ->assertOk();
});

test('receptionist can access to update appointment screen', function () {
    $user = User::factory()->verified()->create(['role' =>  UserRoles::Receptionist->value]);
    $appointment = Appointment::factory()->create();

    actingAs($user)
        ->get("/appointments/{$appointment->slug}/edit")
        ->assertStatus(200)
        ->assertOk();
});

test('the doctor can access the appointment update screen only if the appointment belongs to them', function () {
    $user = User::factory()->verified()->create(['role' =>  UserRoles::Doctor->value]);
    $appointment = Appointment::factory()->create(['doctor_id' => $user->id]);

    actingAs($user)
        ->get("/appointments/{$appointment->slug}/edit")
        ->assertStatus(200)
        ->assertOk();
});

test('the doctor cannot access the appointment update screen if the appointment does not belong to them', function () {
    $user = User::factory()->verified()->create(['role' =>  UserRoles::Doctor->value]);
    $appointment = Appointment::factory()->create();

    actingAs($user)
        ->get("/appointments/{$appointment->slug}/edit")
        ->assertStatus(403)
        ->assertForbidden();
});

test('customer cannot access to update appointment screen', function () {
    $user = User::factory()->verified()->create(['role' =>  UserRoles::Customer->value]);
    $appointment = Appointment::factory()->create();

    actingAs($user)
        ->get("appointments/{$appointment->slug}/edit")
        ->assertStatus(403)
        ->assertForbidden();
});

test('unverified customer cannot access to update appointment screen', function () {
    $user = User::factory()->unverified()->create(['role' =>  UserRoles::Customer->value]);
    $appointment = Appointment::factory()->create();

    actingAs($user)
        ->get("appointments/{$appointment->slug}/edit")
        ->assertStatus(302)
        ->assertRedirect('/email/verify');
});

test('guest cannot access to update appointment screen', function () {
    $appointment = Appointment::factory()->create();

    get("appointments/{$appointment->slug}/edit")
        ->assertStatus(302)
        ->assertRedirect('/login');
});

test('admin can update appointment', function () {
    $user = User::factory()->verified()->create(['role' =>  UserRoles::Admin->value]);
    $doctor = User::factory()->verified()->create(['role' =>  UserRoles::Doctor->value]);
    $customer = User::factory()->verified()->create(['role' =>  UserRoles::Customer->value]);
    $animal = Animal::factory()->create();

    $appointment = Appointment::factory()->create([
        'author_id' => $user->id,
        'doctor_id' => $doctor->id,
        'animal_id' => $animal->id,
        'customer_id' => $customer->id,
        'situation' => "Initial situation",
        'scheduled_at' => now()->addDay(),
        'status' => AppointmentStatus::Scheduled->value
    ]);

    actingAs($user)
        ->put("/appointments/{$appointment->slug}", [
            'doctor' => $doctor->id,
            'situation' => "Updated situation from test by admin user",
            'scheduled_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'status' => AppointmentStatus::Scheduled->value
        ])
        ->assertStatus(302)
        ->assertRedirect("/appointments/{$appointment->slug}")
        ->assertSessionHas('success', 'O agendamento foi atualizado com sucesso.');

    $this->assertDatabaseHas('appointments', [
        'id' => $appointment->id,
        'doctor_id' => $doctor->id,
        'situation' => "Updated situation from test by admin user",
        'scheduled_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
        'status' => AppointmentStatus::Scheduled->value
    ]);
});

test('admin cannot update an appointment with invalid data', function () {
    $user = User::factory()->verified()->create(['role' =>  UserRoles::Admin->value]);

    $appointment = Appointment::factory()->create([
        'author_id' => $user->id
    ]);

    actingAs($user)
        ->put("/appointments/{$appointment->slug}", [
            'situation' => null,
            'scheduled_at' => 'invalid-date',
            'status' => 'invalid-status'
        ])
        ->assertStatus(302)
        ->assertRedirectBack()
        ->assertSessionHasErrors(['situation', 'scheduled_at', 'status']);

    $this->assertDatabaseHas('appointments', [
        'id' => $appointment->id,
        'situation' => $appointment->situation,
        'scheduled_at' => $appointment->scheduled_at,
        'status' => $appointment->status
    ]);
});

test('receptionist cannot update an appointment with invalid data', function () {
    $user = User::factory()->verified()->create(['role' =>  UserRoles::Receptionist->value]);

    $appointment = Appointment::factory()->create([
        'author_id' => $user->id
    ]);

    actingAs($user)
        ->put("/appointments/{$appointment->slug}", [
            'situation' => null,
            'scheduled_at' => 'invalid-date',
            'status' => 'invalid-status'
        ])
        ->assertStatus(302)
        ->assertRedirectBack()
        ->assertSessionHasErrors(['situation', 'scheduled_at', 'status']);

    $this->assertDatabaseHas('appointments', [
        'id' => $appointment->id,
        'situation' => $appointment->situation,
        'scheduled_at' => $appointment->scheduled_at,
        'status' => $appointment->status
    ]);
});

test('doctor cannot update an appointment with invalid data', function () {
    $user = User::factory()->verified()->create(['role' =>  UserRoles::Doctor->value]);

    $appointment = Appointment::factory()->create([
        'author_id' => $user->id
    ]);

    actingAs($user)
        ->put("/appointments/{$appointment->slug}", [
            'situation' => null,
            'scheduled_at' => 'invalid-date',
            'status' => 'invalid-status'
        ])
        ->assertStatus(302)
        ->assertRedirectBack()
        ->assertSessionHasErrors(['situation', 'scheduled_at', 'status']);

    $this->assertDatabaseHas('appointments', [
        'id' => $appointment->id,
        'situation' => $appointment->situation,
        'scheduled_at' => $appointment->scheduled_at,
        'status' => $appointment->status
    ]);
});

test('receptionist can update appointment', function () {
    $user = User::factory()->verified()->create(['role' =>  UserRoles::Receptionist->value]);
    $doctor = User::factory()->verified()->create(['role' =>  UserRoles::Doctor->value]);
    $customer = User::factory()->verified()->create(['role' => UserRoles::Customer->value]);
    $animal = Animal::factory()->create();

    $appointment = Appointment::factory()->create([
        'author_id' => $user->id,
        'doctor_id' => $doctor->id,
        'animal_id' => $animal->id,
        'customer_id' => $customer->id,
        'situation' => "Initial situation",
        'scheduled_at' => now()->addDay(),
        'status' => AppointmentStatus::Scheduled->value
    ]);

    actingAs($user)
        ->put("/appointments/{$appointment->slug}", [
            'doctor' => $doctor->id,
            'situation' => "Updated situation from test by receptionist user",
            'scheduled_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'status' => AppointmentStatus::Scheduled->value
        ])
        ->assertStatus(302)
        ->assertRedirect("/appointments/{$appointment->slug}")
        ->assertSessionHas('success', 'O agendamento foi atualizado com sucesso.');

    $this->assertDatabaseHas('appointments', [
        'id' => $appointment->id,
        'doctor_id' => $doctor->id,
        'situation' => "Updated situation from test by receptionist user",
        'scheduled_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
        'status' => AppointmentStatus::Scheduled->value
    ]);
});

test('doctor can update only their assigned appointments', function () {
    $user = User::factory()->verified()->create(['role' => UserRoles::Doctor->value]);

    $scheduledAt = now()->addDays(2)->format('Y-m-d H:i:s');

    $appointment = Appointment::factory()->create([
        'doctor_id' => $user->id,
    ]);

    actingAs($user)
        ->put("/appointments/{$appointment->slug}", [
            'doctor' => $user->id,
            'situation' => "Updated situation from test by doctor",
            'scheduled_at' => $scheduledAt,
            'status' => AppointmentStatus::Scheduled->value
        ])
        ->assertStatus(302)
        ->assertRedirect("/appointments/{$appointment->slug}")
        ->assertSessionHas('success', 'O agendamento foi atualizado com sucesso.');

    $this->assertDatabaseHas('appointments', [
        'id' => $appointment->id,
        'doctor_id' => $user->id,
        'situation' => "Updated situation from test by doctor",
        'scheduled_at' => $scheduledAt,
        'status' => AppointmentStatus::Scheduled->value
    ]);
});

test('doctor cannot update appointments not assigned to them.', function () {
    $doctorAssigned = User::factory()->verified()->create(['role' => UserRoles::Doctor->value]);
    $doctorNotAssigned = User::factory()->verified()->create(['role' => UserRoles::Doctor->value]);

    $appointment = Appointment::factory()->create([
        'doctor_id' => $doctorAssigned,
        'situation' => "Updated situation from test by doctor",
        'scheduled_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
        'status' => AppointmentStatus::Scheduled->value
    ]);

    actingAs($doctorNotAssigned)
        ->put("/appointments/{$appointment->slug}", [
            'doctor' => $doctorAssigned,
            'situation' => 'Attempted update from test by doctor',
            'scheduled_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'status' => AppointmentStatus::Scheduled->value,
        ])
        ->assertStatus(302)
        ->assertRedirectBack()
        ->assertSessionHasErrors(['doctor', 'scheduled_at', 'status']);

    $this->assertDatabaseMissing('appointments', [
        'situation' => 'Attempted update',
        'scheduled_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
        'status' => AppointmentStatus::Scheduled->value
    ]);
});
