<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\MonthlyDrawWinnerNotificationSent;
use App\Models\MonthlyDrawWinner;
use App\Models\Notification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MonthlyDrawWinnerController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->query('month');
        $year = $request->query('year');
        $winners = MonthlyDrawWinner::whereHas('user.monthly_draws')
            ->when($month, function (Builder $query) use ($month) {
                $query->whereMonth('created_at', $month);
            })
            ->when($year, function (Builder $query) use ($year) {
                $query->whereYear('created_at', $year);
            })
            ->with(['user.monthly_draws'])->paginate(10);
        return response()->json($winners, 200);
    }

    public function show(MonthlyDrawWinner $monthly_draw_winner, Request $request) {
        $monthly_draw_winner = $monthly_draw_winner->load('user');
        $winner = $monthly_draw_winner['user'];
        $subject = 'Congratulations, '.$winner['username'].'!'. ' You’re Wayyti’s Monthly Draw Winner!';
        // return $winner['email'];
        // dd($winner['username']);
        if($request->notify) {
            //Send email here
            Mail::to($winner['email'])->send(new MonthlyDrawWinnerNotificationSent($winner['username'], $subject));
        }
        return $monthly_draw_winner->load('user');
    }


    public function store()
    {
        $last_month = Carbon::now()->subDays(30);
        $last_month_name = $last_month->monthName;
        $last_month_int = $last_month->month;
        $first_week = Carbon::now()->startOfMonth()->addDays(7);
        $entries = User::whereHas('monthly_draws')->get();
        try {
            // if(Carbon::now() <= $first_week) {
                $entries = User::whereHas('monthly_draws', function (Builder $query) use ($last_month_int) {
                    $query->whereMonth('created_at', (int)$last_month_int);
                })
                    ->with('monthly_draws')
                    ->get();
                if(count($entries) > 0) {
                    $entered_users = [];
                    foreach($entries as $entry) {
                        $last_entry = null;
                        $total_monthly_draw = null;
                        foreach($entry->monthly_draws as $monthly_draw) {
                            if($last_entry != null) {
                                $total_monthly_draw = $last_entry += $monthly_draw->entries;
                            } else {
                                $last_entry = $monthly_draw->entries;
                                $total_monthly_draw = $monthly_draw->entries;
                            }
                        }
                        $entered_users[$entry->email] = $total_monthly_draw;
                    }
                    $total_weight = array_sum($entered_users);
                    $random_number = mt_rand(1, $total_weight);
                    $winner_email = null;
                    foreach ($entered_users as $entered_user => $total_entry) {
                        $random_number -= $total_entry;
                        if ($random_number <= 0) {
                            $winner_email = $entered_user;
                            break;
                        }
                    }
                    $get_winner = User::where('email', $winner_email)->first();
                    $monthly_draw_winner = MonthlyDrawWinner::create([
                        'user_id' => $get_winner->id
                    ]);
                    Notification::create([
                        'user_id' => $get_winner->id,
                        'message' => 'You have won the monthly draw!',
                        'type' => 'monthly_draw_won',
                        'monthly_draw_winner_id' => $monthly_draw_winner->id
                    ]);
                    return response()->json($get_winner, 200);
                } else {
                    throw new Exception("No Entries found for the month of ".$last_month_name, 400);
                }
            // } else {
            //     throw new Exception("Monthly draw winner for the month of ".$last_month_name." should be decided until first week of this month", 400);
            // }
        } catch(\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], $e->getCode());
        }
    }
}
