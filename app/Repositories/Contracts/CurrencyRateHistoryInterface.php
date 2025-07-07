<?php

namespace App\Repositories\Contracts;

interface CurrencyRateHistoryInterface
{
    public function getRatesBetweenCurrenciesFromDate(int $fromCurrencyId, int $toCurrencyId, \DateTime $date);
}