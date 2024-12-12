<?php

use App\Console\Commands\PriceUpdate;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\MailIncompleteProfiles;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

Schedule::command(MailIncompleteProfiles::class)
    ->everyMinute();
Schedule::command(PriceUpdate::class)->hourly();
