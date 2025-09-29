<?php

namespace App\Contracts;

use App\Models\Room;
use Illuminate\Database\Eloquent\Collection;

interface RoomRepositoryInterface
{
    public function create(array $data): Room;
    public function find(string $id): ?Room;
    public function findByProperty(string $propertyId): Collection;
    public function update(string $id, array $data): ?Room;
    public function delete(string $id): bool;
    public function findWithPhotos(string $id): ?Room;
}
