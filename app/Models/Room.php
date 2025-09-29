<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasUuids;
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'size',
        'amenities',
        'max_occupancy',
        'base_occupancy',
        'property_id',
        'is_active',
    ];

    protected $casts = [
        'amenities' => 'array',
        'is_active' => 'boolean',
        'max_occupancy' => 'integer',
        'base_occupancy' => 'integer',
    ];

    public function ratePlans(): HasMany
    {
        return $this->hasMany(RoomRatePlan::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(RoomPhoto::class);
    }

    public function primaryPhoto(): HasMany
    {
        return $this->hasMany(RoomPhoto::class)->where('is_primary', true);
    }
}
