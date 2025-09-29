<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Contracts\RoomServiceInterface::class,
            \App\Services\RoomService::class
        );

        $this->app->bind(
            \App\Contracts\RoomRepositoryInterface::class,
            \App\Repositories\RoomRepository::class
        );

        $this->app->bind(
            \App\Contracts\PhotoServiceInterface::class,
            \App\Services\PhotoService::class
        );

        $this->app->bind(
            \App\Contracts\PriceRepositoryInterface::class,
            \App\Repositories\PriceRepository::class
        );

        $this->app->bind(
            \App\Contracts\PhotoRepositoryInterface::class,
            \App\Repositories\PhotoRepository::class
        );
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\RoomRateChanged::class,
            \App\Listeners\DistributeRatesOnPriceChange::class
        );
    }
}
