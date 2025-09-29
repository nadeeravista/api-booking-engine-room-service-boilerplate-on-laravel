<?php

namespace Database\Factories;

use App\Models\RoomRatePlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomPrice>
 */
class RoomPriceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $currencies = ['USD', 'EUR', 'GBP', 'CAD', 'AUD'];
        $basePrice = fake()->numberBetween(50, 500);

        // Add some variation to prices (weekend/weekday, season, etc.)
        $priceMultiplier = fake()->randomFloat(2, 0.7, 2.0);
        $finalPrice = round($basePrice * $priceMultiplier, 2);

        return [
            'room_rate_plan_id' => RoomRatePlan::factory(),
            'date' => fake()->dateTimeBetween('-6 months', '+1 year'),
            'price' => $finalPrice,
            'currency' => fake()->randomElement($currencies),
            'is_available' => fake()->boolean(80), // 80% chance of being available
        ];
    }
}
