<?php

namespace App\Actions\Animal;

use App\Models\Animal;
use Illuminate\Pagination\LengthAwarePaginator;

class ListAnimals
{
    /**
     * Create a new class instance.
     */
    public function handle(string $sortBy = 'updated_at', string $direction = 'desc', int $perPage = 7): LengthAwarePaginator
    {
        //
        $columns = ['id', 'name', 'birthday', 'species', 'breed'];
        $query = Animal::with('owners');
        $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'desc';

        if ($sortBy && in_array($sortBy, $columns) && $direction && in_array($direction, ['asc', 'desc'])) {
            $query->orderBy($sortBy, $direction);
        }

        return $query->paginate($perPage);

    }
}
