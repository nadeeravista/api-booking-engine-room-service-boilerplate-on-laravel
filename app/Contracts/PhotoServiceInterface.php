<?php

namespace App\Contracts;

interface PhotoServiceInterface
{
    public function createRoomPhotos(string $roomId, array $photos): void;
    public function uploadPhotoToS3(string $photoId): void;
}
