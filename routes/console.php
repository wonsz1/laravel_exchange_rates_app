<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('currency:import-current-rates')->dailyAt('00:00');
Schedule::command('app:dispatch-subscription-notification-job')->dailyAt('08:00');