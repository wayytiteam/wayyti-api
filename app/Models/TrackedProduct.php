<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Rinvex\Country\CountryLoader;

class TrackedProduct extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = true;
    protected $fillable = [
        'user_id',
        'folder_id',
        'google_product_id',
    ];

    protected $casts = [
        "deal" => "boolean",
        'saved' => "float"
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function google_product(): BelongsTo
    {
        return $this->belongsTo(GoogleProduct::class);
    }

    public static function get_tracker_badge(User $user) {
        $user_id = $user->id;
        $count_tracked_items = $count_tracked_items = GoogleProduct::whereHas('tracked_products', function(Builder $query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->where('country', $user->country)
        ->count('id');
        $item_tracker_badge = Badge::where('type', 'tracker')
            ->where('requirement_value', '<=', $count_tracked_items)
            ->orderBy('requirement_value', 'desc')
            ->first();
        $existing_tracker_badge = BadgeUser::where('user_id', $user_id)
            ->where('country', $user->country)
            ->whereHas('badge', function($query) {
                $query->where('type', 'tracker');
            })
            ->with('badge')
            ->first();
        if($existing_tracker_badge) {
            if($item_tracker_badge) {
                if($existing_tracker_badge->badge_id !== $item_tracker_badge->id) {
                    if($existing_tracker_badge->badge->requirement_value < $item_tracker_badge->requirement_value) {
                        $new_notification = Notification::create([
                            'user_id' => $user_id,
                            'badge_id' => $item_tracker_badge->id,
                            'message' => 'Achievement Unlocked',
                            'description' => 'You have unlocked'.' '.$item_tracker_badge->name.' '.'badge',
                            'type' => 'achievement_unlocked'
                        ]);
                        if($user->fcm_token) {
                            Notification::send_notification($new_notification->message, $new_notification->message, $user->fcm_token);
                        }
                    }
                    $existing_tracker_badge->badge_id = $item_tracker_badge->id;
                    $existing_tracker_badge->save();
                }
            } else {
                $existing_tracker_badge->delete();
            }
        } else {
            if($item_tracker_badge) {
                BadgeUser::create([
                    'user_id' => $user_id,
                    'badge_id' => $item_tracker_badge->id,
                    'country' => $user->country
                ]);
                Point::create([
                    'points' => $item_tracker_badge->points_equivalent,
                    'user_id' => $user_id,
                    'country' => $user->country
                ]);
                $new_notification = Notification::create([
                    'user_id' => $user_id,
                    'badge_id' => $item_tracker_badge->id,
                    'message' => 'Achievement Unlocked',
                    'description' => 'You have unlocked'.' '.$item_tracker_badge->name.' '.'badge',
                    'type' => 'achievement_unlocked'
                ]);
                if($user->fcm_token) {
                    Notification::send_notification($new_notification->message, $new_notification->message, $user->fcm_token);
                }
            }
        }
        $next_item_tracker_badge = Badge::where('type', 'tracker')
            ->where('requirement_value', '>', $count_tracked_items)
            ->orderBy('requirement_value', 'asc')
            ->first();
        $items_tracked = array(
            'current_tracked_items' => $count_tracked_items,
            'current_badge' => $item_tracker_badge,
            'next_badge' => $next_item_tracker_badge
        );

        return $items_tracked;
    }

    public static function get_savings_badge(User $user) {
        $user_id = $user->id;
        $get_tracked_items = TrackedProduct::where('user_id', $user_id)
            ->whereHas('google_product', function (Builder $query) use ($user) {
                $query->where('country', $user->country);
            });
        $tracked_items = $get_tracked_items->distinct()
            ->sum('saved');
        $first_saving = $get_tracked_items->where('deal', true)
            ->orderBy('created_at', 'asc')
            ->first();
        if($first_saving) {
            $savings_start_date = Carbon::parse($first_saving->created_at)->format('m/d/Y');
        } else {
            $savings_start_date = null;
        }

        $total_saved_value = $tracked_items;
        $currency = Currency::where('country_name', $user->country)->first();
        $current_savings_str = $currency->symbol.(float)$total_saved_value;
        $total_saved_value = $total_saved_value == null ? 0.00 : $total_saved_value;
        $equivalent_savings_badge = Badge::where('type', 'savings')
        ->where('requirement_value', '<=', (int)$total_saved_value)
        ->orderBy('requirement_value', 'desc')
        ->first();
        $existing_savings_badge = BadgeUser::where('user_id', $user_id)
            ->where('country', $user->country)
            ->whereHas('badge', function($query) {
                $query->where('type', 'savings');
            })
            ->with('badge')
            ->first();
            if($existing_savings_badge) {
                if($equivalent_savings_badge) {
                    if($existing_savings_badge->badge_id !== $equivalent_savings_badge->id) {
                        if($existing_savings_badge->badge->requirement_value < $existing_savings_badge->requirement_value) {
                            $new_notification = Notification::create([
                                'user_id' => $user_id,
                                'badge_id' => $existing_savings_badge->id,
                                'message' => 'Achievement Unlocked',
                                'description' => 'You have unlocked'.' '.$existing_savings_badge->name.' '.'badge',
                                'type' => 'achievement_unlocked'
                            ]);
                            if($user->fcm_token) {
                                Notification::send_notification($new_notification->message, $new_notification->message, $user->fcm_token);
                            }
                        }
                        $existing_savings_badge->badge_id = $equivalent_savings_badge->id;
                        $existing_savings_badge->save();
                        $equivalent_savings_badge->requirement_value = $currency->symbol.$equivalent_savings_badge->requirement_value;
                    }
                } else {
                    $existing_savings_badge->delete();
                }
            } else {
                if($equivalent_savings_badge) {
                    BadgeUser::create([
                        'user_id' => $user_id,
                        'badge_id' => $equivalent_savings_badge->id,
                        'country' => $user->country
                    ]);
                    Point::create([
                        'points' => $equivalent_savings_badge->points_equivalent,
                        'user_id' => $user_id,
                        'country' => $user->country
                    ]);
                }
            }
        $next_saving_badge = Badge::where('type', 'savings')
            ->where('requirement_value', '>', (int)$total_saved_value)
            ->orderBy('requirement_value', 'asc')
            ->first();
        $next_saving_badge->requirement_value = $currency->symbol.$next_saving_badge->requirement_value;
        $current_savings = array(
            'current_savings' => "{$current_savings_str}",
            'savings_start_date' => $savings_start_date,
            'current_badge' => $equivalent_savings_badge,
            'next_badge' => $next_saving_badge
        );
        return $current_savings;
    }
}
