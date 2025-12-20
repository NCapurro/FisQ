<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\School;
use App\Models\Department;
use App\Models\Mesa;
use App\Models\ActivityLog;

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $departments = Department::all();

        $query = School::with('department')->orderBy('name', 'asc');

        // 1. Modo Papelera
        if ($request->has('view_deleted')) {
            $query->onlyTrashed();
        }

        // 2. Filtro por Departamento
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // 3. NUEVO: Buscador por Nombre (Muy útil para escuelas)
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 4. CAMBIO CLAVE: Usar paginate
        $schools = $query->paginate(10); 

        return view('schools.index', compact('schools', 'departments'));
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
        
        $school->delete(); // Esto hará la baja lógica en cascada gracias al Trait

        return response()->json(['message' => 'Escuela eliminada'], 200);
    }

    //Restaurar escuela
    public function restore($id)
    {
        // Buscamos INCLUSO entre los eliminados
        $school = School::withoutGlobalScope('active')->findOrFail($id);
        
        $school->restore(); // Método del Trait


        return redirect()->route('schools.index', ['view_deleted' => 1])
                         ->with('success', 'Escuela restaurada correctamente.');
    }
}
