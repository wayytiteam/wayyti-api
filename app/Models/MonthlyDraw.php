<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class MonthlyDraw extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'entries',
    ];

    public static function get_monthly_draw_status(User $user) {
        $user_id = $user->id;
        $overall_total_points = Point::where('user_id', $user_id)
            ->where(function (Builder $query) use ($user) {
                $query->where('country', $user->country)
                    ->orWhereNull('country');
            })
            ->sum('points');
        $monthly_draw_entries = MonthlyDraw::where('user_id', $user_id)
            ->sum('entries');
        $current_total_points = $overall_total_points - ($monthly_draw_entries * 50);
        if($current_total_points < 0) {
            $current_total_points = 0;
        }
        $next_draw_entry = Badge::where('type', 'monthly-draw')
            ->where('points_equivalent', '>', (int)$current_total_points)
            ->orderBy('requirement_value', 'asc')
            ->first();
        $monthly_draw = array(
            'accumulated_points' => (int)$current_total_points,
            'next_draw_entry' => $next_draw_entry,
        );
        return $monthly_draw;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
