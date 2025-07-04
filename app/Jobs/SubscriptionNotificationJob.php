<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;

class SubscriptionNotificationJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Artisan::call('app:subscription-notification');
    }
}
