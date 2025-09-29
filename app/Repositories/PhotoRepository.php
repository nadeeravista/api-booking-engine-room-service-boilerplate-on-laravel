<?php

namespace App\Repositories;

use App\Contracts\PhotoRepositoryInterface;
use App\Models\RoomPhoto;
use Illuminate\Database\Eloquent\Collection;

class PhotoRepository implements PhotoRepositoryInterface
{
    public function create(array $data): RoomPhoto
    {
        return RoomPhoto::create($data);
    }

    public function createMany(array $photos): void
    {
        RoomPhoto::insert($photos);
    }

    public function findByRoom(string $roomId): Collection
    {
        return RoomPhoto::where('room_id', $roomId)->get();
    }

    public function find(string $id): ?RoomPhoto
    {
        return RoomPhoto::find($id);
    }

    public function update(string $id, array $data): ?RoomPhoto
    {
        $photo = RoomPhoto::find($id);
        if (! $photo) {
            return null;
        }

        $photo->update($data);

        return $photo->fresh();
    }

    public function delete(string $id): bool
    {
        $photo = RoomPhoto::find($id);
        if (! $photo) {
            return false;
        }

        return $photo->delete();
    }
}
