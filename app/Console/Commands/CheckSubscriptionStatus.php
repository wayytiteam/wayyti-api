<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckSubscriptionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-subscription-status';

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
        $subscriptions = Subscription::get();
        foreach($subscriptions as $subscription) {
            if(!$subscription->has_subscribed){
                if($subscription->on_trial_mode) {
                    $trial_months_duration = Carbon::parse($subscription->updated_at)->floatDiffInMonths(Carbon::now());
                    if($trial_months_duration >= 2) {
                        $subscription->on_trial_mode = false;
                        $subscription->save();
                    }
                }
            }
        }
    }
}
