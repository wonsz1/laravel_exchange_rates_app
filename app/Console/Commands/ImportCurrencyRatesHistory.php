<?php

namespace App\Console\Commands;

use App\Models\Currency;
use App\Models\CurrencyRateHistory;
use App\Services\ExchangeRateApiService;
use Illuminate\Console\Command;
use Illuminate\Database\DatabaseManager;

class ImportCurrencyRatesHistory extends Command
{
    private const BASE_CURRENCY = 'PLN';
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
        {--from-date=2025-01-01 : Date for historical rates (YYYY-MM-DD format)}
        {--to-date=2025-06-30 : Date for historical rates (YYYY-MM-DD format)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import currency exchange rates from ExchangeRate API';

    /**
     * Execute the console command.
     */
    public function handle(ExchangeRateApiService $exchangeService): void
    {
        $currency = strtoupper($this->option('currency'));
        $fromDate = $this->option('from-date');
        $toDate = $this->option('to-date');

        $fromDate = \DateTime::createFromFormat('Y-m-d', $fromDate);
        $toDate = \DateTime::createFromFormat('Y-m-d', $toDate);

        try {
            $this->info("Importing rates for currency: {$currency} on {$fromDate->format('Y-m-d')} to {$toDate->format('Y-m-d')}");
            $currencies = $this->currency->get();

            foreach ($currencies as $currency) {
                $rates = $exchangeService->getHistoricalExchangeRates($currency->symbol, $fromDate, $toDate);

                if ($rates !== null) {

                    foreach ($rates as $rate) {
                        //Use upsert to avoid duplicates when importing multiple times
                        $this->currencyRateHistory->upsert([
                            'from_currency_id' => $this->currency->where('symbol', self::BASE_CURRENCY)->first()->id,
                            'to_currency_id' => $currency->id,
                            'rate' => $rate['mid'],
                            'date' => $rate['effectiveDate']
                        ], ['from_currency_id', 'to_currency_id', 'date'], ['rate']);

                        $this->info("Successfully imported rate for {$currency->symbol}: {$rate['mid']} on {$rate['effectiveDate']}");
                    }
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
