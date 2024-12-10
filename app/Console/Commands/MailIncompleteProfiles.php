<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use App\Mail\IncompleteRegistrationSent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Builder;

class MailIncompleteProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:incomplete-profiles';

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
        $today = now();
        $incomplete_users = User::where('is_admin', false)
        ->where(function (Builder $query) {
            $query->whereNull('username')
            ->orWhereNull('country')
            ->orWhereNull('age_group');
        })
            ->get();
        foreach($incomplete_users as $user) {
            $created_date = Carbon::parse($user->created_at);
            $days_difference = $created_date->diffInDays(Carbon::parse($today));
            if($days_difference < 7){
                Mail::to($user->email)->send(new IncompleteRegistrationSent());
            } else {
                $user->delete();
            }
        }
        echo('Done');
    }
}
