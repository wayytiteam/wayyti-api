<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Share extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'product_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(Share::class);
    }

    public static function get_share_badge($user_id) {
        $count_shares = Share::where('user_id', $user_id)
            ->count();
        $current_badge = BadgeUser::where('user_id', $user_id)
            ->whereHas('badge', function (Builder $query) {
                $query->where('type', 'share');
            })
            ->first();
        if($current_badge) {
            $current_badge = Badge::find($current_badge->badge_id);
        } else {
            $current_badge = null;
        }
        $next_badge = Badge::where('type', 'share')
            ->orderBy('requirement_value', 'asc')
            ->where('requirement_value', '>', $count_shares)
            ->first();
        $share_deals = array(
            'current_shares' => $count_shares,
            'current_badge' => $current_badge,
            'next_badge' => $next_badge
        );

        return $share_deals;
    }
}
