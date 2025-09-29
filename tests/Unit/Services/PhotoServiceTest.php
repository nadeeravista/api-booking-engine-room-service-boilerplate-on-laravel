<?php

namespace Tests\Unit\Services;

use App\Contracts\PhotoRepositoryInterface;
use App\Jobs\GenerateThumbnail;
use App\Jobs\ModeratePhoto;
use App\Services\PhotoService;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class PhotoServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_room_photos_dispatches_jobs_correctly(): void
    {
        Queue::fake();

        $roomId = 'room-123';
        $photos = [
            [
                'filename' => 'room1.jpg',
                'url' => 'https://s3.amazonaws.com/bucket/room1.jpg',
                'is_primary' => true,
            ],
            [
                'filename' => 'room2.jpg',
                'url' => 'https://s3.amazonaws.com/bucket/room2.jpg',
                'is_primary' => false,
            ],
        ];

        $mockRepository = Mockery::mock(PhotoRepositoryInterface::class);
        $mockRepository->shouldReceive('createMany')
            ->once()
            ->with(Mockery::on(function ($photoData) use ($roomId) {
                return count($photoData) === 2 &&
                    $photoData[0]['room_id'] === $roomId &&
                    $photoData[1]['room_id'] === $roomId &&
                    $photoData[0]['filename'] === 'room1.jpg' &&
                    $photoData[1]['filename'] === 'room2.jpg' &&
                    $photoData[0]['is_primary'] === true &&
                    $photoData[1]['is_primary'] === false &&
                    $photoData[0]['upload_status'] === 'completed' &&
                    $photoData[0]['processing_status'] === 'pending' &&
                    $photoData[0]['is_moderated'] === false;
            }));

        $photoService = new PhotoService($mockRepository);
        $photoService->createRoomPhotos($roomId, $photos);

        Queue::assertPushed(ModeratePhoto::class, 2);
        Queue::assertPushed(GenerateThumbnail::class, 2);
    }

    public function test_create_room_photos_with_single_photo(): void
    {
        Queue::fake();

        $roomId = 'room-456';
        $photos = [
            [
                'filename' => 'single-room.jpg',
                'url' => 'https://s3.amazonaws.com/bucket/single-room.jpg',
                'is_primary' => true,
            ],
        ];

        $mockRepository = Mockery::mock(PhotoRepositoryInterface::class);
        $mockRepository->shouldReceive('createMany')
            ->once()
            ->with(Mockery::on(function ($photoData) use ($roomId) {
                return count($photoData) === 1 &&
                    $photoData[0]['room_id'] === $roomId &&
                    $photoData[0]['filename'] === 'single-room.jpg' &&
                    $photoData[0]['is_primary'] === true;
            }));

        $photoService = new PhotoService($mockRepository);
        $photoService->createRoomPhotos($roomId, $photos);

        Queue::assertPushed(ModeratePhoto::class, 1);
        Queue::assertPushed(GenerateThumbnail::class, 1);
    }

    public function test_create_room_photos_with_no_photos(): void
    {
        Queue::fake();

        $roomId = 'room-789';
        $photos = [];

        $mockRepository = Mockery::mock(PhotoRepositoryInterface::class);
        $mockRepository->shouldReceive('createMany')
            ->once()
            ->with([]);

        $photoService = new PhotoService($mockRepository);
        $photoService->createRoomPhotos($roomId, $photos);

        Queue::assertPushed(ModeratePhoto::class, 0);
        Queue::assertPushed(GenerateThumbnail::class, 0);
    }

    public function test_create_room_photos_with_multiple_photos(): void
    {
        Queue::fake();

        $roomId = 'room-999';
        $photos = [
            ['filename' => 'photo1.jpg', 'url' => 'https://s3.amazonaws.com/bucket/photo1.jpg', 'is_primary' => true],
            ['filename' => 'photo2.jpg', 'url' => 'https://s3.amazonaws.com/bucket/photo2.jpg', 'is_primary' => false],
            ['filename' => 'photo3.jpg', 'url' => 'https://s3.amazonaws.com/bucket/photo3.jpg', 'is_primary' => false],
            ['filename' => 'photo4.jpg', 'url' => 'https://s3.amazonaws.com/bucket/photo4.jpg', 'is_primary' => false],
        ];

        $mockRepository = Mockery::mock(PhotoRepositoryInterface::class);
        $mockRepository->shouldReceive('createMany')
            ->once()
            ->with(Mockery::on(function ($photoData) {
                return count($photoData) === 4;
            }));

        $photoService = new PhotoService($mockRepository);
        $photoService->createRoomPhotos($roomId, $photos);

        Queue::assertPushed(ModeratePhoto::class, 4);
        Queue::assertPushed(GenerateThumbnail::class, 4);
    }

    public function test_upload_photo_to_s3_is_empty(): void
    {
        $mockRepository = Mockery::mock(PhotoRepositoryInterface::class);
        $photoService = new PhotoService($mockRepository);

        $this->expectNotToPerformAssertions();
        $photoService->uploadPhotoToS3('photo-123');
    }
}
