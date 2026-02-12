<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Incident; 
use App\Models\Mesa;     
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class IncidentController extends Controller
{
    /**
     * Mostrar lista de incidencias (Para Admin)
     */
    public function index()
    {
        // Traemos con eager loading para evitar el problema N+1
        $incidents = Incident::with(['user', 'mesa.school'])
            ->orderBy('is_resolved', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('incidents.index', compact('incidents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Si viene de una mesa específica, ya la dejamos seleccionada
        $mesa_id = $request->query('mesa_id');
        
        // Es mejor traer solo las mesas activas
        $mesas = Mesa::where('is_active', true)->orderBy('number')->get();

        return view('incidents.create', compact('mesas', 'mesa_id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|min:10',
            'priority'    => 'required|in:baja,media,alta',
            'mesa_id'     => 'nullable|exists:mesas,id',
        ]);

        Incident::create([
            'user_id'     => Auth::id(),
            'mesa_id'     => $request->mesa_id,
            'description' => $request->description,
            'priority'    => $request->priority,
            'is_resolved' => false,
        ]);

        // Si el fiscal reportó desde una mesa, quizás quieras devolverlo a la mesa
        

        // Si fue un reporte general, volvemos al listado de mesas.
        return redirect()->route('mesas.index')
            ->with('success', 'Incidencia reportada correctamente.');
    }

    /**
     * Marcar incidencia como resuelta
     */
    public function resolve(Incident $incident)
    {
        $incident->update(['is_resolved' => true]);

        return back()->with('success', 'Incidencia marcada como resuelta.');
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


}
