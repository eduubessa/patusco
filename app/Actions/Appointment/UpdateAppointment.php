<?php

declare(strict_types=1);

namespace App\Actions\Appointment;

use App\Models\Appointment;

final class UpdateAppointment
{
    public function update(
        Appointment $appointment,
        array $data
    ): Appointment {
        $update_data = [
            'situation' => $data['situation'],
            'scheduled_at' => $data['scheduled_at'],
            'status' => $data['status'],
        ];

        if (array_key_exists('doctor', $data)) {
            $update_data['doctor_id'] = $data['doctor'];
        }

        $appointment->update($update_data);

        return $appointment;
    }
}
