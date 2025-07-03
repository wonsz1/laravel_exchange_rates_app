<?php

namespace App\Jobs;

use App\Models\Currency;
use App\Models\CurrencyRateHistory;
use App\Services\ExchangeRateApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\WithoutRelations;

class ImportCurrencyRatesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        #[WithoutRelations]
        private Currency $currency,
        #[WithoutRelations]
        private CurrencyRateHistory $currencyRateHistory
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(ExchangeRateApiService $exchangeService): void
    {
        $currencies = $this->currency->get();
        $currentDate = now();
        $baseCurrency = $this->currency->where('symbol', Currency::BASE_CURRENCY)->first();

        foreach ($currencies as $currency) {
            if($currency->symbol === Currency::BASE_CURRENCY) {
                continue;
            }
            
            $currentRate = $exchangeService->getExchangeRate(
                $baseCurrency->symbol,
                $currency->symbol,
                $currentDate
            );
            if ($currentRate === null) {
                continue;
            }

            // Save rate history
            $this->currencyRateHistory->upsert([
                'from_currency_id' => $baseCurrency->id,
                'to_currency_id' => $currency->id,
                'rate' => (int)($currentRate * 10000),
                'date' => $currentDate->format('Y-m-d')
            ], ['from_currency_id', 'to_currency_id', 'date'], ['rate']);
        }
    }
}
