<?php

namespace Tests\Unit\Services;

use App\Contracts\PhotoServiceInterface;
use App\Contracts\PriceRepositoryInterface;
use App\Contracts\RoomRepositoryInterface;
use App\Models\Room;
use App\Models\RoomPrice;
use App\Models\RoomRatePlan;
use App\Services\RoomService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class RoomServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_room_with_photos(): void
    {
        $roomData = [
            'name' => 'Deluxe Suite',
            'description' => 'A beautiful deluxe suite',
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

        $expectedRoom = Mockery::mock(Room::class);
        $expectedRoom->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn('room-123');
        $expectedRoom->shouldReceive('getAttribute')
            ->with('name')
            ->andReturn($roomData['name']);
        $expectedRoom->shouldReceive('getAttribute')
            ->with('description')
            ->andReturn($roomData['description']);
        $expectedRoom->shouldReceive('getAttribute')
            ->with('property_id')
            ->andReturn($roomData['property_id']);

        $freshRoom = new Room();
        $freshRoom->id = 'room-123';
        $freshRoom->name = $roomData['name'];
        $freshRoom->description = $roomData['description'];
        $freshRoom->property_id = $roomData['property_id'];
        $freshRoom->setRelation('photos', new Collection());
        $freshRoom->setRelation('primaryPhoto', null);

        $mockRepository = Mockery::mock(RoomRepositoryInterface::class);
        $mockRepository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) use ($roomData) {
                return $data['name'] === $roomData['name'] &&
                    $data['description'] === $roomData['description'] &&
                    $data['property_id'] === $roomData['property_id'];
            }))
            ->andReturn($expectedRoom);

        $mockRepository->shouldReceive('findWithPhotos')
            ->once()
            ->with($expectedRoom->id)
            ->andReturn($freshRoom);

        $mockPhotoService = Mockery::mock(PhotoServiceInterface::class);
        $mockPhotoService->shouldReceive('createRoomPhotos')
            ->once()
            ->with($expectedRoom->id, $roomData['photos']);

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $roomService = new RoomService(
            $mockRepository,
            $mockPhotoService,
            Mockery::mock(PriceRepositoryInterface::class),
            Mockery::mock(RoomRatePlan::class)
        );

        $result = $roomService->createRoom($roomData);

        $this->assertEquals('room-123', $result->id);
        $this->assertEquals($roomData['name'], $result->name);
        $this->assertEquals($roomData['description'], $result->description);
        $this->assertEquals($roomData['property_id'], $result->property_id);
    }

    public function test_create_room_without_photos(): void
    {
        $roomData = [
            'name' => 'Standard Room',
            'description' => 'A standard room',
            'property_id' => '550e8400-e29b-41d4-a716-446655440001',
            'is_active' => true,
        ];

        $expectedRoom = Mockery::mock(Room::class);
        $expectedRoom->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn('room-456');
        $expectedRoom->shouldReceive('getAttribute')
            ->with('name')
            ->andReturn($roomData['name']);

        $freshRoom = new Room();
        $freshRoom->id = 'room-456';
        $freshRoom->name = $roomData['name'];
        $freshRoom->setRelation('photos', new Collection());
        $freshRoom->setRelation('primaryPhoto', null);

        $mockRepository = Mockery::mock(RoomRepositoryInterface::class);
        $mockRepository->shouldReceive('create')
            ->once()
            ->with($roomData)
            ->andReturn($expectedRoom);

        $mockRepository->shouldReceive('findWithPhotos')
            ->once()
            ->with($expectedRoom->id)
            ->andReturn($freshRoom);

        $mockPhotoService = Mockery::mock(PhotoServiceInterface::class);
        $mockPhotoService->shouldNotReceive('createRoomPhotos');

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $roomService = new RoomService(
            $mockRepository,
            $mockPhotoService,
            Mockery::mock(PriceRepositoryInterface::class),
            Mockery::mock(RoomRatePlan::class)
        );

        $result = $roomService->createRoom($roomData);

        $this->assertEquals('room-456', $result->id);
        $this->assertEquals($roomData['name'], $result->name);
    }

    public function test_get_rooms_by_property(): void
    {
        $propertyId = '550e8400-e29b-41d4-a716-446655440001';
        $expectedRooms = new Collection([
            (object) ['id' => 'room-1', 'name' => 'Room 1'],
            (object) ['id' => 'room-2', 'name' => 'Room 2'],
        ]);

        $mockRepository = Mockery::mock(RoomRepositoryInterface::class);
        $mockRepository->shouldReceive('findByProperty')
            ->once()
            ->with($propertyId)
            ->andReturn($expectedRooms);

        $roomService = new RoomService(
            $mockRepository,
            Mockery::mock(PhotoServiceInterface::class),
            Mockery::mock(PriceRepositoryInterface::class),
            Mockery::mock(RoomRatePlan::class)
        );

        $result = $roomService->getRoomsByProperty($propertyId);

        $this->assertEquals($expectedRooms, $result);
        $this->assertCount(2, $result);
    }

    public function test_create_rate_plan(): void
    {
        $ratePlanData = [
            'name' => 'Standard Rate',
            'description' => 'Standard room rate',
            'room_id' => 'room-123',
            'is_active' => true,
        ];

        $expectedRatePlan = new RoomRatePlan();
        $expectedRatePlan->id = 'rate-123';
        $expectedRatePlan->name = $ratePlanData['name'];

        $mockRatePlan = Mockery::mock(RoomRatePlan::class);
        $mockRatePlan->shouldReceive('create')
            ->once()
            ->with($ratePlanData)
            ->andReturn($expectedRatePlan);

        $roomService = new RoomService(
            Mockery::mock(RoomRepositoryInterface::class),
            Mockery::mock(PhotoServiceInterface::class),
            Mockery::mock(PriceRepositoryInterface::class),
            $mockRatePlan
        );

        $result = $roomService->createRatePlan($ratePlanData);

        $this->assertEquals($expectedRatePlan->id, $result->id);
        $this->assertEquals($expectedRatePlan->name, $result->name);
    }

    public function test_create_price(): void
    {
        $priceData = [
            'room_rate_plan_id' => 'rate-123',
            'date' => '2024-01-01',
            'price' => 150.00,
            'currency' => 'USD',
        ];

        $expectedPrice = new RoomPrice();
        $expectedPrice->id = 'price-123';
        $expectedPrice->price = $priceData['price'];

        $mockPriceRepository = Mockery::mock(PriceRepositoryInterface::class);
        $mockPriceRepository->shouldReceive('create')
            ->once()
            ->with($priceData)
            ->andReturn($expectedPrice);

        $roomService = new RoomService(
            Mockery::mock(RoomRepositoryInterface::class),
            Mockery::mock(PhotoServiceInterface::class),
            $mockPriceRepository,
            Mockery::mock(RoomRatePlan::class)
        );

        $result = $roomService->createPrice($priceData);

        $this->assertEquals($expectedPrice->id, $result->id);
        $this->assertEquals($expectedPrice->price, $result->price);
    }

    public function test_get_prices_for_date_range(): void
    {
        $ratePlanId = 'rate-123';
        $startDate = '2024-01-01';
        $endDate = '2024-01-07';
        $expectedPrices = new Collection([
            (object) ['id' => 'price-1', 'date' => '2024-01-01', 'price' => 150.00],
            (object) ['id' => 'price-2', 'date' => '2024-01-02', 'price' => 160.00],
        ]);

        $mockPriceRepository = Mockery::mock(PriceRepositoryInterface::class);
        $mockPriceRepository->shouldReceive('findByDateRange')
            ->once()
            ->with($ratePlanId, $startDate, $endDate)
            ->andReturn($expectedPrices);

        $roomService = new RoomService(
            Mockery::mock(RoomRepositoryInterface::class),
            Mockery::mock(PhotoServiceInterface::class),
            $mockPriceRepository,
            Mockery::mock(RoomRatePlan::class)
        );

        $result = $roomService->getPricesForDateRange($ratePlanId, $startDate, $endDate);

        $this->assertEquals($expectedPrices, $result);
        $this->assertCount(2, $result);
    }

    public function test_get_room(): void
    {
        $roomId = 'room-123';
        $expectedRoom = new Room();
        $expectedRoom->id = $roomId;
        $expectedRoom->name = 'Test Room';

        $mockRepository = Mockery::mock(RoomRepositoryInterface::class);
        $mockRepository->shouldReceive('find')
            ->once()
            ->with($roomId)
            ->andReturn($expectedRoom);

        $roomService = new RoomService(
            $mockRepository,
            Mockery::mock(PhotoServiceInterface::class),
            Mockery::mock(PriceRepositoryInterface::class),
            Mockery::mock(RoomRatePlan::class)
        );

        $result = $roomService->getRoom($roomId);

        $this->assertEquals($expectedRoom->id, $result->id);
        $this->assertEquals($expectedRoom->name, $result->name);
    }

    public function test_get_room_not_found(): void
    {
        $roomId = 'non-existent-room';

        $mockRepository = Mockery::mock(RoomRepositoryInterface::class);
        $mockRepository->shouldReceive('find')
            ->once()
            ->with($roomId)
            ->andReturn(null);

        $roomService = new RoomService(
            $mockRepository,
            Mockery::mock(PhotoServiceInterface::class),
            Mockery::mock(PriceRepositoryInterface::class),
            Mockery::mock(RoomRatePlan::class)
        );

        $result = $roomService->getRoom($roomId);

        $this->assertNull($result);
    }

    public function test_update_room(): void
    {
        $roomId = 'room-123';
        $updateData = [
            'name' => 'Updated Room Name',
            'description' => 'Updated description',
        ];

        $updatedRoom = new Room();
        $updatedRoom->id = $roomId;
        $updatedRoom->name = $updateData['name'];
        $updatedRoom->description = $updateData['description'];

        $mockRepository = Mockery::mock(RoomRepositoryInterface::class);
        $mockRepository->shouldReceive('update')
            ->once()
            ->with($roomId, $updateData)
            ->andReturn($updatedRoom);

        $roomService = new RoomService(
            $mockRepository,
            Mockery::mock(PhotoServiceInterface::class),
            Mockery::mock(PriceRepositoryInterface::class),
            Mockery::mock(RoomRatePlan::class)
        );

        $result = $roomService->updateRoom($roomId, $updateData);

        $this->assertEquals($updatedRoom->id, $result->id);
        $this->assertEquals($updatedRoom->name, $result->name);
        $this->assertEquals($updatedRoom->description, $result->description);
    }

    public function test_update_room_not_found(): void
    {
        $roomId = 'non-existent-room';
        $updateData = ['name' => 'Updated Name'];

        $mockRepository = Mockery::mock(RoomRepositoryInterface::class);
        $mockRepository->shouldReceive('update')
            ->once()
            ->with($roomId, $updateData)
            ->andReturn(null);

        $roomService = new RoomService(
            $mockRepository,
            Mockery::mock(PhotoServiceInterface::class),
            Mockery::mock(PriceRepositoryInterface::class),
            Mockery::mock(RoomRatePlan::class)
        );

        $result = $roomService->updateRoom($roomId, $updateData);

        $this->assertNull($result);
    }

    public function test_delete_room(): void
    {
        $roomId = 'room-123';

        $mockRepository = Mockery::mock(RoomRepositoryInterface::class);
        $mockRepository->shouldReceive('delete')
            ->once()
            ->with($roomId)
            ->andReturn(true);

        $roomService = new RoomService(
            $mockRepository,
            Mockery::mock(PhotoServiceInterface::class),
            Mockery::mock(PriceRepositoryInterface::class),
            Mockery::mock(RoomRatePlan::class)
        );

        $result = $roomService->deleteRoom($roomId);

        $this->assertTrue($result);
    }

    public function test_delete_room_not_found(): void
    {
        $roomId = 'non-existent-room';

        $mockRepository = Mockery::mock(RoomRepositoryInterface::class);
        $mockRepository->shouldReceive('delete')
            ->once()
            ->with($roomId)
            ->andReturn(false);

        $roomService = new RoomService(
            $mockRepository,
            Mockery::mock(PhotoServiceInterface::class),
            Mockery::mock(PriceRepositoryInterface::class),
            Mockery::mock(RoomRatePlan::class)
        );

        $result = $roomService->deleteRoom($roomId);

        $this->assertFalse($result);
    }

    public function test_transaction_rollback_on_failure(): void
    {
        $roomData = [
            'name' => 'Deluxe Suite',
            'description' => 'A beautiful deluxe suite',
            'property_id' => '550e8400-e29b-41d4-a716-446655440001',
        ];

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                throw new \Exception('Simulated database error');
            });

        $roomService = new RoomService(
            Mockery::mock(RoomRepositoryInterface::class),
            Mockery::mock(PhotoServiceInterface::class),
            Mockery::mock(PriceRepositoryInterface::class),
            Mockery::mock(RoomRatePlan::class)
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Simulated database error');

        $roomService->createRoom($roomData);
    }
}
