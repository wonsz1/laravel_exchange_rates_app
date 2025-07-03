<?php

namespace App\Jobs;

use App\Models\CurrencyRateHistory;
use App\Models\Subscription;
use App\Notifications\CurrencyRateNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Attributes\WithoutRelations;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
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

    public function handle(): void
    {
        $subscriptions = $this->subscription->where('is_active', true)
            ->with(['fromCurrency', 'toCurrency'])
            ->get();

        foreach ($subscriptions as $subscription) {
            $currentRate = $this->currencyRateHistory->where([
                'from_currency_id' => $subscription->fromCurrency->id,
                'to_currency_id' => $subscription->toCurrency->id,
                'date' => now()->format('Y-m-d')
            ])->first();

            Log::info([
                'from_currency_id' => $subscription->fromCurrency->id,
                'to_currency_id' => $subscription->toCurrency->id,
                'date' => now()->format('Y-m-d')
            ]);

            if($currentRate === null) {
                continue;
            }

            if ($this->shouldNotify($subscription, $currentRate)) {
                $this->notifyUser($subscription, $currentRate->rate);
            }
        }
    }

    private function shouldNotify(Subscription $subscription, object $currentRate): bool
    {
        // Check if user was already notified
        if ($subscription->last_notified_at && $currentRate->date == $subscription->last_notified_at) {
            return false;
        }

        return $this->isThresholdMet($subscription, $currentRate->rate);
    }

    private function isThresholdMet(Subscription $subscription, float $currentRate): bool
    {
        return match ($subscription->direction) {
            'above' => $currentRate > $subscription->threshold,
            'below' => $currentRate < $subscription->threshold,
            default => false
        };
    }

    private function notifyUser(Subscription $subscription, int $currentRate): void
    {
        $subscription->user->notify(new CurrencyRateNotification(
            $subscription->fromCurrency,
            $subscription->toCurrency,
            $currentRate / 10000,
            $subscription->threshold,
            $subscription->direction
        ));

        // Update last notified timestamp
        $subscription->update([
            'last_notified_at' => now()
        ]);
    }
}
