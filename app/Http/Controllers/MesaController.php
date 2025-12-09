<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mesa;
use App\Models\School;
use App\Models\User;


class MesaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user(); // Usuario logueado

        if ($user->role === 'admin') {
            // Admin ve todo, cargamos relaciones para mostrar nombres en la tabla
            $mesas = Mesa::with(['school', 'fiscal'])->get();
        } else {
            // Fiscal ve solo lo suyo
            $mesas = Mesa::with(['school'])->where('user_id', $user->id)->get();
        }

        return view('mesas.index', compact('mesas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Necesitamos la lista de escuelas para el <select>
        $schools = School::all();
        return view('mesas.create', compact('schools'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|integer|unique:mesas,number',
            'school_id' => 'required|exists:schools,id',
        ]);

        // Por defecto nace con status 'created' (definido en la migración)
        $mesa = Mesa::create($validated);

        return response()->json([
            'message' => 'Mesa creada exitosamente',
            'mesa' => $mesa
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Cargamos la mesa con sus resultados y partidos políticos asociados
        $mesa = Mesa::with(['school', 'fiscal', 'results.politicalParty'])->findOrFail($id);
        
        return view('mesas.show', compact('mesa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $mesa = Mesa::findOrFail($id);
        $schools = School::all();
        
        // Buscamos solo usuarios con rol 'user' para asignar
        // Asumiendo que en DB el rol fiscal es 'user' 
        $fiscals = User::where('role', 'user')->get(); 

        return view('mesas.edit', compact('mesa', 'schools', 'fiscals'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $mesa = Mesa::findOrFail($id);

        $validated = $request->validate([
            'number' => 'sometimes|integer|unique:mesas,number,' . $mesa->id,
            'school_id' => 'sometimes|exists:schools,id',
            'user_id' => 'nullable|exists:users,id', // Asignar fiscal
            'status' => 'sometimes|in:created,asigned,scrutinized',
        ]);

        // Si asignamos un usuario y el estado seguía en 'created', lo pasamos a 'asigned'
        if (isset($validated['user_id']) && $mesa->status === 'created') {
            $validated['status'] = 'asigned';
        }

        $mesa->update($validated);

        return response()->json([
            'message' => 'Mesa actualizada correctamente',
            'mesa' => $mesa
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $mesa = Mesa::findOrFail($id);
        $mesa->delete();

        return response()->json(['message' => 'Mesa eliminada'], 200);
    }

    /**
     * METODO ESPECIAL: Subir Foto del Acta
     * Se llamará desde Vue con FormData
     */
    public function uploadActa(Request $request, string $id)
    {
        $mesa = Mesa::findOrFail($id);

        // Validación de imagen (JPG/PNG, máx 10MB para cámaras modernas)
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:10240', 
        ]);

        // 1. Guardar archivo en disco 'public' dentro de la carpeta 'actas'
        // Esto devuelve el path relativo: "actas/nombre_archivo.jpg"
        $path = $request->file('image')->store('actas', 'public');

        // 2. Actualizar base de datos
        $mesa->update([
         'image_path' => $path,
         'status' => 'scrutinized'
            ]);

        return response()->json([
            'message' => 'Acta subida correctamente',
            'path' => $path
        ], 200);
    }


}
