<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ExchangeRateApiService
{
    private const BASE_URL = 'https://api.nbp.pl/api/exchangerates/rates/';
    private const CACHE_KEY = 'exchange_rates';
    private const CACHE_DURATION = 3600; // 1 hour
    private const TABLE = 'A';
    private const FORMAT = '?format=json';

    public function getExchangeRate(string $targetCurrency, \DateTime $date): ?float
    {
        // Try to get from cache first If not in cache, fetch from API
        try {
            $rates = Cache::get(self::CACHE_KEY);

            if ($rates) {
                return $rates[$targetCurrency];
            }

            $response = Http::get("{$this->getBaseUrl()}{$this->getTable()}/{$targetCurrency}/{$date->format('Y-m-d')}{$this->getFormat()}");

            if ($response->successful()) {
                $data = $response->json();
                $rates = $data['rates'] ?? null;

                // Cache the rates
                //Cache::put(self::CACHE_KEY, $rates, self::CACHE_DURATION);

                return (float)($rates[0]['mid']) ?? null;
            }

            return null;
        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }

    public function getHistoricalExchangeRates(string $targetCurrency, \DateTime $fromDate, \DateTime $toDate): array
    {
        try {
            $response = Http::get("{$this->getBaseUrl()}{$this->getTable()}/{$targetCurrency}/{$fromDate->format('Y-m-d')}/{$toDate->format('Y-m-d')}{$this->getFormat()}");

            if ($response->successful()) {
                $data = $response->json();
                return $data['rates'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            report($e);
            return [];
        }
    }

    private function getBaseUrl(): string
    {
        return self::BASE_URL;
    }

    private function getTable(): string
    {
        return self::TABLE;
    }

    private function getFormat(): string
    {
        return self::FORMAT;
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
