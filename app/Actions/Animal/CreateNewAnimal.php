<?php

declare(strict_types=1);

namespace App\Actions\Animal;

use App\Helpers\Enums\UserRoles;
use App\Models\Animal;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

final class CreateNewAnimal
{
    /**
     * @param  array  $data  Data from request input
     * @param  User  $creator  Data from user logged
     *
     * @throws Throwable
     */
    public function handle(array $data, User $creator): Animal
    {
        return DB::transaction(function () use ($data, $creator) {
            $animal = Animal::create([
                'name' => $data['name'],
                'sex' => $data['gender'],
                'birthday' => Carbon::parse($data['birthday'])->format('Y-m-d'),
                'species' => $data['species'],
                'breed' => $data['breed'],
                'slug' => $this->generateUniqueSlug(),
            ]);

            if ($creator->role === UserRoles::Customer->value) {
                $animal->owners()->attach($creator->id, ['main_owner' => true]);
            } else {
                $owner = User::where('username', $data['owner'])
                    ->where('role', UserRoles::Customer->value)
                    ->firstOrFail();

                $animal->owners()->attach($owner->id, ['main_owner' => true]);
            }

            return $animal;
        });
    }

    private function generateUniqueSlug(): string
    {
        do {
            $slug = mb_substr(hash('xxh3', microtime(true).Str::uuid()), 0, 12);
        } while (Animal::where('slug', $slug)->exists());

        return $slug;
    }
}
