<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $active = $request->active;
        $banners = Banner::query();
        if($active) {
            $banners->when($active === "true", function (Builder $query) {
            $query->where('active', true);
            });
            $banners->when($active === "false", function (Builder $query) {
                $query->where('active', false);
            });
        }
        $banners = $banners->paginate(10);

        return response()->json($banners, 200);
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
    public function store(Request $request) {
        try {
            $image_file_name = $request->file('image')->getClientOriginalName();
            $image_file_path = Storage::disk('s3')->putFileAs('banner_images', $request->file('image'), $image_file_name);
            $image_file_url = Storage::url($image_file_path);
            $banner = Banner::create([
                'image_path' => $image_file_url,
            ]);
            return response()->json($banner, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Banner $banner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        //
    }
}
