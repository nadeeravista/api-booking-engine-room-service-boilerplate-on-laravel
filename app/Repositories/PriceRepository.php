<?php

namespace App\Repositories;

use App\Contracts\PriceRepositoryInterface;
use App\Models\RoomPrice;
use Illuminate\Database\Eloquent\Collection;

class PriceRepository implements PriceRepositoryInterface
{
    public function create(array $data): RoomPrice
    {
        return RoomPrice::create($data);
    }

    public function find(string $id): ?RoomPrice
    {
        return RoomPrice::find($id);
    }

    public function findByDateRange(string $ratePlanId, string $startDate, string $endDate): Collection
    {
        return RoomPrice::where('room_rate_plan_id', $ratePlanId)
          ->whereBetween('date', [$startDate, $endDate])
          ->get();
    }

    public function update(string $id, array $data): ?RoomPrice
    {
        $price = RoomPrice::find($id);
        if (! $price) {
            return null;
        }

        $price->update($data);

        return $price->fresh();
    }

    public function delete(string $id): bool
    {
        $price = RoomPrice::find($id);
        if (! $price) {
            return false;
        }

        return $price->delete();
    }
}
