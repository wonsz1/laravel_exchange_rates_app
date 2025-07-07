<?php

namespace Tests\Unit\Livewire;

use App\Livewire\CurrencyChart;
use App\Models\Currency;
use App\Models\CurrencyRateHistory;
use App\Repositories\Contracts\CurrencyRateHistoryInterface;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Mockery;

use Tests\TestCase;

class CurrencyChartTest extends TestCase
{
    use RefreshDatabase;

    private $formCurrency;
    private $toCurrency;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create base currency
        $this->formCurrency = Currency::create([
            'symbol' => 'PLN',
            'name' => 'Polish Złoty',
            'iso_code' => 'PLN'
        ]);

        $this->toCurrency = Currency::create([
            'symbol' => 'USD',
            'name' => 'US Dollar',
            'iso_code' => 'USD'
        ]);

    }

    public function test_displays_currency_chart_with_exchange_rates()
    {
        $endDate = new \DateTime();
        $startDate = clone $endDate;
        $startDate->modify('-14 days');

        // Create some sample exchange rates
        $rates = [];
        for($date = $startDate; $date < $endDate; $date->modify('+1 day')){
            $rates[] = CurrencyRateHistory::create([
                'from_currency_id' => $this->formCurrency->id,
                'to_currency_id' => $this->toCurrency->id,
                'date' => $date->format('Y-m-d H:i:s'),
                'rate' => 45000, // 4.50 PLN/USD
            ]);
        }

        // Mock the repository
        $repository = $this->mock(CurrencyRateHistoryInterface::class);
        $repository->shouldReceive('getRatesBetweenCurrenciesFromDate')
            ->with($this->formCurrency->id, $this->toCurrency->id, $startDate)
            ->andReturn($rates);

        // Act
        $component = Livewire::test(CurrencyChart::class, [
            'fromCurrencySymbol' => 'PLN',
            'toCurrencySymbol' => 'USD',
            'currencyModel' => new Currency(),
            'currencyRateHistoryRepository' => $repository,
            'dateTime' => $startDate,
        ]);

        // Assert
        $this->assertCount(14, $component->dates);
        $this->assertCount(14, $component->rates);
        
        // Verify all rates are properly normalized (divided by 10000)
        foreach ($component->rates as $rate) {
            $this->assertEquals(4.5, $rate);
        }

        // Verify dates are in correct format
        foreach ($component->dates as $date) {
            $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}/', $date);
        }
    }

    public function test_handles_no_exchange_rates()
    {
        // Arra
        $repository = $this->mock(CurrencyRateHistoryInterface::class);
        $repository->shouldReceive('getRatesBetweenCurrenciesFromDate')
            ->andReturn([]);

        // Act
        $component = Livewire::test(CurrencyChart::class, [
            'fromCurrencySymbol' => 'PLN',
            'toCurrencySymbol' => 'USD',
            'currencyModel' => new Currency(),
            'currencyRateHistoryRepository' => $repository,
            'dateTime' => new \DateTime(),
        ]);

        // Assert
        $this->assertEmpty($component->dates);
        $this->assertEmpty($component->rates);
    }
}
