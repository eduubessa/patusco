<?php

use App\Helpers\Enums\AppointmentStatus;
use App\Helpers\Enums\UserRoles;
use App\Models\Appointment;
use App\Models\User;
use App\Rules\MaxAppointmentsPerDoctorPerSlot;

it('passes if doctor has less than max appointments per slot', function () {
    $doctor = User::factory()->verified()->create([
        'role' => UserRoles::Doctor->value,
    ]);

    $scheduledAt = now()->addDay()->format("Y-m-d H:i:s");

    Appointment::factory()->count(2)->create([
        'doctor_id' => $doctor->id,
        'scheduled_at' => $scheduledAt,
        'status' => AppointmentStatus::Scheduled->value,
    ]);

    $rule = new MaxAppointmentsPerDoctorPerSlot(5, $scheduledAt);

    $rule->validate("doctor", $doctor->id, function ($message) {
        $this->fail("Validation should pass but failed with message: {$message}");
    });
});
