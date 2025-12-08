<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AnimalFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Animal extends Model
{
    /** @use HasFactory<AnimalFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    public $incrementing = false;

    protected $fillable = [
        'name', 'birthday', 'species', 'breed', 'slug', 'doctor_id',
    ];

    protected $casts = [
        'name' => 'string',
        'species' => 'string',
        'breed' => 'string',
        'slug' => 'string',
        'doctor_id' => 'string',
    ];

    protected $appends = [
        'registration_id',
    ];

    protected $with = [
        'doctor',
    ];

    protected $keyType = 'string';

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'animal_user', 'animal_id', 'owner_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'animal_id', 'id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id', 'id');
    }

    protected function registrationId(): Attribute
    {
        return Attribute::make(
            get: fn (?int $value, array $attributes) => $this->formatRegistrationId($attributes['id']),
        );
    }

    private function formatRegistrationId(string $uuid): string
    {
        $groups = explode('-', $uuid);
        if (count($groups) !== 5) {
            return 'ERR-UUID';
        }

        $chars = [];
        $prefix = 'V';

        for ($i = 0; $i < 5; $i++) {
            $groupChars = mb_str_split($groups[$i]);
            if (isset($groupChars[3])) {
                $chars[] = $groupChars[3];
            } else {
                $chars[] = 'X'; // Caractere de fallback
            }
        }

        $rawId = implode('', $chars); // Ex: A53A0

        return "{$prefix}-".mb_strtoupper($rawId); // Ex: V-A53A0
    }
}
