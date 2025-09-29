<?php

namespace App\Models;

use App\Events\RoomRateChanged;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomPrice extends Model
{
    use HasUuids;
    use HasFactory;

    protected $fillable = [
        'room_rate_plan_id',
        'date',
        'price',
        'currency',
        'is_available',
    ];

    protected $casts = [
        'date' => 'date',
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::updated(function (RoomPrice $roomPrice) {
            if ($roomPrice->wasChanged(['price', 'currency', 'is_available'])) {
                event(new RoomRateChanged(
                    $roomPrice,
                    $roomPrice->getOriginal(),
                    $roomPrice->getChanges()
                ));
            }
        });
    }

    public function ratePlan(): BelongsTo
    {
        return $this->belongsTo(RoomRatePlan::class);
    }
}
