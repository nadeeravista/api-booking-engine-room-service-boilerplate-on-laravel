<?php

namespace App\Jobs;

use App\Contracts\PhotoRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateThumbnail implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $roomId,
        public string $photoId,
        public string $s3Key
    ) {}

    public function handle(PhotoRepositoryInterface $photoRepository): void
    {
        $thumbnailUrl = $this->generateThumbnailUrl();
        $this->updatePhotoThumbnail($photoRepository, $thumbnailUrl);

        Log::info('Thumbnail generation completed', [
            'room_id' => $this->roomId,
            'photo_id' => $this->photoId,
            'thumbnail_url' => $thumbnailUrl,
        ]);
    }

    private function generateThumbnailUrl(): string
    {
        return "https://s3.amazonaws.com/bucket/thumbnails/{$this->s3Key}";
    }

    private function updatePhotoThumbnail(PhotoRepositoryInterface $photoRepository, string $thumbnailUrl): void
    {
        $photoRepository->update($this->photoId, [
            'thumbnail_url' => $thumbnailUrl,
        ]);
    }
}
