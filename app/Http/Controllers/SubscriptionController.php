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
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        try {
            // Generate App Store Server API JWT
            $jwt_token = app(AppStoreJwtService::class)->generateToken();

            // Sandbox transaction lookup
            $url = 'https://api.storekit-sandbox.itunes.apple.com/inApps/v1/transactions/'.
                $request->transaction_id;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$jwt_token,
                'Accept' => 'application/json',
            ])->get($url);

            if (! $response->successful()) {
                return [
                    'valid' => false,
                    'reason' => 'apple_api_error',
                    'status' => $response->status(),
                    'desc' => $response->body(),
                ];
            }

            $data = $response->json();

            // signedTransactionInfo is a JWT
            $signed_transaction_info = $data['signedTransactionInfo'] ?? null;

            if (! $signed_transaction_info) {
                return [
                    'valid' => false,
                    'reason' => 'missing_signed_transaction_info',
                ];
            }
            $decoded = app(AppStoreJwtService::class)
                ->decodeToken($signed_transaction_info);
            if (! isset($decoded->expiresDate)) {
                return [
                    'valid' => false,
                    'reason' => 'missing_expires_date',
                ];
            }

            $expires_at = Carbon::createFromTimestampMs($decoded->expiresDate);
            $is_active = $expires_at->isFuture();

            Subscription::updateOrCreate(
                [
                    'user_id' => $request->user_id,
                ],
                [
                    'type' => $request->type ?? 'apple_pay',
                    'product_id' => $decoded->productId,
                    'subscription_id' => $decoded->originalTransactionId,
                    'purchase_token' => $request->transaction_id,
                    'has_subscribed' => $is_active,          // false if expired
                    'on_trial_mode' => ($decoded->offerType ?? null) === 1,
                    'expires_at' => $expires_at,              // always saved
                ]
            );

            return response([
                'valid' => true,
                'active' => $is_active,
                'expired' => ! $is_active,
                'product_id' => $decoded->productId,
                'expires_at' => $expires_at,
                'environment' => $decoded->environment ?? null,
                'transaction_reason' => $decoded->transactionReason ?? null,
            ], 200);
        } catch (\Throwable $e) {
            return [
                'valid' => false,
                'reason' => 'verification_failed',
                'error' => $e->getMessage(),
            ];
        }
    }
}
