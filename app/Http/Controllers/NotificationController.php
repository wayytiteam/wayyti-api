<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\GoogleProduct;
use App\Models\Notification;
use App\Models\TrackedProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::find(Auth::id());
        $get_notifications =  Notification::where('user_id', $user->id)
            ->with(['monthly_draw_winner', 'tracked_product.google_product', 'badge',])
            ->orderBy('created_at', 'desc');
        $notification_list = $get_notifications->paginate(10);
        $count_unread_notifications = $get_notifications->where('read', false)->count();
        return response()->json([
            'notifications' => $notification_list,
            'count_unread' => $count_unread_notifications
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
        $message = null;
        $type = null;
        if($request->tracked_product_id) {
            $product = TrackedProduct::where('id', $request->tracked_product_id)->first();
            $product = $product->load('google_product');
            $title = $product->google_product->title;
            if($request->price_state === 'up') {
                $message = $title.' '.'has went up in price';
                $type = 'price_up';
            }else {
                $message = $title.' '.'dropped in price';
                $type = 'price_down';
            }
        }
        if($request->badge_id) {
            $message = 'Achievement Unlocked';
            $type = 'achievement_unlocked';
        }
        $new_notification = Notification::create([
            'user_id' => $user->id,
            'tracked_product_id' => $request->tracked_product_id,
            'badge_id' => $request->badge_id,
            'message' => $message,
            'description' => 'You have unlocked'.' '.$request->tracked_product_id->name.' '.'badge',
            'type' => $type
        ]);

        return response()->json($new_notification, 200);
    }

    public function test(Request $request) {
        $title = $request->title;
        $body = $request->body;
        $token = $request->fcm_token;
        Notification::send_notification($title, $body, $token);
        return response()->json([
            'message' => 'Hope it works. Please check on your end'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Notification $notification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notification $notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notification $notification)
    {
        $fields = $request->only(array_keys($notification->getAttributes()));
        $notification->update($fields);

        return response()->json($notification, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification)
    {
        //
    }
}
