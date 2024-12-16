<?php

namespace App\Http\Controllers;

use App\Models\BadgeUser;
use App\Models\Attendance;
use App\Models\Badge;
use App\Models\MonthlyDraw;
use App\Models\Referral;
use App\Models\Share;
use App\Models\TrackedProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BadgeUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::find(Auth::id());
        $get_recent_badges = BadgeUser::where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->with('badge')
            ->get();
        $recent_badges = [];
        foreach($get_recent_badges as $recent_badge) {
            $recent_badge = Badge::find($recent_badge->badge->id);
            $recent_badges[] = $recent_badge;
        }
        $monthly_draw = MonthlyDraw::get_monthly_draw_status($user);
        $items_tracked = TrackedProduct::get_tracker_badge($user);
        $login_streak = Attendance::get_login_badge($user->id);
        $share_deals = Share::get_share_badge($user);
        $referrals = Referral::get_referral_status($user);
        $total_savings = TrackedProduct::get_savings_badge($user);
        return response()->json([
            'recent_badges' => $recent_badges,
            'monthly_draw' => $monthly_draw,
            'items_tracked' => $items_tracked,
            'login_streak' => $login_streak,
            'share_deals' => $share_deals,
            'refer_a_friend' => $referrals,
            'total_savings' => $total_savings
        ], 200);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
