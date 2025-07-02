<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subscription;

class SubscriptionSeeder extends Seeder
{
    public function __construct(private Subscription $subscription)
    {
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subscriptions = [
            [
                'user_id' => 1,
                'from_currency_id' => 4,
                'to_currency_id' => 2,
                'threshold' => 1,
                'direction' => 'above',
                'is_active' => true
            ],
            [
                'user_id' => 1,
                'from_currency_id' => 4,
                'to_currency_id' => 3,
                'threshold' => 1,
                'direction' => 'above',
                'is_active' => true
            ]
        ];

        foreach ($subscriptions as $subscription) {
            $this->subscription->create($subscription);
        }
    }
}
