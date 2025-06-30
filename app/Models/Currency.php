<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
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
    public function fromSubscriptions()
    {
        return $this->hasMany(Subscription::class, 'from_currency_id');
    }

    public function toSubscriptions()
    {
        return $this->hasMany(Subscription::class, 'to_currency_id');
    }

    public function ratesAsBase()
    {
        return $this->hasMany(CurrencyRateHistory::class, 'from_currency_id');
    }

    public function ratesAsTarget()
    {
        return $this->hasMany(CurrencyRateHistory::class, 'to_currency_id');
    }
}
