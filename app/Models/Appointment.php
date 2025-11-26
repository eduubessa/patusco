<?php

namespace App\Models;

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
        'animal_id', 'doctor_id', 'situation', 'schedule_at', 'status'
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

    public function scopeForUser($query, User $user)
    {
        if(in_array($user->role, ['admin', 'receptionist'])) return $query; // Without filter

        if($user->role === 'doctor') return $query->where('doctor_id', $user->id);
        if($user->role === 'customer') return $query->where('author_id', $user->id);

        return $query->whereRaw('0 = 1'); // 0 rows if role invalid!
    }
}
