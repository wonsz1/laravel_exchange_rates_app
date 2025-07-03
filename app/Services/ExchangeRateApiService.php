<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ExchangeRateApiService implements ExchangeRateApiServiceInterface
{
    public function getExchangeRate(?string $sourceCurrency, string $targetCurrency, \DateTime $date): ?float
    {
        try {
            $response = Http::get("{$this->getBaseUrl()}{$this->getTable()}/{$targetCurrency}/{$date->format('Y-m-d')}{$this->getFormat()}");

            if ($response->successful()) {
                $data = $response->json();
                $rates = $data['rates'] ?? null;

                return (float)($rates[0]['mid']) ?? null;
            }

            return null;
        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }

    public function getHistoricalExchangeRates(?string $sourceCurrency, string $targetCurrency, \DateTime $fromDate, \DateTime $toDate): array
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
}
