<?php

namespace App\Http\Controllers;

use App\Models\Share;
use App\Models\User;
use App\Models\Point;
use Exception;
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
        $share_deal = Share::get_share_badge($user);
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
                    'share_id' => $share->id,
                    'points' => '15'
                ]);
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
