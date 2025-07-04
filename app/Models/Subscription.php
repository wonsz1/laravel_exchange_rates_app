<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Currency;
use ApiPlatform\Metadata\ApiResource;

/**
 * @property-read User $user
 * @property-read Currency $fromCurrency
 * @property-read Currency $toCurrency
 */
#[ApiResource]
class Subscription extends Model
{
    /**
     * Threshold represents the exchange rate value that triggers a notification for the user.
     *
     * Example Usage:
     * If a user wants to be notified when 1 USD is worth more than 4.50 PLN
     * They would create a subscription with:
     * from_currency_id: USD
     * to_currency_id: PLN
     * threshold: 4.50
     * direction: 'above'
     *
     *  */
    protected $fillable = [
        'user_id',
        'from_currency_id',
        'to_currency_id',
        'threshold',
        'direction',
        'is_active',
        'last_notified_at'
    ];

    protected $casts = [
        'threshold' => 'decimal:4',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fromCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    public function toCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }
}
