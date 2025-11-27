<?php

namespace App\Actions\Appointment;

use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateNewAppointment
{
    public function handle(array $data): Appointment
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = $this->generateUniqueSlug();

            return Appointment::create([
                'author_id' => $data['author'],
                'customer_id' => $data['customer'],
                'doctor_id' => $data['doctor'],
                'animal_id' => $data['animal'],
                'situation' => $data['situation'],
                'scheduled_at' => $data['scheduled_at'],
                'status' => $data['status'],
                'slug' => $data['slug'],
            ]);
        });
    }

    protected function generateUniqueSlug(): string
    {
        do {
            $slug = substr(hash('xxh3', microtime(true).Str::uuid()), 0, 12);
        } while (Appointment::where('slug', $slug)->exists());

        return $slug;
    }
}
