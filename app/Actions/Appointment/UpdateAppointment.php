<?php

namespace App\Actions\Appointment;

use App\Models\Appointment;

class UpdateAppointment
{

    public function __invokable(array $input, string $owner_id): Appointment
    {
        return new Appointment();
    }
}
