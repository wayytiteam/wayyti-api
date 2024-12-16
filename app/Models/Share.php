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

    public static function get_share_badge(User $user) {
        $count_shares = Share::where('user_id', $user->id)
            ->count();
        $badge_equivalent = Badge::where('type', 'share')
            ->orderBy('requirement_value', 'desc')
            ->where('requirement_value', '<=', $count_shares)
            ->first();
        $current_badge = BadgeUser::where('user_id', $user->id)
            ->whereHas('badge', function (Builder $query) {
                $query->where('type', 'share');
            })
            ->first();
        $current_badge_details = null;
        if($current_badge) {
            $current_badge_details = Badge::find($current_badge->badge_id);
            if($current_badge->badge_id != $badge_equivalent->id) {
                if($current_badge->badge->requirement_value < $badge_equivalent->requirement_value) {
                    $new_notification = Notification::create([
                        'user_id' => $user->id,
                        'badge_id' => $badge_equivalent->id,
                        'message' => 'Achievement Unlocked',
                        'description' => 'You have unlocked the'.' '.$badge_equivalent->name.' '.'Badge',
                        'type' => 'achievement_unlocked'
                    ]);
                    if($user->fcm_token) {
                        Notification::send_notification($new_notification->message, $new_notification->description, $user->fcm_token);
                    }
                }
                $current_badge->badge_id = $badge_equivalent->id;
                $current_badge->save();
            }
        } else {
            if($badge_equivalent) {
                BadgeUser::create([
                    'user_id' => $user->id,
                    'badge_id' => $badge_equivalent->id
                ]);
                $new_notification = Notification::create([
                    'user_id' => $user->id,
                    'badge_id' => $badge_equivalent->id,
                    'message' => 'Achievement Unlocked',
                    'description' => 'You have unlocked the'.' '.$badge_equivalent->name.' '.'Badge',
                    'type' => 'achievement_unlocked'
                ]);
                if($user->fcm_token) {
                    Notification::send_notification($new_notification->message, $new_notification->description, $user->fcm_token);
                }
            }
        }
        $next_badge = Badge::where('type', 'share')
            ->orderBy('requirement_value', 'asc')
            ->where('requirement_value', '>', $count_shares)
            ->first();
        if(!$next_badge) {
            $next_badge = null;
        }
        $share_deals = array(
            'current_shares' => $count_shares,
            'current_badge' => $current_badge_details,
            'next_badge' => $next_badge
        );

        return $share_deals;
    }
}
