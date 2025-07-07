<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\CurrencyRateHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class ImportCurrentCurrencyRatesTest extends TestCase
{
    use RefreshDatabase;

    private $plnCurrency;
    private $usdCurrency;
    private $eurCurrency;
    private $gbpCurrency;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create base currency
        $this->plnCurrency = Currency::create([
            'symbol' => Currency::BASE_CURRENCY,
            'name' => 'Polish Złoty',
            'iso_code' => Currency::BASE_CURRENCY
        ]);

        $this->usdCurrency = Currency::create([
            'symbol' => 'USD',
            'name' => 'US Dollar',
            'iso_code' => 'USD'
        ]);

        $this->eurCurrency = Currency::create([
            'symbol' => 'EUR',
            'name' => 'Euro',
            'iso_code' => 'EUR'
        ]);

        $this->gbpCurrency = Currency::create([
            'symbol' => 'GBP',
            'name' => 'British Pound',
            'iso_code' => 'GBP'
        ]);
    }

    public function test_import_single_currency_rate(): void
    {
        $this->artisan('currency:import-current-rates', [
            '--currency' => 'USD',
            '--date' => '2025-07-03'
        ])
        ->expectsOutput('Importing rates for currency: USD on 2025-07-03')
        ->assertExitCode(0);

        // Assert
        $this->assertDatabaseHas('currency_rates_history', [
            'from_currency_id' => $this->plnCurrency->id,
            'to_currency_id' => $this->usdCurrency->id,
            'date' => '2025-07-03'
        ]);
        
        // Verify the rate is a positive integer
        $rate = CurrencyRateHistory::where('from_currency_id', $this->plnCurrency->id)
            ->where('to_currency_id', $this->usdCurrency->id)
            ->where('date', '2025-07-03')
            ->value('rate');
        
        $this->assertGreaterThan(0, $rate);
    }

    public function test_import_all_currencies(): void
    {
        $this->artisan('currency:import-current-rates')
        ->assertExitCode(0);

        // Assert
        $this->assertDatabaseHas('currency_rates_history', [
            'from_currency_id' => $this->plnCurrency->id,
            'to_currency_id' => $this->usdCurrency->id,
            'date' => now()->format('Y-m-d')
        ]);
        $this->assertDatabaseHas('currency_rates_history', [
            'from_currency_id' => $this->plnCurrency->id,
            'to_currency_id' => $this->eurCurrency->id,
            'date' => now()->format('Y-m-d')
        ]);
        $this->assertDatabaseHas('currency_rates_history', [
            'from_currency_id' => $this->plnCurrency->id,
            'to_currency_id' => $this->gbpCurrency->id,
            'date' => now()->format('Y-m-d')
        ]);
    }

    public function test_import_currency_rates_successfully()
    {
        Http::fake([
            'https://api.nbp.pl/api/exchangerates/rates/A/USD/2025-07-03?format=json' => Http::response([
                'rates' => [
                    ['mid' => 4.20]
                ]
            ])
        ]);

        $this->artisan('currency:import-current-rates', ['--currency' => 'USD', '--date' => '2025-07-03'])
             ->assertExitCode(0)
             ->expectsOutput('Importing rates for currency: USD on 2025-07-03')
             ->expectsOutput('Successfully imported rate for USD: 4.2')
             ->doesntExpectOutput('Could not get rate for USD')
             ->expectsOutput('Import completed!')
             ->run();

        $this->assertDatabaseHas('currency_rates_history', [
            'from_currency_id' => $this->plnCurrency->id,
            'to_currency_id' => $this->usdCurrency->id,
            'rate' => 42000,
        ]);
    }

    public function test_import_with_invalid_currency(): void
    {
        $this->artisan('currency:import-current-rates', [
            '--currency' => 'INVALID'
        ])
        ->assertExitCode(0);

        // Assert
        $this->assertDatabaseMissing('currency_rates_history', [
            'to_currency_id' => Currency::where('symbol', 'INVALID')->first()->id ?? 0
        ]);
    }

    public function test_import_with_invalid_date(): void
    {
        try {
            $this->artisan('currency:import-current-rates', [
                '--date' => 'invalid-date'
            ]);
            $this->fail('Expected exception was not thrown');
        } catch (\InvalidArgumentException $e) {
            $this->assertInstanceOf(\InvalidArgumentException::class, $e);
            $this->assertStringContainsString('Invalid date format. Please use YYYY-MM-DD format.', $e->getMessage());
        }
    }
}
