<?php

namespace App\Services;

interface ExchangeRateApiServiceInterface
{
    public const BASE_URL = 'https://api.nbp.pl/api/exchangerates/rates/';
    public const TABLE = 'A';
    public const FORMAT = '?format=json';

    public function getExchangeRate(?string $sourceCurrency, string $targetCurrency, \DateTime $date): ?float;

    public function getHistoricalExchangeRates(?string $sourceCurrency, string $targetCurrency, \DateTime $fromDate, \DateTime $toDate): array;
}
