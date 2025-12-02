<?php

namespace App\Actions\Animal;

use App\Actions\Appointment\UpdateAppointment;
use App\Models\Animal;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Carbon;

class UpdateAnimal
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

        if(!$animal->update($update_data)){
            return false;
        }

        return $animal;

    }
}
