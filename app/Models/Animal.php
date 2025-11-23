<?php

namespace App\Models;

use Database\Factories\AnimalFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Animal extends Model
{
    /** @use HasFactory<AnimalFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name', 'species', 'breed', 'doctor_id'
    ];

    protected $casts = [
        'name' => 'string',
        'species' => 'string',
        'breed' => 'string',
        'doctor_id' => 'string'
    ];

    protected $with = [
        'doctor'
    ];

    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(User::class, "animal_user", "animal_id", "owner_id");
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id', 'id');
    }
}
