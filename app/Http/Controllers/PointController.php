<?php

namespace App\Http\Controllers;

use App\Models\MonthlyDraw;
use App\Models\Point;
use App\Models\User;
use App\Models\Badge;
use App\Models\BadgeUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PointController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = User::find(Auth::id());
        $get_points = Point::where('user_id', $user->id)
            ->where(function (Builder $query) use ($user) {
                $query->where('country', $user->country)
                    ->orWhereNull('country');
            });
        $get_ads_points = Point::where('user_id', $user->id)
            ->where('ads_point', true)
            ->sum('points');
        $over_all_points = $get_points->sum('points');
        $rank_equivalent = Badge::where('type', 'rank')
            ->where('points_equivalent', '<=', (int)$over_all_points)
            ->orderBy('points_equivalent', 'desc')
            ->first();
        if ($rank_equivalent) {
            $current_rank = BadgeUser::where('user_id', $user->id)
                ->whereHas('badge', function (Builder $query) {
                    $query->where('type', 'rank');
                })
                ->first();
            if ($current_rank) {
                if ($current_rank->badge_id !== $rank_equivalent->id) {
                    $current_rank->badge_id = $rank_equivalent->id;
                    $current_rank->save();
                    $rank_equivalent = $current_rank->badge;
                }
            } else {
                BadgeUser::create([
                    'user_id' => $user->id,
                    'badge_id' => $rank_equivalent->id,
                    'country' => $user->country
                ]);
            }
        }
        $accumulated_points = array(
            'total_points' => (int)$over_all_points,
            'current_rank' => $rank_equivalent,
            'ads_points' => (int)$get_ads_points
        );

        return response()->json([
            'accumulated_points' => $accumulated_points,
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
        $user = User::find($request->user_id);
        $add_points = $request->points;
        $points_added = Point::create([
            'user_id' => $user->id,
            'points' => $request->points,
            'ads_point' => $request->type === 'advertisement' ? true : false
        ]);

        return response()->json($points_added, 200);
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
