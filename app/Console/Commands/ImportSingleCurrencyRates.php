<?php

namespace App\Console\Commands;

use App\Models\Currency;
use App\Models\CurrencyRateHistory;
use App\Services\ExchangeRateApiService;
use Illuminate\Console\Command;

class ImportSingleCurrencyRates extends Command
{
    private const BASE_CURRENCY = 'PLN';
    public function __construct(private Currency $currency, private CurrencyRateHistory $currencyRateHistory)
    {
        parent::__construct();
    }

    /**
     * @var string
     */
    protected $signature = 'currency:import-rates
        {--currency=USD : Currency to get rates for}
        {--date=today : Date for historical rates (YYYY-MM-DD format)}';

    /**
     * @var string
     */
    protected $description = 'Import currency exchange rates from ExchangeRate API';
    public function handle(ExchangeRateApiService $exchangeService): void
    {
        $currency = strtoupper($this->option('currency'));
        $date = $this->option('date');

        if ($date === 'today') {
            $date = now();
        } else {
            $date = \DateTime::createFromFormat('Y-m-d', $date);
        }

        try {
            $this->info("Importing rates for currency: {$currency} on {$date->format('Y-m-d')}");
            $currencies = $this->currency->get();

            foreach ($currencies as $currency) {
                $rate = $exchangeService->getExchangeRate($currency->symbol, $date);

                if ($rate !== null) {
                    //Use upsert to avoid duplicates when importing multiple times
                    $this->currencyRateHistory->upsert([
                        'from_currency_id' => $this->currency->where('symbol', self::BASE_CURRENCY)->first()->id,
                        'to_currency_id' => $currency->id,
                        'rate' => $rate,
                        'date' => $date->format('Y-m-d')
                    ], ['from_currency_id', 'to_currency_id', 'date'], ['rate']);

                    $this->info("Successfully imported rate for {$currency->symbol}: {$rate}");
                } else {
                    $this->warn("Could not get rate for {$currency->symbol}");
                }
            }

            $this->info('Import completed!');
        } catch (\Exception $e) {
            $this->error("Error importing currency rates: {$e->getMessage()}");
            throw $e;
        }
    }

}
