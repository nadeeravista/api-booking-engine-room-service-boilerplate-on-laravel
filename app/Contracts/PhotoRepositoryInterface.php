<?php

namespace App\Contracts;

use App\Models\RoomPhoto;
use Illuminate\Database\Eloquent\Collection;

interface PhotoRepositoryInterface
{
    public function create(array $data): RoomPhoto;
    public function createMany(array $photos): void;
    public function findByRoom(string $roomId): Collection;
    public function find(string $id): ?RoomPhoto;
    public function update(string $id, array $data): ?RoomPhoto;
    public function delete(string $id): bool;
}
