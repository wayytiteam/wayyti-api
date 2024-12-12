<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use App\Models\User;
use App\Models\Badge;
use App\Models\BadgeUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\Point;

class ReferralController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::find(Auth::id());
        $referrals = Referral::get_referral_status($user->id);
        return response()->json($referrals, 200);
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
        $user = User::find(Auth::id());
        try {
            if($user) {
                $new_referral = Referral::create([
                    'user_id' => $user->id
                ]);
                $count_referrals = Referral::where('user_id', $user->id)
                    ->count();
                $badge_equivalent = Badge::where('type', 'referral')
                    ->orderBy('requirement_value', 'desc')
                    ->where('requirement_value', '<=', $count_referrals)
                    ->first();
                $current_badge = BadgeUser::where('user_id', $user->id)
                    ->whereHas('badge', function (Builder $query) {
                        $query->where('type', 'referral');
                    })
                    ->first();
                if($current_badge) {
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
                                Notification::send_notification($new_notification->message, $new_notification->message, $user->fcm_token);
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
                            Notification::send_notification($new_notification->message, $new_notification->message, $user->fcm_token);
                        }
                    }
                }
                Point::create([
                    'user_id' => $user->id,
                    'points' => 20,
                    'referral_id' => $new_referral->id
                ]);
                return response()->json(null, 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Referral $referral)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Referral $referral)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Referral $referral)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Referral $referral)
    {
        //
    }
}
