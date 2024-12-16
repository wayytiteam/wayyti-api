<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GoogleProduct;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use Goutte\Client;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = User::find(Auth::id());
        $keyword = $request->keyword;
        $products = GoogleProduct::where(function (Builder $query) use ($keyword) {
            $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($keyword) . '%']);
        })
        ->where('country', $user->country)
        ->paginate(10);
        return response()->json($products, 200);
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function scrape(Request $request)
    {
        $username = "wayyti_OchkV";
        $password = "_qLQ+duP77UDhWj";

        $url = "https://realtime.oxylabs.io/v1/queries";

        $params = array(
            // 'source' => 'google_shopping_search',
            'domain' => 'universal',
            'url' => $request->query('url'),
            // 'query' => $request->query('keyword'),
            'geo_location' => $request->query('geo_location'),
            'render' => 'html',
            'browser_instructions' =>  [
                [
                    'type' => 'input',
                    'value' => 'shoes',
                    'selector' => [
                        'type' => 'xpath',
                        'value' => "//input[@class='search-box__input--O34g']"
                    ]
                ]
            ],
            'parsing_instructions' => [
                "description" => [
                    "_fns" => [
                        [
                            "_fn" => "xpath_one",
                            "_args" => ["//div[@class='']"]
                        ]
                    ]
                ]
            ]
            // 'pages' => 2,
            // 'parse' => true,
            // 'context' => [
            //     ['key' => 'sort_by', 'value' => 'pd'],
            //     ['key' => 'min_price', 'value' => 20]
            // ]
        );
        // $sample_instructions = [
        //     'type' => 'input',
        //     'value' => 'shoes',
        //     'selector' => [
        //         'type' => 'xpath',
        //         'value' => "//input[@class='searchInput']"
        //     ]
        // ];

        // return $sample_instructions;


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

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        // return response()->json($result, 200);
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
        // echo $response;

        // if (curl_errno($ch)) {
        //     echo 'Error:' . curl_error($ch);
        // }
        // curl_close ($ch);
        $response_data = json_decode($response, true);
        $x_path_result = [
            "description" => [
                "_fns" => [
                    "_fn" => "xpath",
                    "_args" => [".//div[@class='left-1AmIx']/text()"]
                ]
            ]
        ];
        return $x_path_result;
        $item_results = [];

        if (isset($response_data['results'][0]['content']['results']['organic'])) {
            $items = $response_data['results'][0]['content']['results']['organic'];
            $item_results['items'] = $items;
            // return response()->json($item_results, 200);
        } else {
            return "No Items found";
        }
        // return $item_results;
    }
}
