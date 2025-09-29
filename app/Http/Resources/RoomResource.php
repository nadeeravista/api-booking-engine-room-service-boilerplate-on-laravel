<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'size' => $this->size,
            'amenities' => $this->amenities,
            'max_occupancy' => $this->max_occupancy,
            'base_occupancy' => $this->base_occupancy,
            'property_id' => $this->property_id,
            'is_active' => $this->is_active,
            'photos' => $this->whenLoaded('photos', function () {
                return $this->photos->map(function ($photo) {
                    return [
                        'id' => $photo->id,
                        'filename' => $photo->filename,
                        'url' => $photo->url,
                        'thumbnail_url' => $photo->thumbnail_url,
                        'width' => $photo->width,
                        'height' => $photo->height,
                        'sort_order' => $photo->sort_order,
                        'is_primary' => $photo->is_primary,
                        'mime_type' => $photo->mime_type,
                        'file_size' => $photo->file_size,
                        'upload_status' => $photo->upload_status,
                        'processing_status' => $photo->processing_status,
                        'is_moderated' => $photo->is_moderated,
                    ];
                });
            }),
            'primary_photo' => $this->whenLoaded('primaryPhoto', function () {
                return $this->primaryPhoto->first() ? [
                    'id' => $this->primaryPhoto->first()->id,
                    'filename' => $this->primaryPhoto->first()->filename,
                    'url' => $this->primaryPhoto->first()->url,
                    'thumbnail_url' => $this->primaryPhoto->first()->thumbnail_url,
                    'width' => $this->primaryPhoto->first()->width,
                    'height' => $this->primaryPhoto->first()->height,
                ] : null;
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
