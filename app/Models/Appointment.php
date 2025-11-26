<?php

namespace App\Models;

use App\Helpers\Enums\UserRoles;
use Couchbase\Role;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    /** @use HasFactory<\Database\Factories\AppointmentFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected $hidden = [
        'id'
    ];

    protected $fillable = [
        'customer_id', 'animal_id', 'doctor_id', 'situation', 'schedule_at', 'status'
    ];

    protected $casts = [
        'situation' => 'string',
        'schedule_at' => 'datetime',
        'status' => 'string'
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'animal_id');
    }

    public function scopeSortByColumn($query, ?string $column, string $direction = "asc")
    {
        $allowedColumns = ['created_at','updated_at', 'status', 'doctor_id', 'customer_id'];

        if (!$column || !in_array($column, $allowedColumns, true)) {
            $column = 'updated_at';
        }

        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($column, $direction);
    }

    public function scopeForUser($query, User $user)
    {
        return match($user->role) {
            UserRoles::Admin->value, UserRoles::Receptionist->value => $query,
            UserRoles::Doctor->value => $query->where('doctor_id', $user->id),
            UserRoles::Customer->value => $query->where('customer_id', $user->id),
            default => $query->whereRaw('0 = 1')
        };
    }
}
