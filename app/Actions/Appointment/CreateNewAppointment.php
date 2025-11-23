<?php

namespace App\Actions\Appointment;

use App\Helpers\Enums\AppointmentStatus;
use App\Models\Appointment;
use Carbon\Carbon;

class CreateNewAppointment
{
    public function create(array $input, string $owner_id): Appointment
    {
        $schedule_at = Carbon::parse($input['schedule_at']);

        return Appointment::create([
            'owner_id' => $owner_id,
            'animal_id' => $input['animal_id'],
            'situation' => $input['situation'],
            'schedule_at' => $schedule_at,
            'status' => AppointmentStatus::Pending->value
        ]);
    }
}
