<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Currency;

/**
 * @property-read Currency $fromCurrency
 * @property-read Currency $toCurrency
 */
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
        'rate' => 'int',
        'date' => 'datetime'
    ];

    // Relationships
    public function fromCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    public function toCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }
}
