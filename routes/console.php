<?php

use App\Console\Commands\CheckSubscriptionStatus;
use App\Console\Commands\PriceUpdate;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\MailIncompleteProfiles;

Schedule::command(MailIncompleteProfiles::class)
    ->daily();
Schedule::command(PriceUpdate::class)->hourly();
Schedule::command(CheckSubscriptionStatus::class)->daily();
