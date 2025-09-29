<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam name string The name of the room. Example: Deluxe Suite
 * @bodyParam description string The description of the room. Example: Spacious room with ocean view
 * @bodyParam size string The size of the room. Example: Large
 * @bodyParam amenities array The amenities available in the room. Example: ["wifi", "ac", "tv"]
 * @bodyParam max_occupancy integer The maximum occupancy of the room. Example: 4
 * @bodyParam base_occupancy integer The base occupancy of the room. Example: 2
 * @bodyParam is_active boolean Whether the room is active. Example: true
 */
class UpdateRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'size' => 'nullable|string|max:100',
            'amenities' => 'nullable|array',
            'max_occupancy' => 'sometimes|integer|min:1',
            'base_occupancy' => 'sometimes|integer|min:1',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
