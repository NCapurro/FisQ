<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\PoliticalParty;
use App\Models\Incident; 
use App\Models\Mesa;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ResultadosExport; 
use App\Exports\IncidentesExport; 
use Illuminate\Support\Str;

class ReportController extends Controller
{
    // ==========================================
    // 1. REPORTE MATRIZ (Resultados x Depto)
    // ==========================================
    public function resultados(Request $request)
    {
        $data = $this->calcularMatriz();
        
        // Si pide exportar
        if ($request->has('format')) {
            if ($request->format === 'pdf') {
                $pdf = Pdf::loadView('reports.resultados_pdf', $data);
                return $pdf->setPaper('a4', 'landscape')->stream('resultados-departamentales.pdf');
            }
            if ($request->format === 'excel') {
                return Excel::download(new ResultadosExport($data), 'resultados.xlsx');
            }
        }

        return view('reports.resultados', $data);
    }

    // Función auxiliar para no repetir código en PDF/Excel/Web
    private function calcularMatriz()
    {
        // Filtramos las mesas activas dentro del Eager Loading
        $departments = Department::with([
            'schools.mesas' => function($query) {
                // 1. Aquí aplicamos el filtro a la tabla 'mesas'
                $query->where('is_active', true)
                      // 2. Y encadenamos la carga de resultados SOLO para esas mesas activas
                      ->with('results'); 
            }
        ])->get();

        $parties = PoliticalParty::whereNotIn('name', ['Blanco', 'Nulo', 'Impugnado'])->get();

        $reporte = [];

        foreach ($departments as $depto) {
            $fila = [
                'departamento' => $depto->name,
                'votos_partidos' => [],
                'total_valido' => 0,
                'electores' => 0, // Para calcular participación
                'votantes' => 0   // Suma real de votos emitidos
            ];

            // Iteramos escuelas y mesas para sumar electores
            $electoresDepto = 0;
            $votosTotalesDepto = 0; // Incluye nulos y blancos

            foreach($depto->schools as $school) {
                foreach($school->mesas as $mesa) {
                    $electoresDepto += $mesa->electores_totales ?: 350; // Default 350 si es null
                    // Aquí podrías sumar también los votos nulos/blancos para el total de participación
                }
            }
            
            $fila['electores'] = $electoresDepto;

            // Calculamos votos por partido
            foreach ($parties as $party) {
                // Magia de Laravel Collections: sumamos sin hacer mil queries SQL
                $votos = $depto->schools->flatMap->mesas->flatMap->results
                        ->where('political_party_id', $party->id)
                        ->sum('votes');

                $fila['votos_partidos'][$party->id] = $votos;
                $fila['total_valido'] += $votos;
            }

            // Cálculo de Participación (Estimado sobre votos válidos por ahora)
            // Para hacerlo exacto deberías sumar nulos y blancos también
            $fila['participacion'] = ($electoresDepto > 0) 
                ? round(($fila['total_valido'] / $electoresDepto) * 100, 1) 
                : 0;

            $reporte[] = $fila;
        }

        return compact('reporte', 'parties');
    }

    // ==========================================
    // 2. REPORTE MAPA DE CALOR (Ganadores)
    // ==========================================
    public function mapa(Request $request)
    {
       $departments = Department::with([
            'schools.mesas' => function($query) {
                $query->where('is_active', true)
                      ->with('results'); 
            }
        ])->get(); //No deberian existir mesas inactivas, pero por las dudas filtramos acá también
       
        $parties = PoliticalParty::all(); // Incluimos todos para buscar colores

        $coloresMap = [];
        $ganadores = [];

        foreach ($departments as $depto) {
            $maxVotos = -1;
            $ganadorColor = '#cccccc'; // Gris por defecto (Empate o sin datos)
            $ganadorNombre = 'Sin Datos';

            foreach ($parties as $party) {
                if (in_array($party->name, ['Blanco', 'Nulo', 'Impugnado'])) continue;

                $votos = $depto->schools->flatMap->mesas->flatMap->results
                        ->where('political_party_id', $party->id)
                        ->sum('votes');

                if ($votos > $maxVotos && $votos > 0) {
                    $maxVotos = $votos;
                    $ganadorColor = $party->color_hex;
                    $ganadorNombre = $party->name;
                }
            }

            // Normalizamos el nombre para que coincida con el ID del SVG
            // Ej: "Paraná" -> "parana", "Gualeguaychú" -> "gualeguaychu"
            $slug = Str::slug($depto->name); 
            
            $coloresMap[$slug] = $ganadorColor;
            
            $ganadores[] = [
                'depto' => $depto->name,
                'ganador' => $ganadorNombre,
                'votos' => $maxVotos,
                'color' => $ganadorColor
            ];
        }

        if ($request->has('format') && $request->format === 'pdf') {
            $pdf = Pdf::loadView('reports.mapa_pdf', compact('coloresMap', 'ganadores'));
            return $pdf->stream('mapa-ganadores.pdf');
        }

        return view('reports.mapa', compact('coloresMap', 'ganadores'));
    }

    // ==========================================
    // 3. REPORTE DE INCIDENTES
    // ==========================================
    public function incidentes(Request $request)
    {
        $query = Incident::with(['user', 'mesa.school']);

        // Filtros opcionales
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->fecha);
        }

        $incidentes = $query->latest()->get();

        if ($request->has('format')) {
            if ($request->format === 'pdf') {
                $pdf = Pdf::loadView('reports.incidentes_pdf', compact('incidentes'));
                return $pdf->stream('reporte-incidentes.pdf');
            }
            if ($request->format === 'excel') {
                return Excel::download(new IncidentesExport($incidentes), 'incidentes.xlsx');
            }
        }

        return view('reports.incidentes', compact('incidentes'));
    }
}