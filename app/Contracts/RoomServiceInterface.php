<?php

namespace App\Contracts;

use App\Models\Room;
use App\Models\RoomPrice;
use App\Models\RoomRatePlan;

interface RoomServiceInterface
{
    public function createRoom(array $data): Room;
    public function getRoomsByProperty(string $propertyId): \Illuminate\Database\Eloquent\Collection;
    public function getRoom(string $id): ?Room;
    public function updateRoom(string $id, array $data): ?Room;
    public function deleteRoom(string $id): bool;
    public function createRatePlan(array $data): RoomRatePlan;
    public function createPrice(array $data): RoomPrice;
    public function getPricesForDateRange(string $ratePlanId, string $startDate, string $endDate): \Illuminate\Database\Eloquent\Collection;
}
