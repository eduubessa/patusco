<?php

namespace App\Policies;

use App\Helpers\Enums\UserRoles;
use App\Models\Animal;
use App\Models\User;

class AnimalPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            UserRoles::Admin,
            UserRoles::Receptionist,
            UserRoles::Doctor
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Animal $animal): bool
    {
        return match($user->role) {
            UserRoles::Admin->value => true,
            UserRoles::Receptionist->value => true,
            UserRoles::Doctor->value => true,
            UserRoles::Customer->value => $animal->owners->contains('id', $user->id),
            default => false
        };
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return match($user->role) {
            UserRoles::Admin->value => true,
            UserRoles::Receptionist->value => true,
            UserRoles::Customer->value => true,
            default => false
        };
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Animal $animal): bool
    {
        return match($user->role) {
            UserRoles::Admin->value => true,
            UserRoles::Receptionist->value => true,
            UserRoles::Doctor->value => true,
            UserRoles::Customer->value => $animal->owners->containers('id', $user->id),
            default => false
        };
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Animal $animal): bool
    {
        return match ($user->role) {
            UserRoles::Admin->value => true,
            UserRoles::Receptionist->value => true,
            UserRoles::Customer->value => $animal->owners->contains('id', $user->id),
            default => false
        };
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Animal $animal): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Animal $animal): bool
    {
        return false;
    }
}
