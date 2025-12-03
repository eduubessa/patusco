<?php

namespace App\Actions\Appointment;

use App\Helpers\Enums\UserRoles;
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
        $query = Appointment::with('doctor', 'animal', 'animal.owners')
            ->ofAnimalType($species)
            ->betweenDates($startDate, $endDate);

        if($user->hasRole(UserRoles::Customer->value)){
            $query->where('customer_id', $user->id);
        }else if($user->hasRole(UserRoles::Doctor->value)){
            $query->where('doctor_id', $user->id);
        }

        return $query->sortByColumn($sortBy, $sortDirection)
            ->paginate($paginate);
    }
}
