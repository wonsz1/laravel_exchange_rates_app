<?php

namespace App\Repositories;

use App\Repositories\Contracts\CurrencyRateHistoryInterface;
use App\Models\CurrencyRateHistory;

class CurrencyRateHistoryRepository implements CurrencyRateHistoryInterface
{
    public function getRatesBetweenCurrenciesFromDate(int $fromCurrencyId, int $toCurrencyId, \DateTime $date)
    {
        return CurrencyRateHistory::where('from_currency_id', $fromCurrencyId)
            ->where('to_currency_id', $toCurrencyId)
            ->whereDate('date', '>=', $date)
            ->get();
    }
}