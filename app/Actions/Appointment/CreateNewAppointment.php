<?php

declare(strict_types=1);

namespace App\Actions\Appointment;

use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class CreateNewAppointment
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

    private function generateUniqueSlug(): string
    {
        do {
            $slug = mb_substr(hash('xxh3', microtime(true).Str::uuid()), 0, 12);
        } while (Appointment::where('slug', $slug)->exists());

        return $slug;
    }
}
