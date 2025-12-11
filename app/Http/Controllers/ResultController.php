<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Result;
use App\Models\Mesa;
use App\Models\PoliticalParty;
use Illuminate\Support\Facades\DB;

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

        $parties = PoliticalParty::all();

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
        ]);

        try {
            DB::beginTransaction();

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


            // Actualizar estado de la mesa
            $mesa->update(['status' => 'scrutinized','image_path' => $pathEnBaseDeDatos]);

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
        // CORREGIDO: Usamos $id que viene por parámetro
        $mesa = Mesa::with(['results.politicalParty', 'school', 'fiscal'])->findOrFail($id);

        return view('results.show', compact('mesa'));
    }
}