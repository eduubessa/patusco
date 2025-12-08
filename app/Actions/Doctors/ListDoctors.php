<?php

declare(strict_types=1);

namespace App\Actions\Doctors;

use App\Models\User;

final class ListDoctors
{
    /**
     * Create a new class instance.
     */
    public function handle(string $sortBy = 'updated_at', string $direction = 'desc', int $perPage = 20)
    {
        //
        $columns = ['id', 'name', 'username', 'email', 'created_at', 'updated_at'];

        $query = User::select($columns)->Doctor();
        $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'desc';

        if ($sortBy && in_array($sortBy, $columns) && $direction && in_array($direction, ['asc', 'desc'])) {
            $query->orderBy($sortBy, $direction);
        }

        return $query->paginate($perPage);
    }
}
