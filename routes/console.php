<?php

use App\Console\Commands\CheckSubscriptionStatus;
use App\Console\Commands\PriceUpdate;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\MailIncompleteProfiles;

Schedule::command(MailIncompleteProfiles::class)
    ->daily();
Schedule::command(PriceUpdate::class)->dailyAt('07:30');
Schedule::command(PriceUpdate::class)->dailyAt('12:30');
Schedule::command(PriceUpdate::class)->dailyAt('18:30');
Schedule::command(CheckSubscriptionStatus::class)->daily();
