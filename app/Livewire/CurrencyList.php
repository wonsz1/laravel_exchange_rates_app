<?php

namespace App\Livewire;

use App\Models\Currency;
use Livewire\Component;

class CurrencyList extends Component
{
    public function render()
    {
        $currencies = Currency::where('is_active', true)->get();
        return view('livewire.currency-list', ['currencies' => $currencies]);
    }
}
