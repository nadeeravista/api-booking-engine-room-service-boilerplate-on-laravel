<?php

namespace Tests\Unit\Jobs;

use App\Jobs\GenerateThumbnail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class GenerateThumbnailSimpleTest extends TestCase
{
    public function test_generate_thumbnail_job_can_be_dispatched(): void
    {
        Queue::fake();

        $job = new GenerateThumbnail(
            roomId: 'room-123',
            photoId: 'photo-456',
            s3Key: 'rooms/room-123/photo.jpg'
        );

        dispatch($job);

        Queue::assertPushed(GenerateThumbnail::class);
    }

    public function test_generate_thumbnail_job_implements_should_queue(): void
    {
        $job = new GenerateThumbnail(
            roomId: 'room-123',
            photoId: 'photo-456',
            s3Key: 'rooms/room-123/photo.jpg'
        );

        $this->assertInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class, $job);
    }

    public function test_generate_thumbnail_job_uses_queueable_trait(): void
    {
        $job = new GenerateThumbnail(
            roomId: 'room-123',
            photoId: 'photo-456',
            s3Key: 'rooms/room-123/photo.jpg'
        );

        $this->assertTrue(method_exists($job, 'onQueue'));
        $this->assertTrue(method_exists($job, 'onConnection'));
    }

    public function test_generate_thumbnail_calls_repository_update_with_correct_parameters(): void
    {
        $mockRepository = \Mockery::mock(\App\Contracts\PhotoRepositoryInterface::class);
        $mockRepository->shouldReceive('update')
            ->once()
            ->with('photo-456', \Mockery::on(function ($data) {
                return isset($data['thumbnail_url'])
                    && $data['thumbnail_url'] === 'https://s3.amazonaws.com/bucket/thumbnails/rooms/room-123/photo.jpg';
            }))
            ->andReturn(new \App\Models\RoomPhoto());

        $job = new GenerateThumbnail('room-123', 'photo-456', 'rooms/room-123/photo.jpg');
        $job->handle($mockRepository);
    }

    public function test_generate_thumbnail_generates_correct_thumbnail_url(): void
    {
        $mockRepository = \Mockery::mock(\App\Contracts\PhotoRepositoryInterface::class);
        $mockRepository->shouldReceive('update')
            ->once()
            ->with('photo-456', \Mockery::on(function ($data) {
                // Verify the thumbnail URL is correctly generated from the S3 key
                return $data['thumbnail_url'] === 'https://s3.amazonaws.com/bucket/thumbnails/rooms/room-123/photo.jpg';
            }))
            ->andReturn(new \App\Models\RoomPhoto());

        $job = new GenerateThumbnail('room-123', 'photo-456', 'rooms/room-123/photo.jpg');
        $job->handle($mockRepository);
    }
}
