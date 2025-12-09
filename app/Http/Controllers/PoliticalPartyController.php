<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PoliticalParty;

class PoliticalPartyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Devuelve todos los partidos políticos
        $parties = PoliticalParty::all();
    
        return view('political_parties.index', compact('parties'));
    
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
        $validated = $request->validate([
            'name' => 'required|string|unique:political_parties,name|max:50',
            'abbreviation' => 'required|string|unique:political_parties,abbreviation|max:10',
            'color_hex' => 'required|string|size:7', 
        ]);
        
        $party = PoliticalParty::create($validated);
    
        //Devolver JSON
        return response()->json([
            'message' => 'Partido político creado con éxito',
            'party' => $party], 201);
   
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $party = PoliticalParty::findOrFail($id);
        return view('political_parties.show', compact('party'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $party = PoliticalParty::findOrFail($id);
        return view('political_parties.edit', compact('party'));
    
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $party = PoliticalParty::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|unique:political_parties,name,'.$party->id.'|max:50',
            'abbreviation' => 'sometimes|string|unique:political_parties,abbreviation,'.$party->id.'|max:10',
            'color_hex' => 'sometimes|string|size:7', 
        ]);

        $party->update($validated);

        return response()->json([
            'message' => 'Partido político actualizado con éxito',
            'party' => $party], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $party = PoliticalParty::findOrFail($id);
        $party->delete();

        return response()->json([
            'message' => 'Partido político eliminado con éxito'], 200);
    }
}
