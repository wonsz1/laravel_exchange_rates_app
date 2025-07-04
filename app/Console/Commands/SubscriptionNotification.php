<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Models\CurrencyRateHistory;
use App\Notifications\CurrencyRateNotification;
use \Illuminate\Log\LogManager as Log;

class SubscriptionNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:subscription-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $log;

    /**
     * Execute the console command.
     */
    public function handle(Subscription $subscriptionModel, CurrencyRateHistory $currencyRateHistoryModel, Log $log)
    {
        $this->log = $log;
        $subscriptions = $subscriptionModel->where('is_active', true)
            ->with(['fromCurrency', 'toCurrency'])
            ->get();

        foreach ($subscriptions as $subscription) {
            $currentRate = $currencyRateHistoryModel->where([
                'from_currency_id' => $subscription->fromCurrency->id,
                'to_currency_id' => $subscription->toCurrency->id,
                'date' => now()->format('Y-m-d')
            ])->first();

            if($currentRate === null) {
                $this->log->info("No rate found for subscription: " . $subscription->id);
                continue;
            }

            if ($this->shouldNotify($subscription, $currentRate)) {
                $this->notifyUser($subscription, $currentRate->rate);
            } else {
                $this->log->info("Subscription {$subscription->id} should not be notified");
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

    private function isThresholdMet(Subscription $subscription, int $currentRate): bool
    {
        $currentRateFloat = $currentRate / 10000;
        return match ($subscription->direction) {
            'above' => $currentRateFloat > $subscription->threshold,
            'below' => $currentRateFloat < $subscription->threshold,
            default => false
        };
    }

    private function notifyUser(Subscription $subscription, int $currentRate): void
    {
        $this->log->info("Notifying user {$subscription->user->name} about rate change for {$subscription->fromCurrency->symbol} to {$subscription->toCurrency->symbol}");

        $subscription->user->notify(new CurrencyRateNotification(
            $subscription->fromCurrency,
            $subscription->toCurrency,
            $currentRate / 10000,
            (float)$subscription->threshold,
            $subscription->direction
        ));

        // Update last notified timestamp
        $subscription->update([
            'last_notified_at' => now()->format('Y-m-d')
        ]);
    }
}
