<?php

namespace App\Actions\Appointment;

use App\Models\Appointment;
use Illuminate\Support\Collection;

class ListAppointments
{
    public function handle(): Collection
    {
        return Appointment::latest()->get();
    }
}
