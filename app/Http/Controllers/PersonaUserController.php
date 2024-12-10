<?php

namespace App\Http\Controllers;

use App\Models\PersonaUser;
use Illuminate\Http\Request;

class PersonaUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        try {
            $persona = PersonaUser::create([
                'persona_id' => $request->persona_id,
                'user_id' => $request->user_id
            ]);
            return response()->json($persona, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PersonaUser $personaUser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PersonaUser $personaUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PersonaUser $personaUser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PersonaUser $personaUser)
    {
        //
    }
}
