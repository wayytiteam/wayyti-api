<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\User;
use App\Services\AppStoreJwtService;
use Carbon\Carbon;
use Google\Client;
use Google\Service\AndroidPublisher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

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

        return $subscriptions;
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
            $new_subscription = Subscription::updateOrCreate([
                'user_id' => $user->id,
            ], [
                'user_id' => $user->id,
                'type' => $request->type,
            ]);

            return response()->json($new_subscription, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Subscription $subscription)
    {
        // $subscription->load('user');
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

    public function verify_google_subscription(Request $request)
    {
        $package_name = $request->package_name;
        $purchase_token = $request->purchase_token;
        $product_id = $request->product_id;
        $user_id = $request->user_id;
        try {
            $client = new Client;
            $bucket_file = 'google-play-service-key.json';
            $local_file_path = storage_path('google-play-service-key.json');
            $file_content = Storage::disk('s3')->get($bucket_file);
            file_put_contents($local_file_path, $file_content);
            $client->setAuthConfig($local_file_path);
            // $client->setAuthConfig(config('services.google.config'));
            $client->addScope(AndroidPublisher::ANDROIDPUBLISHER);

            $service = new AndroidPublisher($client);

            $result = $service->purchases_subscriptions->get(
                $package_name,
                $product_id,
                $purchase_token
            );

            if ($result->getPaymentState() !== 1) {
                return [
                    'valid' => false,
                    'reason' => 'invalid_payment_state',
                    'data' => $result,
                ];
            }

            Subscription::updateOrCreate(
                [
                    'user_id' => $user_id,
                ],
                [
                    'type' => $request->type,
                    'product_id' => $request->product_id,
                    'purchase_token' => $request->purchase_token,
                    'subscription_id' => $result->getOrderId(),
                    'has_subscribed' => true,
                    'on_trial_mode' => false,
                ]
            );

            return [
                'valid' => true,
                'data' => $result,
            ];
        } catch (\Throwable $e) {
            return [
                'valid' => false,
                'reason' => 'verification_failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function verify_apple_subscription(Request $request)
    {
        try {
            $jwt_token = app(AppStoreJwtService::class)->generateToken();

            // Sandbox URL (switch to production when live)
            $url = 'https://api.storekit-sandbox.itunes.apple.com/inApps/v1/transactions/'.$request->transaction_id;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$jwt_token,
                'Accept' => 'application/json',
            ])->get($url);

            if (! $response->successful()) {
                return [
                    'valid' => false,
                    'reason' => 'apple_api_error',
                    'desc' => $response->body(),
                    'status' => $response->status(),
                ];
            }

            $data = $response->json();
            $signed_transaction_info = $data['signedTransactionInfo'] ?? null;

            if (! $signed_transaction_info) {
                return [
                    'valid' => false,
                    'reason' => 'missing_signed_transaction_info',
                ];
            }

            $decoded = app(AppStoreJwtService::class)
                ->decodeToken($signed_transaction_info);

            /*
            | Apple Status Reference
            | expirationDate > now = ACTIVE
            */
            if (
                isset($decoded->expirationDate) &&
                now()->timestamp * 1000 < $decoded->expirationDate
            ) {
                return [
                    'valid' => true,
                    'product_id' => $decoded->productId,
                    'expires_at' => Carbon::createFromTimestampMs($decoded->expirationDate),
                    'data' => $decoded,
                ];
            }

            return [
                'valid' => false,
                'reason' => 'subscription_expired',
                'data' => $decoded,
            ];
        } catch (\Throwable $e) {
            return [
                'valid' => false,
                'reason' => 'verification_failed',
                'error' => $e->getMessage(),
            ];
        }
    }
    // public function verify_subscription(Request $request) {
    //     $client = new Client();
    //     $bucket_file = 'google-play-service-key.json';
    //     $local_file_path = storage_path('google-play-service-key.json');
    //     // dd($local_file_path);
    //     $file_content = Storage::disk('s3')->get($bucket_file);
    //     file_put_contents($local_file_path, $file_content);
    //     $client->setAuthConfig($local_file_path);
    //     // $client->addScope(AndroidPublisher::ANDROIDPUBLISHER);
    //     $client->addScope('https://www.googleapis.com/auth/androidpublisher');
    //     $service = new AndroidPublisher($client);
    //     try {
    //         $subscription = $service->purchases_subscriptions->get($request->package_name, $request->product_id, $request->purchase_token);
    //         // return $subscription->paymentState;
    //         dd($subscription);
    //     } catch (\Exception $e) {
    //         return $e;
    //     }
    // }

    // public function subscription_check(){
    //     $subscriptions = Subscription::get();
    //     foreach($subscriptions as $subscription) {
    //         if(!$subscription->has_subscribed){
    //             if($subscription->on_trial_mode) {
    //                 $trial_months_duration = Carbon::parse($subscription->updated_at)->floatDiffInMonths(Carbon::now());
    //                 if($trial_months_duration >= 2) {
    //                     $subscription->on_trial_mode = false;
    //                     $subscription->save();
    //                 }
    //             }
    //         }
    //     }
    // }
}
