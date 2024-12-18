<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->query('type');
        $status = $request->query('status');
        $subscriptions = Subscription::query();
        $subscriptions = $subscriptions->when($type, function (Builder $query) use ($type) {
            $query->where('type', $type);
        })->when($status, function (Builder $query) use ($status) {
            $query->where('status', $status);
        })->with('user')
        ->paginate(10);

        return response()->json($subscriptions, 200);
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
        try {
            $new_subscription = Subscription::create([
                'user_id' => $user->id,
                'type' => $request->type,
            ]);
            return response()->json($new_subscription, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Subscription $subscription)
    {
        $subscription->load('user');
        return response($subscription, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subscription $subscriptions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subscription $subscription)
    {
        $subscription->update($request->only(array_keys($subscription->getAttributes())));
        $subscription->save();
        return response($subscription, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscription $subscriptions)
    {
        //
    }
}
