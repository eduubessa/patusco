<?php

namespace App\Actions\Appointment;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\Gate;
use Illuminate\Support\Collection;

class ListAppointments
{
    public function handle(User $user, ?string $sortBy, ?string $sortDirection, int $paginate = 7): Collection
    {
        return Appointment::forUser($user)
            ->sortByColumn($sortBy, $sortDirection)
            ->paginate($paginate);
    }
}
