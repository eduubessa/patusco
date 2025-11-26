<?php

namespace App\Actions\Appointment;

use App\Models\Appointment;
use Illuminate\Auth\Access\Gate;
use Illuminate\Support\Collection;

class ListAppointments
{
    public function handle(User $user): Collection
    {
        if(Gate::allows('appointment-list', Appointment::class)) return Appointment::all();
        return Appointment::forUser($user)->latest()->get();
    }
}
