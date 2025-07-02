<?php

namespace App\Jobs;

use App\Models\Currency;
use App\Models\CurrencyRateHistory;
use App\Models\Subscription;
use App\Notifications\CurrencyRateNotification;
use App\Services\ExchangeRateApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Attributes\WithoutRelations;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SubscriptionNotificationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    /**
     * Create a new job instance.
     *
     * If your queued job accepts an Eloquent model in its constructor,
     * only the identifier for the model will be serialized onto the queue.
     * When the job is actually handled, the queue system will automatically re-retrieve
     * the full model instance from the database. It's all totally transparent to your
     * application and prevents issues that can arise from serializing full Eloquent model instances.
     * https://laravel.com/docs/12.x/queues
     */
    public function __construct(
        #[WithoutRelations]
        private Subscription $subscription,
        #[WithoutRelations]
        private CurrencyRateHistory $currencyRateHistory
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(ExchangeRateApiService $exchangeService): void
    {
        //Log::info("SubscriptionNotificationJob started for base currency: ");

        // Get all active subscriptions
        $subscriptions = $this->subscription->where('is_active', true)
            ->with(['fromCurrency', 'toCurrency'])
            ->get();

        $currentDate = now();

        foreach ($subscriptions as $subscription) {
            // Get current exchange rate
            $currentRate = $exchangeService->getExchangeRate(
                $subscription->fromCurrency->symbol,
                $subscription->toCurrency->symbol,
                $currentDate
            );
            if ($currentRate === null) {
                continue;
            }

            // Save rate history
            $this->currencyRateHistory->upsert([
                'from_currency_id' => $subscription->fromCurrency->id,
                'to_currency_id' => $subscription->toCurrency->id,
                'rate' => (int)($currentRate * 10000),
                'date' => $currentDate->format('Y-m-d')
            ], ['from_currency_id', 'to_currency_id', 'date'], ['rate']);

            // Check if threshold is met and notify if needed
            if ($this->shouldNotify($subscription, $currentRate)) {
                $this->notifyUser($subscription, $currentRate);
            }
        }
    }

    /**
     * Check if notification should be sent based on threshold
     */
    private function shouldNotify(Subscription $subscription, float $currentRate): bool
    {
        // Check if threshold was previously met
        if ($subscription->last_notified_at) {
            $lastRate = $this->currencyRateHistory->where('from_currency_id', $subscription->from_currency_id)
                ->where('to_currency_id', $subscription->to_currency_id)
                ->where('date', '<=', $subscription->last_notified_at)
                ->orderBy('date', 'desc')
                ->first();

            if ($lastRate && $this->isThresholdMet($subscription, $lastRate->rate)) {
                return false; // Already notified about this threshold
            }
        }

        return $this->isThresholdMet($subscription, $currentRate);
    }

    /**
     * Check if current rate meets the subscription threshold
     */
    private function isThresholdMet(Subscription $subscription, float $currentRate): bool
    {
        return match ($subscription->direction) {
            'above' => $currentRate > $subscription->threshold,
            'below' => $currentRate < $subscription->threshold,
            default => false
        };
    }

    /**
     * Send notification to user
     */
    private function notifyUser(Subscription $subscription, float $currentRate): void
    {
        $subscription->user->notify(new CurrencyRateNotification(
            $subscription->fromCurrency,
            $subscription->toCurrency,
            $currentRate,
            $subscription->threshold,
            $subscription->direction
        ));

        // Update last notified timestamp
        $subscription->update([
            'last_notified_at' => now()
        ]);
    }
}
