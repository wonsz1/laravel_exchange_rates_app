<?php

namespace App\Console\Commands;

use App\Jobs\SubscriptionNotificationJob;
use Illuminate\Console\Command;
use App\Models\CurrencyRateHistory;
use App\Models\Subscription;
use Illuminate\Log\LogManager;

class DispatchSubscriptionNotificationJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:dispatch-subscription-notification-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Dispatching SubscriptionNotificationJob...");
        dispatch(new SubscriptionNotificationJob(new Subscription(), new CurrencyRateHistory()))->onQueue('subscription-notifications');

        $this->info("SubscriptionNotificationJob dispatched successfully!");

        return Command::SUCCESS;
    }
}
