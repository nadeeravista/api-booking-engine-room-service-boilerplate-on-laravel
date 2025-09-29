<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomRatePlan extends Model
{
    use HasUuids;
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'base_occupancy',
        'room_id',
        'is_active',
        'valid_from',
        'valid_to',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'base_occupancy' => 'integer',
        'valid_from' => 'date',
        'valid_to' => 'date',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(RoomPrice::class);
    }
}
