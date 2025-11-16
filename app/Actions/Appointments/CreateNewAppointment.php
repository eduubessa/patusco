<?php

namespace App\Actions\Appointments;

use App\Models\Appointment;

class CreateNewAppointment
{
    /**
     * Invoke the class instance.
     */
    public function __invoke(): void
    {
        //
        $appointment = Appointment::create([
            'user_id' => auth()->id(),
            ''
        ]);
    }
}
