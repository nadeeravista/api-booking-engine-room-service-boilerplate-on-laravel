<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ModeratePhoto;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ModeratePhotoSimpleTest extends TestCase
{
    public function test_moderate_photo_job_can_be_dispatched(): void
    {
        Queue::fake();

        $job = new ModeratePhoto(
            roomId: 'room-123',
            photoId: 'photo-456',
            s3Key: 'rooms/room-123/photo.jpg'
        );

        dispatch($job);

        Queue::assertPushed(ModeratePhoto::class);
    }

    public function test_moderate_photo_job_handles_special_characters_in_s3_keys(): void
    {
        $specialKeys = [
            'rooms/room-123/photo with spaces.jpg',
            'rooms/room-456/photo-with-dashes.jpg',
            'rooms/room-789/photo_with_underscores.jpg',
            'rooms/room-101/photo.with.dots.jpg',
        ];

        foreach ($specialKeys as $s3Key) {
            $job = new ModeratePhoto(
                roomId: 'room-123',
                photoId: 'photo-456',
                s3Key: $s3Key
            );

            $this->assertEquals($s3Key, $job->s3Key);
        }
    }

    public function test_moderate_photo_job_implements_should_queue(): void
    {
        $job = new ModeratePhoto(
            roomId: 'room-123',
            photoId: 'photo-456',
            s3Key: 'rooms/room-123/photo.jpg'
        );

        $this->assertInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class, $job);
    }

    public function test_moderate_photo_job_uses_queueable_trait(): void
    {
        $job = new ModeratePhoto(
            roomId: 'room-123',
            photoId: 'photo-456',
            s3Key: 'rooms/room-123/photo.jpg'
        );

        $this->assertTrue(method_exists($job, 'onQueue'));
        $this->assertTrue(method_exists($job, 'onConnection'));
    }

    public function test_moderate_photo_calls_repository_update_with_correct_parameters(): void
    {
        $mockRepository = \Mockery::mock(\App\Contracts\PhotoRepositoryInterface::class);
        $mockRepository->shouldReceive('update')
            ->once()
            ->with('photo-456', \Mockery::on(function ($data) {
                return isset($data['is_moderated'])
                    && $data['is_moderated'] === true
                    && isset($data['moderated_at'])
                    && isset($data['moderation_result'])
                    && $data['moderation_result']['approved'] === true;
            }))
            ->andReturn(new \App\Models\RoomPhoto());

        $job = new ModeratePhoto('room-123', 'photo-456', 'rooms/room-123/photo.jpg');
        $job->handle($mockRepository);
    }

    public function test_moderate_photo_performs_moderation_logic(): void
    {
        $mockRepository = \Mockery::mock(\App\Contracts\PhotoRepositoryInterface::class);
        $mockRepository->shouldReceive('update')
            ->once()
            ->with('photo-456', \Mockery::on(function ($data) {
                $result = $data['moderation_result'];

                return is_array($result)
                    && isset($result['approved'])
                    && isset($result['confidence'])
                    && isset($result['flags'])
                    && isset($result['moderated_at'])
                    && $result['approved'] === true
                    && $result['confidence'] === 0.95;
            }))
            ->andReturn(new \App\Models\RoomPhoto());

        $job = new ModeratePhoto('room-123', 'photo-456', 'rooms/room-123/photo.jpg');
        $job->handle($mockRepository);
    }
}
