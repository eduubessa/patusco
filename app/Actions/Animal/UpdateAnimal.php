<?php

declare(strict_types=1);

namespace App\Actions\Animal;

use App\Models\Animal;

final class UpdateAnimal
{
    /**
     * Create a new class instance.
     */
    public function handle(
        Animal $animal,
        array $data
    ): Animal|bool {
        //
        $update_data = [
            'name' => $data['name'],
            'sex' => $data['gender'],
            'birthday' => $data['birthday'],
            'species' => $data['species'],
            'breed' => $data['breed'],
        ];

        if (! $animal->update($update_data)) {
            return false;
        }

        return $animal;

    }
}
