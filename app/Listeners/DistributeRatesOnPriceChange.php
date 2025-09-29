<?php

namespace App\Listeners;

use App\Events\RoomRateChanged;
use Illuminate\Support\Facades\Log;

class DistributeRatesOnPriceChange
{
    public function handle(RoomRateChanged $event): void
    {
        Log::info('Price distribution triggered');
    }
}
