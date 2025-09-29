<?php

namespace App\Repositories;

use App\Contracts\RoomRepositoryInterface;
use App\Models\Room;
use Illuminate\Database\Eloquent\Collection;

class RoomRepository implements RoomRepositoryInterface
{
    public function create(array $data): Room
    {
        return Room::create($data);
    }

    public function find(string $id): ?Room
    {
        return Room::with(['photos', 'primaryPhoto'])->find($id);
    }

    public function findByProperty(string $propertyId): Collection
    {
        return Room::with(['photos', 'primaryPhoto'])
            ->where('property_id', $propertyId)
            ->get();
    }

    public function update(string $id, array $data): ?Room
    {
        $room = Room::find($id);
        if (! $room) {
            return null;
        }

        $room->update($data);

        return $room->fresh();
    }

    public function delete(string $id): bool
    {
        $room = Room::find($id);
        if (! $room) {
            return false;
        }

        return $room->delete();
    }

    public function findWithPhotos(string $id): ?Room
    {
        return Room::with(['photos', 'primaryPhoto'])->find($id);
    }
}
