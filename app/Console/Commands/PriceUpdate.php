<?php

namespace App\Console\Commands;

use App\Models\GoogleProduct;
use App\Models\Notification;
use App\Models\TrackedProduct;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class PriceUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $oxylabs_username = env('OXYLABS_USERNAME');
        $oxylabs_password = env('OXYLABS_PASSWORD');
        $url = "https://realtime.oxylabs.io/v1/queries";
        $products = GoogleProduct::whereHas('tracked_products')->with('users', 'tracked_products')->get();
        foreach($products as $product)
        {
            $tracked_product = TrackedProduct::where('google_product_id', $product->id)->first();
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

                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                }

                curl_close($ch);
                $response_data = json_decode($result, true);

                if(isset($response_data['results'][0]['content']['pricing'])) {
                    $title = $response_data['results'][0]['content']['title'];
                    $matching_item = null;
                    if(count($response_data['results'][0]['content']['pricing']['online']) > 0) {
                        $items = $response_data['results'][0]['content']['pricing']['online'];
                        foreach($items as $item) {
                            if($item['seller'] === $product->merchant) {
                                $matching_item = $item;
                            }
                        }
                        if($matching_item) {
                            if($matching_item['price'] < $product->latest_price) {
                                $product->original_price = $product->latest_price;
                                $product->latest_price = $matching_item['price'];
                                $product->save();
                                $new_notification = Notification::create([
                                    'user_id' => $user->id,
                                    'message' => $title.' '.'has dropped in price',
                                    'description' => 'Current Price: '.$product->currency.$matching_item['price'],
                                    'tracked_product_id' => $tracked_product->id,
                                    'type' => 'price_down',
                                    'country' => $user["country"]
                                ]);
                                if($user->fcm_token) {
                                    Notification::send_notification($new_notification->message, $new_notification->description, $user->fcm_token);
                                }
                            }
                            if($matching_item['price'] > $product->latest_price) {
                                $product->original_price = $product->latest_price;
                                $product->latest_price = $matching_item['price'];
                                $product->save();
                                $new_notification = Notification::create([
                                    'user_id' => $user->id,
                                    'message' => $title.' '.'has gone up in price',
                                    'description' => 'Current Price: '.$product->currency.$matching_item['price'],
                                    'tracked_product_id' => $tracked_product->id,
                                    'type' => 'price_up',
                                    'country' => $user["country"]
                                ]);
                                if($user->fcm_token) {
                                    Notification::send_notification($new_notification->message, $new_notification->description, $user->fcm_token);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
