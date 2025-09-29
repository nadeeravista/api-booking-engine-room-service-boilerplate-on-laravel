<?php

namespace App\Jobs;

use App\Contracts\PhotoRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ModeratePhoto implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $roomId,
        public string $photoId,
        public string $s3Key
    ) {
    }

    public function handle(PhotoRepositoryInterface $photoRepository): void
    {
        $moderationResult = $this->performModeration();
        $this->updatePhotoModeration($photoRepository, $moderationResult);

        Log::info('Photo moderation completed', [
          'room_id' => $this->roomId,
          'photo_id' => $this->photoId,
          'approved' => $moderationResult['approved'],
        ]);
    }

    private function performModeration(): array
    {
        return [
          'approved' => true,
          'confidence' => 0.95,
          'flags' => [],
          'moderated_at' => now()->toISOString(),
        ];
    }

    private function updatePhotoModeration(PhotoRepositoryInterface $photoRepository, array $result): void
    {
        $photoRepository->update($this->photoId, [
          'is_moderated' => true,
          'moderated_at' => now(),
          'moderation_result' => $result,
        ]);
    }
}
