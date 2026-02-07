<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Result;
use App\Models\Department;
use App\Models\PoliticalParty;
use Illuminate\Support\Facades\DB;
use App\Models\Mesa;

class GraphicController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::all();

        // 1. Definimos cuáles son los partidos "Válidos" (Candidatos)
        // Excluimos Blanco y Nulo para el cálculo base
        $validParties = PoliticalParty::whereNotIn('name', ['Blanco', 'Nulo', 'Impugnado'])->get();

        // 2. Calculamos el TOTAL DE VOTOS VÁLIDOS (El denominador)
        // Hacemos una consulta para sumar todo lo que sea de estos partidos
        $validVotesQuery = Result::whereIn('political_party_id', $validParties->pluck('id'));
        
        // Aplicamos el mismo filtro de departamento al totalizador
        if ($request->filled('department_id')) {
            $validVotesQuery->whereHas('mesa.school', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }
        
        $totalValidVotes = $validVotesQuery->sum('votes');

        $labels = [];
        $data = [];
        $colors = [];

        // 3. Recorremos SOLO los partidos válidos para el gráfico
        foreach ($validParties as $party) {
            
            // Consulta de votos para este partido específico
            $query = Result::where('political_party_id', $party->id);

            // Filtro de depto
            if ($request->filled('department_id')) {
                $query->whereHas('mesa.school', function($q) use ($request) {
                    $q->where('department_id', $request->department_id);
                });
            }

            $partyVotes = $query->sum('votes');

            // 4. Calculamos Porcentaje: (Votos Partido / Votos Válidos) * 100
            if ($partyVotes > 0) { 
                $percentage = ($totalValidVotes > 0) 
                            ? round(($partyVotes / $totalValidVotes) * 100, 2) 
                            : 0;

                $labels[] = $party->name;
                $data[] = $percentage; // <--- Aquí guardamos el % (ej: 45.5)
                $colors[] = $party->color_hex;
            }
        }

        // 1. Iniciamos la consulta base de Mesas
    $mesasQuery = Mesa::query();

    // 2. Si hay filtro de departamento, filtramos las mesas por su escuela
    if ($request->filled('department_id')) {
        $mesasQuery->whereHas('school', function($q) use ($request) {
            $q->where('department_id', $request->department_id);
        });
    }

    // 3. Obtenemos los contadores (usamos clone para no afectar la query original)
    $totalMesas = (clone $mesasQuery)->count();
    $mesasEscrutadas = (clone $mesasQuery)->where('status', 'scrutinized')->count();

    // 4. Calculamos porcentaje (evitando división por cero)
    $avance = $totalMesas > 0 ? round(($mesasEscrutadas / $totalMesas) * 100, 1) : 0;

        return view('graphics.index', compact('labels', 'data', 'colors', 'departments', 'avance', 'totalMesas', 'mesasEscrutadas'));
    }
}