<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user_id = $request->user_id;
        $folders = Folder::when($user_id, function(Builder $query) use ($user_id){
            $query->where('user_id', $user_id);
        })
        ->with(['tracked_products.google_product'])
        ->paginate(10);

        return response()->json($folders, 200);
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
        try{
            $folder = Folder::create([
                'user_id' => $request->user_id,
                'name' => $request->name
            ]);
            return response()->json($folder, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Folder $folder)
    {
        $folder->load('tracked_products');

        return response()->json($folder, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Folder $folder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Folder $folder)
    {
        try {
            foreach ($request->all() as $key => $value) {
                if (array_key_exists($key, $folder->getAttributes())) {
                    $folder->$key = $value;
                }
            }
            $folder->save();
            return response()->json($folder, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Folder $folder)
    {
        try{
            $folder->delete();
            return response()->json([
                'message' => 'Folder deleted'
            ]);
        }catch(\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }
}
