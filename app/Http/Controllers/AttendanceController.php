<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Badge;
use App\Models\BadgeUser;
use App\Models\User;
use App\Models\LoginStreakBadge;
use App\Models\Notification;
use App\Models\Point;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::find(Auth::id());
        // $user_id = $user->id;
        $login_streak = Attendance::get_login_badge($user->id);
        return response()->json($login_streak, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $now = Carbon::now();
        $user = User::find($request->user_id);
        // $current_streak = Attendance::where('user_id', $user->id)
        // ->count();
        // return $current_streak;
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('created_at', $now)
            ->first();
        if(!$attendance) {
            $attendance = Attendance::create([
                'user_id' => $user->id
            ]);
        }
        $start_of_last_login_streak = DB::table('attendances as x')
            ->select('x.id', 'x.created_at', DB::raw("DATE_PART('day', x.created_at::timestamp - y.created_at::timestamp) AS date_diff"))
            ->join('attendances as y', function ($join) {
                $join->on('x.user_id', '=', 'y.user_id')
                    ->whereRaw('x.created_at > y.created_at');
            })
            ->where('x.user_id', $user->id)
            ->groupBy('x.id', 'x.created_at', 'y.created_at')
            ->havingRaw('MAX(DATE_PART(\'day\', x.created_at::timestamp - y.created_at::timestamp)) != 1')
            ->orderByDesc('x.created_at')
            ->limit(1)
            ->first();
        if($start_of_last_login_streak) {
            $current_streak = Attendance::where('user_id', $user->id)
                ->whereDate('created_at', '>=', $start_of_last_login_streak->created_at)
                ->count();
        } else {
            $current_streak = Attendance::where('user_id', $user->id)
                ->count();
            // $current_streak = 1;
        }
        // return $current_streak;
        $login_badge_acquired = Badge::where('type', 'login')
            ->where('requirement_value', '<=', $current_streak)
            ->orderBy('requirement_value', 'desc')
            ->first();
        $existing_badge = BadgeUser::where('user_id', $user->id)
            ->whereHas('badge', function ($query) use ($login_badge_acquired) {
                $query->where('type', 'login')
                    // ->where('id', $login_badge_acquired->id)
                    ;
            })
            ->with('badge')
            ->first();
        if($existing_badge) {
            if($existing_badge->badge_id !== $login_badge_acquired->id){
                $existing_badge->badge_id = $login_badge_acquired->id;
                $existing_badge->save();
                Point::create([
                    'user_id' => $user->id,
                    'attendance_id' => $attendance->id,
                    'points' => $existing_badge->badge->points_equivalent,
                ]);
                if($login_badge_acquired->points_equivalent > $existing_badge->badge->points_equivalent) {
                    $new_notification = Notification::create([
                        'user_id' => $user->id,
                        'message' => 'Achievement Unlocked',
                        'description' => 'You have unlocked the'.' '.$login_badge_acquired->name.' '.'Badge',
                        'badge_id' => $login_badge_acquired->id,
                        'type' => 'achievement_unlocked'
                    ]);
                    if($user->fcm_token){
                        Notification::send_notification($new_notification->message, $new_notification->description, $user->fcm_token, $new_notification);
                    }
                }
            }
        } else {
            $new_badge = BadgeUser::create([
                'user_id' => $user->id,
                'badge_id' => $login_badge_acquired->id
            ]);
            Point::create([
                'user_id' => $user->id,
                'attendance_id' => $attendance->id,
                'points' => $login_badge_acquired->points_equivalent
            ]);
            $new_notification = Notification::create([
                'user_id' => $user->id,
                'message' => 'Achievement Unlocked',
                'description' => 'You have unlocked the'.' '.$login_badge_acquired->name.' '.'Badge',
                'badge_id' => $login_badge_acquired->id,
                'type' => 'achievement_unlocked'
            ]);
            if($user->fcm_token) {
                Notification::send_notification($new_notification->message, $new_notification->description, $user->fcm_token, $new_notification);
            }
        }
        $next_login_badge = Badge::where('type', 'login')
            ->where('requirement_value', '>', $current_streak)
            ->orderBy('requirement_value', 'asc')
            ->first();
        return response()->json([
            'current_streak' => $current_streak,
            'current_badge' => $login_badge_acquired,
            'next_badge' => $next_login_badge
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }
}
