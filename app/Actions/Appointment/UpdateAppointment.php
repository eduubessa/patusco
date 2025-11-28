<?php

namespace App\Actions\Appointment;

use App\Models\Appointment;

class UpdateAppointment
{
    public function update(Appointment $appointment, array $data): Appointment
    {
        $appointment->update([
            'doctor_id' => $data['doctor'],
            'situation' => $data['situation'],
            'scheduled_at' => $data['scheduled_at'],
            'status' => $data['status'],
        ]);

        return $appointment;
    }
}
