<?php

namespace App\Console\Commands;

use App\Models\Currency;
use App\Models\CurrencyRateHistory;
use App\Services\ExchangeRateApiServiceInterface;
use Illuminate\Console\Command;

class ImportCurrentCurrencyRates extends Command
{
    public function __construct(private Currency $currency, private CurrencyRateHistory $currencyRateHistory)
    {
        parent::__construct();
    }

    /**
     * @var string
     */
    protected $signature = 'currency:import-current-rates
    {--currency : Currency to get rates for. If not specified, all currencies will be imported }
    {--date=today : Date for historical rates (YYYY-MM-DD format) }';

    /**
     * @var string
     */
    protected $description = 'Import currency exchange rates from ExchangeRate API';

    public function handle(ExchangeRateApiServiceInterface $exchangeService): void
    {
        $currency = $this->option('currency');
        $date = $this->option('date');

        if (!$date || $date === 'today') {
            $date = now();
        } else {
            $date = \DateTime::createFromFormat('Y-m-d', $date);
        }

        if($currency) {
            $this->importRateForCurrency(strtoupper($currency), $date, $exchangeService);
        } else {
            $this->importAllRates($date, $exchangeService);
        }
    }

    private function importRateForCurrency(string $currency, \DateTime $date, ExchangeRateApiServiceInterface $exchangeService)
    {
        try {
            $this->info("Importing rates for currency: {$currency} on {$date->format('Y-m-d')}");

            $rate = $exchangeService->getExchangeRate(Currency::BASE_CURRENCY, $currency, $date);

            if ($rate !== null) {
                //Use upsert to avoid duplicates when importing multiple times
                $this->currencyRateHistory->upsert([
                    'from_currency_id' => $this->currency->where('symbol', Currency::BASE_CURRENCY)->first()->id,
                    'to_currency_id' => $this->currency->where('symbol', $currency)->first()->id,
                    'rate' => (int)($rate * 10000),
                    'date' => $date->format('Y-m-d')
                ], ['from_currency_id', 'to_currency_id', 'date'], ['rate']);

                $this->info("Successfully imported rate for {$currency}: {$rate}");
            } else {
                $this->warn("Could not get rate for {$currency}");
            }

            $this->info('Import completed!');
        } catch (\Exception $e) {
            $this->error("Error importing currency rates: {$e->getMessage()}");
            throw $e;
        }
    }

    private function importAllRates(\DateTime $date, ExchangeRateApiServiceInterface $exchangeService)
    {
        $this->info("Importing rates for all currencies.");

        $this->currency->get()->each(function ($currency) use ($date, $exchangeService) {
            $this->importRateForCurrency($currency->symbol, $date, $exchangeService);
        });
    }

}
