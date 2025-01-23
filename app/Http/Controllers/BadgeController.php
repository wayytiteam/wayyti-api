<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class BadgeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = User::find(Auth::id());
        $currency = Currency::where('country_name', $user->country)->first();
        dd($currency);
        $badges = Badge::where('type', $request->badge_type)
            ->orderBy('requirement_value', 'asc')
            ->paginate(20);

        if ($request->badge_type === 'savings') {
            $badges->getCollection()->transform(function ($badge) use ($currency) {
                $badge->requirement_value = $currency->symbol.$badge->requirement_value;
                return $badge;
            });
        }
        return response()->json($badges, 200);
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
            $new_badge = Badge::create([
                'name' => $request->name,
                'type' => $request->type,
                'points_equivalent' => $request->points_equivalent,
                'requirement_value' => $request->requirement_value
            ]);
            return response()->json($new_badge, 200);
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
