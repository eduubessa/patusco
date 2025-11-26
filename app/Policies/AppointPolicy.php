<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AppointPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'receptionist', 'doctor', 'customer']);
    }

    public function viewAll(User $user): bool
    {
        return in_array($user->role, ['admin', 'receptionist']);
    }

    public function view(User $user, Appointment $appointment): bool
    {
        if($this->viewAll($user)) return true;
        if($user->role === 'doctor') return $appointment->doctor_id === $user->id;
        if($user->role === 'customer') return $appointment->customer_id === $user->id;

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'receptionist', 'customer']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Appointment $appointment): bool
    {
        return in_array($user->role, ['admin', 'receptionist']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->role === 'admin' || $appointment->author_id === $user->id;
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
