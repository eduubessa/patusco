<?php

namespace App\Actions\Appointment;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class ListAppointments
{
    public function handle(
        User $user,
        ?string $sortBy,
        ?string $sortDirection,
        int $paginate = 10,
        ?string $species = null,
        ?string $startDate = null,
        ?string $endDate = null,
    ): LengthAwarePaginator {
        return Appointment::with('doctor', 'animal', 'animal.owners')
            ->ofAnimalType($species)
            ->betweenDates($startDate, $endDate)
            ->sortByColumn($sortBy, $sortDirection)
            ->paginate($paginate);
    }
}
