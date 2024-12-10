<?php

namespace App\Http\Controllers;

use App\Models\GoogleProduct;
use App\Models\Share;
use App\Models\User;
use App\Models\Point;
use App\Models\Badge;
use App\Models\BadgeUser;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShareController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::find(Auth::id());
        $share_deal = Share::get_share_badge($user->id);
        return response()->json($share_deal, 200);
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
        try {
            $user = User::find(Auth::id());
            if($user) {
                $share = Share::create([
                    'user_id' => $user->id,
                    'product_id' => $request->product_id
                ]);
                Point::create([
                    'user_id' => $user->id,
                    'share_id' => $share->id
                ]);
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
                return response()->json([
                    'message' => 'Thank you for sharing this product'
                ], 200);
            } else {
                throw new Exception("User not found");
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Share $share)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Share $share)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Share $share)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Share $share)
    {
        //
    }
}
