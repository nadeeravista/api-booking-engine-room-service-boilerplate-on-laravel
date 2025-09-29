<?php

namespace App\Services;

use App\Contracts\PhotoRepositoryInterface;
use App\Contracts\PhotoServiceInterface;
use App\Jobs\GenerateThumbnail;
use App\Jobs\ModeratePhoto;
use Illuminate\Support\Str;

class PhotoService implements PhotoServiceInterface
{
    public function __construct(
        private PhotoRepositoryInterface $photoRepository
    ) {
    }

    public function createRoomPhotos(string $roomId, array $photos): void
    {
        $photoData = $this->preparePhotoData($roomId, $photos);

        $this->photoRepository->createMany($photoData);

        $this->dispatchPhotoJobs($roomId, $photoData);
    }

    public function uploadPhotoToS3(string $photoId): void
    {
    }

    private function preparePhotoData(string $roomId, array $photos): array
    {
        $photoData = [];

        foreach ($photos as $photo) {
            $photoId = (string) Str::uuid();
            $s3Key = $this->extractS3KeyFromUrl($photo['url']);

            $photoData[] = [
                'id' => $photoId,
                'room_id' => $roomId,
                'filename' => $photo['filename'],
                'url' => $photo['url'],
                's3_key' => $s3Key,
                'is_primary' => $photo['is_primary'] ?? false,
                'upload_status' => 'completed',
                'processing_status' => 'pending',
                'is_moderated' => false,
            ];
        }

        return $photoData;
    }

    private function dispatchPhotoJobs(string $roomId, array $photoData): void
    {
        foreach ($photoData as $photo) {
            ModeratePhoto::dispatch($roomId, $photo['id'], $photo['s3_key']);
            GenerateThumbnail::dispatch($roomId, $photo['id'], $photo['s3_key']);
        }
    }

    private function extractS3KeyFromUrl(string $url): string
    {
        $parsedUrl = parse_url($url);

        return ltrim($parsedUrl['path'] ?? '', '/');
    }
}
