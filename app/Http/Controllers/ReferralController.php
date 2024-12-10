<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use App\Models\User;
use App\Models\Badge;
use App\Models\BadgeUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                Referral::create([
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
                        $current_badge->badge_id = $badge_equivalent->id;
                        $current_badge->save();
                    }
                } else {
                    BadgeUser::create([
                        'user_id' => $user->id,
                        'badge_id' => $badge_equivalent->id
                    ]);
                }
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
