<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;


#[ApiResource(
    operations: [
        new \ApiPlatform\Metadata\Get(normalizationContext: ['groups' => ['currency:read']]),
        new \ApiPlatform\Metadata\GetCollection(normalizationContext: ['groups' => ['currency:read', 'currency:list']]),
        new \ApiPlatform\Metadata\Post(denormalizationContext: ['groups' => ['currency:write']]),
    ]
)]
class Currency extends Model
{
    public const BASE_CURRENCY = 'PLN';

    #[Groups(['currency:read', 'currency:list', 'subscription:read'])]
    public function getId(): int
    {
        return $this->id;
    }

    #[Groups(['currency:read', 'currency:list', 'currency:write', 'subscription:read'])]
    public function getSymbol(): string
    {
        return $this->symbol;
    }

    #[Groups(['currency:read', 'currency:list', 'currency:write'])]
    public function getName(): string
    {
        return $this->name;
    }
    
    protected $fillable = [
        'symbol',
        'name',
        'iso_code',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
    public function fromSubscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'from_currency_id');
    }

    public function toSubscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'to_currency_id');
    }

    public function ratesAsBase(): HasMany
    {
        return $this->hasMany(CurrencyRateHistory::class, 'from_currency_id');
    }

    public function ratesAsTarget(): HasMany
    {
        return $this->hasMany(CurrencyRateHistory::class, 'to_currency_id');
    }
}
