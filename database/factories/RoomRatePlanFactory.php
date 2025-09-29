<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomRatePlan>
 */
class RoomRatePlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ratePlanNames = [
          'Bed & Breakfast',
          'Full Board',
          'Half Board',
          'Room Only',
          'All Inclusive',
          'Continental Breakfast',
          'American Breakfast',
          'Dinner Included',
        ];

        $descriptions = [
          'Includes breakfast for all guests',
          'All meals included',
          'Breakfast and dinner included',
          'Room accommodation only',
          'All meals, drinks, and activities included',
          'Light breakfast included',
          'Full American breakfast included',
          'Three-course dinner included',
        ];

        $validFrom = fake()->dateTimeBetween('-1 year', '+1 year');
        $validTo = fake()->dateTimeBetween($validFrom, '+2 years');

        return [
          'name' => fake()->randomElement($ratePlanNames),
          'description' => fake()->randomElement($descriptions),
          'base_occupancy' => fake()->numberBetween(1, 4),
          'room_id' => Room::factory(),
          'is_active' => fake()->boolean(85), // 85% chance of being active
          'valid_from' => $validFrom,
          'valid_to' => $validTo,
        ];
    }
}
