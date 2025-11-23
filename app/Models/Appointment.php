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
}
