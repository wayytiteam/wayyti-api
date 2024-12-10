<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MonthlyDraw;
use App\Models\Point;
use App\Models\Badge;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class MonthlyDrawController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::find(Auth::id());
        $monthly_draw = MonthlyDraw::get_monthly_draw_status($user->id);
        return response()->json($monthly_draw, 200);
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
                $monthly_draw_entry = Badge::where('type', 'monthly-draw')
                    ->where('requirement_value', $request->entries)
                    ->first();
                $overall_total_points = Point::where('user_id', $user->id)->sum('points');
                $monthly_draw_entries = MonthlyDraw::where('user_id', $user->id)
                    ->sum('entries');
                $current_points = $overall_total_points - ($monthly_draw_entries * 50);
                if($current_points >= $monthly_draw_entry->points_equivalent) {
                    MonthlyDraw::create([
                        'user_id' => $user->id,
                        'entries' => $request->entries
                    ]);
                } else {
                    throw new Exception("Insufficient points for this entry");
                }
                return response()->json([
                    'message' => 'You’ve entered! The winner will be notified by email within 14 days of month’s end.'
                ], 200);
            }
            else {
                throw new Exception('User not Found');
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
