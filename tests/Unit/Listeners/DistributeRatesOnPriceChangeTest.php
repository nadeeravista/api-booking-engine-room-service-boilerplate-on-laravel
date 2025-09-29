<?php

namespace Tests\Unit\Listeners;

use App\Events\RoomRateChanged;
use App\Listeners\DistributeRatesOnPriceChange;
use App\Models\RoomPrice;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class DistributeRatesOnPriceChangeTest extends TestCase
{
    public function test_listener_logs_price_distribution_triggered(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Price distribution triggered');

        $roomPrice = new RoomPrice([
            'id' => 'price-123',
            'room_rate_plan_id' => 'rate-plan-456',
        ]);

        $event = new RoomRateChanged($roomPrice, [], []);
        $listener = new DistributeRatesOnPriceChange();

        $listener->handle($event);
    }
}
