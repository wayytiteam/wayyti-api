<?php

namespace App\Http\Controllers;

use App\Models\GoogleProduct;
use App\Models\Point;
use App\Models\TrackedProduct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Badge;
use App\Models\BadgeUser;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Currency;
use Exception;
use Astrotomic\Intl\Countries;
use PhpParser\Node\Expr\Throw_;

class TrackedProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $folder_id = $request->query('folder_id');
        $keyword = $request->query('keyword');
        $user_id = $request->query('user_id');
        $user = User::find($user_id);
        $sort = $request->query('sort');
        $get_tracked_products = TrackedProduct::select(DB::raw('DISTINCT ON (google_product_id) *'));
        $tracked_products = $get_tracked_products->whereHas('google_product', function (Builder $query) use ($user) {
            $query->where('country', $user->country);
        })
        ->when($user_id, function (Builder $query) use ($user_id) {
            $query->where('user_id', $user_id);
        })
        ->when($folder_id, function (Builder $query) use ($folder_id) {
            $query->where('folder_id', $folder_id);
        })
        ->when($keyword, function (Builder $query) use ($keyword){
            $query->whereHas('google_product', function (Builder $q) use ($keyword) {
                $q->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($keyword) . '%']);
            });
        })
        ->when($sort, function (Builder $query) use ($sort){
            $query->orderBy('updated_at', $sort);
        })
        ->with('google_product')
        ->paginate(10);
        $items_tracked = TrackedProduct::get_tracker_badge($user);
        $current_savings = TrackedProduct::get_savings_badge($user);
        $count_all_tracked_products = $get_tracked_products->count();
        return response()->json([
            'tracked_products' => $tracked_products,
            'items_tracked' => $items_tracked,
            'savings' => $current_savings,
            'count_all_tracked_products' => $count_all_tracked_products
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
        $products = $request->products;
        $single_product = $request->product_id;
        // $products = $request->folders;
        $user = User::find(Auth::id());
        // try {
            if($user) {
                if($products) {
                    $registered_products = [];
                foreach($products as $product)
                    {
                        $product_data = GoogleProduct::updateOrCreate([
                            "product_id" => $product["product_id"],
                            "merchant" =>  $product["merchant"]
                        ], [
                            "product_id" => $product["product_id"],
                            "title" => $product["title"],
                            "image" => $product["image"],
                            "merchant" => $product["merchant"],
                            "original_price" => $product["original_price"],
                            "latest_price" => $product["latest_price"],
                            'currency' => $product["currency"],
                            'country' => $user->country,
                            'description' => $product["description"] ?? null,
                            'link' => $product["link"] ?? null
                        ]);
                        $registered_products[] = $product;
                        $folders = $product["folders"];
                        if(count($folders) == 0) {
                            $empty_folder = [
                                'id' => null
                            ];
                            $folders[] = $empty_folder;
                        }
                        foreach($folders as $folder) {
                            $tracked_product = TrackedProduct::updateOrCreate([
                                'google_product_id' => $product_data->id,
                                'user_id' => $user->id,
                                'folder_id' => $folder["id"],
                            ], [
                                'google_product_id' => $product_data->id,
                                'user_id' => $user->id,
                                'folder_id' => $folder["id"],
                            ]);
                            $tracked_product = TrackedProduct::where('user_id', $user->id)
                                ->where('google_product_id', $product_data->id)
                                ->where('folder_id', $folder["id"])
                                ->first();
                            if(!$tracked_product) {
                                $tracked_product = TrackedProduct::create([
                                    'google_product_id' => $product_data->id,
                                    'user_id' => $user->id,
                                    'folder_id' => $folder["id"],
                                ]);
                                Point::create([
                                    'user_id' => $user->id,
                                    'tracked_product_id' => $tracked_product->id,
                                    'points' => 5
                                ]);
                            }
                            $tracked_product->load('folder', 'google_product');
                            $tracked_products[] = $tracked_product;
                        }
                    }
                }
                if($single_product) {
                    $check_product = TrackedProduct::where('user_id', $user->id)
                        ->whereHas('google_product', function (Builder $query) use ($request) {
                            $query->where('product_id', $request->product_id);
                        })
                        ->first();
                    if($check_product) {
                        throw new Exception("This is item has already been tracked");
                    } else {
                        $check_google_product = GoogleProduct::where('product_id', $request->product)
                            ->where('merchant', $request->merchant)
                            ->first();
                        if(!$check_google_product) {
                            $new_google_product = GoogleProduct::create([
                                'product_id' => $request->product_id,
                                'title' => $request->title,
                                'merchant' => $request->merchant,
                                'image' => $request->image,
                                'original_price' => $request->original_price,
                                'latest_price' => $request->latest_price,
                                'currency' => $request->currency,
                                'country' => $user->country,
                                'description' => $request->description ?? null,
                                'link' => $request->link ?? null
                            ]);
                        }
                        $new_tracked_product = TrackedProduct::create([
                            'user_id' => $user->id,
                            'google_product_id' => $new_google_product->id,
                        ]);
                        Point::create([
                            'user_id' => $user->id,
                            'tracked_product_id' => $new_tracked_product->id,
                            'points' => 5
                        ]);
                    }
                    return response()->json($new_tracked_product, 200);
                }
                return response()->json($tracked_products, 200);
            } else {
                throw new Exception("User not found");
            }
        // } catch (\Exception $e) {
        //     return response()->json([
        //         "error" => $e->getMessage()
        //     ], 400);
        // }
    }

    /**
     * Display the specified resource.
     */
    public function show(TrackedProduct $tracked_product)
    {
        $tracked_product->load('google_product');
        return response()->json($tracked_product, 200);
    }

    public function google_product_details(Request $request)
    {
        $user = User::find(Auth::id());
        try {
            $google_product = GoogleProduct::where('product_id', $request->product_id)
                ->where('merchant', $request->merchant)
                ->with('tracked_products')
                ->first();
            $tracked_product = TrackedProduct::where('user_id', $user->id)
                ->where('google_product_id', $google_product->id)
                ->with('google_product')
                ->first();
            if($google_product) {
                if($tracked_product) {
                    return response()->json($tracked_product, 200);
                } else {
                    Throw new Exception("This product is not yet tracked", 404);
                }
            } else {
                Throw new Exception("This product is not yet tracked", 404);
            }
            return $tracked_product;

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TrackedProduct $trackedProduct)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TrackedProduct $tracked_product)
    {
        $user = User::find(Auth::id());
        // try {
            foreach ($request->all() as $key => $value) {
                if (array_key_exists($key, $tracked_product->getAttributes())) {
                    $tracked_product->$key = $value;
                }
            }
            if($request->deal) {
                $product_detail = GoogleProduct::find($tracked_product->google_product_id);
                $saved_value = ((float)$product_detail->original_price - (float)$product_detail->latest_price) * (float)$request->quantity;
                $saved_value = $saved_value < 0 ? $saved_value = 0 : $saved_value = $saved_value;
                $tracked_google_product = TrackedProduct::where('user_id', $user->id)
                    ->where('google_product_id', $tracked_product->google_product_id)
                    ->get();
                foreach($tracked_google_product as $tracked_product) {
                    $tracked_product->deal = $request->deal;
                    $tracked_product->saved = $saved_value;
                    $tracked_product->save();
                }
                $get_current_badge = BadgeUser::where('user_id', $user->id)
                    ->whereHas('badge', function (Builder $query) {
                        $query->where('type', 'savings');
                    })
                    ->with('badge')
                    ->first();
                $point_equivalent_badge = Badge::where('type', 'savings')
                    ->orderBy('requirement_value', 'desc')
                    ->where('requirement_value', '<=', (int)$saved_value)
                    ->first();
                if($get_current_badge) {
                    if($point_equivalent_badge) {
                        if($get_current_badge->badge->id !== $point_equivalent_badge->id) {
                            $get_current_badge->badge_id = $point_equivalent_badge->id;
                            $get_current_badge->save();
                            if($get_current_badge->badge->point_equivalent < $point_equivalent_badge->point_equivalent) {
                                $new_notification = Notification::create([
                                    'user_id' => $user->id,
                                    'message' => 'Achievement_unlocked',
                                    'description' => 'You have unlocked'.' '.$point_equivalent_badge->name.' '.'badge',
                                    'type' => 'achievement_unlocked',
                                    'badge_id' => $get_current_badge->badge_id
                                ]);
                                if($user->fcm_token) {
                                    Notification::send_notification($new_notification->message, $new_notification->message, $user->fcm_token);
                                }
                            }
                        }
                    }
                } else {
                    if($point_equivalent_badge) {
                        BadgeUser::create([
                            'user_id' => $user->id,
                            'badge_id' => $point_equivalent_badge->id,
                            'country' => $user->country
                        ]);
                        $new_notification = Notification::create([
                            'user_id' => $user->id,
                            'message' => 'Achievement_unlocked',
                            'description' => 'You have unlocked'.' '.$point_equivalent_badge->name.' '.'badge',
                            'type' => 'achievement_unlocked',
                            'badge_id' => $point_equivalent_badge->id
                        ]);
                        if($user->fcm_token) {
                            Notification::send_notification($new_notification->message, $new_notification->message, $user->fcm_token);
                        }
                    }
                }
                if($point_equivalent_badge) {
                    Point::updateOrCreate([
                        'user_id' => $user->id,
                        'google_product_id' => $tracked_product->google_product_id,
                        'country' => $user->country
                    ], [
                        'user_id' => $user->id,
                        'google_product_id' => $tracked_product->google_product_id,
                        'points' => $saved_value,
                        'country' => $user->country
                    ]);
                }
            }
            $tracked_product->save();
            $tracked_product->load('google_product');

            return response()->json($tracked_product, 200);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'error' => $e->getMessage()
        //     ], 400);
        // }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $google_product_id)
    {
        $user = User::find(Auth::id());
         $tracked_products = TrackedProduct::where('user_id', $user->id)
            ->where('google_product_id', $google_product_id)
            ->get();
        foreach($tracked_products as $tracked_product) {
            $tracked_product->delete();
        }
        return response()->json([
            'message' => 'Success'
        ], 200);
    }
}
