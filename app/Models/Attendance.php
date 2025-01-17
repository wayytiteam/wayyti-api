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
            ->select('x.id', 'x.created_at', DB::raw("DATE_PART('day', x.created_at::timestamp - y.created_at::timestamp) AS date_diff"))
            ->join('attendances as y', function ($join) {
                $join->on('x.user_id', '=', 'y.user_id')
                    ->whereRaw('x.created_at > y.created_at');
            })
            ->where('x.user_id', $user_id)
            ->groupBy('x.id', 'x.created_at', 'y.created_at')
            ->havingRaw('MAX(DATE_PART(\'day\', x.created_at::timestamp - y.created_at::timestamp)) != 1')
            ->orderByDesc('x.created_at')
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
        if(!$next_login_badge) {
            $next_login_badge = null;
        }
        $login_streak = array(
            'current_streak' => $current_streak,
            'current_badge' => $current_badge,
            'next_badge' => $next_login_badge
        );

        return $login_streak;
    }
}
