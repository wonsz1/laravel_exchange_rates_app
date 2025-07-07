<?php

namespace App\Console\Commands;

use App\Models\Currency;
use App\Models\CurrencyRateHistory;
use App\Services\ExchangeRateApiService;
use Illuminate\Console\Command;
use Illuminate\Database\DatabaseManager;

class ImportCurrencyRatesHistory extends Command
{
    public function __construct(private Currency $currency, private CurrencyRateHistory $currencyRateHistory)
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:import-rates-history
        {--currency=USD : Currency to get rates for}
        {--from-date= : Date for historical rates (YYYY-MM-DD format)}
        {--to-date= : Date for historical rates (YYYY-MM-DD format)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import currency exchange rates from ExchangeRate API';

    public function handle(ExchangeRateApiService $exchangeService): void
    {
        $currency = strtoupper($this->option('currency'));
        $fromDate = $this->option('from-date');
        $toDate = $this->option('to-date');

        $fromDate = \DateTime::createFromFormat('Y-m-d', $fromDate);
        $toDate = \DateTime::createFromFormat('Y-m-d', $toDate);

        try {
            $this->info("Importing rates for currency: {$currency} on {$fromDate->format('Y-m-d')} to {$toDate->format('Y-m-d')}");
            $currency = $this->currency->where('symbol', $currency)->first();
            $baseCurrency = $this->currency->where('symbol', Currency::BASE_CURRENCY)->first();

                $rates = $exchangeService->getHistoricalExchangeRates($baseCurrency->symbol, $currency->symbol, $fromDate, $toDate);

                foreach ($rates as $rate) {
                    //Use upsert to avoid duplicates when importing multiple times
                    $this->currencyRateHistory->upsert([
                        'from_currency_id' => $baseCurrency->id,
                        'to_currency_id' => $currency->id,
                        'rate' => (int)($rate['mid'] * 10000),
                        'date' => $rate['effectiveDate']
                    ], ['from_currency_id', 'to_currency_id', 'date'], ['rate']);

                    $this->info("Successfully imported rate for {$currency->symbol}: {$rate['mid']} on {$rate['effectiveDate']}");
                }
            

            $this->info('Import completed!');
        } catch (\Exception $e) {
            $this->error("Error importing currency rates: {$e->getMessage()}");
            throw $e;
        }
    }
}
