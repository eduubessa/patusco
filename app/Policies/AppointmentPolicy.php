<?php

namespace App\Policies;

use App\Helpers\Enums\UserRoles;
use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    private const FULL_ACCESS = [UserRoles::Admin->value, UserRoles::Receptionist->value];

    private const ALL_ROLES = [UserRoles::Admin->value, UserRoles::Doctor->value, UserRoles::Receptionist->value, UserRoles::Doctor->value];

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'receptionist', 'doctor', 'customer']);
    }

    public function view(User $user, Appointment $appointment): bool
    {
        return match ($user->role) {
            UserRoles::Admin->value, UserRoles::Receptionist->value => true,
            UserRoles::Doctor->value => $appointment->doctor_id === $user->id,
            UserRoles::Customer->value => $appointment->customer_id === $user->id,
            default => false
        };
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'receptionist', 'customer'], true);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Appointment $appointment): bool
    {
        return match($user->role) {
            UserRoles::Admin->value, UserRoles::Receptionist->value, => true,
            UserRoles::Doctor->value => $appointment->doctor_id === $user->id,
            default => false
        };
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->role === UserRoles::Admin;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Appointment $appointment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Appointment $appointment): bool
    {
        return false;
    }
}
