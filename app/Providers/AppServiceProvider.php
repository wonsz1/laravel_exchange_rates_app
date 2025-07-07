<?php

namespace App\Providers;

use App\Services\ExchangeRateApiServiceInterface;
use App\Services\ExchangeRateApiService;
use App\Repositories\Contracts\CurrencyRateHistoryInterface;
use App\Repositories\CurrencyRateHistoryRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ExchangeRateApiServiceInterface::class, ExchangeRateApiService::class);
        $this->app->bind(CurrencyRateHistoryInterface::class, CurrencyRateHistoryRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
