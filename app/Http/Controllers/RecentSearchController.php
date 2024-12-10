<?php

namespace App\Http\Controllers;

use App\Models\RecentSearch;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecentSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::find(Auth::id());
        $searches = RecentSearch::where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->limit(4)
            ->get();
        return response()->json($searches, 200);
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
            $used_keyword = RecentSearch::where('user_id', $user->id)
                ->where('keyword', $request->keyword)
                ->first();
            if($used_keyword) {
                $used_keyword->updated_at = Carbon::parse(now());
                $used_keyword->save();
            } else {
                RecentSearch::create([
                    'user_id' => $user->id,
                    'keyword' => $request->keyword
                ]);
            }
            return response()->json(null, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(RecentSearch $recentSearch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RecentSearch $recentSearch)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RecentSearch $recentSearch)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RecentSearch $recent_search)
    {
        try {
            $recent_search->delete();
            return response()->json(null, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }
}
