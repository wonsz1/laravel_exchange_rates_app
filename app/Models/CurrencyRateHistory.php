<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyRateHistory extends Model
{
    protected $table = 'currency_rates_history';
    protected $fillable = [
        'from_currency_id',
        'to_currency_id',
        'rate',
        'date'
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'date' => 'datetime'
    ];

    // Relationships
    public function fromCurrency()
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    public function toCurrency()
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }
}
