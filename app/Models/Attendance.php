<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Attendance extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function get_login_badge($user_id) {
        $start_of_last_login_streak = DB::table('attendances as x')
        ->select('x.id', 'y.created_at')
        ->distinct()
        ->joinSub(DB::table('attendances as a')
                ->select('a.id', 'a.created_at', DB::raw("MIN(DATE_PART('day', a.created_at::timestamp - b.created_at::timestamp)) AS date_diff"))
                ->join('attendances as b', function($join) {
                    $join->on('a.id', '!=', 'b.id')
                         ->whereRaw("DATE_PART('day', a.created_at::timestamp - b.created_at::timestamp) > 0");
                })
                ->where('a.user_id', $user_id)
                ->groupBy('a.id'),
            'y','x.id','=','y.id')
        ->where('x.user_id', $user_id)
        ->where('y.date_diff', '!=', 1)
        ->groupBy('x.id', 'y.created_at', 'y.date_diff')
        ->orderByDesc('y.created_at')
        ->limit(1)
        ->first();
        if($start_of_last_login_streak) {
            $current_streak = Attendance::where('user_id', $user_id)
                ->whereDate('created_at', '>=', $start_of_last_login_streak->created_at)
                ->count();
        } else {
            $current_streak = Attendance::where('user_id', $user_id)
                ->count();
        }
        $current_badge = BadgeUser::where('user_id', $user_id)
            ->whereHas('badge', function ($query) {
                $query->where('type', 'login');
            })
            ->orderBy('created_at', 'desc')
            ->with('badge')
            ->first();
        if(!$current_badge) {
            $current_badge = null;
        } else {
            $current_badge = Badge::where('id', $current_badge->badge_id)->first();
        }
        $next_login_badge = Badge::where('type', 'login')
            ->where('requirement_value', '>', $current_streak)
            ->orderBy('requirement_value', 'asc')
            ->first();
        $login_streak = array(
            'current_streak' => $current_streak,
            'current_badge' => $current_badge,
            'next_badge' => $next_login_badge
        );

        return $login_streak;
    }
}
