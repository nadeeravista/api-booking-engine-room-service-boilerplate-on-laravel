<?php

namespace App\Services;

use App\Contracts\PhotoServiceInterface;
use App\Contracts\PriceRepositoryInterface;
use App\Contracts\RoomRepositoryInterface;
use App\Contracts\RoomServiceInterface;
use App\Models\Room;
use App\Models\RoomPrice;
use App\Models\RoomRatePlan;
use Illuminate\Support\Facades\DB;

class RoomService implements RoomServiceInterface
{
    public function __construct(
        private RoomRepositoryInterface $roomRepository,
        private PhotoServiceInterface $photoService,
        private PriceRepositoryInterface $priceRepository,
        private RoomRatePlan $roomRatePlan
    ) {
    }

    public function createRoom(array $data): Room
    {
        return DB::transaction(function () use ($data) {
            $photos = $data['photos'] ?? [];
            unset($data['photos']);

            $room = $this->roomRepository->create($data);

            if (! empty($photos)) {
                $this->photoService->createRoomPhotos($room->id, $photos);
            }

            return $this->roomRepository->findWithPhotos($room->id);
        });
    }

    public function getRoomsByProperty(string $propertyId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->roomRepository->findByProperty($propertyId);
    }

    public function createRatePlan(array $data): RoomRatePlan
    {
        return $this->roomRatePlan->create($data);
    }

    public function createPrice(array $data): RoomPrice
    {
        return $this->priceRepository->create($data);
    }

    public function getPricesForDateRange(string $ratePlanId, string $startDate, string $endDate): \Illuminate\Database\Eloquent\Collection
    {
        return $this->priceRepository->findByDateRange($ratePlanId, $startDate, $endDate);
    }

    public function getRoom(string $id): ?Room
    {
        return $this->roomRepository->find($id);
    }

    public function updateRoom(string $id, array $data): ?Room
    {
        return $this->roomRepository->update($id, $data);
    }

    public function deleteRoom(string $id): bool
    {
        return $this->roomRepository->delete($id);
    }
}
