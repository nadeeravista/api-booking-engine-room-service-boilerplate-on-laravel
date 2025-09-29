<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roomTypes = ['Deluxe Suite', 'Standard Room', 'Two Bedroom', 'Single Room', 'Family Suite', 'Executive Room'];
        $sizes = ['Small', 'Medium', 'Large', 'Extra Large'];
        $amenities = [
          ['wifi', 'ac', 'tv'],
          ['wifi', 'ac', 'tv', 'minibar'],
          ['wifi', 'ac', 'tv', 'minibar', 'balcony'],
          ['wifi', 'ac', 'tv', 'minibar', 'balcony', 'kitchenette'],
        ];

        return [
          'name' => fake()->randomElement($roomTypes),
          'description' => fake()->sentence(10),
          'size' => fake()->randomElement($sizes),
          'amenities' => fake()->randomElement($amenities),
          'max_occupancy' => fake()->numberBetween(1, 6),
          'base_occupancy' => fake()->numberBetween(1, 4),
          'property_id' => fake()->uuid(),
          'is_active' => fake()->boolean(90), // 90% chance of being active
        ];
    }
}
