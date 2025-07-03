<?php

namespace App\Providers;

use App\Services\ExchangeRateApiServiceInterface;
use App\Services\ExchangeRateApiService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ExchangeRateApiServiceInterface::class, ExchangeRateApiService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
