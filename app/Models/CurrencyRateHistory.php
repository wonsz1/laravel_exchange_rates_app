<?php

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read Currency $fromCurrency
 * @property-read Currency $toCurrency
 */
#[ApiResource(
    operations: [
        new \ApiPlatform\Metadata\Get(normalizationContext: ['groups' => ['currency_rate_history:read']]),
        new \ApiPlatform\Metadata\GetCollection(normalizationContext: ['groups' => ['currency_rate_history:read', 'currency_rate_history:list']]),
        new \ApiPlatform\Metadata\Post(denormalizationContext: ['groups' => ['currency_rate_history:write']]),
    ]
)]
class CurrencyRateHistory extends Model
{
    protected $table = 'currency_rates_history';
    protected $fillable = [
        'from_currency_id',
        'to_currency_id',
        'rate',
        'date'
    ];

    #[Groups(['currency_rate_history:read', 'currency_rate_history:list', 'currency_rate_history:write'])]
    #[ApiProperty(example: '421234')]
    public function getRate(): int
    {
        return $this->rate;
    }

    #[Groups(['currency_rate_history:read', 'currency_rate_history:list', 'currency_rate_history:write'])]
    #[ApiProperty(example: '2025-01-01T00:00:00')]
    public function getDate(): string
    {
        return $this->date;
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
