<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Referral extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id'
    ];

    public static function get_referral_status($user_id) {
        $count_referrals = Referral::where('user_id', $user_id)
        ->count();
        $current_badge = BadgeUser::where('user_id', $user_id)
            ->whereHas('badge', function (Builder $query) {
                $query->where('type', 'referral');
            })
            ->first();
        if($current_badge) {
            $current_badge = Badge::find($current_badge->badge_id);
        } else {
            $current_badge = null;
        }
        $next_badge = Badge::where('type', 'referral')
            ->orderBy('requirement_value', 'asc')
            ->where('requirement_value', '>', $count_referrals)
            ->first();
        if(!$next_badge) {
            $next_badge = null;
        }
        $referrals = array(
            'current_shares' => $count_referrals,
            'current_badge' => $current_badge,
            'next_badge' => $next_badge
        );
        return $referrals;
    }
}
