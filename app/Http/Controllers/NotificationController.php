<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\GoogleProduct;
use App\Models\Notification;
use App\Models\TrackedProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Google\Client as GoogleClient;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function index()
    {

        // $twelve_midnight_to_2am = Notification::whereTime('created_at', '>=', '00:00:00')
        // ->whereTime('created_at', '<', '02:00:00')
        // ->orderBy('created_at', 'desc')
        // ->take(100)
        // ->get();

        // $two_am_to_6am = Notification::whereTime('created_at', '>=', '02:00:00')
        // ->whereTime('created_at', '<', '06:00:00')
        // ->orderBy('created_at', 'desc')
        // ->take(100)
        // ->get();

        // $six_am_to_12pm = Notification::whereTime('created_at', '>=', '06:00:00')
        // ->whereTime('created_at', '<', '12:00:00')
        // ->orderBy('created_at', 'desc')
        // ->take(100)
        // ->get();

        // return response()->json([
        //     '12-2AM' => $twelve_midnight_to_2am,
        //     '2AM-6Am' => $two_am_to_6am,
        //     '6AM-12PM' => $six_am_to_12pm
        // ], 200);

        $user = User::find(Auth::id());
        $get_notifications = Notification::where('user_id', $user->id)
            ->where(function (Builder $query) use ($user) {
            $query->where('country', $user->country)
                ->orWhereNull('country');
            })
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
                $message = $title.' '.'has gone up in price';
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
            'description' => 'You have unlocked the'.' '.$request->tracked_product_id->name.' '.'Badge',
            'type' => $type
        ]);

        return response()->json($new_notification, 200);
    }

    public function test(Request $request) {
        $title = $request->title;
        $body = $request->body;
        $fcm_token = $request->fcm_token;
        Notification::send_notification($title, $body, $fcm_token);
        // $project_id = config('services.fcm.project_id');
        // $bucket_file = 'smartsale-private-key.json';
        // $local_file_path = storage_path('smartsale-private-key.json');
        // $file_content = Storage::disk('s3')->get($bucket_file);
        // file_put_contents($local_file_path, $file_content);
        // $client = new GoogleClient();
        // $client->setAuthConfig($local_file_path);
        // $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        // $client->fetchAccessTokenWithAssertion();
        // $token = $client->getAccessToken();
        // $access_token = $token['access_token'];

        // $headers = [
        //     "Authorization: Bearer $access_token",
        //     'Content-type: application/json'
        // ];

        // $data = [
        //     "message" => [
        //         "token" => $fcm_token,
        //         "notification" => [
        //             "title" => $title,
        //             "body" => $body
        //         ],
        //     ]
        // ];

        // $payload = json_encode($data);

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$project_id}/messages:send");
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        // curl_setopt($ch, CURLOPT_VERBOSE, true);
        // $response = curl_exec($ch);
        // return ($response);
        // return response()->json([
        //     'message' => 'Hope it works. Please check on your end'
        // ], 200);
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
