<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Result;
use App\Models\Mesa;
use App\Models\PoliticalParty;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;

class ResultController extends Controller
{
    /**
     * Muestra el formulario para cargar votos (La "Boleta Virtual").
     */
    public function create(Request $request, $mesaId)
    {
        $mesa = Mesa::with('results')->findOrFail($mesaId);

        // SEGURIDAD: Solo admin o el fiscal asignado
        if ($request->user()->role !== 'admin' && $mesa->user_id !== $request->user()->id) {
            abort(403, 'No tienes permiso para cargar resultados en esta mesa.');
        }

        $parties = PoliticalParty::orderBy('id', 'desc')->get();

        return view('results.create', compact('mesa', 'parties'));
    }

    /**
     * Guarda el escrutinio (Transacción Segura).
     */
    public function store(Request $request, $mesaId)
    {
        $mesa = Mesa::findOrFail($mesaId);

        // 1. SEGURIDAD
        if ($request->user()->role !== 'admin' && $mesa->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // 2. VALIDACIÓN
        $request->validate([
            'votes' => 'required|array',
            'votes.*' => 'integer|min:0',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'photo' => 'nullable|image|max:10240',
            'electores_totales' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();
            // --- LÓGICA DE AUDITORÍA SIMPLIFICADA ---
            // Si el usuario que envía la petición NO es el fiscal asignado a la mesa
            if ($request->user()->id !== $mesa->user_id) {
                ActivityLog::registrar(
                    'intervencion_externa', 
                    'Escrutinio', 
                    "Un agente externo ID: {$request->user()->id} cargó/modificó resultados en la Mesa N° {$mesa->number} - ID: {$mesa->id}"
                );
            }

            foreach ($request->votes as $partyId => $voteCount) {
                Result::updateOrCreate(
                    [       //array de búsqueda
                        'mesa_id' => $mesa->id,
                        'political_party_id' => $partyId
                    ],
                    [       //array de valores a insertar o actualizar
                        'votes' => $voteCount,
                        'user_id' => $request->user()->id,
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                    ]
                );
            }

            // 2. LÓGICA DE LA FOTO DE ACTA ESCANEADA
        $pathEnBaseDeDatos = $mesa->image_path; // Mantenemos la que tenía por si no sube nueva

        if ($request->hasFile('photo')) {
            // A. Recibimos el archivo binario desde el formulario
            $archivo = $request->file('photo');

            // B. Guardamos el archivo físico en el disco duro del servidor
            // Laravel crea un nombre único (ej: "asd897a9s8d7.jpg") y lo pone en la carpeta "actas"
            $rutaGuardada = $archivo->store('actas', 'public'); 

            // C. Asignamos esa ruta string a nuestra variable
            $pathEnBaseDeDatos = $rutaGuardada; 
        }

            // --- ACTUALIZACIÓN DE LA MESA ---
            
            // Preparamos los datos básicos a actualizar
            $datosMesa = [
                'status' => 'scrutinized',
                'image_path' => $pathEnBaseDeDatos
            ];

            // Si el usuario mandó el total de electores, lo agregamos al array
            if ($request->filled('electores_totales')) { 
                $datosMesa['electores_totales'] = $request->input('electores_totales');
            }

            // Actualizar estado de la mesa
            // Ejecutamos el update con todos los datos juntos
            $mesa->update($datosMesa); // <--- MODIFICADO

            DB::commit();

            return response()->json(['message' => 'Resultados cargados correctamente'], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al guardar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Ver los resultados finales de una mesa.
     */
    public function show(string $id)
{
    $mesa = Mesa::with([
        // Aquí ordenamos la relación 'results' directamente desde SQL
        'results' => function ($query) {
            $query->orderBy('political_party_id', 'desc');
        }, 
        'results.politicalParty', 
        'school', 
        'fiscal'
    ])->findOrFail($id);

    return view('results.show', compact('mesa'));
}

    //Metodo para eliminar un resultado (baja logica)
    public function destroy(string $id)
    {
        $result = Result::findOrFail($id);
        $result->delete(); // Esto hará la baja lógica gracias al Trait

        // Log: ELIMINAR
        ActivityLog::registrar(
            'eliminar', 
            'Resultados', 
            "Eliminó el Resultado ID: {$result->id} de la Mesa ID: {$result->mesa_id}"
        );


        return response()->json(['message' => 'Resultado eliminado'], 200);
    }

    //Restaurar resultado
    public function restore($id)
    {
        // Buscamos INCLUSO entre los eliminados
        $result = Result::withoutGlobalScope('active')->findOrFail($id);
        
        $result->restore(); // Método del Trait
        //Log: RESTAURAR
        ActivityLog::registrar(
            'restaurar', 
            'Resultados', 
            "Restauró el Resultado ID: {$result->id} de la Mesa ID: {$result->mesa_id}"
        );

        return redirect()->route('mesas.show', $result->mesa_id)
                         ->with('success', 'Resultado restaurado correctamente.');
    }
}