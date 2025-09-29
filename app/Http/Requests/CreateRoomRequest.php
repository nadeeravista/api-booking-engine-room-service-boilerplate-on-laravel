<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam name string required The name of the room. Example: Deluxe Suite
 * @bodyParam description string The description of the room. Example: Spacious room with ocean view
 * @bodyParam size string The size of the room. Example: Large
 * @bodyParam amenities array The amenities available in the room. Example: ["wifi", "ac", "tv"]
 * @bodyParam max_occupancy integer required The maximum occupancy of the room. Example: 4
 * @bodyParam base_occupancy integer required The base occupancy of the room. Example: 2
 * @bodyParam property_id string required The property ID. Example: 550e8400-e29b-41d4-a716-446655440001
 * @bodyParam is_active boolean Whether the room is active. Example: true
 * @bodyParam photos array Optional array of photo objects. Example: [{"filename": "room1.jpg", "url": "https://s3...", "is_primary": true}]
 */
class CreateRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'size' => 'nullable|string|max:100',
            'amenities' => 'nullable|array',
            'max_occupancy' => 'required|integer|min:1',
            'base_occupancy' => 'required|integer|min:1',
            'property_id' => 'required|uuid',
            'is_active' => 'boolean',
            'photos' => 'nullable|array',
            'photos.*.filename' => 'required_with:photos|string|max:255',
            'photos.*.url' => 'required_with:photos|string|url',
            'photos.*.thumbnail_url' => 'nullable|string|url',
            'photos.*.width' => 'nullable|integer|min:1',
            'photos.*.height' => 'nullable|integer|min:1',
            'photos.*.mime_type' => 'nullable|string|max:100',
            'photos.*.file_size' => 'nullable|integer|min:1',
            'photos.*.sort_order' => 'nullable|integer|min:0',
            'photos.*.is_primary' => 'nullable|boolean',
        ];
    }
}
