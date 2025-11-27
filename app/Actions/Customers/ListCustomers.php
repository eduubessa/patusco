<?php

namespace App\Actions\Customers;

use App\Models\User;

class ListCustomers
{
    public function handle(string $sortBy = 'updated_at', string $direction = 'desc', int $perPage = 12)
    {
        //
        $columns = ['id', 'name', 'username', 'email', 'created_at', 'updated_at'];

        $query = User::select($columns)->Customer();
        $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'desc';

        if ($sortBy && in_array($sortBy, $columns) && $direction && in_array($direction, ['asc', 'desc'])) {
            $query->orderBy($sortBy, $direction);
        }

        return $query->paginate($perPage);
    }
}
