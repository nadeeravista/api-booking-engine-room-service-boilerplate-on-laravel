<?php

namespace App\Contracts;

use App\Models\RoomPrice;
use Illuminate\Database\Eloquent\Collection;

interface PriceRepositoryInterface
{
    public function create(array $data): RoomPrice;
    public function find(string $id): ?RoomPrice;
    public function findByDateRange(string $ratePlanId, string $startDate, string $endDate): Collection;
    public function update(string $id, array $data): ?RoomPrice;
    public function delete(string $id): bool;
}
