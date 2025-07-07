<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Currency;
use Carbon\Carbon;
use App\Repositories\Contracts\CurrencyRateHistoryInterface;

class CurrencyChart extends Component
{
    public $fromCurrency;
    public $toCurrency;
    public $rates = [];
    public $dates = [];
    public $fromDate;

    public function mount(string $fromCurrencySymbol, string $toCurrencySymbol, Currency $currencyModel, CurrencyRateHistoryInterface $currencyRateHistoryRepository)
    {
        $this->fromCurrency = $currencyModel->where('symbol', $fromCurrencySymbol)->first();
        $this->toCurrency = $currencyModel->where('symbol', $toCurrencySymbol)->first();

        //[todo] make this selectable on the view
        $fromDate = Carbon::now()->subDays(7);

        $this->rates = [];
        $this->dates = [];
        $this->fromDate = $fromDate;

        $rates = $currencyRateHistoryRepository->getRatesBetweenCurrenciesFromDate($this->fromCurrency->id, $this->toCurrency->id, $fromDate);

        foreach ($rates as $rate) {
            $this->dates[] = Carbon::parse($rate->date)->format('Y-m-d');
            $this->rates[] = $rate->rate / 10000;
        }
    }

    public function render()
    {
        return view('livewire.currency-chart');
    }
}
