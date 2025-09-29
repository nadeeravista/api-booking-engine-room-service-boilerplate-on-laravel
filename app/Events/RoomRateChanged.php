<?php

namespace App\Events;

use App\Models\RoomPrice;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoomRateChanged
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public RoomPrice $roomPrice,
        public array $oldData = [],
        public array $newData = []
    ) {
    }
}
