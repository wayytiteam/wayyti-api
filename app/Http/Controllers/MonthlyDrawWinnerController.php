<?php

namespace App\Http\Controllers;
use App\Models\MonthlyDrawWinner;
use App\Models\Notification;

use Illuminate\Http\Request;

class MonthlyDrawWinnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function update(MonthlyDrawWinner $monthly_draw_winner, Request $request)
    {
        try {
            $monthly_draw_winner->update($request->only(array_keys($monthly_draw_winner->getAttributes())));
            if($request->email) {
                // $monthly_draw_winner->password = $request->new_password;
            }
            $monthly_draw_winner->save();

            return response()->json($monthly_draw_winner, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
