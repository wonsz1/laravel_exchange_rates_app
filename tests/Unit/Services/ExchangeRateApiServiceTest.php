<?php

namespace Tests\Unit\Services;

use App\Services\ExchangeRateApiService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExchangeRateApiServiceTest extends TestCase
{
    private ExchangeRateApiService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExchangeRateApiService();
    }

    public function test_get_exchange_rate_success()
    {
        // Arrange
        $date = new \DateTime('2025-07-03');
        $targetCurrency = 'USD';
        
        Http::fake([
            'https://api.nbp.pl/api/exchangerates/rates/A/USD/2025-07-03?format=json' => Http::response([
                'rates' => [
                    ['mid' => 4.20]
                ]
            ])
        ]);

        // Act
        $result = $this->service->getExchangeRate(null, $targetCurrency, $date);

        // Assert
        $this->assertEquals(4.20, $result);
    }

    public function test_get_exchange_rate_failure()
    {
        // Arrange
        $date = new \DateTime('2025-07-03');
        $targetCurrency = 'USD';
        
        Http::fake([
            'https://api.nbp.pl/api/exchangerates/rates/A/USD/2025-07-03?format=json' => Http::response([], 404)
        ]);

        // Act
        $result = $this->service->getExchangeRate(null, $targetCurrency, $date);

        // Assert
        $this->assertNull($result);
    }

    public function test_get_exchange_rate_exception()
    {
        // Arrange
        $date = new \DateTime('2025-07-03');
        $targetCurrency = 'USD';
        
        Http::fake([
            'https://api.nbp.pl/api/exchangerates/rates/A/USD/2025-07-03?format=json' => Http::response(null, 500)
        ]);

        // Act
        $result = $this->service->getExchangeRate(null, $targetCurrency, $date);

        // Assert
        $this->assertNull($result);
    }

    public function test_get_historical_exchange_rates_success()
    {
        // Arrange
        $fromDate = new \DateTime('2025-07-01');
        $toDate = new \DateTime('2025-07-03');
        $targetCurrency = 'USD';
        
        Http::fake([
            'https://api.nbp.pl/api/exchangerates/rates/A/USD/2025-07-01/2025-07-03?format=json' => Http::response([
                'rates' => [
                    ['mid' => 4.20],
                    ['mid' => 4.21],
                    ['mid' => 4.22]
                ]
            ])
        ]);

        // Act
        $result = $this->service->getHistoricalExchangeRates(null, $targetCurrency, $fromDate, $toDate);

        // Assert
        $this->assertCount(3, $result);
        $this->assertArrayHasKey('mid', $result[0]);
    }

    public function test_get_historical_exchange_rates_failure()
    {
        // Arrange
        $fromDate = new \DateTime('2025-07-01');
        $toDate = new \DateTime('2025-07-03');
        $targetCurrency = 'USD';
        
        Http::fake([
            'https://api.nbp.pl/api/exchangerates/rates/A/USD/2025-07-01/2025-07-03?format=json' => Http::response([], 404)
        ]);

        // Act
        $result = $this->service->getHistoricalExchangeRates(null, $targetCurrency, $fromDate, $toDate);

        // Assert
        $this->assertEmpty($result);
    }

    public function test_get_historical_exchange_rates_exception()
    {
        // Arrange
        $fromDate = new \DateTime('2025-07-01');
        $toDate = new \DateTime('2025-07-03');
        $targetCurrency = 'USD';
        
        Http::fake([
            'https://api.nbp.pl/api/exchangerates/rates/A/USD/2025-07-01/2025-07-03?format=json' => Http::response(null, 500)
        ]);

        // Act
        $result = $this->service->getHistoricalExchangeRates(null, $targetCurrency, $fromDate, $toDate);

        // Assert
        $this->assertEmpty($result);
    }
}
