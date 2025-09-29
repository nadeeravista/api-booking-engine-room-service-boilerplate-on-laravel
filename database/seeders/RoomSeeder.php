<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomPhoto;
use App\Models\RoomPrice;
use App\Models\RoomRatePlan;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create just 2 rooms with minimal data
        $propertyId = '550e8400-e29b-41d4-a716-446655440001'; // Single property

        // Get existing rooms or create new ones
        $rooms = Room::where('property_id', $propertyId)->get();

        if ($rooms->count() == 0) {
            // Create 2 rooms if none exist
            $rooms = Room::factory()
                ->count(2)
                ->create(['property_id' => $propertyId]);
        }

        foreach ($rooms as $room) {
            // Create 2 rate plans per room
            $ratePlans = RoomRatePlan::factory()
                ->count(2)
                ->create(['room_id' => $room->id]);

            foreach ($ratePlans as $ratePlan) {
                // Create prices for just the next 7 days
                $startDate = now();
                $endDate = now()->addDays(7);

                for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                    RoomPrice::factory()->create([
                        'room_rate_plan_id' => $ratePlan->id,
                        'date' => $date->format('Y-m-d'),
                        'price' => fake()->numberBetween(80, 200),
                        'currency' => 'USD',
                    ]);
                }
            }

            // Create 3-5 photos per room (only if no photos exist)
            if ($room->photos()->count() == 0) {
                $photoCount = fake()->numberBetween(3, 5);
                $photos = RoomPhoto::factory()
                    ->count($photoCount)
                    ->create(['room_id' => $room->id]);

                // Set one photo as primary
                if ($photos->count() > 0) {
                    $photos->first()->update(['is_primary' => true]);
                }
            }
        }

        $this->command->info('Created 2 rooms with 2 rate plans each, 7 days of prices, and 3-5 photos per room!');
    }
}
