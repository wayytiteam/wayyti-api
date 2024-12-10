<?php

namespace App\Http\Controllers;

use App\Models\GoogleProduct;
use App\Models\Notification;
use App\Models\TrackedProduct;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoogleProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = User::find(Auth::id());
        $keyword = $request->keyword;
        $google_products = GoogleProduct::where(function (Builder $query) use ($keyword) {
            $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($keyword) . '%']);
        })
        ->where('country', $user->country)
        ->paginate(10);

        if(!$user->country) {
            return response()->json([
                'message' => 'Country has not been set'
            ], 400);
        }

        return response()->json($google_products, 200);
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
        $keyword = $request->keyword;
        $domain = $request->domain;

        if(!$user->country) {
            return response()->json([
                'message' => 'Country has not been set'
            ], 400);
        }

        $username = "wayyti_OchkV";
        $password = "_qLQ+duP77UDhWj";

        $url = "https://realtime.oxylabs.io/v1/queries";

        $params = array(
            'source' => 'google_shopping_search',
            'domain' => $domain,
            'query' => $keyword,
            'geo_location' => $user->country,
            'pages' => 1,
            'parse' => true,
            'context' => [
                ['key' => 'sort_by', 'value' => 'pd'],
                ['key' => 'min_price', 'value' => 20]
            ]
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);

        $headers = array();
        $headers[] = "Content-Type: application/json";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        return $response;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $google_product_id)
    {
        try {
            $google_product = GoogleProduct::whereHas('tracked_products', function (Builder $query) {
                $query->where('user_id', Auth::id());
            })
            ->where('product_id', $google_product_id)
            ->first();
            if(!$google_product) {
                throw new Exception("This product was not tracked yet");
            }
            return response()->json($google_product, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GoogleProduct $googleProduct)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $google_product_id)
    {
        $product = GoogleProduct::updateOrCreate([
            'product_id' => $google_product_id
        ],[
            'price' => $request->price,
            'merchant' => $request->seller
        ]);

        return response()->json($product, 200);

        // return response()->json($product, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GoogleProduct $googleProduct)
    {
        //
    }

    public function scrape(Request $request)
    {
        $username = env('OXYLABS_USERNAME');
        $password = env('OXYLABS_PASSWORD');

        $url = "https://realtime.oxylabs.io/v1/queries";

        $params = array(
            'source' => 'google_shopping_product',
            'geo_location' => $request->query('geo_location'),
            'domain' => 'com.ph',
            'query' => $request->query('product_id'),
            'parse' => true,
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);

        $headers = array();
        $headers[] = "Content-Type: application/json";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        return $result;

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        // $username = "wayyti_OchkV";
        // $password = "_qLQ+duP77UDhWj";

        // $data = [
        //     "source" => "google_shopping_search",
        //     "query" => $request->query('keyword'),
        //     "parse" => true
        // ];

        // $ch = curl_init();

        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // $response = curl_exec($ch);

        // if (curl_errno($ch)) {
        //     echo 'Error:' . curl_error($ch);
        // }
        // curl_close ($ch);
        // $response_data = json_decode($response, true);

        // $item_results = [];

        // if (isset($response_data['results'][0]['content']['results']['organic'])) {
        //     $items = $response_data['results'][0]['content']['results']['organic'];

        //     foreach($items as $item) {
        //         echo "Title: " . $item['title'] . "\n";
        //         echo "Price: $" . $item['price'] . "\n";
        //         echo "Rating: " . $item['rating'] . " stars\n\n";
        //     }
        //     $item_results['items'] = $items;
        // } else {
        //     echo "No Items found";
        // }

        // return response()->json($item_results, 200);


        $response_data = json_decode($result, true);
        $item_results = [];
        if(isset($response_data['results'][0]['content']['pricing'])) {
            $title = $response_data['results'][0]['content']['title'];
            if(count($response_data['results'][0]['content']['pricing']['online']) > 0) {
                $merchants = $response_data['results'][0]['content']['pricing']['online'];
                foreach($merchants as $merchant) {

                }
                // $item_results['item']['title'] = $title;
                // $item_results['item']['price'] = $item['price'];
                // $item_results['item']['merchant'] = $item['seller'];
            } else {
                echo "No Items found";
            }
        } else {
            echo "No Items Found";
        }

        return response()->json($item_results, 200);
    }

    public function test_price_update(Request $request){
        {
            $oxylabs_username = env('OXYLABS_USERNAME');
            $oxylabs_password = env('OXYLABS_PASSWORD');
            $url = "https://realtime.oxylabs.io/v1/queries";
            $products = GoogleProduct::whereHas('tracked_products')->with('users')->get();
            foreach($products as $product)
            {
                $users = $product['users'];
                foreach($users as $user)
                {
                    $params = array(
                        'source' => 'google_shopping_product',
                        'geo_location' => $user["country"],
                        'domain' => 'com',
                        'query' => $product->product_id,
                        'parse' => true,
                    );
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_USERPWD, $oxylabs_username . ":" . $oxylabs_password);

                    $headers = array();
                    $headers[] = "Content-Type: application/json";
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    $result = curl_exec($ch);
                    // return $result;
                    if (curl_errno($ch)) {
                        echo 'Error:' . curl_error($ch);
                    }

                    curl_close($ch);
                    $response_data = json_decode($result, true);

                    if(isset($response_data['results'][0]['content']['pricing'])) {
                        $title = $response_data['results'][0]['content']['title'];
                        if(count($response_data['results'][0]['content']['pricing']['online']) > 0) {
                            return $response_data['results'][0]['content']['pricing']['online'];
                            $item = $response_data['results'][0]['content']['pricing']['online'][0];
                            // if($item['price'] < $product->latest_price) {
                            //     $product->original_price = $product->latest_price;
                            //     $product->latest_price = $item['price'];
                            //     $product->save();
                            //     $new_notification = Notification::create([
                            //         'user_id' => $user->id,
                            //         'message' => $title.' '.'dropped in price',
                            //         'google_product_id' => $product->id,
                            //         'type' => 'price_down'
                            //     ]);
                            //     if($user->fcm_token) {
                            //         Notification::send_notification($new_notification->mesage, $new_notification->message, $user->fcm_token, $new_notification);
                            //     }
                            // } elseif($item['price'] > $product->latest_price) {
                            //     $product->original_price = $product->latest_price;
                            //     $product->latest_price = $item['price'];
                            //     $product->save();
                            //     $new_notification = Notification::create([
                            //         'user_id' => $user->id,
                            //         'message' => $title.' '.'has went up in price',
                            //         'google_product_id' => $product->id,
                            //         'type' => 'price_up'
                            //     ]);
                            // }
                        }
                    }
                }
            }
        }
    }
}
