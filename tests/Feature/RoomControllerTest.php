<?php

namespace Tests\Feature;

use App\Contracts\RoomServiceInterface;
use App\Models\Room;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class RoomControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_rooms_returns_successful_response(): void
    {
        $propertyId = '550e8400-e29b-41d4-a716-446655440001';

        $room = new Room();
        $room->id = 'room-123';
        $room->name = 'Deluxe Suite';
        $room->property_id = $propertyId;
        $room->is_active = true;
        $room->description = 'A beautiful suite';
        $room->max_occupancy = 4;
        $room->base_occupancy = 2;
        $room->created_at = now();
        $room->updated_at = now();
        $room->setRelation('photos', new Collection());
        $room->setRelation('primaryPhoto', null);

        $mockService = Mockery::mock(RoomServiceInterface::class);
        $mockService->shouldReceive('getRoomsByProperty')
            ->once()
            ->with($propertyId)
            ->andReturn(new Collection([$room]));

        $this->app->instance(RoomServiceInterface::class, $mockService);

        $response = $this->getJson("/api/rooms?property_id={$propertyId}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'description',
                    'max_occupancy',
                    'base_occupancy',
                    'property_id',
                    'is_active',
                    'photos',
                    'primary_photo',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_get_rooms_requires_property_id(): void
    {
        $response = $this->getJson('/api/rooms');

        $response->assertStatus(400)
            ->assertJson(['error' => 'property_id is required']);
    }

    public function test_create_room_with_photos(): void
    {
        $roomData = [
            'name' => 'Deluxe Suite',
            'description' => 'A beautiful deluxe suite with ocean view',
            'max_occupancy' => 4,
            'base_occupancy' => 2,
            'property_id' => '550e8400-e29b-41d4-a716-446655440001',
            'is_active' => true,
            'photos' => [
                [
                    'filename' => 'room1.jpg',
                    'url' => 'https://s3.amazonaws.com/bucket/room1.jpg',
                    'is_primary' => true,
                ],
            ],
        ];

        $room = new Room();
        $room->id = 'room-123';
        $room->name = $roomData['name'];
        $room->description = $roomData['description'];
        $room->property_id = $roomData['property_id'];
        $room->is_active = $roomData['is_active'];
        $room->max_occupancy = $roomData['max_occupancy'];
        $room->base_occupancy = $roomData['base_occupancy'];
        $room->created_at = now();
        $room->updated_at = now();
        $room->setRelation('photos', new Collection());
        $room->setRelation('primaryPhoto', null);

        $mockService = Mockery::mock(RoomServiceInterface::class);
        $mockService->shouldReceive('createRoom')
            ->once()
            ->with($roomData)
            ->andReturn($room);

        $this->app->instance(RoomServiceInterface::class, $mockService);

        $response = $this->postJson('/api/rooms', $roomData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name',
                'description',
                'max_occupancy',
                'base_occupancy',
                'property_id',
                'is_active',
                'photos',
                'primary_photo',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_create_room_validation_errors(): void
    {
        $response = $this->postJson('/api/rooms', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'property_id']);
    }

    public function test_get_room_by_id(): void
    {
        $roomId = 'room-123';

        $room = new Room();
        $room->id = $roomId;
        $room->name = 'Test Room';
        $room->description = 'A test room';
        $room->property_id = 'property-123';
        $room->is_active = true;
        $room->max_occupancy = 2;
        $room->base_occupancy = 1;
        $room->created_at = now();
        $room->updated_at = now();
        $room->setRelation('photos', new Collection());
        $room->setRelation('primaryPhoto', null);

        $mockService = Mockery::mock(RoomServiceInterface::class);
        $mockService->shouldReceive('getRoom')
            ->once()
            ->with($roomId)
            ->andReturn($room);

        $this->app->instance(RoomServiceInterface::class, $mockService);

        $response = $this->getJson("/api/rooms/{$roomId}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'description',
                'max_occupancy',
                'base_occupancy',
                'property_id',
                'is_active',
                'photos',
                'primary_photo',
                'created_at',
                'updated_at',
            ])
            ->assertJson([
                'id' => $roomId,
                'name' => 'Test Room',
            ]);
    }

    public function test_get_room_not_found(): void
    {
        $roomId = 'non-existent-id';

        $mockService = Mockery::mock(RoomServiceInterface::class);
        $mockService->shouldReceive('getRoom')
            ->once()
            ->with($roomId)
            ->andReturn(null);

        $this->app->instance(RoomServiceInterface::class, $mockService);

        $response = $this->getJson("/api/rooms/{$roomId}");

        $response->assertStatus(404)
            ->assertJson(['error' => 'Room not found']);
    }

    public function test_update_room(): void
    {
        $roomId = 'room-123';
        $updateData = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'max_occupancy' => 6,
        ];

        $room = new Room();
        $room->id = $roomId;
        $room->name = $updateData['name'];
        $room->description = $updateData['description'];
        $room->property_id = 'property-123';
        $room->is_active = true;
        $room->max_occupancy = $updateData['max_occupancy'];
        $room->base_occupancy = 1;
        $room->created_at = now();
        $room->updated_at = now();
        $room->setRelation('photos', new Collection());
        $room->setRelation('primaryPhoto', null);

        $mockService = Mockery::mock(RoomServiceInterface::class);
        $mockService->shouldReceive('updateRoom')
            ->once()
            ->with($roomId, $updateData)
            ->andReturn($room);

        $this->app->instance(RoomServiceInterface::class, $mockService);

        $response = $this->putJson("/api/rooms/{$roomId}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'description',
                'max_occupancy',
                'base_occupancy',
                'property_id',
                'is_active',
                'photos',
                'primary_photo',
                'created_at',
                'updated_at',
            ])
            ->assertJson([
                'id' => $roomId,
                'name' => $updateData['name'],
                'description' => $updateData['description'],
                'max_occupancy' => $updateData['max_occupancy'],
            ]);
    }

    public function test_update_room_not_found(): void
    {
        $roomId = 'non-existent-id';
        $updateData = [
            'name' => 'Updated Name',
        ];

        $mockService = Mockery::mock(RoomServiceInterface::class);
        $mockService->shouldReceive('updateRoom')
            ->once()
            ->with($roomId, $updateData)
            ->andReturn(null);

        $this->app->instance(RoomServiceInterface::class, $mockService);

        $response = $this->putJson("/api/rooms/{$roomId}", $updateData);

        $response->assertStatus(404)
            ->assertJson(['error' => 'Room not found']);
    }

    public function test_delete_room(): void
    {
        $roomId = 'room-123';

        $mockService = Mockery::mock(RoomServiceInterface::class);
        $mockService->shouldReceive('deleteRoom')
            ->once()
            ->with($roomId)
            ->andReturn(true);

        $this->app->instance(RoomServiceInterface::class, $mockService);

        $response = $this->deleteJson("/api/rooms/{$roomId}");

        $response->assertStatus(204);
    }

    public function test_delete_room_not_found(): void
    {
        $roomId = 'non-existent-id';

        $mockService = Mockery::mock(RoomServiceInterface::class);
        $mockService->shouldReceive('deleteRoom')
            ->once()
            ->with($roomId)
            ->andReturn(false);

        $this->app->instance(RoomServiceInterface::class, $mockService);

        $response = $this->deleteJson("/api/rooms/{$roomId}");

        $response->assertStatus(404)
            ->assertJson(['error' => 'Room not found']);
    }
}
