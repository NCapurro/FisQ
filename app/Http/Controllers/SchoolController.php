<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\School;
use App\Models\Department;
use App\Models\Mesa;

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //Devuelve todas las escuelas
        $schools = School::with('department')->get();
        return view('schools.index', compact('schools'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::all();
        
        return view('schools.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'address' => 'nullable|string|max:150',
            'department_id' => 'required|exists:departments,id', // Debe existir en la tabla departments
        ]);
        $school = School::create($validated);

        return response()->json([
            'message' => 'Escuela creada exitosamente',
            'school' => $school
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Cargamos la escuela, su departamento y sus mesas asociadas
        $school = School::with(['department', 'mesas'])->findOrFail($id);
        
        return view('schools.show', compact('school'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $school = School::findOrFail($id);
        $departments = Department::all(); // Para llenar el select
        
        return view('schools.edit', compact('school', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $school = School::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'address' => 'nullable|string|max:150',
            'department_id' => 'sometimes|exists:departments,id',
        ]);

        $school->update($validated);

        return response()->json([
            'message' => 'Escuela actualizada correctamente',
            'school' => $school
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $school = School::findOrFail($id);
        
        // Al borrar la escuela, se borran las mesas en cascada 
        
        $school->delete();

        return response()->json(['message' => 'Escuela eliminada'], 200);
    }
}
