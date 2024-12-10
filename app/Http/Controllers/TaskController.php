<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\IncompleteRegistrationSent;
use Illuminate\Support\Facades\Artisan;

class TaskController extends Controller
{
    public function mail_incomplete_profiles()
    {
        $today = now();
        $incomplete_users = User::where('username', null)
              ->orWhere('country', null)
              ->orWhere('age_group', null)
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
        return response()->json([
            'message' => "Task execution done"
        ], 200);
    }
}
