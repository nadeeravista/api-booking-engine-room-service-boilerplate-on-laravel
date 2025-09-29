<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomPhoto>
 */
class RoomPhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filename = fake()->word() . '.jpg';
        $s3Key = 'rooms/' . fake()->uuid() . '/' . $filename;

        return [
            'room_id' => Room::factory(),
            'filename' => $filename,
            's3_key' => $s3Key,
            's3_bucket' => 'booking-engine-photos',
            's3_region' => 'us-east-1',
            'mime_type' => 'image/jpeg',
            'file_size' => fake()->numberBetween(50000, 2000000), // 50KB to 2MB
            'url' => 'https://booking-engine-photos.s3.us-east-1.amazonaws.com/' . $s3Key,
            'thumbnail_url' => 'https://booking-engine-photos.s3.us-east-1.amazonaws.com/thumbnails/' . $s3Key,
            'width' => fake()->numberBetween(800, 4000),
            'height' => fake()->numberBetween(600, 3000),
            'sort_order' => fake()->numberBetween(0, 10),
            'is_primary' => fake()->boolean(20), // 20% chance of being primary
            'is_active' => true,
            'metadata' => [
                'camera' => fake()->randomElement(['Canon EOS R5', 'Nikon D850', 'Sony A7R IV']),
                'lens' => fake()->randomElement(['24-70mm f/2.8', '50mm f/1.4', '85mm f/1.8']),
                'iso' => fake()->numberBetween(100, 3200),
                'aperture' => fake()->randomFloat(1, 1.4, 8.0),
                'shutter_speed' => fake()->randomFloat(2, 1 / 1000, 1 / 60),
            ],
        ];
    }
}
