<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomPhoto extends Model
{
    use HasUuids;
    use HasFactory;

    protected $fillable = [
      'room_id',
      'filename',
      's3_key',
      's3_bucket',
      's3_region',
      'mime_type',
      'file_size',
      'url',
      'thumbnail_url',
      'width',
      'height',
      'sort_order',
      'is_primary',
      'is_active',
      'metadata',
      'upload_status',
      'processing_status',
      'is_moderated',
      'moderated_at',
      'moderation_result',
    ];

    protected $casts = [
      'file_size' => 'integer',
      'width' => 'integer',
      'height' => 'integer',
      'sort_order' => 'integer',
      'is_primary' => 'boolean',
      'is_active' => 'boolean',
      'metadata' => 'array',
      'is_moderated' => 'boolean',
      'moderated_at' => 'datetime',
      'moderation_result' => 'array',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
