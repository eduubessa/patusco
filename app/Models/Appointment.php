<?php

namespace App\Models;

use App\Helpers\Enums\AppointmentStatus;
use App\Helpers\Enums\UserRoles;
use Carbon\Carbon;
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
        'id',
    ];

    protected $fillable = [
        'author_id',
        'customer_id',
        'animal_id',
        'doctor_id',
        'situation',
        'schedule_at',
        'status',
        'slug',
        'scheduled_at',
    ];

    protected $casts = [
        'situation' => 'string',
        'schedule_at' => 'datetime',
        'status' => 'string',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeSortByColumn($query, ?string $column = null, ?string $direction = null)
    {
        $allowedColumns = ['created_at', 'updated_at', 'status', 'doctor_id', 'customer_id'];

        if (! $column || ! in_array($column, $allowedColumns, true)) {
            $column = 'updated_at';
        }

        $direction = $direction ?? 'asc';
        $direction = strtolower($direction);
        $direction = $direction === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($column, $direction);
    }

    /**
     * @param $query
     * @param User $user
     * @return \LaravelIdea\Helper\App\Models\_IH_Appointment_QB
     */
    public function scopeForUser($query, User $user)
    {
        return match ($user->role) {
            UserRoles::Admin->value, UserRoles::Receptionist->value => $query,
            UserRoles::Doctor->value => $query->where('doctor_id', $user->id),
            UserRoles::Customer->value => $query->where('customer_id', $user->id),
            default => $query->whereRaw('1 = 0')
        };
    }

    /**
     * @param $query
     * @param string $doctor_id
     * @param Carbon $scheduledAt
     * @return mixed
     */
    public function scopeScheduledForDoctor($query, string $doctor_id, Carbon $scheduledAt): mixed
    {
        return $query->where('doctor_id', $doctor_id)
                ->where('scheduled_at', $scheduledAt)
                ->where('status', AppointmentStatus::Scheduled);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'animal_id');
    }
}
